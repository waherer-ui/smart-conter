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

    $store = Store::create([
        'owner_id' => $user->id,
        'name' => $request->name,
        'address' => $request->address,
        'phone' => $request->phone,
        'is_active' => true,
    ]);

    $user->stores()->attach($store->id, [
        'role' => 'owner',
    ]);

    session([
        'active_store_id' => $store->id,
    ]);

    return redirect()
        ->route('dashboard')
        ->with(
            'success',
            'Toko ' . $store->name . ' berhasil dibuat.'
        );
}
}