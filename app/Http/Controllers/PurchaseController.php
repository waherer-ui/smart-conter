<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Store;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\ProductHistory;
use App\Services\AuditLogService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    public function index(Request $request)
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

        $search = trim(
            (string) $request->input('search')
        );

        $supplierId = $request->input('supplier_id');

        $date = $request->input('date');

        $purchases = Purchase::where(
            'store_id',
            $store->id
        )
        ->with([
            'supplier',
            'user',
            'items',
        ])

        // 🔎 CARI PRODUK / INVOICE
        ->when($search !== '', function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'invoice_number',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhereHas(
                    'items',
                    function ($itemQuery) use ($search) {

                        $itemQuery->where(
                            'product_name',
                            'like',
                            '%' . $search . '%'
                        );
                    }
                );
            });
        })

        // 🏪 FILTER SUPPLIER
        ->when(
            $supplierId,
            function ($query) use ($supplierId) {

                $query->where(
                    'supplier_id',
                    $supplierId
                );
            }
        )

        // 📅 FILTER TANGGAL
        ->when(
            $date,
            function ($query) use ($date) {

                $query->whereDate(
                    'purchase_date',
                    $date
                );
            }
        )

        ->latest('purchase_date')
        ->latest('id')
        ->paginate(15)
        ->withQueryString();

        $suppliers = $store->suppliers()
            ->orderBy('name')
            ->get();

        return view(
            'purchases.index',
            compact(
                'purchases',
                'suppliers',
                'store',
                'search',
                'supplierId',
                'date'
            )
        );
    }

    public function create()
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

        $suppliers = $store->suppliers()
            ->orderBy('name')
            ->get();

        $products = $store->products()
            ->orderBy('name')
            ->get();

        return view(
            'purchases.create',
            compact(
                'store',
                'suppliers',
                'products'
            )
        );
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'supplier_id' => [
                'required',
                'integer',
            ],

            'invoice_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'item_type' => [
                'required',
                'array',
                'min:1',
            ],

            'item_type.*' => [
                'required',
                'in:existing,new',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | AMBIL STORE AKTIF
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
        | VALIDASI SUPPLIER MILIK STORE
        |--------------------------------------------------------------------------
        */

        $supplier = $store->suppliers()
            ->findOrFail(
                $request->supplier_id
            );

        /*
        |--------------------------------------------------------------------------
        | USER LOGIN
        |--------------------------------------------------------------------------
        */

        $userId = (int) session('user_id');

        if (!$userId) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Sesi pengguna tidak ditemukan. Silakan login kembali.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA ITEM
        |--------------------------------------------------------------------------
        */

        $itemTypes = $request->input(
            'item_type',
            []
        );

        $productIds = $request->input(
            'product_id',
            []
        );

        $capitalPrices = $request->input(
            'capital_price',
            []
        );

        $quantities = $request->input(
            'quantity',
            []
        );

        $newNames = $request->input(
            'new_name',
            []
        );

        $newCategories = $request->input(
            'new_category',
            []
        );

        $newBrands = $request->input(
            'new_brand',
            []
        );

        $newCapitalPrices = $request->input(
            'new_capital_price',
            []
        );

        $newPrices = $request->input(
            'new_price',
            []
        );

        $newQuantities = $request->input(
            'new_quantity',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN ADA ITEM
        |--------------------------------------------------------------------------
        */

        if (count($itemTypes) < 1) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Belum ada produk yang ditambahkan ke pembelian.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DALAM TRANSACTION
        |--------------------------------------------------------------------------
        */

        $purchase = DB::transaction(function () use (
            $store,
            $supplier,
            $userId,
            $request,
            $itemTypes,
            $productIds,
            $capitalPrices,
            $quantities,
            $newNames,
            $newCategories,
            $newBrands,
            $newCapitalPrices,
            $newPrices,
            $newQuantities
        ) {

            /*
            |--------------------------------------------------------------------------
            | NOMOR INVOICE
            |--------------------------------------------------------------------------
            */

            $invoiceNumber = trim(
                (string) $request->invoice_number
            );

            if ($invoiceNumber === '') {

                $invoiceNumber =
                    'PB-' . now()->format('YmdHis');
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT HEADER PEMBELIAN
            |--------------------------------------------------------------------------
            */

            $purchase = Purchase::create([
                'store_id' => $store->id,
                'supplier_id' => $supplier->id,
                'user_id' => $userId,
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $request->purchase_date,
                'total' => 0,
                'notes' => $request->notes
                    ? trim($request->notes)
                    : null,
            ]);

            $total = 0;

            /*
            |--------------------------------------------------------------------------
            | INDEX TERPISAH
            |--------------------------------------------------------------------------
            */

            $existingIndex = 0;
            $newIndex = 0;

            /*
            |--------------------------------------------------------------------------
            | PETUGAS
            |--------------------------------------------------------------------------
            */

            $username = session(
                'username',
                'Tidak diketahui'
            );

            $role = session(
                'user_role',
                'tidak diketahui'
            );

            $petugas =
                ucfirst($role) . ': ' . $username;

            /*
            |--------------------------------------------------------------------------
            | LOOP SEMUA ITEM
            |--------------------------------------------------------------------------
            */

            foreach ($itemTypes as $type) {

                /*
                |--------------------------------------------------------------------------
                | PRODUK LAMA
                |--------------------------------------------------------------------------
                */

                if ($type === 'existing') {

                    if (
                        !isset($productIds[$existingIndex]) ||
                        !isset($capitalPrices[$existingIndex]) ||
                        !isset($quantities[$existingIndex])
                    ) {

                        throw new \Exception(
                            'Data produk lama tidak lengkap.'
                        );
                    }

                    $productId =
                        (int) $productIds[$existingIndex];

                    $capitalPrice =
                        (float) $capitalPrices[$existingIndex];

                    $quantity =
                        (int) $quantities[$existingIndex];

                    if ($capitalPrice < 0) {

                        throw new \Exception(
                            'Harga modal produk tidak valid.'
                        );
                    }

                    if ($quantity < 1) {

                        throw new \Exception(
                            'Jumlah produk minimal 1.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CARI PRODUK DALAM STORE AKTIF
                    |--------------------------------------------------------------------------
                    */

                    $product = $store->products()
                        ->findOrFail(
                            $productId
                        );

                    $stockBefore =
                        (int) $product->stock;

                    $stockAfter =
                        $stockBefore + $quantity;

                    $subtotal =
                        $capitalPrice * $quantity;

                    $total += $subtotal;

                    /*
                    |--------------------------------------------------------------------------
                    | PURCHASE ITEM
                    |--------------------------------------------------------------------------
                    */

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'capital_price' => $capitalPrice,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | TAMBAH STOK
                    |--------------------------------------------------------------------------
                    */

                    $product->increment(
                        'stock',
                        $quantity
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE MODAL TERBARU
                    |--------------------------------------------------------------------------
                    */

                    $product->update([
                        'capital_price' => $capitalPrice,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | RIWAYAT RESTOCK
                    |--------------------------------------------------------------------------
                    */

                    ProductHistory::create([
                        'store_id' => $store->id,
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'added_stock' => $quantity,
                        'status_type' =>
                            'Restock | ' .
                            $petugas .
                            ' | Stok: ' .
                            $stockBefore .
                            ' → ' .
                            $stockAfter,
                    ]);

                    $existingIndex++;

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUK BARU
                |--------------------------------------------------------------------------
                */

                if ($type === 'new') {

                    if (
                        !isset($newNames[$newIndex]) ||
                        !isset($newCategories[$newIndex]) ||
                        !isset($newCapitalPrices[$newIndex]) ||
                        !isset($newPrices[$newIndex]) ||
                        !isset($newQuantities[$newIndex])
                    ) {

                        throw new \Exception(
                            'Data produk baru tidak lengkap.'
                        );
                    }

                    $name =
                        trim(
                            (string) $newNames[$newIndex]
                        );

                    $category =
                        trim(
                            (string) $newCategories[$newIndex]
                        );

                    $brand =
                        isset($newBrands[$newIndex])
                            ? trim(
                                (string) $newBrands[$newIndex]
                            )
                            : null;

                    $capitalPrice =
                        (float) $newCapitalPrices[$newIndex];

                    $price =
                        (float) $newPrices[$newIndex];

                    $quantity =
                        (int) $newQuantities[$newIndex];

                    if ($name === '') {

                        throw new \Exception(
                            'Nama produk baru wajib diisi.'
                        );
                    }

                    if ($category === '') {

                        throw new \Exception(
                            'Kategori produk baru wajib diisi.'
                        );
                    }

                    if ($capitalPrice < 0) {

                        throw new \Exception(
                            'Harga modal produk baru tidak valid.'
                        );
                    }

                    if ($price < 0) {

                        throw new \Exception(
                            'Harga jual produk baru tidak valid.'
                        );
                    }

                    if ($quantity < 1) {

                        throw new \Exception(
                            'Jumlah pembelian produk baru minimal 1.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEK DUPLIKAT PRODUK
                    |--------------------------------------------------------------------------
                    */

                    $duplicate = $store->products()
                        ->whereRaw(
                            'LOWER(TRIM(name)) = ?',
                            [strtolower($name)]
                        )
                        ->whereRaw(
                            'LOWER(TRIM(category)) = ?',
                            [strtolower($category)]
                        )
                        ->exists();

                    if ($duplicate) {

                        throw new \Exception(
                            'Produk "' .
                            $name .
                            '" dengan kategori "' .
                            $category .
                            '" sudah ada.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEK BATAS PRODUK PAKET
                    |--------------------------------------------------------------------------
                    */

                    if (!$store->canAddProduct()) {

                        throw new \Exception(
                            'Batas jumlah produk pada paket Anda telah tercapai.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BUAT PREFIX SKU
                    |--------------------------------------------------------------------------
                    */

                    $prefix = strtoupper(
                        substr(
                            preg_replace(
                                '/[^a-zA-Z]/',
                                '',
                                $category
                            ),
                            0,
                            3
                        )
                    );

                    if ($prefix === '') {
                        $prefix = 'PRD';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | NOMOR SKU
                    |--------------------------------------------------------------------------
                    */

                    $existingSkus = Product::where(
                        'store_id',
                        $store->id
                    )
                    ->where(
                        'category',
                        $category
                    )
                    ->where(
                        'sku',
                        'like',
                        $prefix . '-%'
                    )
                    ->pluck('sku');

                    $maxNumber = 0;

                    foreach ($existingSkus as $existingSku) {

                        if (
                            preg_match(
                                '/^' .
                                preg_quote(
                                    $prefix,
                                    '/'
                                ) .
                                '-(\d+)$/i',
                                $existingSku,
                                $matches
                            )
                        ) {

                            $number = (int) $matches[1];

                            if ($number > $maxNumber) {
                                $maxNumber = $number;
                            }
                        }
                    }

                    $nextNumber =
                        $maxNumber + 1;

                    $sku =
                        $prefix .
                        '-' .
                        str_pad(
                            $nextNumber,
                            3,
                            '0',
                            STR_PAD_LEFT
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | PENGAMAN SKU TERAKHIR
                    |--------------------------------------------------------------------------
                    */

                    while (
                        Product::where(
                            'store_id',
                            $store->id
                        )
                        ->where(
                            'sku',
                            $sku
                        )
                        ->exists()
                    ) {

                        $nextNumber++;

                        $sku =
                            $prefix .
                            '-' .
                            str_pad(
                                $nextNumber,
                                3,
                                '0',
                                STR_PAD_LEFT
                            );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BUAT PRODUK BARU
                    |--------------------------------------------------------------------------
                    */

                    $product = Product::create([
                        'store_id' => $store->id,
                        'sku' => $sku,
                        'name' => $name,
                        'category' => $category,
                        'brand' => $brand ?: null,
                        'capital_price' => $capitalPrice,
                        'price' => $price,
                        'stock' => $quantity,
                        'image' => null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | PURCHASE ITEM
                    |--------------------------------------------------------------------------
                    */

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'capital_price' => $capitalPrice,
                        'quantity' => $quantity,
                        'subtotal' =>
                            $capitalPrice * $quantity,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL
                    |--------------------------------------------------------------------------
                    */

                    $total +=
                        $capitalPrice * $quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | RIWAYAT PRODUK BARU
                    |--------------------------------------------------------------------------
                    */

                    ProductHistory::create([
                        'store_id' => $store->id,
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'added_stock' => $quantity,
                        'status_type' =>
                            'Produk Baru | ' .
                            $petugas .
                            ' | Stok Awal: ' .
                            $quantity,
                    ]);

                    $newIndex++;

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | TIPE TIDAK DIKENAL
                |--------------------------------------------------------------------------
                */

                throw new \Exception(
                    'Tipe item pembelian tidak valid.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL PEMBELIAN
            |--------------------------------------------------------------------------
            */

            $purchase->update([
                'total' => $total,
            ]);

            return $purchase;
        });

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - PEMBELIAN BERHASIL
        |--------------------------------------------------------------------------
        |
        | Diletakkan di luar DB::transaction().
        | Jadi hanya pembelian yang benar-benar berhasil yang dicatat.
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'purchase_created',
            'Mencatat pembelian dari supplier "' .
            $supplier->name .
            '" dengan invoice "' .
            $purchase->invoice_number .
            '" sebesar Rp' .
            number_format(
                $purchase->total,
                0,
                ',',
                '.'
            ) .
            '.',
            $purchase,
            null,
            $store->id,
            null,
            [
                'supplier_id' =>
                    $supplier->id,

                'supplier_name' =>
                    $supplier->name,

                'invoice_number' =>
                    $purchase->invoice_number,

                'purchase_date' =>
                    $purchase->purchase_date,

                'total' =>
                    $purchase->total,

                'items_count' =>
                    $purchase->items()->count(),

                'notes' =>
                    $purchase->notes,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SELESAI
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('purchase.index')
            ->with(
                'success',
                'Pembelian berhasil disimpan dan stok berhasil diperbarui.'
            );
    }

    public function show($id)
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
        ->findOrFail($id);

        return view(
            'purchases.show',
            compact(
                'purchase',
                'store'
            )
        );
    }
}