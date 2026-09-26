<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Mendapatkan ID toko aktif.
     */
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Menampilkan laporan penjualan dan pengeluaran.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | ROLE USER
        |--------------------------------------------------------------------------
        */

        $isAdmin = session('user_role') === 'admin';
        $isGuest = !session('logged_in');
        $userId = session('user_id');

        if ($isGuest) {
            $userId = -1;
        }

        $storeId = $this->activeStoreId();
        $tab = $request->input('tab', 'laporan');


        /*
        |--------------------------------------------------------------------------
        | FILTER KASIR
        |--------------------------------------------------------------------------
        |
        | Admin:
        |   - null = Semua Kasir
        |   - ID tertentu = Kasir tertentu
        |
        | Kasir:
        |   - selalu menggunakan user_id miliknya sendiri
        |
        */

        if ($isAdmin) {

            $cashierId = $request->filled('cashier_id')
                ? (int) $request->cashier_id
                : null;

        } else {

            $cashierId = $userId;
        }


        /*
        |--------------------------------------------------------------------------
        | DAFTAR KASIR
        |--------------------------------------------------------------------------
        |
        | Hanya kasir yang terhubung ke toko aktif.
        |
        */

        $kasir = $isAdmin
            ? User::where('role', 'kasir')
                ->whereHas('stores', function ($query) use ($storeId) {
                    $query->where('stores.id', $storeId);
                })
                ->orderBy('name')
                ->get()
            : collect();


/*
|--------------------------------------------------------------------------
| FILTER PERIODE
|--------------------------------------------------------------------------
*/

$period = $request->input('period', 'today');

switch ($period) {

    case '7days':

        $startDate = Carbon::today()
            ->subDays(6)
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;


    case '1month':

        $startDate = Carbon::today()
            ->subMonth()
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;


    case '3months':

        $startDate = Carbon::today()
            ->subMonths(3)
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;


    case '6months':

        $startDate = Carbon::today()
            ->subMonths(6)
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;


    case '1year':

        $startDate = Carbon::today()
            ->subYear()
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;


    case 'custom':

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();

        break;


    case 'today':
    default:

        $period = 'today';

        $startDate = Carbon::today()
            ->startOfDay();

        $endDate = Carbon::today()
            ->endOfDay();

        break;
}


/*
|--------------------------------------------------------------------------
| BATAS RIWAYAT SESUAI PAKET
|--------------------------------------------------------------------------
*/

$store = \App\Models\Store::find($storeId);

$historyDays = $store?->getLimit('history_days');

$historyRestricted = false;

if ($historyDays !== null) {

    $historyLimitDate = now()
        ->subDays($historyDays)
        ->startOfDay();

    /*
    | Jika seluruh periode yang diminta berada
    | di luar batas riwayat paket.
    */

    if ($endDate->lt($historyLimitDate)) {
        $historyRestricted = true;
    }

}

/*
|--------------------------------------------------------------------------
| ANALITIK LANJUTAN
|--------------------------------------------------------------------------
*/

$analitik = null;

