<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel transaksi penjualan.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Nomor invoice unik
            $table->string('invoice_number')->unique();

            // Kasir yang melakukan transaksi
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Nilai transaksi
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Pembayaran
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('change', 12, 2)->default(0);

            // Tunai / QRIS / Debit Card
            $table->string('payment_method');

            $table->timestamps();

            // Mempercepat pencarian laporan berdasarkan kasir dan tanggal
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Menghapus tabel transaksi.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};