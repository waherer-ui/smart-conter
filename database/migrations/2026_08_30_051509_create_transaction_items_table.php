<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat detail barang dari setiap transaksi.
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();

            // Transaksi induk
            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            // Produk yang dijual
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Snapshot data produk saat transaksi
            $table->string('product_name');
            $table->string('sku')->nullable();

            // Snapshot harga modal / HPP saat transaksi
            $table->decimal('capital_price', 12, 2)->default(0);

            // Harga jual saat transaksi
            $table->decimal('price', 12, 2)->default(0);

            // Jumlah barang
            $table->integer('quantity')->default(1);

            // Total harga jual item
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();

            // Index untuk pencarian laporan
            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    /**
     * Menghapus tabel detail transaksi.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};