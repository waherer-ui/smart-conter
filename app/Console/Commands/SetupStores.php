<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('app:setup-stores')]
#[Description('Setup toko dan relasi user untuk multi-store')]
class SetupStores extends Command
{
    public function handle()
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */

            $admin = User::find(1);

            if (!$admin) {
                throw new \Exception('User admin dengan ID 1 tidak ditemukan.');
            }

            /*
            |--------------------------------------------------------------------------
            | TOKO UTAMA
            |--------------------------------------------------------------------------
            */

            $tokoUtama = Store::firstOrCreate(
                [
                    'owner_id' => $admin->id,
                    'name' => 'Toko Utama',
                ],
                [
                    'address' => null,
                    'phone' => null,
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | TOKO CABANG
            |--------------------------------------------------------------------------
            */

            $tokoCabang = Store::firstOrCreate(
                [
                    'owner_id' => $admin->id,
                    'name' => 'Toko Cabang',
                ],
                [
                    'address' => null,
                    'phone' => null,
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | ADMIN → DUA TOKO
            |--------------------------------------------------------------------------
            */

            $admin->stores()->syncWithoutDetaching([
                $tokoUtama->id => [
                    'role' => 'owner',
                ],
                $tokoCabang->id => [
                    'role' => 'owner',
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | KASIR LAMA → TOKO UTAMA
            |--------------------------------------------------------------------------
            */

            $kasir = User::where('id', '!=', $admin->id)
                ->where('role', 'kasir')
                ->first();

            if ($kasir) {
                $kasir->stores()->syncWithoutDetaching([
                    $tokoUtama->id => [
                        'role' => 'kasir',
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DATA LAMA → TOKO UTAMA
            |--------------------------------------------------------------------------
            */

            DB::table('products')
                ->whereNull('store_id')
                ->update([
                    'store_id' => $tokoUtama->id,
                ]);

            DB::table('product_histories')
                ->whereNull('store_id')
                ->update([
                    'store_id' => $tokoUtama->id,
                ]);

            DB::table('transactions')
                ->whereNull('store_id')
                ->update([
                    'store_id' => $tokoUtama->id,
                ]);

            DB::table('expenses')
                ->whereNull('store_id')
                ->update([
                    'store_id' => $tokoUtama->id,
                ]);

            DB::table('settings')
                ->whereNull('store_id')
                ->update([
                    'store_id' => $tokoUtama->id,
                ]);

            $this->info('Setup multi-store berhasil.');
            $this->line('');
            $this->line('Toko Utama : ' . $tokoUtama->id . ' - ' . $tokoUtama->name);
            $this->line('Toko Cabang: ' . $tokoCabang->id . ' - ' . $tokoCabang->name);
            $this->line('Admin      : ' . $admin->name . ' → kedua toko');

            if ($kasir) {
                $this->line(
                    'Kasir      : ' . $kasir->name . ' → Toko Utama'
                );
            } else {
                $this->line('Kasir      : tidak ditemukan');
            }
        });

        return self::SUCCESS;
    }
}