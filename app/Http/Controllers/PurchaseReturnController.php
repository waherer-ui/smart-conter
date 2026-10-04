<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }


    /*
    |--------------------------------------------------------------------------
    | FORM RETUR SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function create($purchaseId)
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('stock_purchase')) {

            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Pembelian & Stok Masuk hanya tersedia pada paket Pro dan Premium.'
                );
        }


        $purchase = Purchase::where(
            'store_id',
            $store->id
        )
        ->with([
            'supplier',
            'user',
            'items.product',
        ])
        ->findOrFail($purchaseId);


        /*
        |--------------------------------------------------------------------------
        | JUMLAH YANG SUDAH DIRETUR
        |--------------------------------------------------------------------------
        */

        $returnedQuantities = PurchaseReturnItem::whereHas(
            'purchaseReturn',
            function ($query) use ($purchase) {

                $query->where(
                    'purchase_id',
                    $purchase->id
                );
            }
        )
        ->selectRaw(
            'purchase_item_id, SUM(quantity) as total_returned'
        )
        ->groupBy(
            'purchase_item_id'
        )
        ->pluck(
            'total_returned',
            'purchase_item_id'
        );


        /*
        |--------------------------------------------------------------------------
        | SIAPKAN DATA ITEM
        |--------------------------------------------------------------------------
        */

        foreach ($purchase->items as $item) {

            $alreadyReturned = (int) (
                $returnedQuantities[$item->id] ?? 0
            );

            $item->already_returned =
                $alreadyReturned;

            $item->returnable_quantity =
                max(
                    0,
                    (int) $item->quantity -
                    $alreadyReturned
                );

            $item->current_stock =
                $item->product
                    ? (int) $item->product->stock
                    : 0;
        }


        return view(
            'purchase_returns.create',
            compact(
                'purchase',
                'store'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN RETUR SUPPLIER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $purchaseId)
    {
        /*
        |--------------------------------------------------------------------------
        | STORE AKTIF
        |--------------------------------------------------------------------------
        */

        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('stock_purchase')) {

            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Pembelian & Stok Masuk hanya tersedia pada paket Pro dan Premium.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'quantity' => [
                'required',
                'array',
            ],

            'quantity.*' => [
                'required',
                'integer',
                'min:0',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

        ]);


        $userId = session('user_id');


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        try {

    $purchaseReturn = DB::transaction(
        function () use (
            $validated,
            $purchaseId,
            $store,
            $userId
        ) {

                /*
                |--------------------------------------------------------------------------
                | KUNCI PEMBELIAN
                |--------------------------------------------------------------------------
                */

                $purchase = Purchase::where(
                    'store_id',
                    $store->id
                )
                ->lockForUpdate()
                ->findOrFail($purchaseId);


                /*
                |--------------------------------------------------------------------------
                | KUNCI ITEM PEMBELIAN
                |--------------------------------------------------------------------------
                */

                $purchaseItems = PurchaseItem::where(
                    'purchase_id',
                    $purchase->id
                )
                ->with('product')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');


                /*
                |--------------------------------------------------------------------------
                | JUMLAH RETUR SEBELUMNYA
                |--------------------------------------------------------------------------
                */

                $returnedQuantities = PurchaseReturnItem::whereHas(
                    'purchaseReturn',
                    function ($query) use ($purchase) {

                        $query->where(
                            'purchase_id',
                            $purchase->id
                        );
                    }
                )
                ->selectRaw(
                    'purchase_item_id, SUM(quantity) as total_returned'
                )
                ->groupBy(
                    'purchase_item_id'
                )
                ->pluck(
                    'total_returned',
                    'purchase_item_id'
                );


                /*
                |--------------------------------------------------------------------------
                | SIAPKAN ITEM YANG DIRETUR
                |--------------------------------------------------------------------------
                */

                $returnItems = [];

                $total = 0;


                foreach ($validated['quantity'] as $purchaseItemId => $quantity) {

                    $quantity = (int) $quantity;


                    if ($quantity <= 0) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PASTIKAN ITEM BERASAL DARI PEMBELIAN INI
                    |--------------------------------------------------------------------------
                    */

                    $purchaseItem =
                        $purchaseItems->get(
                            (int) $purchaseItemId
                        );


                    if (!$purchaseItem) {

                        throw new \RuntimeException(
                            'Item pembelian tidak valid.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH SUDAH DIRETUR
                    |--------------------------------------------------------------------------
                    */

                    $alreadyReturned = (int) (
                        $returnedQuantities[$purchaseItem->id] ?? 0
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH YANG MASIH BOLEH DIRETUR
                    |--------------------------------------------------------------------------
                    */

                    $remaining =
                        max(
                            0,
                            (int) $purchaseItem->quantity -
                            $alreadyReturned
                        );


                    if ($quantity > $remaining) {

                        throw new \RuntimeException(
                            "Jumlah retur {$purchaseItem->product_name} "
                            . "melebihi sisa barang yang dapat diretur."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PRODUK
                    |--------------------------------------------------------------------------
                    */

                    $product = Product::where(
                        'id',
                        $purchaseItem->product_id
                    )
                    ->where(
                        'store_id',
                        $store->id
                    )
                    ->lockForUpdate()
                    ->first();


                    if (!$product) {

                        throw new \RuntimeException(
                            "Produk {$purchaseItem->product_name} tidak ditemukan."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CEK STOK
                    |--------------------------------------------------------------------------
                    */

                    $stockBefore =
                        (int) $product->stock;


                    if ($quantity > $stockBefore) {

                        throw new \RuntimeException(
                            "Stok {$purchaseItem->product_name} "
                            . "tidak mencukupi untuk retur."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG SUBTOTAL
                    |--------------------------------------------------------------------------
                    */

                    $subtotal =
                        $quantity *
                        (float) $purchaseItem->capital_price;


                    $total += $subtotal;


                    /*
                    |--------------------------------------------------------------------------
                    | KURANGI STOK
                    |--------------------------------------------------------------------------
                    */

                    $product->decrement(
                        'stock',
                        $quantity
                    );


                    $stockAfter =
                        $stockBefore -
                        $quantity;


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN ITEM RETUR
                    |--------------------------------------------------------------------------
                    */

                    $returnItems[] = [

                        'purchase_item_id' =>
                            $purchaseItem->id,

                        'product_id' =>
                            $product->id,

                        'product_name' =>
                            $purchaseItem->product_name,

                        'sku' =>
                            $purchaseItem->sku,

                        'capital_price' =>
                            $purchaseItem->capital_price,

                        'quantity' =>
                            $quantity,

                        'subtotal' =>
                            $subtotal,

                        'stock_before' =>
                            $stockBefore,

                        'stock_after' =>
                            $stockAfter,

                        'product' =>
                            $product,

                    ];
                }


                /*
                |--------------------------------------------------------------------------
                | HARUS ADA BARANG YANG DIRETUR
                |--------------------------------------------------------------------------
                */

                if (empty($returnItems)) {

                    throw new \RuntimeException(
                        'Masukkan minimal satu jumlah barang yang akan diretur.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NOMOR RETUR
                |--------------------------------------------------------------------------
                */

                $returnNumber =
                    'RT-PB-' .
                    now()->format('Ymd-His') .
                    '-' .
                    strtoupper(
                        substr(
                            uniqid(),
                            -5
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | HEADER RETUR
                |--------------------------------------------------------------------------
                */

                $purchaseReturn =
                    PurchaseReturn::create([

                        'store_id' =>
                            $store->id,

                        'purchase_id' =>
                            $purchase->id,

                        'supplier_id' =>
                            $purchase->supplier_id,

                        'user_id' =>
                            $userId,

                        'return_number' =>
                            $returnNumber,

                        'return_date' =>
                            now()->toDateString(),

                        'total' =>
                            $total,

                        'reason' =>
                            $validated['reason'] ?? null,

                        'notes' =>
                            $validated['notes'] ?? null,

                    ]);


                /*
                |--------------------------------------------------------------------------
                | SIMPAN ITEM + PRODUCT HISTORY
                |--------------------------------------------------------------------------
                */

                foreach ($returnItems as $item) {

                    PurchaseReturnItem::create([

                        'purchase_return_id' =>
                            $purchaseReturn->id,

                        'purchase_item_id' =>
                            $item['purchase_item_id'],

                        'product_id' =>
                            $item['product_id'],

                        'product_name' =>
                            $item['product_name'],

                        'sku' =>
                            $item['sku'],

                        'capital_price' =>
                            $item['capital_price'],

                        'quantity' =>
                            $item['quantity'],

                        'subtotal' =>
                            $item['subtotal'],

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | RIWAYAT STOK
                    |--------------------------------------------------------------------------
                    */

                    $userName =
                        session('user_name')
                        ?? session('username')
                        ?? 'Admin';


                    ProductHistory::create([

                        'store_id' =>
                            $store->id,

                        'product_id' =>
                            $item['product_id'],

                        'user_id' =>
                            $userId,

                        'sku' =>
                            $item['sku'],

                        'name' =>
                            $item['product_name'],

                        'added_stock' =>
                            0,

                        'status_type' =>
                            'Retur Supplier | '
                            . $userName
                            . ' | Stok: '
                            . $item['stock_before']
                            . ' → '
                            . $item['stock_after'],

                        'description' =>
                            'Retur supplier '
                            . $purchaseReturn->return_number,

                    ]);
                }


                return $purchaseReturn;
        }
    );

} catch (\RuntimeException $e) {

    return redirect()
        ->back()
        ->withInput()
        ->with(
            'error',
            $e->getMessage()
        );
}

/*
|--------------------------------------------------------------------------
| AUDIT LOG
|--------------------------------------------------------------------------
*/

AuditLogService::log(
    'purchase_return_created',
    'Mencatat retur supplier "' .
    $purchaseReturn->supplier->name .
    '" dengan nomor retur "' .
    $purchaseReturn->return_number .
    '" sebesar Rp' .
    number_format(
        $purchaseReturn->total,
        0,
        ',',
        '.'
    ) .
    '.',
    $purchaseReturn,
    null
);

/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

return redirect()
    ->route(
        'purchase.show',
        $purchaseId
    )
    ->with(
        'success',
        'Retur supplier berhasil disimpan.'
    );
    }
}