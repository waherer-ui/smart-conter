<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Services\AuditLogService;
use App\Models\Customer;
use App\Models\Debt;
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
    | IDENTIFIKASI CASHBON
    |--------------------------------------------------------------------------
    */

    $isCashbon =
        $validated['payment_method'] === 'Cashbon / Utang';

    /*
    |--------------------------------------------------------------------------
    | CASHBON HARUS MEMAKAI FITUR UTANG
    |--------------------------------------------------------------------------
    */

    if ($isCashbon) {

        $store = \App\Models\Store::find($storeId);

        if (!$store || !$store->hasFeature('debt')) {

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

        if (
            empty($validated['customer_id'])
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Silakan pilih pelanggan untuk transaksi Cashbon / Utang.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Total Cashbon Tidak Boleh Nol
        |--------------------------------------------------------------------------
        */

        if (
            (float) $validated['total'] <= 0
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Transaksi Cashbon harus memiliki total tagihan.',
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
    | Validasi Pembayaran Tunai
    |--------------------------------------------------------------------------
    */

    if (
        $validated['payment_method'] === 'Tunai'
        && $validated['paid'] < $validated['total']
    ) {

        return response()->json([
            'success' => false,
            'message' =>
                'Nominal pembayaran kurang dari total tagihan.',
        ], 422);
    }
    
    /*
|--------------------------------------------------------------------------
| Validasi Pembayaran Cashbon
|--------------------------------------------------------------------------
*/

if ($isCashbon) {

    if (
        (float) $validated['paid'] >
        (float) $validated['total']
    ) {

        return response()->json([
            'success' => false,
            'message' =>
                'Nominal pembayaran tidak boleh melebihi total tagihan.',
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
                | Hitung Kembalian
                |--------------------------------------------------------------------------
                */

                $change = $isCashbon
                    ? 0
                    : max(
                        0,
                        $validated['paid']
                        - $validated['total']
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
                        $validated['subtotal'],

                    'discount' =>
                        $validated['discount'],

                    'total' =>
                        $validated['total'],

                    'paid' =>
                        $validated['paid'],

                    'change' =>
                        $change,

                    'payment_method' =>
                        $validated['payment_method'],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Simpan Detail Produk
                |--------------------------------------------------------------------------
                */

                foreach (
                    $validated['items']
                    as $item
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Produk
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | Produk Tidak Ditemukan
                    |--------------------------------------------------------------------------
                    */

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

                    if (
                        $product->stock
                        < $item['quantity']
                    ) {

                        throw new \Exception(
                            'Stok produk "' .
                            $product->name .
                            '" tidak mencukupi.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Hitung Subtotal Item
                    |--------------------------------------------------------------------------
                    */

                    $itemSubtotal =
                        $product->price
                        * $item['quantity'];

                    /*
                    |--------------------------------------------------------------------------
                    | Simpan Transaction Item
                    |--------------------------------------------------------------------------
                    */

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
                            $product->price,

                        'quantity' =>
                            $item['quantity'],

                        'subtotal' =>
                            $itemSubtotal,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Kurangi Stok
                    |--------------------------------------------------------------------------
                    */

                    $product->stock -=
                        $item['quantity'];

                    $product->save();
                }

                /*
|--------------------------------------------------------------------------
| BUAT CATATAN UTANG JIKA CASHBON
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

    /*
    |--------------------------------------------------------------------------
    | Hitung Sisa Utang
    |--------------------------------------------------------------------------
    */

    $remainingDebt =
        (float) $transaction->total -
        (float) $transaction->paid;

    /*
    |--------------------------------------------------------------------------
    | Buat Utang Hanya Jika Masih Ada Sisa
    |--------------------------------------------------------------------------
    */

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

    $historyDays = $store?->getLimit('history_days');

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
            now()->subDays($historyDays)->startOfDay()
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
                now()->subDays(6)->startOfDay()
            );

            break;

        case '1month':

            $query->where(
                'created_at',
                '>=',
                now()->subMonth()->startOfDay()
            );

            break;

        case '1year':

            $query->where(
                'created_at',
                '>=',
                now()->subYear()->startOfDay()
            );

            break;

        case '3years':

            $query->where(
                'created_at',
                '>=',
                now()->subYears(3)->startOfDay()
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

            $requestedEndDate = \Carbon\Carbon::parse(
                $request->end_date
            )->endOfDay();

            if ($requestedEndDate->lt($historyLimitDate)) {
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