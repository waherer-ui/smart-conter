<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with([
            'owner.stores',
            'plan',
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view(
            'admin-kasirku.subscriptions.index',
            compact('subscriptions')
        );
    }

    public function show(Subscription $subscription)
    {
        $subscription->load([
            'owner.stores',
            'owner',
            'plan',
        ]);

        return view(
            'admin-kasirku.subscriptions.show',
            compact('subscription')
        );
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load([
            'owner.stores',
            'owner',
            'plan',
        ]);

        $plans = Plan::orderBy('price')->get();

        return view(
            'admin-kasirku.subscriptions.edit',
            compact('subscription', 'plans')
        );
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate(
            [
                'plan_id' => ['required', 'exists:plans,id'],
                'status' => ['required', 'in:active,pending,expired,cancelled'],
                'starts_at' => ['required', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            ],
            [
                'plan_id.required' => 'Paket wajib dipilih.',
                'plan_id.exists' => 'Paket yang dipilih tidak tersedia.',

                'status.required' => 'Status langganan wajib dipilih.',
                'status.in' => 'Status langganan tidak valid.',

                'starts_at.required' => 'Tanggal mulai wajib diisi.',
                'starts_at.date' => 'Tanggal mulai tidak valid.',

                'ends_at.date' => 'Tanggal berakhir tidak valid.',
                'ends_at.after_or_equal' =>
                    'Tanggal berakhir harus sama atau setelah tanggal mulai.',
            ]
        );

        $subscription->update([
            'plan_id' => $validated['plan_id'],
            'status' => $validated['status'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        return redirect()
            ->route('admin-kasirku.subscriptions.show', $subscription)
            ->with('success', 'Langganan berhasil diperbarui.');
    }
}