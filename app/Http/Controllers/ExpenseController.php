<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran.
     */
    public function index(Request $request)
    {
        $query = Expense::with('user')
            ->latest('expense_date')
            ->latest('id');

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal
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
        | Filter kategori
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
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $totalPengeluaran = $pengeluaran->sum('amount');

        $totalHariIni = Expense::whereDate(
            'expense_date',
            today()
        )->sum('amount');

        $totalBulanIni = Expense::whereMonth(
            'expense_date',
            now()->month
        )
        ->whereYear(
            'expense_date',
            now()->year
        )
        ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Kategori
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk pilihan filter.
        |
        */

        $kategori = Expense::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

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
            'category.required' => 'Kategori pengeluaran wajib diisi.',
            'category.max' => 'Kategori terlalu panjang.',

            'description.max' => 'Keterangan terlalu panjang.',

            'amount.required' => 'Jumlah pengeluaran wajib diisi.',
            'amount.numeric' => 'Jumlah pengeluaran harus berupa angka.',
            'amount.min' => 'Jumlah pengeluaran minimal Rp 1.',

            'expense_date.required' => 'Tanggal pengeluaran wajib diisi.',
            'expense_date.date' => 'Tanggal pengeluaran tidak valid.',
        ]);

        Expense::create([
            'user_id' => session('user_id'),
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
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
        $pengeluaran = Expense::findOrFail($id);

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
        $pengeluaran = Expense::findOrFail($id);

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
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
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
        $pengeluaran = Expense::findOrFail($id);

        $pengeluaran->delete();

        return redirect()
            ->route('pengeluaran')
            ->with(
                'success',
                'Pengeluaran berhasil dihapus.'
            );
    }
}