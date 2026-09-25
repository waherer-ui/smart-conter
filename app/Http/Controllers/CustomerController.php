<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    private function debtFeatureAllowed()
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->hasFeature('debt')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Catatan Utang hanya tersedia pada paket Pro dan Premium.'
                );
        }

        return null;
    }

    public function index()
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        $customers = Customer::where(
            'store_id',
            $this->activeStoreId()
        )
        ->latest()
        ->paginate(15);

        return view(
            'customers.index',
            compact(
                'customers',
                'store'
            )
        );
    }

    public function create()
    {
        return view('customers.create');
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
        ]);

        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->canAddCustomer()) {
            return redirect()
                ->route('pelanggan.index')
                ->with(
                    'error',
                    'Batas jumlah pelanggan pada paket Anda sudah tercapai. Silakan upgrade paket untuk menambah pelanggan baru.'
                );
        }

        $customer = Customer::create([
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - PELANGGAN DIBUAT
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'customer_created',
            'Menambahkan pelanggan "' .
            $customer->name .
            '".',
            $customer,
            null,
            $store->id,
            null,
            [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
            ]
        );

        return redirect()
            ->route('pelanggan.index')
            ->with(
                'success',
                'Pelanggan berhasil ditambahkan.'
            );
    }

    public function show($id)
    {
        $store = Store::find(
            $this->activeStoreId()
        );

        $customer = Customer::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with([
            'debts' => function ($query) {
                $query
                    ->with([
                        'payments' => function ($paymentQuery) {
                            $paymentQuery->latest(
                                'payment_date'
                            );
                        },

                        'user',
                    ])
                    ->latest('debt_date');
            },
        ])
        ->findOrFail($id);

        $totalDebt = $customer->debts
            ->sum(function ($debt) {
                return max(
                    0,
                    (float) $debt->amount -
                    (float) $debt->paid_amount
                );
            });

        return view(
            'customers.show',
            compact(
                'customer',
                'totalDebt',
                'store'
            )
        );
    }

    public function createDebt($id)
    {
        if ($redirect = $this->debtFeatureAllowed()) {
            return $redirect;
        }

        $customer = Customer::where(
            'store_id',
            $this->activeStoreId()
        )
        ->findOrFail($id);

        return view(
            'customers.debt-create',
            compact('customer')
        );
    }

    public function storeDebt(Request $request, $id)
    {
        if ($redirect = $this->debtFeatureAllowed()) {
            return $redirect;
        }

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'debt_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:debt_date',
            ],
        ]);

        $customer = Customer::where(
            'store_id',
            $this->activeStoreId()
        )
        ->findOrFail($id);

        $debt = \App\Models\Debt::create([
            'store_id' => $this->activeStoreId(),

            'customer_id' => $customer->id,

            'user_id' => session('user_id'),

            'description' => $request->description
                ? trim($request->description)
                : null,

            'amount' => $request->amount,

            'paid_amount' => 0,

            'debt_date' => $request->debt_date,

            'due_date' => $request->due_date
                ?: null,

            'status' => 'unpaid',
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - UTANG DIBUAT
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'debt_created',
            'Mencatat utang pelanggan "' .
            $customer->name .
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
            $this->activeStoreId(),
            null,
            [
                'customer_id' =>
                    $customer->id,

                'customer_name' =>
                    $customer->name,

                'amount' =>
                    $debt->amount,

                'paid_amount' =>
                    $debt->paid_amount,

                'debt_date' =>
                    $debt->debt_date,

                'due_date' =>
                    $debt->due_date,

                'status' =>
                    $debt->status,
            ]
        );

        return redirect()
            ->route(
                'pelanggan.show',
                $customer->id
            )
            ->with(
                'success',
                'Utang pelanggan berhasil dicatat.'
            );
    }

    public function createDebtPayment($id)
    {
        if ($redirect = $this->debtFeatureAllowed()) {
            return $redirect;
        }

        $debt = \App\Models\Debt::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with('customer')
        ->findOrFail($id);

        if ($debt->status === 'paid') {
            return redirect()
                ->route(
                    'pelanggan.show',
                    $debt->customer_id
                )
                ->with(
                    'error',
                    'Utang ini sudah lunas.'
                );
        }

        return view(
            'customers.debt-payment',
            compact('debt')
        );
    }

    public function storeDebtPayment(Request $request, $id)
    {
        if ($redirect = $this->debtFeatureAllowed()) {
            return $redirect;
        }

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $debt = \App\Models\Debt::where(
            'store_id',
            $this->activeStoreId()
        )
        ->with('customer')
        ->findOrFail($id);

        $remaining = $debt->remaining_amount;

        if ((float) $request->amount > $remaining) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Jumlah pembayaran melebihi sisa utang.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA SEBELUM PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $oldValues = [
            'paid_amount' =>
                $debt->paid_amount,

            'status' =>
                $debt->status,

            'remaining_amount' =>
                $remaining,
        ];

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        $payment = \App\Models\DebtPayment::create([
            'debt_id' => $debt->id,

            'user_id' =>
                session('user_id'),

            'amount' =>
                $request->amount,

            'payment_date' =>
                $request->payment_date,

            'note' => $request->note
                ? trim($request->note)
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | HITUNG STATUS UTANG
        |--------------------------------------------------------------------------
        */

        $newPaidAmount =
            (float) $debt->paid_amount +
            (float) $request->amount;

        $newStatus =
            $newPaidAmount >=
            (float) $debt->amount
                ? 'paid'
                : 'partial';

        $debt->update([
            'paid_amount' =>
                $newPaidAmount,

            'status' =>
                $newStatus,
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - PEMBAYARAN UTANG
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'debt_payment',
            'Mencatat pembayaran utang pelanggan "' .
            $debt->customer->name .
            '" sebesar Rp' .
            number_format(
                $payment->amount,
                0,
                ',',
                '.'
            ) .
            '. Sisa utang: Rp' .
            number_format(
                max(
                    0,
                    (float) $debt->amount -
                    $newPaidAmount
                ),
                0,
                ',',
                '.'
            ) .
            '.',
            $payment,
            null,
            $this->activeStoreId(),
            $oldValues,
            [
                'paid_amount' =>
                    $newPaidAmount,

                'status' =>
                    $newStatus,

                'remaining_amount' =>
                    max(
                        0,
                        (float) $debt->amount -
                        $newPaidAmount
                    ),

                'payment_amount' =>
                    $payment->amount,
            ]
        );

        return redirect()
            ->route(
                'pelanggan.show',
                $debt->customer_id
            )
            ->with(
                'success',
                'Pembayaran utang berhasil dicatat.'
            );
    }
    
    public function ajaxStore(Request $request)
{
    $storeId = session('active_store_id');

    if (!$storeId) {
        return response()->json([
            'success' => false,
            'message' => 'Toko aktif tidak ditemukan.',
        ], 422);
    }

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'phone' => [
            'nullable',
            'string',
            'max:50',
        ],

        'address' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    $customer = Customer::create([
        'store_id' => $storeId,
        'name' => $validated['name'],
        'phone' => $validated['phone'] ?? null,
        'address' => $validated['address'] ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Pelanggan berhasil ditambahkan.',
        'customer' => [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
        ],
    ]);
}
}