<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // OWNER YANG MELAKUKAN PEMBAYARAN
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // PAKET YANG DIPILIH
            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            // NOMINAL PEMBAYARAN
            $table->decimal('amount', 12, 2);

            // METODE PEMBAYARAN
            $table->string('payment_method')->nullable();

            // STATUS PEMBAYARAN
            // pending, paid, failed, expired
            $table->string('status')->default('pending');

            // NOMOR INVOICE / REFERENSI TRANSAKSI
            $table->string('reference')->unique();

            // WAKTU PEMBAYARAN BERHASIL
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->index(['owner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};