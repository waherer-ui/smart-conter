<?php

namespace App\Http\Controllers;

use App\Models\Setting;
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
        /*
        |--------------------------------------------------------------------------
        | Ambil pengaturan
        |--------------------------------------------------------------------------
        |
        | Kita hanya menggunakan satu baris konfigurasi toko.
        | Jika belum ada, buat otomatis dengan nilai default.
        |
        */

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

        return view(
            'setting',
            compact('settings')
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

        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil / Buat Setting
        |--------------------------------------------------------------------------
        */

        $storeId = $this->activeStoreId();

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