<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat relasi user dengan toko.
     */
    public function up(): void
    {
        Schema::create('store_user', function (Blueprint $table) {
            $table->id();

            // Toko
            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            // User
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Role user di toko
            $table->string('role')->default('kasir');

            $table->timestamps();

            // Satu user tidak boleh terdaftar dua kali di toko yang sama
            $table->unique(['store_id', 'user_id']);

            $table->index('store_id');
            $table->index('user_id');
        });
    }

    /**
     * Menghapus tabel relasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_user');
    }
};