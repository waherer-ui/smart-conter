<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveStore
{
    /**
     * Menentukan toko aktif untuk user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guest tidak perlu toko aktif.
        if (!session('logged_in')) {
            return $next($request);
        }

              $user = \App\Models\User::find(session('user_id'));
      
      // Kalau user tidak ditemukan.
      if (!$user) {
          return $next($request);
      }

        // Ambil toko aktif dari session.
        $activeStoreId = session('active_store_id');

        // Pastikan toko aktif memang milik user.
        $store = $user->stores()
            ->where('stores.id', $activeStoreId)
            ->first();

        // Kalau belum punya toko aktif, gunakan toko pertama.
        if (!$store) {
            $store = $user->stores()
                ->orderBy('stores.id')
                ->first();

            if ($store) {
                session(['active_store_id' => $store->id]);
            }
        }

        // Simpan object toko aktif ke request.
        $request->attributes->set('activeStore', $store);

        return $next($request);
    }
}