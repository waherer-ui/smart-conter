<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanLimitSeeder extends Seeder
{
    public function run(): void
    {
        $limits = [
            'free' => [
                'max_stores' => 1,
                'max_staff' => 1,
                'max_products' => 100,
                'max_customers' => 50,
                'history_days' => 30,
            ],

            'pro' => [
                'max_stores' => 3,
                'max_staff' => 5,
                'max_products' => 5000,
                'max_customers' => 5000,
                'history_days' => null,
            ],

            'premium' => [
                'max_stores' => 10,
                'max_staff' => 15,
                'max_products' => null,
                'max_customers' => null,
                'history_days' => null,
            ],
        ];

        foreach ($limits as $planSlug => $planLimits) {

            $plan = DB::table('plans')
                ->where('slug', $planSlug)
                ->first();

            foreach ($planLimits as $key => $value) {
                DB::table('plan_limits')->updateOrInsert(
                    [
                        'plan_id' => $plan->id,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}