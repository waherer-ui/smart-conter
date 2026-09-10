<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Mendapatkan ID toko aktif.
     */
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Menampilkan daftar pengeluaran.
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


        /*
        |--------------------------------------------------------------------------
        | DATA PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $query = Expense::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with('user')
        ->latest('expense_date')
        ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | KONTROL AKSES DATA
        |--------------------------------------------------------------------------
        |
        | Admin  : Melihat semua pengeluaran toko aktif
        | Kasir  : Hanya pengeluaran miliknya di toko aktif
        | Guest  : Tidak melihat data pengeluaran
        |
        */

        if ($isGuest) {

            $query->where('user_id', -1);

        } elseif (!$isAdmin) {

            $query->where('user_id', $userId);
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER TANGGAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_date')) {

            $query->whereDate(
                'expense_date',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {

            $query->whereDate(
                'expense_date',
                '<=',
                $request->end_date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->where(
                'category',
                $request->category
            );
        }


        $pengeluaran = $query->get();


        /*
        |--------------------------------------------------------------------------
        | TOTAL PENGELUARAN SESUAI AKSES USER
        |--------------------------------------------------------------------------
        */

        $totalPengeluaran = $pengeluaran->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN HARI INI
        |--------------------------------------------------------------------------
        */

        $hariIniQuery = Expense::where(
            'store_id',
            $this->activeStoreId()
        )
        ->whereDate(
            'expense_date',
            today()
        );

        if ($isGuest) {

            $hariIniQuery->where('user_id', -1);

        } elseif (!$isAdmin) {

            $hariIniQuery->where('user_id', $userId);
        }

        $totalHariIni = $hariIniQuery->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN BULAN INI
        |--------------------------------------------------------------------------
        */

        $bulanIniQuery = Expense::where(
            'store_id',
            $this->activeStoreId()
        )
        ->whereMonth(
            'expense_date',
            now()->month
        )
        ->whereYear(
            'expense_date',
            now()->year
        );

        if ($isGuest) {

            $bulanIniQuery->where('user_id', -1);

        } elseif (!$isAdmin) {

            $bulanIniQuery->where('user_id', $userId);
        }

        $totalBulanIni = $bulanIniQuery->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | KATEGORI
        |--------------------------------------------------------------------------
        |
        | Admin  : Semua kategori toko aktif
        | Kasir  : Kategori dari pengeluaran miliknya
        | Guest  : Kosong
        |
        */

        $kategoriQuery = Expense::where(
            'store_id',
            $this->activeStoreId()
        );

        if ($isGuest) {

            $kategoriQuery->where('user_id', -1);

        } elseif (!$isAdmin) {

            $kategoriQuery->where('user_id', $userId);
        }

        $kategori = $kategoriQuery
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');


        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view('pengeluaran', compact(
            'pengeluaran',
            'totalPengeluaran',
            'totalHariIni',
            'totalBulanIni',
            'kategori'
        ));
    }


    /**
     * Menyimpan pengeluaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'expense_date' => [
                'required',
                'date',
            ],
        ], [
            'category.required' =>
                'Kategori pengeluaran wajib diisi.',

            'category.max' =>
                'Kategori terlalu panjang.',

            'description.max' =>
                'Keterangan terlalu panjang.',

            'amount.required' =>
                'Jumlah pengeluaran wajib diisi.',

            'amount.numeric' =>
                'Jumlah pengeluaran harus berupa angka.',

            'amount.min' =>
                'Jumlah pengeluaran minimal Rp 1.',

            'expense_date.required' =>
                'Tanggal pengeluaran wajib diisi.',

            'expense_date.date' =>
                'Tanggal pengeluaran tidak valid.',
        ]);

        Expense::create([
            'store_id' =>
                $this->activeStoreId(),

            'user_id' =>
                session('user_id'),

            'category' =>
                $validated['category'],

            'description' =>
                $validated['description'] ?? null,

            'amount' =>
                $validated['amount'],

            'expense_date' =>
                $validated['expense_date'],
        ]);

        return redirect()
            ->route('pengeluaran')
            ->with(
                'success',
                'Pengeluaran berhasil ditambahkan.'
            );
    }


    /**
     * Menampilkan data pengeluaran untuk diedit.
     */
    public function edit($id)
    {
        $pengeluaran = Expense::where(
            'store_id',
            $this->activeStoreId()
        )->findOrFail($id);

        return view(
            'pengeluaran-edit',
            compact('pengeluaran')
        );
    }


    /**
     * Memperbarui pengeluaran.
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Expense::where(
            'store_id',
            $this->activeStoreId()
        )->findOrFail($id);

        $validated = $request->validate([
            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'expense_date' => [
                'required',
                'date',
            ],
        ]);

        $pengeluaran->update([
            'category' =>
                $validated['category'],

            'description' =>
                $validated['description'] ?? null,

            'amount' =>
                $validated['amount'],

            'expense_date' =>
                $validated['expense_date'],
        ]);

        return redirect()
            ->route('pengeluaran')
            ->with(
                'success',
                'Pengeluaran berhasil diperbarui.'
            );
    }


    /**
     * Menghapus pengeluaran.
     */
    public function destroy($id)
    {
        $pengeluaran = Expense::where(
            'store_id',
            $this->activeStoreId()
        )->findOrFail($id);

        $pengeluaran->delete();

        return redirect()
            ->route('pengeluaran')
            ->with(
                'success',
                'Pengeluaran berhasil dihapus.'
            );
    }
}