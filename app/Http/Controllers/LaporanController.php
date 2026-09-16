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
        | PERIODE LAPORAN
        |--------------------------------------------------------------------------
        */

        $startDate = $request->filled('start_date')
    ? Carbon::parse($request->start_date)->startOfDay()
    : Carbon::today()->startOfDay();

$endDate = $request->filled('end_date')
    ? Carbon::parse($request->end_date)->endOfDay()
    : Carbon::today()->endOfDay();


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

                // Periode
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
                'labaBersihHariIni'
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

            // Periode
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
            'kasBersihHariIni'
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