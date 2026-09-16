<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus unique SKU global
            $table->dropUnique(['sku']);
        });

        Schema::table('products', function (Blueprint $table) {
            // SKU hanya wajib unik di dalam toko yang sama
            $table->unique(
                ['store_id', 'sku'],
                'products_store_id_sku_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_store_id_sku_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            // Kembalikan unique SKU global
            $table->unique('sku');
        });
    }
};