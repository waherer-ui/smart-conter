<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionReturn;
use App\Models\TransactionReturnItem;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionReturnController extends Controller
{
    /**
     * Form retur barang dari transaksi penjualan.
     */
    public function create(int $transactionId)
    {
        $activeStoreId = (int) session('active_store_id');

        if (!$activeStoreId) {
            abort(403, 'Toko aktif belum dipilih.');
        }

        $transaction = Transaction::with([
            'customer',
            'user',
            'items.product',
            'items.returnItems',
        ])
            ->where('id', $transactionId)
            ->where('store_id', $activeStoreId)
            ->firstOrFail();

        return view(
            'transaction_returns.create',
            compact('transaction')
        );
    }

    /**
     * Menyimpan retur barang dari transaksi penjualan.
     */
    public function store(Request $request, int $transactionId)
    {
        $activeStoreId = (int) session('active_store_id');

        if (!$activeStoreId) {
            abort(403, 'Toko aktif belum dipilih.');
        }

        $request->validate([
            'items' => [
                'required',
                'array',
            ],

            'items.*.quantity' => [
                'nullable',
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
                'max:2000',
            ],
        ]);

        $transaction = Transaction::where(
            'store_id',
            $activeStoreId
        )
        ->with([
            'customer',
            'items.returnItems',
        ])
        ->findOrFail($transactionId);

        $userId = (int) (
            session('user_id')
            ?: auth()->id()
        );

        if (!$userId) {
            abort(403, 'Pengguna belum terautentikasi.');
        }

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | CEK ITEM RETUR
            |--------------------------------------------------------------------------
            */

            $returnItems = [];

            foreach ($transaction->items as $transactionItem) {
                $requestedQuantity = (int) (
                    $request->input(
                        "items.{$transactionItem->id}.quantity",
                        0
                    )
                );

                if ($requestedQuantity <= 0) {
                    continue;
                }

                $soldQuantity =
                    (int) $transactionItem->quantity;

                $returnedQuantity =
                    (int) $transactionItem
                        ->returnItems
                        ->sum('quantity');

                $remainingQuantity =
                    $soldQuantity -
                    $returnedQuantity;

                if ($requestedQuantity > $remainingQuantity) {
                    throw new \RuntimeException(
                        'Jumlah retur untuk "' .
                        $transactionItem->product_name .
                        '" melebihi jumlah yang masih dapat diretur.'
                    );
                }

                $returnItems[] = [
                    'transaction_item' =>
                        $transactionItem,

                    'quantity' =>
                        $requestedQuantity,
                ];
            }

            if (empty($returnItems)) {
                throw new \RuntimeException(
                    'Tidak ada barang yang dipilih untuk diretur.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | NOMOR RETUR
            |--------------------------------------------------------------------------
            */

            do {
                $returnNumber =
                    'RT-PJ-' .
                    now()->format('Ymd-His') .
                    '-' .
                    strtoupper(
                        substr(
                            bin2hex(random_bytes(3)),
                            0,
                            6
                        )
                    );
            } while (
                TransactionReturn::where(
                    'return_number',
                    $returnNumber
                )->exists()
            );

            /*
            |--------------------------------------------------------------------------
            | TOTAL RETUR
            |--------------------------------------------------------------------------
            */

            $total = 0;

            foreach ($returnItems as $returnItem) {
                $transactionItem =
                    $returnItem['transaction_item'];

                $quantity =
                    $returnItem['quantity'];

                $total +=
                    (float) $transactionItem->price *
                    $quantity;
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN HEADER RETUR
            |--------------------------------------------------------------------------
            */

            $transactionReturn =
                TransactionReturn::create([
                    'store_id' =>
                        $activeStoreId,

                    'transaction_id' =>
                        $transaction->id,

                    'customer_id' =>
                        $transaction->customer_id,

                    'user_id' =>
                        $userId,

                    'return_number' =>
                        $returnNumber,

                    'return_date' =>
                        now()->toDateString(),

                    'total' =>
                        $total,

                    'reason' =>
                        $request->reason
                            ? trim($request->reason)
                            : null,

                    'notes' =>
                        $request->notes
                            ? trim($request->notes)
                            : null,
                ]);

            /*
            |--------------------------------------------------------------------------
            | SIMPAN ITEM + TAMBAH STOK
            |--------------------------------------------------------------------------
            */

            foreach ($returnItems as $returnItem) {
                $transactionItem =
                    $returnItem['transaction_item'];

                $quantity =
                    $returnItem['quantity'];

                $product = Product::where(
                    'id',
                    $transactionItem->product_id
                )
                ->where(
                    'store_id',
                    $activeStoreId
                )
                ->lockForUpdate()
                ->firstOrFail();

                $subtotal =
                    (float) $transactionItem->price *
                    $quantity;

                TransactionReturnItem::create([
                    'transaction_return_id' =>
                        $transactionReturn->id,

                    'transaction_item_id' =>
                        $transactionItem->id,

                    'product_id' =>
                        $product->id,

                    'product_name' =>
                        $transactionItem->product_name,

                    'sku' =>
                        $transactionItem->sku,

                    'price' =>
                        $transactionItem->price,

                    'quantity' =>
                        $quantity,

                    'subtotal' =>
                        $subtotal,
                ]);

                $stockBefore =
                    (int) $product->stock;

                $product->increment(
                    'stock',
                    $quantity
                );

                $product->refresh();

                /*
                |--------------------------------------------------------------------------
                | PRODUCT HISTORY
                |--------------------------------------------------------------------------
                */

                ProductHistory::create([
    'store_id' =>
        $activeStoreId,

    'product_id' =>
        $product->id,

    'user_id' =>
        $userId,

    'sku' =>
        $product->sku,

    'name' =>
        $product->name,

    'added_stock' =>
        0,

    'status_type' =>
        'Retur Penjualan | '
        . (
            session('user_name')
            ?? session('username')
            ?? 'Admin'
        )
        . ' | Stok: '
        . $stockBefore
        . ' → '
        . $product->stock,

    'description' =>
        'Retur penjualan '
        . $transaction->invoice_number
        . ' - '
        . $returnNumber,
]);
            }

            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG
            |--------------------------------------------------------------------------
            */

            AuditLogService::log(
                'transaction_return_created',
                'Mencatat retur penjualan "' .
                $transaction->invoice_number .
                '" dengan nomor retur "' .
                $returnNumber .
                '" sebesar Rp' .
                number_format(
                    $total,
                    0,
                    ',',
                    '.'
                ) .
                '.',
                $transactionReturn,
                null,
                $activeStoreId
            );

            DB::commit();

            return redirect()
                ->route(
                    'transaksi.show',
                    $transaction->id
                )
                ->with(
                    'success',
                    'Retur penjualan berhasil diproses.'
                );

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}