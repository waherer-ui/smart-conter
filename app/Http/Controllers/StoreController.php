<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function switch(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail(
            session('user_id')
        );

        $store = $user->stores()
            ->where('stores.id', $id)
            ->firstOrFail();

        // Batas toko mengikuti paket Owner
        $storeLimit = $store->getLimit('max_stores');

        // Urutan toko milik Owner berdasarkan ID
        $storeIds = $user->stores()
            ->orderBy('stores.id')
            ->pluck('stores.id')
            ->values();

        $storeIndex = $storeIds->search(
            $store->id
        );

        // Jika toko berada di luar batas paket → kunci
        if (
            $storeLimit !== null &&
            $storeIndex !== false &&
            $storeIndex >= $storeLimit
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Toko ini terkunci pada paket Anda. Silakan upgrade paket untuk mengakses toko tersebut.'
                );
        }

        session([
            'active_store_id' => $store->id,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Berhasil pindah ke ' .
                $store->name
            );
    }


    public function create()
    {
        return view('stores.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:30',
        ]);

        $user = \App\Models\User::findOrFail(
            session('user_id')
        );

        $activeStore = $user->stores()->first();

        $storeLimit =
            $activeStore?->getLimit(
                'max_stores'
            );

        $storeCount =
            $user->stores()->count();

        /*
         * =====================================================
         * CEK BATAS TOKO
         * =====================================================
         */

        if (
            $storeLimit !== null &&
            $storeCount >= $storeLimit
        ) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Batas jumlah toko pada paket Anda sudah tercapai. Silakan upgrade paket untuk menambah toko baru.'
                );
        }

        /*
         * =====================================================
         * BUAT TOKO
         * =====================================================
         */

        $store = Store::create([
            'owner_id' =>
                $user->id,

            'name' =>
                $request->name,

            'address' =>
                $request->address,

            'phone' =>
                $request->phone,

            'is_active' =>
                true,
        ]);

        /*
         * =====================================================
         * HUBUNGKAN OWNER DENGAN TOKO
         * =====================================================
         */

        $user->stores()->attach(
            $store->id,
            [
                'role' => 'owner',
            ]
        );

        /*
         * =====================================================
         * SET TOKO AKTIF
         * =====================================================
         */

        session([
            'active_store_id' =>
                $store->id,
        ]);

        /*
         * =====================================================
         * AUDIT LOG
         * =====================================================
         */

        AuditLogService::log(
            'store_created',

            'Menambahkan toko baru "' .
            $store->name .
            '".',

            $store,

            $user->id,

            $store->id,

            null,

            [
                'store_id' =>
                    $store->id,

                'store_name' =>
                    $store->name,

                'owner_id' =>
                    $user->id,

                'owner_name' =>
                    $user->name,

                'address' =>
                    $store->address,

                'phone' =>
                    $store->phone,

                'is_active' =>
                    $store->is_active,
            ]
        );

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Toko ' .
                $store->name .
                ' berhasil dibuat.'
            );
    }
}