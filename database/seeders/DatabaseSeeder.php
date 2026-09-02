<?php

namespace Database\seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Akun Admin
        User::create([
            'name' => 'septian',
            'email' => 'admin@smartpos.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Membuat Akun Kasir
        User::create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@smartpos.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);
    }
}