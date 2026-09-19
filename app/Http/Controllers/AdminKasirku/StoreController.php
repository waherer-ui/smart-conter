<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua Owner (role 'admin' dan bukan platform admin)
        $owners = User::where('role', 'admin')
            ->where('is_platform_admin', 0)
            ->withCount('stores')
            ->paginate(10);

        // 2. Ambil ID owner yang dipilih (default: owner pertama di list)
        $selectedOwnerId = $request->query('owner_id', $owners->first()?->id);

        $selectedOwner = null;
        if ($selectedOwnerId) {
            $selectedOwner = User::with([
                'subscription.plan',
                'stores.users',
                'stores.products',
                'stores.customers',
            ])->find($selectedOwnerId);
        }

        return view('admin-kasirku.stores.index', compact('owners', 'selectedOwner'));
    }

    public function show(Store $store)
{
    $store->load([
        'owner',
        'users',
        'products',
        'customers',
        'transactions',
        'suppliers',
        'purchases',
        'productHistories',
    ]);

    return view(
        'admin-kasirku.stores.show',
        compact('store')
    );
}
}
