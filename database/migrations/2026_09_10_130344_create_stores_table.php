<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel toko.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            // User pemilik toko
            $table->foreignId('owner_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Informasi toko
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();

            // Menandai toko yang sedang aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index pemilik toko
            $table->index('owner_id');
        });
    }

    /**
     * Menghapus tabel toko.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};