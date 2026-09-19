<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Menampilkan seluruh owner beserta toko dan stafnya.
     */
    public function index()
    {
        $owners = User::query()
            ->where('role', 'admin')
            ->whereHas('ownedStores')
            ->with([
                'subscription.plan',
                'ownedStores' => function ($query) {
                    $query->with('users');
                },
            ])
            ->withCount('ownedStores')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view(
            'admin-kasirku.users.index',
            compact('owners')
        );
    }

    /**
     * Menampilkan detail owner beserta seluruh toko miliknya.
     */
    public function show(User $user)
    {
        abort_unless($user->role === 'admin', 404);

        $user->load([
            'subscription.plan',
            'ownedStores' => function ($query) {
                $query->with([
                    'users',
                    'products',
                    'customers',
                    'transactions',
                ]);
            },
        ]);

        return view(
            'admin-kasirku.users.show',
            compact('user')
        );
    }
}