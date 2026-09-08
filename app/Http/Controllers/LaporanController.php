<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
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
        $userId = session('user_id');

        $isGuest = !session('logged_in');

          if ($isGuest) {
              $userId = -1;
          }
        /*
        |--------------------------------------------------------------------------
        | FILTER KASIR
        |--------------------------------------------------------------------------
        |
        | Admin:
        |   - null = Semua Kasir
        |   - ID tertentu = kasir tertentu
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

            // Kasir tidak boleh memilih kasir lain
            $cashierId = $userId;
        }


        /*
        |--------------------------------------------------------------------------
        | DAFTAR KASIR
        |--------------------------------------------------------------------------
        |
        | Hanya dibutuhkan untuk dropdown Admin.
        |
        */

        $kasir = $isAdmin
            ? User::where('role', 'kasir')
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
        | TRANSAKSI PENJUALAN
        |--------------------------------------------------------------------------
        */

        $transaksiQuery = Transaction::with('user')
            ->whereBetween('created_at', [
                $startDate,
                $endDate
            ]);

        if ($cashierId) {
            $transaksiQuery->where('user_id', $cashierId);
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
            function ($query) use ($startDate, $endDate, $cashierId) {

                $query->whereBetween('created_at', [
                    $startDate,
                    $endDate
                ]);

                if ($cashierId) {
                    $query->where('user_id', $cashierId);
                }
            }
        )->sum('quantity');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $pengeluaranQuery = Expense::with('user')
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
            $pengeluaranQuery->where('user_id', $cashierId);
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

        $totalPengeluaran = $pengeluaran->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | SETORAN / KAS BERSIH PERIODE
        |--------------------------------------------------------------------------
        |
        | Setoran = Omzet - Pengeluaran
        |
        */

        $kasBersih = $omzet - $totalPengeluaran;


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI HARI INI
        |--------------------------------------------------------------------------
        */

        $transaksiHariIniQuery = Transaction::query()
            ->whereDate(
                'created_at',
                Carbon::today()
            );

        if ($cashierId) {
            $transaksiHariIniQuery->where('user_id', $cashierId);
        }

        $transaksiHariIni = $transaksiHariIniQuery->get();


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

        $barangTerjualHariIni = TransactionItem::whereHas(
            'transaction',
            function ($query) use ($cashierId) {

                $query->whereDate(
                    'created_at',
                    Carbon::today()
                );

                if ($cashierId) {
                    $query->where('user_id', $cashierId);
                }
            }
        )->sum('quantity');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN HARI INI
        |--------------------------------------------------------------------------
        */

        $pengeluaranHariIniQuery = Expense::query()
            ->whereDate(
                'expense_date',
                Carbon::today()
            );

        if ($cashierId) {
            $pengeluaranHariIniQuery->where('user_id', $cashierId);
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
                    $startDate,
                    $endDate,
                    $cashierId
                ) {

                    $query->whereBetween('created_at', [
                        $startDate,
                        $endDate
                    ]);

                    if ($cashierId) {
                        $query->where('user_id', $cashierId);
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
                $omzet - $hpp;


            /*
            |--------------------------------------------------------------------------
            | LABA BERSIH
            |--------------------------------------------------------------------------
            */

            $labaBersih =
                $labaKotor - $totalPengeluaran;


            /*
            |--------------------------------------------------------------------------
            | HPP HARI INI
            |--------------------------------------------------------------------------
            */

            $hppHariIni = TransactionItem::whereHas(
                'transaction',
                function ($query) use ($cashierId) {

                    $query->whereDate(
                        'created_at',
                        Carbon::today()
                    );

                    if ($cashierId) {
                        $query->where('user_id', $cashierId);
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
}