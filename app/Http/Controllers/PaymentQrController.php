<?php

namespace App\Http\Controllers;

use App\Models\PaymentQr;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentQrController extends Controller
{
    private function currentStore(): Store
    {
        $store = Store::find(session('active_store_id'));

        if (!$store) {
            abort(403, 'Toko aktif tidak ditemukan.');
        }

        return $store;
    }

    public function store(Request $request)
    {
        $store = $this->currentStore();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        // Simpan QR ke S3
        $path = $request->file('image')->store(
            'payment-qrs/' . $store->id,
            's3'
        );

        PaymentQr::create([
            'store_id' => $store->id,
            'name' => $validated['name'],
            'image_path' => $path,
            'is_active' => true,
        ]);

        return back()->with(
            'success',
            'QR pembayaran berhasil ditambahkan.'
        );
    }

    public function destroy(PaymentQr $paymentQr)
    {
        $store = $this->currentStore();

        abort_unless(
            $paymentQr->store_id === $store->id,
            403
        );

        // Hapus gambar QR dari S3
        if ($paymentQr->image_path) {
            Storage::disk('s3')->delete(
                $paymentQr->image_path
            );
        }

        $paymentQr->delete();

        return back()->with(
            'success',
            'QR pembayaran berhasil dihapus.'
        );
    }

    public function toggle(PaymentQr $paymentQr)
    {
        $store = $this->currentStore();

        abort_unless(
            $paymentQr->store_id === $store->id,
            403
        );

        $paymentQr->update([
            'is_active' => !$paymentQr->is_active,
        ]);

        return back()->with(
            'success',
            'Status QR pembayaran diperbarui.'
        );
    }
}