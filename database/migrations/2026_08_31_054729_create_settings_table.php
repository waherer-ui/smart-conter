<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel settings.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Informasi toko
            $table->string('store_name')->default('Smart POS');
            $table->text('store_address')->nullable();
            $table->string('store_phone', 30)->nullable();
            $table->string('store_email')->nullable();

            // Pengaturan transaksi
            $table->string('currency', 10)->default('IDR');
            $table->boolean('allow_discount')->default(true);
            $table->decimal('max_discount', 5, 2)->default(100);

            // Pengaturan stok
            $table->unsignedInteger('minimum_stock')->default(5);

            // Pengaturan struk
            $table->text('receipt_footer')->nullable();
            $table->boolean('show_cashier')->default(true);
            $table->boolean('show_payment_method')->default(true);
            $table->boolean('show_discount')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel settings.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};