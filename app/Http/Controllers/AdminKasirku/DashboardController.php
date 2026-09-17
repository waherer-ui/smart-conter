<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $totalStores = Store::count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->count();

        return view('admin-kasirku.dashboard', compact(
            'totalUsers',
            'totalStores',
            'activeSubscriptions'
        ));
    }
}
