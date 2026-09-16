<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'description' => 'Paket gratis untuk kebutuhan dasar toko.',
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 29000,
                'description' => 'Paket lengkap untuk toko yang berkembang.',
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 59000,
                'description' => 'Paket lengkap dengan fitur lanjutan untuk bisnis multi-toko.',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                [
                    'name' => $plan['name'],
                    'price' => $plan['price'],
                    'description' => $plan['description'],
                    'is_active' => $plan['is_active'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}