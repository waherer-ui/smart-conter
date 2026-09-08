<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
            /**
             * Menyimpan transaksi penjualan.
             */
            public function store(Request $request)
            {
              
              $query = Transaction::with([
            'user',
            'items'
        ])->latest();
        
        if (!session('logged_in')) {
            $query->where('user_id', -1);
        }
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi Pembayaran
        |--------------------------------------------------------------------------
        */

        if (
            $validated['payment_method'] === 'Tunai'
            && $validated['paid'] < $validated['total']
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran kurang dari total tagihan.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | User Login
        |--------------------------------------------------------------------------
        */

        $userId = session('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Session kasir tidak ditemukan. Silakan login kembali.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Transaksi + Detail + Kurangi Stok
        |--------------------------------------------------------------------------
        */

        try {

            $transaction = DB::transaction(function () use ($validated, $userId) {

                /*
                |--------------------------------------------------------------------------
                | Generate Nomor Invoice
                |--------------------------------------------------------------------------
                */

                $invoiceNumber =
                    'INV-' .
                    now()->format('YmdHis') .
                    '-' .
                    strtoupper(substr(uniqid(), -4));


                /*
                |--------------------------------------------------------------------------
                | Hitung Kembalian
                |--------------------------------------------------------------------------
                */

                $change = max(
                    0,
                    $validated['paid'] - $validated['total']
                );


                /*
                |--------------------------------------------------------------------------
                | Buat Transaksi
                |--------------------------------------------------------------------------
                */

                $transaction = Transaction::create([

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

                foreach ($validated['items'] as $item) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Produk
                    |--------------------------------------------------------------------------
                    */

                    $product = Product::where(
                        'id',
                        $item['id']
                    )
                    ->lockForUpdate()
                    ->first();


                    if (!$product) {

                        throw new \Exception(
                            'Produk tidak ditemukan.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Cek Stok
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $product->stock <
                        $item['quantity']
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
                        $product->price *
                        $item['quantity'];


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


                return $transaction;
            });


            /*
            |--------------------------------------------------------------------------
            | Berhasil
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' =>
                    true,

                'message' =>
                    'Transaksi berhasil disimpan.',

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
 * Menampilkan riwayat transaksi.
 */
public function index(Request $request)
{
    $query = Transaction::with([
        'user',
        'items'
    ])->latest();

    /*
    |--------------------------------------------------------------------------
    | Filter Pencarian
    |--------------------------------------------------------------------------
    */

    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('invoice_number', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                });

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Tanggal
    |--------------------------------------------------------------------------
    */

    if ($request->filled('date')) {

        $query->whereDate(
            'created_at',
            $request->date
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil Data
    |--------------------------------------------------------------------------
    */

    $transactions = $query
        ->paginate(10)
        ->withQueryString();

    return view(
        'riwayat',
        compact('transactions')
    );
}
}