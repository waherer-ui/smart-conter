<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $freePlan = DB::table('plans')
            ->where('slug', 'free')
            ->first();

        if (!$freePlan) {
            $this->command->error('Paket Free belum tersedia.');
            return;
        }

        $stores = Store::all();

        foreach ($stores as $store) {

            $hasSubscription = DB::table('subscriptions')
                ->where('store_id', $store->id)
                ->where('status', 'active')
                ->exists();

            if (!$hasSubscription) {
                DB::table('subscriptions')->insert([
                    'store_id' => $store->id,
                    'plan_id' => $freePlan->id,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Subscription Free untuk toko yang belum memiliki subscription berhasil dibuat.');
    }
}