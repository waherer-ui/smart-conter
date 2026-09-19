<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama Platform
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();

        $totalStores = Store::count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Statistik Paket Aktif
        |--------------------------------------------------------------------------
        */

        $freeSubscriptions = Subscription::where('status', 'active')
            ->whereHas('plan', function ($query) {
                $query->where('slug', 'free');
            })
            ->count();

        $proSubscriptions = Subscription::where('status', 'active')
            ->whereHas('plan', function ($query) {
                $query->where('slug', 'pro');
            })
            ->count();

        $premiumSubscriptions = Subscription::where('status', 'active')
            ->whereHas('plan', function ($query) {
                $query->where('slug', 'premium');
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Ringkasan Platform
        |--------------------------------------------------------------------------
        */

        // Estimasi pendapatan bulanan dari subscription aktif
        $estimatedMrr = Subscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->plan?->price ?? 0;
            });


        // Subscription yang akan kadaluwarsa dalam 7 hari
        $expiringSoon = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                now(),
                now()->addDays(7),
            ])
            ->count();


        // Total transaksi seluruh toko
        $totalTransactions = Transaction::count();


        /*
        |--------------------------------------------------------------------------
        | Aktivitas Terbaru
        |--------------------------------------------------------------------------
        */

        $recentOwners = User::query()
            ->where('role', 'admin')
            ->latest()
            ->take(5)
            ->get();

        $recentStores = Store::query()
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = Transaction::query()
    ->with('store')
    ->latest()
    ->take(5)
    ->get();


        /*
        |--------------------------------------------------------------------------
        | Timeline Aktivitas
        |--------------------------------------------------------------------------
        */

        $activities = collect();

        foreach ($recentOwners as $owner) {
            $activities->push([
                'type' => 'owner',
                'title' => 'Owner baru terdaftar',
                'name' => $owner->name,
                'description' => $owner->email,
                'date' => $owner->created_at,
                'icon' => '👤',
            ]);
        }

        foreach ($recentStores as $store) {
            $activities->push([
                'type' => 'store',
                'title' => 'Toko baru ditambahkan',
                'name' => $store->name,
                'description' => 'Toko terdaftar di platform',
                'date' => $store->created_at,
                'icon' => '🏪',
            ]);
        }

        foreach ($recentTransactions as $transaction) {
            $activities->push([
                'type' => 'transaction',
                'title' => 'Transaksi baru',
                'name' => $transaction->invoice_number,
                'description' => ($transaction->store?->name ?? 'Toko tidak diketahui')
                    . ' • Rp' . number_format(
                        $transaction->total,
                        0,
                        ',',
                        '.'
                    ),
                'date' => $transaction->created_at,
                'icon' => '📈',
            ]);
        }

        $activities = $activities
            ->sortByDesc('date')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Filter Aktivitas
        |--------------------------------------------------------------------------
        */

        $activity = $request->get('activity', 'all');


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view('admin-kasirku.dashboard', compact(
            'totalUsers',
            'totalStores',
            'activeSubscriptions',
            'freeSubscriptions',
            'proSubscriptions',
            'premiumSubscriptions',
            'estimatedMrr',
            'expiringSoon',
            'totalTransactions',
            'recentOwners',
            'recentStores',
            'recentTransactions',
            'activity',
            'activities',
        ));
    }
}