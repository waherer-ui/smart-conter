<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Menampilkan halaman pengaturan.
     */
    public function index()
    {
        $storeId = $this->activeStoreId();

        $settings = Setting::where(
            'store_id',
            $storeId
        )->first();

        if (!$settings) {

            $settings = Setting::create([
                'store_id' => $storeId,

                'store_name' => 'Smart POS',
                'store_address' => null,
                'store_phone' => null,
                'store_email' => null,

                'currency' => 'IDR',
                'allow_discount' => true,
                'max_discount' => 100,

                'minimum_stock' => 5,

                'receipt_footer' => null,
                'show_cashier' => true,
                'show_payment_method' => true,
                'show_discount' => true,
            ]);
        }

        $store = Store::find($storeId);

        return view(
            'setting',
            compact('settings', 'store')
        );
    }

    /**
     * Menyimpan perubahan pengaturan.
     */
    public function update(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'store_name' => [
                'required',
                'string',
                'max:150',
            ],

            'store_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'store_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'store_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'currency' => [
                'required',
                'string',
                'max:10',
            ],

            'allow_discount' => [
                'nullable',
                'boolean',
            ],

            'max_discount' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'receipt_footer' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'show_cashier' => [
                'nullable',
                'boolean',
            ],

            'show_payment_method' => [
                'nullable',
                'boolean',
            ],

            'show_discount' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Lokasi Toko
            |--------------------------------------------------------------------------
            */

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'attendance_radius' => [
                'required',
                'integer',
                'min:10',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Jam Kerja
            |--------------------------------------------------------------------------
            */

            'work_start_time' => [
                'required',
                'date_format:H:i',
            ],

            'work_end_time' => [
                'required',
                'date_format:H:i',
            ],

            'late_tolerance' => [
                'required',
                'integer',
                'min:0',
                'max:180',
            ],

        ], [

            'store_name.required' =>
                'Nama toko wajib diisi.',

            'store_name.max' =>
                'Nama toko terlalu panjang.',

            'store_email.email' =>
                'Format email tidak valid.',

            'max_discount.min' =>
                'Maksimal diskon tidak boleh kurang dari 0%.',

            'max_discount.max' =>
                'Maksimal diskon tidak boleh lebih dari 100%.',

            'minimum_stock.integer' =>
                'Minimum stok harus berupa angka.',

            'minimum_stock.min' =>
                'Minimum stok tidak boleh kurang dari 0.',

            'latitude.between' =>
                'Latitude tidak valid.',

            'longitude.between' =>
                'Longitude tidak valid.',

            'attendance_radius.min' =>
                'Radius absensi minimal 10 meter.',

            'attendance_radius.max' =>
                'Radius absensi maksimal 1000 meter.',

            'work_start_time.date_format' =>
                'Format jam masuk tidak valid.',

            'work_end_time.date_format' =>
                'Format jam pulang tidak valid.',

            'late_tolerance.min' =>
                'Toleransi keterlambatan tidak boleh kurang dari 0 menit.',

            'late_tolerance.max' =>
                'Toleransi keterlambatan maksimal 180 menit.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ID Toko Aktif
        |--------------------------------------------------------------------------
        */

        $storeId = $this->activeStoreId();

        /*
        |--------------------------------------------------------------------------
        | Ambil / Buat Setting
        |--------------------------------------------------------------------------
        */

        $settings = Setting::where(
            'store_id',
            $storeId
        )->first();

        if (!$settings) {

            $settings = new Setting();

            $settings->store_id = $storeId;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Toko
        |--------------------------------------------------------------------------
        */

        $store = Store::findOrFail($storeId);

        /*
        |--------------------------------------------------------------------------
        | Simpan Nilai Lama Untuk Audit
        |--------------------------------------------------------------------------
        */

        $oldValues = $settings->exists
            ? [
                'store_name' =>
                    $settings->store_name,

                'store_address' =>
                    $settings->store_address,

                'store_phone' =>
                    $settings->store_phone,

                'store_email' =>
                    $settings->store_email,

                'currency' =>
                    $settings->currency,

                'allow_discount' =>
                    $settings->allow_discount,

                'max_discount' =>
                    $settings->max_discount,

                'minimum_stock' =>
                    $settings->minimum_stock,

                'receipt_footer' =>
                    $settings->receipt_footer,

                'show_cashier' =>
                    $settings->show_cashier,

                'show_payment_method' =>
                    $settings->show_payment_method,

                'show_discount' =>
                    $settings->show_discount,

                'latitude' =>
                    $store->latitude,

                'longitude' =>
                    $store->longitude,

                'attendance_radius' =>
                    $store->attendance_radius,

                'work_start_time' =>
                    $store->work_start_time,

                'work_end_time' =>
                    $store->work_end_time,

                'late_tolerance' =>
                    $store->late_tolerance,
            ]
            : null;

        /*
        |--------------------------------------------------------------------------
        | Simpan Pengaturan
        |--------------------------------------------------------------------------
        */

        $settings->store_name =
            $validated['store_name'];

        $settings->store_address =
            $validated['store_address'] ?? null;

        $settings->store_phone =
            $validated['store_phone'] ?? null;

        $settings->store_email =
            $validated['store_email'] ?? null;

        $settings->currency =
            $validated['currency'];

        $settings->allow_discount =
            $request->boolean('allow_discount');

        $settings->max_discount =
            $validated['max_discount'];

        $settings->minimum_stock =
            $validated['minimum_stock'];

        $settings->receipt_footer =
            $validated['receipt_footer'] ?? null;

        $settings->show_cashier =
            $request->boolean('show_cashier');

        $settings->show_payment_method =
            $request->boolean('show_payment_method');

        $settings->show_discount =
            $request->boolean('show_discount');

        $settings->save();

        /*
        |--------------------------------------------------------------------------
        | Simpan Lokasi & Jam Kerja Toko
        |--------------------------------------------------------------------------
        */

        $store->latitude =
            $validated['latitude'] ?? null;

        $store->longitude =
            $validated['longitude'] ?? null;

        $store->attendance_radius =
            $validated['attendance_radius'];

        $store->work_start_time =
            $validated['work_start_time'];

        $store->work_end_time =
            $validated['work_end_time'];

        $store->late_tolerance =
            $validated['late_tolerance'];

        $store->save();

        /*
        |--------------------------------------------------------------------------
        | Nilai Baru Untuk Audit Log
        |--------------------------------------------------------------------------
        */

        $newValues = [
            'store_name' =>
                $settings->store_name,

            'store_address' =>
                $settings->store_address,

            'store_phone' =>
                $settings->store_phone,

            'store_email' =>
                $settings->store_email,

            'currency' =>
                $settings->currency,

            'allow_discount' =>
                $settings->allow_discount,

            'max_discount' =>
                $settings->max_discount,

            'minimum_stock' =>
                $settings->minimum_stock,

            'receipt_footer' =>
                $settings->receipt_footer,

            'show_cashier' =>
                $settings->show_cashier,

            'show_payment_method' =>
                $settings->show_payment_method,

            'show_discount' =>
                $settings->show_discount,

            'latitude' =>
                $store->latitude,

            'longitude' =>
                $store->longitude,

            'attendance_radius' =>
                $store->attendance_radius,

            'work_start_time' =>
                $store->work_start_time,

            'work_end_time' =>
                $store->work_end_time,

            'late_tolerance' =>
                $store->late_tolerance,
        ];

        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'settings_updated',

            'Mengubah pengaturan toko "' .
            $settings->store_name .
            '".',

            $settings,

            null,

            $storeId,

            $oldValues,

            $newValues
        );

        /*
        |--------------------------------------------------------------------------
        | Kembali
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('setting')
            ->with(
                'success',
                'Pengaturan berhasil disimpan.'
            );
    }
}