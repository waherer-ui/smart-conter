<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\Plan;

use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with([
            'limits',
            'features',
            'subscriptions',
        ])
        ->orderBy('price')
        ->get();

        return view(
            'admin-kasirku.plans.index',
            compact('plans')
        );
    }

    public function edit(Plan $plan)
    {
        $plan->load([
            'limits',
            'features',
        ]);

        return view(
            'admin-kasirku.plans.edit',
            compact('plan')
        );
    }
    
    public function update(Request $request, Plan $plan)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'slug' => [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            'unique:plans,slug,' . $plan->id,
        ],
        'price' => ['required', 'numeric', 'min:0'],
        'description' => ['nullable', 'string'],
        'is_active' => ['required', 'boolean'],
    ]);

    $plan->update([
        'name' => trim($validated['name']),
        'slug' => strtolower(trim($validated['slug'])),
        'price' => $validated['price'],
        'description' => $validated['description'] ?? null,
        'is_active' => $validated['is_active'],
    ]);

    return redirect()
        ->route('admin-kasirku.plans.edit', $plan)
        ->with('success', 'Paket berhasil diperbarui.');
}
}