<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Store;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->with([
                'features' => function ($query) {
                    $query->where('is_active', true);
                },
                'limits',
            ])
            ->orderBy('price')
            ->get();

        $activeStore = Store::find(
            session('active_store_id')
        );

        return view(
            'paket.index',
            compact('plans', 'activeStore')
        );
    }
}