<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat / memperbarui Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@smartpos.com'],
            [
                'name' => 'septian',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Membuat / memperbarui Akun Kasir
        User::updateOrCreate(
            ['email' => 'kasir@smartpos.com'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
            ]
        );
        
                // Membuat / memperbarui Akun Admin KasirKU
        User::updateOrCreate(
            ['email' => 'admin@kasirku.com'],
            [
                'name' => 'Admin KasirKU',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_platform_admin' => true,
            ]
        );

        // Seeder Paket Subscription KasirKU
        $this->call([
            PlanSeeder::class,
            FeatureSeeder::class,
            PlanFeatureSeeder::class,
            PlanLimitSeeder::class,
            StoreSubscriptionSeeder::class,
        ]);
    }
}