if ($tab === 'analitik') {

    if ($store && $store->hasFeature('advanced_analytics')) {

        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI ANALITIK
        |--------------------------------------------------------------------------
        */

        $analitikTransaksiQuery = Transaction::query()
            ->where('store_id', $storeId)
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->whereDate('created_at', '<=', $endDate->toDateString());

        if ($cashierId) {
            $analitikTransaksiQuery->where(
                'user_id',
                $cashierId
            );
        }

        $analitikTransaksi = $analitikTransaksiQuery
            ->orderBy('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TREN PENJUALAN HARIAN
        |--------------------------------------------------------------------------
        */

        $trenPenjualan = $analitikTransaksi
            ->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'tanggal' => $date,
                    'omzet' => $items->sum('total'),
                    'transaksi' => $items->count(),
                ];
            })
            ->sortKeys()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | ITEM TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $analitikItems = TransactionItem::with('product')
            ->whereHas('transaction', function ($query) use (
                $storeId,
                $startDate,
                $endDate,
                $cashierId
            ) {

                $query->where('store_id', $storeId)
                    ->whereDate(
                        'created_at',
                        '>=',
                        $startDate->toDateString()
                    )
                    ->whereDate(
                        'created_at',
                        '<=',
                        $endDate->toDateString()
                    );

                if ($cashierId) {
                    $query->where(
                        'user_id',
                        $cashierId
                    );
                }

            })
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PRODUK TERLARIS
        |--------------------------------------------------------------------------
        */

        $produkTerlaris = $analitikItems
            ->groupBy('product_id')
            ->map(function ($items) {

                $itemPertama = $items->first();

                return [
                    'product_id' => $itemPertama->product_id,

                    'nama' => $itemPertama->product?->name
                        ?? $itemPertama->product_name
                        ?? 'Produk dihapus',

                    'terjual' => $items->sum('quantity'),

                    'omzet' => $items->sum('subtotal'),
                ];

            })
            ->sortByDesc('terjual')
            ->values()
            ->take(10);


        /*
        |--------------------------------------------------------------------------
        | PRODUK PALING MENGUNTUNGKAN
        |--------------------------------------------------------------------------
        */

        $produkMenguntungkan = $analitikItems
            ->groupBy('product_id')
            ->map(function ($items) {

                $itemPertama = $items->first();

                $omzet = $items->sum('subtotal');

                $hpp = $items->sum(function ($item) {
                    return (float) $item->capital_price
                        * (float) $item->quantity;
                });

                return [
                    'product_id' => $itemPertama->product_id,

                    'nama' => $itemPertama->product?->name
                        ?? $itemPertama->product_name
                        ?? 'Produk dihapus',

                    'terjual' => $items->sum('quantity'),

                    'omzet' => $omzet,

                    'hpp' => $hpp,

                    'laba' => $omzet - $hpp,
                ];

            })
            ->sortByDesc('laba')
            ->values()
            ->take(10);


        /*
        |--------------------------------------------------------------------------
        | METODE PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $metodePembayaran = $analitikTransaksi
            ->groupBy(function ($transaction) {
                return strtolower(
                    $transaction->payment_method ?? 'lainnya'
                );
            })
            ->map(function ($items, $method) {

                return [
                    'metode' => ucfirst($method),

                    'transaksi' => $items->count(),

                    'total' => $items->sum('total'),
                ];

            })
            ->sortByDesc('total')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | JAM RAMAI
        |--------------------------------------------------------------------------
        */

        $jamRamai = $analitikTransaksi
            ->groupBy(function ($transaction) {
                return $transaction->created_at->format('H');
            })
            ->map(function ($items, $hour) {

                return [
                    'jam' => $hour . ':00',

                    'transaksi' => $items->count(),

                    'omzet' => $items->sum('total'),
                ];

            })
            ->sortByDesc('transaksi')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | RATA-RATA TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $rataRataTransaksi = $analitikTransaksi->count() > 0
            ? $analitikTransaksi->sum('total')
                / $analitikTransaksi->count()
            : 0;


        /*
        |--------------------------------------------------------------------------
        | DATA ANALITIK
        |--------------------------------------------------------------------------
        */

        $analitik = [
            'trenPenjualan' => $trenPenjualan,

            'produkTerlaris' => $produkTerlaris,

            'produkMenguntungkan' => $produkMenguntungkan,

            'metodePembayaran' => $metodePembayaran,

            'jamRamai' => $jamRamai,

            'rataRataTransaksi' => $rataRataTransaksi,
        ];
    }
}


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI PENJUALAN
        |--------------------------------------------------------------------------
        */

        $transaksiQuery = Transaction::with('user')
            ->where(
                'store_id',
                $storeId
            )
            ->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);

        if ($cashierId) {
            $transaksiQuery->where(
                'user_id',
                $cashierId
            );
        }

        $transaksi = $transaksiQuery
            ->latest('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN PENJUALAN
        |--------------------------------------------------------------------------
        */

        $omzet = $transaksi->sum('total');

        $totalTransaksi = $transaksi->count();

        $totalDiskon = $transaksi->sum('discount');


        /*
        |--------------------------------------------------------------------------
        | BARANG TERJUAL
        |--------------------------------------------------------------------------
        */

        $barangTerjual = TransactionItem::whereHas(
            'transaction',
            function ($query) use (
                $storeId,
                $startDate,
                $endDate,
                $cashierId
            ) {

                $query->where(
                    'store_id',
                    $storeId
                );

                $query->whereBetween('created_at', [
                    $startDate,
                    $endDate
                ]);

                if ($cashierId) {
                    $query->where(
                        'user_id',
                        $cashierId
                    );
                }
            }
        )->sum('quantity');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $pengeluaranQuery = Expense::with('user')
            ->where(
                'store_id',
                $storeId
            )
            ->whereDate(
                'expense_date',
                '>=',
                $startDate->toDateString()
            )
            ->whereDate(
                'expense_date',
                '<=',
                $endDate->toDateString()
            );

        if ($cashierId) {
            $pengeluaranQuery->where(
                'user_id',
                $cashierId
            );
        }

        $pengeluaran = $pengeluaranQuery
            ->latest('expense_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOTAL PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $totalPengeluaran =
            $pengeluaran->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | SETORAN / KAS BERSIH PERIODE
        |--------------------------------------------------------------------------
        |
        | Setoran = Omzet - Pengeluaran
        |
        */

        $kasBersih =
            $omzet
            - $totalPengeluaran;


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI HARI INI
        |--------------------------------------------------------------------------
        */

        $transaksiHariIniQuery = Transaction::query()
            ->where(
                'store_id',
                $storeId
            )
            ->whereDate(
                'created_at',
                Carbon::today()
            );

        if ($cashierId) {
            $transaksiHariIniQuery->where(
                'user_id',
                $cashierId
            );
        }

        $transaksiHariIni =
            $transaksiHariIniQuery->get();


        /*
        |--------------------------------------------------------------------------
        | PENDAPATAN HARI INI
        |--------------------------------------------------------------------------
        */

        $totalPendapatanHariIni =
            $transaksiHariIni->sum('total');


        /*
        |--------------------------------------------------------------------------
        | TOTAL TRANSAKSI HARI INI
        |--------------------------------------------------------------------------
        */

        $totalTransaksiHariIni =
            $transaksiHariIni->count();


        /*
        |--------------------------------------------------------------------------
        | BARANG TERJUAL HARI INI
        |--------------------------------------------------------------------------
        */

        $barangTerjualHariIni =
            TransactionItem::whereHas(
                'transaction',
                function ($query) use (
                    $storeId,
                    $cashierId
                ) {

                    $query->where(
                        'store_id',
                        $storeId
                    );

                    $query->whereDate(
                        'created_at',
                        Carbon::today()
                    );

                    if ($cashierId) {
                        $query->where(
                            'user_id',
                            $cashierId
                        );
                    }
                }
            )->sum('quantity');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN HARI INI
        |--------------------------------------------------------------------------
        */

        $pengeluaranHariIniQuery =
            Expense::query()
                ->where(
                    'store_id',
                    $storeId
                )
                ->whereDate(
                    'expense_date',
                    Carbon::today()
                );

        if ($cashierId) {
            $pengeluaranHariIniQuery->where(
                'user_id',
                $cashierId
            );
        }

        $totalPengeluaranHariIni =
            $pengeluaranHariIniQuery->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | SETORAN HARI INI
        |--------------------------------------------------------------------------
        */

        $kasBersihHariIni =
            $totalPendapatanHariIni
            - $totalPengeluaranHariIni;


        /*
        |--------------------------------------------------------------------------
        | DATA KEUANGAN ADMIN
        |--------------------------------------------------------------------------
        */

        if ($isAdmin) {

            /*
            |--------------------------------------------------------------------------
            | HPP / MODAL BARANG
            |--------------------------------------------------------------------------
            */

            $hpp = TransactionItem::whereHas(
                'transaction',
                function ($query) use (
                    $storeId,
                    $startDate,
                    $endDate,
                    $cashierId
                ) {

                    $query->where(
                        'store_id',
                        $storeId
                    );

                    $query->whereBetween(
                        'created_at',
                        [
                            $startDate,
                            $endDate
                        ]
                    );

                    if ($cashierId) {
                        $query->where(
                            'user_id',
                            $cashierId
                        );
                    }
                }
            )
            ->get()
            ->sum(function ($item) {

                return $item->capital_price
                    * $item->quantity;
            });


            /*
            |--------------------------------------------------------------------------
            | LABA KOTOR
            |--------------------------------------------------------------------------
            */

            $labaKotor =
                $omzet
                - $hpp;


            /*
            |--------------------------------------------------------------------------
            | LABA BERSIH
            |--------------------------------------------------------------------------
            */

            $labaBersih =
                $labaKotor
                - $totalPengeluaran;


            /*
            |--------------------------------------------------------------------------
            | HPP HARI INI
            |--------------------------------------------------------------------------
            */

            $hppHariIni =
                TransactionItem::whereHas(
                    'transaction',
                    function ($query) use (
                        $storeId,
                        $cashierId
                    ) {

                        $query->where(
                            'store_id',
                            $storeId
                        );

                        $query->whereDate(
                            'created_at',
                            Carbon::today()
                        );

                        if ($cashierId) {
                            $query->where(
                                'user_id',
                                $cashierId
                            );
                        }
                    }
                )
                ->get()
                ->sum(function ($item) {

                    return $item->capital_price
                        * $item->quantity;
                });


            /*
            |--------------------------------------------------------------------------
            | LABA KOTOR HARI INI
            |--------------------------------------------------------------------------
            */

            $labaKotorHariIni =
                $totalPendapatanHariIni
                - $hppHariIni;


            /*
            |--------------------------------------------------------------------------
            | LABA BERSIH HARI INI
            |--------------------------------------------------------------------------
            */

            $labaBersihHariIni =
                $labaKotorHariIni
                - $totalPengeluaranHariIni;


            /*
            |--------------------------------------------------------------------------
            | RETURN ADMIN
            |--------------------------------------------------------------------------
            */

            return view('laporan', compact(

                // Role & Filter
                'isAdmin',
                'cashierId',
                'kasir',
                'tab',
                'store',

                // Periode
                'period',
                'startDate',
                'endDate',
                'historyDays',
                'historyRestricted',

                // Penjualan
                'transaksi',
                'omzet',
                'totalTransaksi',
                'totalDiskon',
                'barangTerjual',

                // Pengeluaran
                'pengeluaran',
                'totalPengeluaran',

                // Setoran
                'kasBersih',

                // Hari Ini
                'totalPendapatanHariIni',
                'totalTransaksiHariIni',
                'barangTerjualHariIni',
                'totalPengeluaranHariIni',
                'kasBersihHariIni',

                // Keuangan Admin
                'hpp',
                'labaKotor',
                'labaBersih',
                'hppHariIni',
                'labaKotorHariIni',
                'labaBersihHariIni',
                // Analitik Lanjutan
                'analitik'
            ));
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN KASIR
        |--------------------------------------------------------------------------
        */

        return view('laporan', compact(

            // Role & Filter
            'isAdmin',
            'cashierId',
            'kasir',
            'tab',
            'store',

            // Periode
            'period',
            'startDate',
            'endDate',
            'historyDays',
            'historyRestricted',

            // Penjualan
            'transaksi',
            'omzet',
            'totalTransaksi',
            'totalDiskon',
            'barangTerjual',

            // Pengeluaran
            'pengeluaran',
            'totalPengeluaran',

            // Setoran
            'kasBersih',

            // Hari Ini
            'totalPendapatanHariIni',
            'totalTransaksiHariIni',
            'barangTerjualHariIni',
            'totalPengeluaranHariIni',
            'kasBersihHariIni',
            // Analitik Lanjutan
            'analitik'
        ));
    }

        public function piutang()
    {
        $storeId = $this->activeStoreId();

        $store = \App\Models\Store::find($storeId);

        /*
        |--------------------------------------------------------------------------
        | CEK FITUR
        |--------------------------------------------------------------------------
        */

        if (!$store || !$store->hasFeature('receivable_report')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Laporan Piutang hanya tersedia pada paket Pro dan Premium.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA PIUTANG
        |--------------------------------------------------------------------------
        */

        $customers = Customer::where(
            'store_id',
            $storeId
        )
        ->with([
            'debts' => function ($query) {
                $query->latest('debt_date');
            }
        ])
        ->latest()
        ->get();

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN
        |--------------------------------------------------------------------------
        */

        $totalPiutang = 0;
        $totalTerbayar = 0;
        $jumlahPelanggan = 0;

        foreach ($customers as $customer) {

            $sisaPelanggan = $customer->debts->sum(
                function ($debt) {
                    return max(
                        0,
                        (float) $debt->amount -
                        (float) $debt->paid_amount
                    );
                }
            );

            $terbayarPelanggan = $customer->debts->sum(
                'paid_amount'
            );

            $totalPiutang += $sisaPelanggan;
            $totalTerbayar += $terbayarPelanggan;

            if ($sisaPelanggan > 0) {
                $jumlahPelanggan++;
            }
        }

        return view(
            'laporan.piutang',
            compact(
                'customers',
                'totalPiutang',
                'totalTerbayar',
                'jumlahPelanggan'
            )
        );
    }
}