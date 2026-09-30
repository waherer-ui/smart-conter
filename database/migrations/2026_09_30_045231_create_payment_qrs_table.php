<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_qrs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            // Nama QR, misalnya: QRIS Toko
            $table->string('name', 100);

            // Lokasi file QR di storage
            $table->string('image_path');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['store_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_qrs');
    }
};