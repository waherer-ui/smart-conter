<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentAccountController extends Controller
{
    private function storeId(): int
    {
        return (int) session('active_store_id');
    }

    private function checkFeature()
    {
        $store = \App\Models\Store::find($this->storeId());

        if (!$store || !$store->hasFeature('payment_bank')) {
            abort(403, 'Fitur Rekening & Pembayaran hanya tersedia untuk paket Pro dan Premium.');
        }

        return $store;
    }

    public function index()
{
    $store = $this->checkFeature();

    $accounts = PaymentAccount::where('store_id', $store->id)
        ->latest()
        ->get();

    $qrs = $store->paymentQrs()
        ->latest()
        ->get();

    return view('payment-settings.index', [
        'accounts' => $accounts,
        'qrs' => $qrs,
    ]);
}

    public function store(Request $request)
    {
        $store = $this->checkFeature();

        $validated = $request->validate([
            'type' => ['required', 'in:bank,ewallet'],
            'provider' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
        ]);

        PaymentAccount::create([
            'store_id' => $store->id,
            'type' => $validated['type'],
            'provider' => $validated['provider'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'is_active' => true,
        ]);

        return back()->with(
            'success',
            'Rekening / e-wallet berhasil ditambahkan.'
        );
    }

    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $store = $this->checkFeature();

        abort_unless(
            $paymentAccount->store_id === $store->id,
            403
        );

        $validated = $request->validate([
            'type' => ['required', 'in:bank,ewallet'],
            'provider' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
        ]);

        $paymentAccount->update($validated);

        return back()->with(
            'success',
            'Rekening / e-wallet berhasil diperbarui.'
        );
    }

    public function destroy(PaymentAccount $paymentAccount)
    {
        $store = $this->checkFeature();

        abort_unless(
            $paymentAccount->store_id === $store->id,
            403
        );

        $paymentAccount->delete();

        return back()->with(
            'success',
            'Rekening / e-wallet berhasil dihapus.'
        );
    }

    public function toggle(PaymentAccount $paymentAccount)
    {
        $store = $this->checkFeature();

        abort_unless(
            $paymentAccount->store_id === $store->id,
            403
        );

        $paymentAccount->update([
            'is_active' => !$paymentAccount->is_active,
        ]);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}