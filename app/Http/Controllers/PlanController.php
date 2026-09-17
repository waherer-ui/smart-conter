<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

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
    
    public function select(Request $request, Plan $plan)
{
    $user = User::findOrFail(
        session('user_id')
    );

    $subscription = $user->subscription;

    if (!$subscription) {
        return redirect()
            ->route('paket')
            ->with(
                'error',
                'Subscription Owner tidak ditemukan.'
            );
    }

    $subscription->update([
        'plan_id' => $plan->id,
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => null,
    ]);

    return redirect()
        ->route('paket')
        ->with(
            'success',
            'Paket ' . $plan->name . ' berhasil dipilih.'
        );
}
}