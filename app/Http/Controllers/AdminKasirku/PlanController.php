<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $features = Feature::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin-kasirku.plans.edit',
            compact('plan', 'features')
        );
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:plans,slug,' . $plan->id,
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'limits' => [
                'nullable',
                'array',
            ],

            'limits.*' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'features' => [
                'nullable',
                'array',
            ],

            'features.*' => [
                'integer',
                'exists:features,id',
            ],
        ]);

        DB::transaction(function () use ($validated, $plan) {

            /*
             * =====================================================
             * INFORMASI UTAMA PAKET
             * =====================================================
             */
            $plan->update([
                'name' => trim($validated['name']),
                'slug' => strtolower(trim($validated['slug'])),
                'price' => $validated['price'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
            ]);


            /*
             * =====================================================
             * UPDATE LIMIT
             * =====================================================
             *
             * Nilai kosong = Unlimited (NULL).
             */
            if ($plan->limits()->exists()) {

                foreach ($plan->limits as $limit) {

                    $value = $validated['limits'][$limit->key]
                        ?? null;

                    $limit->update([
                        'value' => $value,
                    ]);
                }
            }


            /*
             * =====================================================
             * UPDATE FITUR
             * =====================================================
             *
             * Checkbox yang dicentang akan disimpan
             * ke tabel pivot plan_features.
             */
            $featureIds = $validated['features'] ?? [];

            $plan->features()->sync($featureIds);
        });

        return redirect()
            ->route('admin-kasirku.plans.edit', $plan)
            ->with('success', 'Paket berhasil diperbarui.');
    }
}
