<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    public function index()
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $suppliers = Supplier::where(
            'store_id',
            $this->activeStoreId()
        )
        ->latest()
        ->paginate(15);

        return view(
            'suppliers.index',
            compact(
                'suppliers',
                'store'
            )
        );
    }

    public function create()
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        return view(
            'suppliers.create',
            compact('store')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $supplier = Supplier::create([
            'store_id' => $store->id,

            'name' => trim(
                $request->name
            ),

            'phone' => $request->phone
                ? trim($request->phone)
                : null,

            'address' => $request->address
                ? trim($request->address)
                : null,

            'notes' => $request->notes
                ? trim($request->notes)
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - SUPPLIER DIBUAT
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'supplier_created',
            'Menambahkan supplier "' .
            $supplier->name .
            '".',
            $supplier,
            null,
            $store->id,
            null,
            [
                'name' =>
                    $supplier->name,

                'phone' =>
                    $supplier->phone,

                'address' =>
                    $supplier->address,

                'notes' =>
                    $supplier->notes,
            ]
        );

        return redirect()
            ->route('supplier.index')
            ->with(
                'success',
                'Supplier berhasil ditambahkan.'
            );
    }

    public function show(Request $request, $id)
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $supplier = Supplier::where(
            'store_id',
            $this->activeStoreId()
        )
        ->findOrFail($id);

        // =========================================================
        // FILTER RIWAYAT PEMBELIAN
        // =========================================================

        $period = $request->input(
            'period',
            'month'
        );

        $search = trim(
            (string) $request->input('search')
        );

        $startDate = $request->input(
            'start_date'
        );

        $endDate = $request->input(
            'end_date'
        );

        $purchasesQuery = $supplier->purchases()
            ->where(
                'store_id',
                $store->id
            )
            ->with('items')
            ->latest('purchase_date')
            ->latest('id');

        // =========================================================
        // FILTER PERIODE
        // =========================================================

        if ($period === 'today') {

            $purchasesQuery->whereDate(
                'purchase_date',
                now()->toDateString()
            );

        } elseif ($period === '7days') {

            $purchasesQuery
                ->whereDate(
                    'purchase_date',
                    '>=',
                    now()
                        ->subDays(6)
                        ->toDateString()
                )
                ->whereDate(
                    'purchase_date',
                    '<=',
                    now()->toDateString()
                );

        } elseif ($period === 'custom') {

            // Jika rentang tanggal belum lengkap,
            // jangan tampilkan semua data.
            if (!$startDate || !$endDate) {

                $purchasesQuery->whereRaw(
                    '1 = 0'
                );

            } else {

                $purchasesQuery->whereBetween(
                    'purchase_date',
                    [
                        $startDate,
                        $endDate,
                    ]
                );
            }

        } else {

            // Default: 1 bulan terakhir
            $purchasesQuery
                ->whereDate(
                    'purchase_date',
                    '>=',
                    now()
                        ->subMonth()
                        ->toDateString()
                )
                ->whereDate(
                    'purchase_date',
                    '<=',
                    now()->toDateString()
                );
        }

        // =========================================================
        // CARI NOMOR INVOICE
        // =========================================================

        if ($search !== '') {

            $purchasesQuery->where(
                'invoice_number',
                'like',
                '%' . $search . '%'
            );
        }

        $purchases = $purchasesQuery
            ->get();

        return view(
            'suppliers.show',
            compact(
                'supplier',
                'store',
                'purchases',
                'period',
                'search',
                'startDate',
                'endDate'
            )
        );
    }

    public function edit($id)
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $supplier = Supplier::where(
            'store_id',
            $this->activeStoreId()
        )
        ->findOrFail($id);

        return view(
            'suppliers.edit',
            compact(
                'supplier',
                'store'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('supplier')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Supplier hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $supplier = Supplier::where(
            'store_id',
            $this->activeStoreId()
        )
        ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA LAMA
        |--------------------------------------------------------------------------
        */

        $oldValues = [
            'name' =>
                $supplier->name,

            'phone' =>
                $supplier->phone,

            'address' =>
                $supplier->address,

            'notes' =>
                $supplier->notes,
        ];

        /*
        |--------------------------------------------------------------------------
        | UPDATE SUPPLIER
        |--------------------------------------------------------------------------
        */

        $supplier->update([
            'name' => trim(
                $request->name
            ),

            'phone' => $request->phone
                ? trim($request->phone)
                : null,

            'address' => $request->address
                ? trim($request->address)
                : null,

            'notes' => $request->notes
                ? trim($request->notes)
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - SUPPLIER DIPERBARUI
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'supplier_updated',
            'Mengubah data supplier "' .
            $supplier->name .
            '".',
            $supplier,
            null,
            $store->id,
            $oldValues,
            [
                'name' =>
                    $supplier->name,

                'phone' =>
                    $supplier->phone,

                'address' =>
                    $supplier->address,

                'notes' =>
                    $supplier->notes,
            ]
        );

        return redirect()
            ->route(
                'supplier.show',
                $supplier->id
            )
            ->with(
                'success',
                'Data supplier berhasil diperbarui.'
            );
    }
}