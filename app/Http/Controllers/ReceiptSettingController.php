<?php

namespace App\Http\Controllers;

use App\Models\ReceiptSetting;
use App\Models\Store;
use Illuminate\Http\Request;

class ReceiptSettingController extends Controller
{
    /**
     * Halaman pengaturan custom struk.
     */
    public function index()
    {
        $storeId = session('active_store_id');

        if (!$storeId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Toko aktif tidak ditemukan.');
        }

        $store = Store::findOrFail($storeId);

        // Pastikan fitur Custom Struk tersedia di paket toko.
        if (!$store->hasFeature('custom_receipt')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Custom Struk hanya tersedia pada paket Pro dan Premium.'
                );
        }

        // Buat pengaturan default jika belum ada.
        $receiptSetting = ReceiptSetting::firstOrCreate(
            ['store_id' => $store->id],
            [
                'business_name' => $store->name,
                'address'       => $store->address,
                'phone'         => $store->phone,
                'email'         => null,
                'header_text'   => null,
                'footer_text'   => 'Terima kasih 🙏',
                'logo'          => null,
            ]
        );

        return view(
            'receipt-settings.index',
            compact('store', 'receiptSetting')
        );
    }

    /**
     * Simpan pengaturan custom struk.
     */
    public function update(Request $request)
    {
        $storeId = session('active_store_id');

        if (!$storeId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Toko aktif tidak ditemukan.');
        }

        $store = Store::findOrFail($storeId);

        // Pastikan fitur Custom Struk tersedia di paket toko.
        if (!$store->hasFeature('custom_receipt')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Custom Struk hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:1000'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'header_text'   => ['nullable', 'string', 'max:1000'],
            'footer_text'   => ['nullable', 'string', 'max:1000'],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $receiptSetting = ReceiptSetting::firstOrCreate(
            ['store_id' => $store->id]
        );

        /*
         * ==========================================
         * UPLOAD LOGO
         * ==========================================
         *
         * Logo disimpan langsung ke:
         *
         * public/receipt-logos/
         *
         * Tidak menggunakan storage:link.
         */
        if ($request->hasFile('logo')) {

            $logo = $request->file('logo');

            $directory = public_path('receipt-logos');

            // Buat folder jika belum ada.
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            /*
             * Hapus logo lama jika ada.
             */
            if (
                $receiptSetting->logo &&
                str_starts_with(
                    $receiptSetting->logo,
                    'receipt-logos/'
                )
            ) {
                $oldLogo = public_path($receiptSetting->logo);

                if (is_file($oldLogo)) {
                    @unlink($oldLogo);
                }
            }

            /*
             * Buat nama file unik.
             */
            $extension = strtolower(
                $logo->getClientOriginalExtension()
            );

            $filename =
                'receipt-logo-' .
                $store->id .
                '-' .
                uniqid() .
                '.' .
                $extension;

            /*
             * Pindahkan file ke public/receipt-logos/
             */
            $logo->move($directory, $filename);

            /*
             * Simpan path relatif ke database.
             */
            $validated['logo'] =
                'receipt-logos/' . $filename;
        }

        /*
         * Jika tidak upload logo baru,
         * pertahankan logo lama.
         */
        if (!array_key_exists('logo', $validated)) {
            $validated['logo'] = $receiptSetting->logo;
        }

        $receiptSetting->update([
            'business_name' => $validated['business_name'] ?? null,
            'address'       => $validated['address'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'email'         => $validated['email'] ?? null,
            'header_text'   => $validated['header_text'] ?? null,
            'footer_text'   => $validated['footer_text'] ?? null,
            'logo'          => $validated['logo'],
        ]);

        return redirect()
            ->route('receipt-settings.index')
            ->with(
                'success',
                'Pengaturan struk berhasil disimpan.'
            );
    }

    /**
     * Hapus logo struk.
     */
    public function deleteLogo()
    {
        $storeId = session('active_store_id');

        if (!$storeId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Toko aktif tidak ditemukan.');
        }

        $store = Store::findOrFail($storeId);

        // Pastikan fitur Custom Struk tersedia.
        if (!$store->hasFeature('custom_receipt')) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Fitur Custom Struk hanya tersedia pada paket Pro dan Premium.'
                );
        }

        $receiptSetting = ReceiptSetting::where(
            'store_id',
            $store->id
        )->first();

        if (!$receiptSetting) {
            return redirect()
                ->route('receipt-settings.index')
                ->with('error', 'Pengaturan struk tidak ditemukan.');
        }

        /*
         * Hanya hapus file yang memang berada
         * di folder receipt-logos.
         */
        if (
            $receiptSetting->logo &&
            str_starts_with(
                $receiptSetting->logo,
                'receipt-logos/'
            )
        ) {
            $logoPath = public_path($receiptSetting->logo);

            if (is_file($logoPath)) {
                @unlink($logoPath);
            }
        }

        $receiptSetting->update([
            'logo' => null,
        ]);

        return redirect()
            ->route('receipt-settings.index')
            ->with(
                'success',
                'Logo struk berhasil dihapus.'
            );
    }
}