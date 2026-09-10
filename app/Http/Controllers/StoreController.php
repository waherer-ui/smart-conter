<?php

namespace App\Http\Controllers;

use App\Models\Store;
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

        session([
            'active_store_id' => $store->id,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Berhasil pindah ke ' . $store->name
            );
    }
}