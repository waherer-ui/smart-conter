<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Toko tempat staf melakukan absensi
            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            // Staf yang melakukan absensi
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tanggal absensi
            $table->date('date');

            // Waktu masuk dan keluar
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();

            // Lokasi GPS saat check-in
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Foto selfie saat check-in
            $table->string('photo')->nullable();

            // Status absensi
            $table->string('status')->default('hadir');

            // Catatan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu staf hanya punya satu absensi per toko per hari
            $table->unique(['store_id', 'user_id', 'date']);

            $table->index(['store_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};