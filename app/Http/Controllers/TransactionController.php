<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Services\AuditLogService;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\PriceRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Mendapatkan ID toko aktif.
     */
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Menentukan harga produk berdasarkan aturan harga aktif.
     */
    private function getRulePrice(Product $product, int $quantity): float
{
    $now = now();

    $rule = PriceRule::where('store_id', $product->store_id)
        ->where('is_active', true)
        ->where(function ($query) use ($product) {
            $query->where('product_id', $product->id)
                ->orWhereNull('product_id');
        })
        ->where(function ($query) use ($now) {
            $query->whereNull('start_at')
                ->orWhere('start_at', '<=', $now);
        })
        ->where(function ($query) use ($now) {
            $query->whereNull('end_at')
                ->orWhere('end_at', '>=', $now);
        })
        ->orderByRaw(
            'CASE WHEN product_id IS NULL THEN 0 ELSE 1 END'
        )
        ->latest('id')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | Tidak ada aturan
    |--------------------------------------------------------------------------
    */

    if (!$rule) {
        return (float) $product->price;
    }

    /*
    |--------------------------------------------------------------------------
    | Harga dasar
    |--------------------------------------------------------------------------
    */

    $price = (float) $product->price;

    /*
    |--------------------------------------------------------------------------
    | RESELLER / GROSIR
    |--------------------------------------------------------------------------
    */

    if ($rule->type === 'reseller') {

        /*
        |--------------------------------------------------------------------------
        | Belum mencapai minimal quantity
        |--------------------------------------------------------------------------
        */

        if (
            !$rule->min_quantity ||
            $quantity < $rule->min_quantity
        ) {
            return $price;
        }

        /*
        |--------------------------------------------------------------------------
        | Produk tertentu
        |
        | Gunakan harga khusus tetap.
        |--------------------------------------------------------------------------
        */

        if (
            $rule->product_id &&
            $rule->special_price !== null
        ) {
            return max(
                0,
                (float) $rule->special_price
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Semua produk
        |
        | Gunakan potongan Rp atau % dari harga normal.
        |--------------------------------------------------------------------------
        */

        if (
            !$rule->product_id &&
            $rule->discount_value !== null
        ) {

            if ($rule->discount_type === 'nominal') {

                $price -=
                    (float) $rule->discount_value;

            } elseif ($rule->discount_type === 'percent') {

                $price -=
                    $price *
                    (
                        (float) $rule->discount_value
                        / 100
                    );
            }

            return max(0, $price);
        }

        return $price;
    }

    /*
    |--------------------------------------------------------------------------
    | PROMOSI
    |--------------------------------------------------------------------------
    */

    if ($rule->type === 'promotion') {

        if ($rule->discount_type === 'nominal') {

            $price -=
                (float) $rule->discount_value;

        } elseif ($rule->discount_type === 'percent') {

            $price -=
                $price *
                (
                    (float) $rule->discount_value
                    / 100
                );
        }

        return max(0, $price);
    }

    return $price;
}

    /**
     * Menyimpan transaksi penjualan.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi Data
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total' => [
                'required',
                'numeric',
                'min:0',
            ],

            'paid' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'required',
                'string',
                'max:50',
            ],

            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | User Login
        |--------------------------------------------------------------------------
        */

        $userId = session('user_id');

        if (!$userId) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Session kasir tidak ditemukan. Silakan login kembali.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Toko Aktif
        |--------------------------------------------------------------------------
        */

        $storeId = $this->activeStoreId();

        if (!$storeId) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Toko aktif tidak ditemukan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Toko Aktif
        |--------------------------------------------------------------------------
        */

        $store = \App\Models\Store::find($storeId);

        if (!$store) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Toko aktif tidak ditemukan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI METODE PEMBAYARAN SESUAI PAKET
        |--------------------------------------------------------------------------
        */

        $paymentMethod = $validated['payment_method'];

        /*
        |--------------------------------------------------------------------------
        | QRIS / TRANSFER
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod === 'QRIS / Transfer'
            && !$store->hasFeature('payment_qr')
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Metode pembayaran QRIS / Transfer hanya tersedia pada paket Pro dan Premium. Silakan upgrade paket terlebih dahulu.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | DEBIT CARD
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod === 'Debit Card'
            && !$store->hasFeature('payment_bank')
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Metode pembayaran Debit Card hanya tersedia pada paket Pro dan Premium. Silakan upgrade paket terlebih dahulu.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | IDENTIFIKASI CASHBON
        |--------------------------------------------------------------------------
        */

        $isCashbon =
            $paymentMethod === 'Cashbon / Utang';

        /*
        |--------------------------------------------------------------------------
        | CASHBON HARUS MEMAKAI FITUR UTANG
        |--------------------------------------------------------------------------
        */

        if ($isCashbon) {

            if (!$store->hasFeature('debt')) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Fitur Cashbon / Utang hanya tersedia pada paket Pro dan Premium.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Cashbon Wajib Memilih Pelanggan
            |--------------------------------------------------------------------------
            */

            if (empty($validated['customer_id'])) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Silakan pilih pelanggan untuk transaksi Cashbon / Utang.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan Pelanggan Milik Toko Aktif
            |--------------------------------------------------------------------------
            */

            $customerExists = Customer::where(
                'store_id',
                $storeId
            )
            ->where(
                'id',
                $validated['customer_id']
            )
            ->exists();

            if (!$customerExists) {

                return response()->json([
                    'success' => false,
                    'message' =>
                        'Pelanggan tidak ditemukan di toko aktif.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Transaksi + Detail + Stok + Utang
        |--------------------------------------------------------------------------
        */

        try {

            $transaction = DB::transaction(
                function () use (
                    $validated,
                    $userId,
                    $storeId,
                    $isCashbon
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Generate Nomor Invoice
                    |--------------------------------------------------------------------------
                    */

                    $invoiceNumber =
                        'INV-' .
                        now()->format('YmdHis') .
                        '-' .
                        strtoupper(
                            substr(
                                uniqid(),
                                -4
                            )
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Hitung Harga Aktual + Lock Produk
                    |--------------------------------------------------------------------------
                    */

                    $actualSubtotal = 0;
                    $lockedProducts = [];

                    foreach ($validated['items'] as $item) {

                        $product = Product::where(
                            'store_id',
                            $storeId
                        )
                        ->where(
                            'id',
                            $item['id']
                        )
                        ->lockForUpdate()
                        ->first();

                        if (!$product) {

                            throw new \Exception(
                                'Produk tidak ditemukan di toko aktif.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Cek Stok
                        |--------------------------------------------------------------------------
                        */

                        if ($product->stock < $item['quantity']) {

                            throw new \Exception(
                                'Stok produk "' .
                                $product->name .
                                '" tidak mencukupi.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Harga Khusus / Harga Normal
                        |--------------------------------------------------------------------------
                        */

                        $unitPrice = $this->getRulePrice(
                            $product,
                            (int) $item['quantity']
                        );

                        $itemSubtotal =
                            $unitPrice *
                            (int) $item['quantity'];

                        $actualSubtotal += $itemSubtotal;

                        $lockedProducts[] = [
                            'product' =>
                                $product,

                            'quantity' =>
                                (int) $item['quantity'],

                            'unit_price' =>
                                $unitPrice,

                            'subtotal' =>
                                $itemSubtotal,
                        ];
                    }

                    $actualSubtotal = round(
                        $actualSubtotal,
                        2
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Diskon Manual
                    |--------------------------------------------------------------------------
                    */

                    $manualDiscount = min(
                        (float) $validated['discount'],
                        $actualSubtotal
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Total Aktual
                    |--------------------------------------------------------------------------
                    */

                    $actualTotal = round(
                        $actualSubtotal -
                        $manualDiscount,
                        2
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Validasi Pembayaran Tunai
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $validated['payment_method'] === 'Tunai'
                        && $validated['paid'] < $actualTotal
                    ) {

                        throw new \Exception(
                            'Nominal pembayaran kurang dari total tagihan.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Validasi Pembayaran Cashbon
                    |--------------------------------------------------------------------------
                    */

                    if ($isCashbon) {

                        if ($actualTotal <= 0) {

                            throw new \Exception(
                                'Transaksi Cashbon harus memiliki total tagihan.'
                            );
                        }

                        if (
                            (float) $validated['paid']
                            > $actualTotal
                        ) {

                            throw new \Exception(
                                'Nominal pembayaran tidak boleh melebihi total tagihan.'
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Hitung Kembalian
                    |--------------------------------------------------------------------------
                    */

                    $change = $isCashbon
                        ? 0
                        : max(
                            0,
                            (float) $validated['paid']
                            - $actualTotal
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Buat Transaksi
                    |--------------------------------------------------------------------------
                    */

                    $transaction = Transaction::create([

                        'store_id' =>
                            $storeId,

                        'customer_id' =>
                            $validated['customer_id'] ?? null,

                        'invoice_number' =>
                            $invoiceNumber,

                        'user_id' =>
                            $userId,

                        'subtotal' =>
                            $actualSubtotal,

                        'discount' =>
                            $manualDiscount,

                        'total' =>
                            $actualTotal,

                        'paid' =>
                            $validated['paid'],

                        'change' =>
                            $change,

                        'payment_method' =>
                            $validated['payment_method'],
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Simpan Detail Produk + Kurangi Stok
                    |--------------------------------------------------------------------------
                    */

                    foreach ($lockedProducts as $row) {

                        $product =
                            $row['product'];

                        $quantity =
                            $row['quantity'];

                        $unitPrice =
                            $row['unit_price'];

                        $itemSubtotal =
                            $row['subtotal'];

                        $transaction->items()->create([

                            'product_id' =>
                                $product->id,

                            'product_name' =>
                                $product->name,

                            'sku' =>
                                $product->sku,

                            'capital_price' =>
                                $product->capital_price,

                            'price' =>
                                $unitPrice,

                            'quantity' =>
                                $quantity,

                            'subtotal' =>
                                $itemSubtotal,
                        ]);

                        $product->stock -=
                            $quantity;

                        $product->save();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Cashbon
                    |--------------------------------------------------------------------------
                    */

                    if ($isCashbon) {

                        $customer = Customer::where(
                            'store_id',
                            $storeId
                        )
                        ->lockForUpdate()
                        ->find(
                            $validated['customer_id']
                        );

                        if (!$customer) {

                            throw new \Exception(
                                'Pelanggan tidak ditemukan di toko aktif.'
                            );
                        }

                        $remainingDebt =
                            (float) $transaction->total -
                            (float) $transaction->paid;

                        if ($remainingDebt > 0) {

                            Debt::create([

                                'store_id' =>
                                    $storeId,

                                'customer_id' =>
                                    $customer->id,

                                'user_id' =>
                                    $userId,

                                'description' =>
                                    'Cashbon transaksi ' .
                                    $transaction->invoice_number,

                                'amount' =>
                                    $remainingDebt,

                                'paid_amount' =>
                                    0,

                                'debt_date' =>
                                    now()->toDateString(),

                                'due_date' =>
                                    null,

                                'status' =>
                                    'unpaid',
                            ]);
                        }
                    }

                    return $transaction;
                }
            );

            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG TRANSAKSI
            |--------------------------------------------------------------------------
            */

            AuditLogService::log(
                'transaction_created',
                'Membuat transaksi "' .
                $transaction->invoice_number .
                '" dengan total Rp' .
                number_format(
                    $transaction->total,
                    0,
                    ',',
                    '.'
                ) .
                ' menggunakan pembayaran ' .
                $transaction->payment_method .
                '.',
                $transaction,
                null,
                $storeId,
                null,
                [
                    'invoice_number' =>
                        $transaction->invoice_number,

                    'subtotal' =>
                        $transaction->subtotal,

                    'discount' =>
                        $transaction->discount,

                    'total' =>
                        $transaction->total,

                    'paid' =>
                        $transaction->paid,

                    'change' =>
                        $transaction->change,

                    'payment_method' =>
                        $transaction->payment_method,

                    'customer_id' =>
                        $validated['customer_id'] ?? null,

                    'items_count' =>
                        count($validated['items']),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG CASHBON
            |--------------------------------------------------------------------------
            */

            if ($isCashbon) {

                $customer = Customer::where(
                    'store_id',
                    $storeId
                )
                ->find(
                    $validated['customer_id']
                );

                if ($customer) {

                    $debt = Debt::where(
                        'store_id',
                        $storeId
                    )
                    ->where(
                        'customer_id',
                        $customer->id
                    )
                    ->where(
                        'description',
                        'Cashbon transaksi ' .
                        $transaction->invoice_number
                    )
                    ->latest('id')
                    ->first();

                    if ($debt) {

                        AuditLogService::log(
                            'debt_created',
                            'Mencatat cashbon pelanggan "' .
                            $customer->name .
                            '" dari transaksi "' .
                            $transaction->invoice_number .
                            '" sebesar Rp' .
                            number_format(
                                $debt->amount,
                                0,
                                ',',
                                '.'
                            ) .
                            '.',
                            $debt,
                            null,
                            $storeId,
                            null,
                            [
                                'customer_id' =>
                                    $customer->id,

                                'customer_name' =>
                                    $customer->name,

                                'transaction_id' =>
                                    $transaction->id,

                                'invoice_number' =>
                                    $transaction->invoice_number,

                                'amount' =>
                                    $debt->amount,

                                'paid_amount' =>
                                    $debt->paid_amount,

                                'status' =>
                                    $debt->status,
                            ]
                        );
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Berhasil
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' =>
                    true,

                'message' =>
                    $isCashbon
                        ? 'Transaksi Cashbon berhasil disimpan dan dicatat sebagai utang pelanggan.'
                        : 'Transaksi berhasil disimpan.',

                'invoice_number' =>
                    $transaction->invoice_number,

                'transaction_id' =>
                    $transaction->id,

                'change' =>
                    $transaction->change,
            ]);

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Gagal
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' =>
                    false,

                'message' =>
                    $e->getMessage(),

            ], 422);
        }
    }

    /**
     * Menampilkan struk transaksi dalam bentuk PDF.
     */
    public function receiptPdf($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Toko Aktif
        |--------------------------------------------------------------------------
        */

        $store = \App\Models\Store::find(
            $this->activeStoreId()
        );

        /*
        |--------------------------------------------------------------------------
        | Batas Riwayat Sesuai Paket
        |--------------------------------------------------------------------------
        */

        $historyDays = $store?->getLimit('history_days');

        /*
        |--------------------------------------------------------------------------
        | Ambil Transaksi
        |--------------------------------------------------------------------------
        */

        $transaction = Transaction::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with([
            'user',
            'items',
            'customer',
            'Store',
        ])
        ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Cek Batas Riwayat
        |--------------------------------------------------------------------------
        */

        if ($historyDays !== null) {

            $historyLimitDate = now()
                ->subDays($historyDays)
                ->startOfDay();

            if (
                $transaction->created_at
                    ->lt($historyLimitDate)
            ) {

                return redirect()
                    ->route('riwayat')
                    ->with(
                        'error',
                        'Struk transaksi ini berada di luar batas riwayat paket Anda. Silakan upgrade paket untuk mengakses transaksi lama.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'kasir.receipt-pdf',
            compact('transaction')
        );

        $pdf->setPaper(
            [0, 0, 226.77, 600],
            'portrait'
        );

        return $pdf->stream(
            'struk-' .
            $transaction->invoice_number .
            '.pdf'
        );
    }

    /**
     * Menampilkan riwayat transaksi.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Paket Toko Aktif
        |--------------------------------------------------------------------------
        */

        $store = \App\Models\Store::find(
            $this->activeStoreId()
        );

        $historyDays =
            $store?->getLimit('history_days');

        /*
        |--------------------------------------------------------------------------
        | Query Transaksi Toko Aktif
        |--------------------------------------------------------------------------
        */

        $query = Transaction::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with([
            'user',
            'items',
            'customer',
        ])
        ->latest();

        /*
        |--------------------------------------------------------------------------
        | Batas Riwayat Sesuai Paket
        |--------------------------------------------------------------------------
        */

        if ($historyDays !== null) {

            $query->where(
                'created_at',
                '>=',
                now()
                    ->subDays($historyDays)
                    ->startOfDay()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'invoice_number',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhereHas(
                    'user',
                    function ($userQuery) use ($search) {

                        $userQuery->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        );
                    }
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Periode
        |--------------------------------------------------------------------------
        */

        if ($request->filled('period')) {

            switch ($request->period) {

                case 'today':

                    $query->whereDate(
                        'created_at',
                        today()
                    );

                    break;

                case '7days':

                    $query->where(
                        'created_at',
                        '>=',
                        now()
                            ->subDays(6)
                            ->startOfDay()
                    );

                    break;

                case '1month':

                    $query->where(
                        'created_at',
                        '>=',
                        now()
                            ->subMonth()
                            ->startOfDay()
                    );

                    break;

                case '1year':

                    $query->where(
                        'created_at',
                        '>=',
                        now()
                            ->subYear()
                            ->startOfDay()
                    );

                    break;

                case '3years':

                    $query->where(
                        'created_at',
                        '>=',
                        now()
                            ->subYears(3)
                            ->startOfDay()
                    );

                    break;

                case 'custom':

                    if (
                        $request->filled('start_date') &&
                        $request->filled('end_date')
                    ) {

                        $query->whereBetween(
                            'created_at',
                            [
                                \Carbon\Carbon::parse(
                                    $request->start_date
                                )->startOfDay(),

                                \Carbon\Carbon::parse(
                                    $request->end_date
                                )->endOfDay(),
                            ]
                        );
                    }

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Data
        |--------------------------------------------------------------------------
        */

        $transactions = $query
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Status Riwayat Di Luar Batas Paket
        |--------------------------------------------------------------------------
        */

        $historyRestricted = false;

        if (
            $historyDays !== null &&
            $request->filled('period')
        ) {

            if ($request->period === 'custom') {

                if (
                    $request->filled('start_date') &&
                    $request->filled('end_date')
                ) {

                    $historyLimitDate = now()
                        ->subDays($historyDays)
                        ->startOfDay();

                    $requestedEndDate =
                        \Carbon\Carbon::parse(
                            $request->end_date
                        )->endOfDay();

                    if (
                        $requestedEndDate
                            ->lt($historyLimitDate)
                    ) {

                        $historyRestricted = true;
                    }
                }
            }

            if (in_array($request->period, [
                '1year',
                '3years'
            ])) {

                $historyRestricted = true;
            }
        }

        return view(
            'riwayat',
            compact(
                'transactions',
                'historyDays',
                'historyRestricted'
            )
        );
    }
}