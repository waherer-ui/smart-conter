<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Menampilkan seluruh toko di platform KasirKU.
     */
    public function index()
{
    $stores = Store::with([
        'owner.subscription.plan',
        'users',
    ])
    ->orderBy('created_at', 'desc')
    ->paginate(20);

    return view(
        'admin-kasirku.stores.index',
        compact('stores')
    );
}
}