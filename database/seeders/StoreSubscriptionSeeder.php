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

        $ownerIds = Store::query()
            ->whereNotNull('owner_id')
            ->distinct()
            ->pluck('owner_id');

        foreach ($ownerIds as $ownerId) {

            $hasSubscription = DB::table('subscriptions')
                ->where('owner_id', $ownerId)
                ->exists();

            if (!$hasSubscription) {
                DB::table('subscriptions')->insert([
                    'owner_id' => $ownerId,
                    'plan_id' => $freePlan->id,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info(
            'Subscription Free untuk owner yang belum memiliki subscription berhasil dibuat.'
        );
    }
}