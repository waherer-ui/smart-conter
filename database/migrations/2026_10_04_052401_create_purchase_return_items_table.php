<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RETUR SUPPLIER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_return_id')
                ->constrained('purchase_returns')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | ITEM PEMBELIAN ASAL
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_item_id')
                ->constrained('purchase_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PRODUK
            |--------------------------------------------------------------------------
            */

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT PRODUK
            |--------------------------------------------------------------------------
            */

            $table->string('product_name');

            $table->string('sku')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | HARGA MODAL SAAT RETUR
            |--------------------------------------------------------------------------
            */

            $table->decimal('capital_price', 15, 2);

            /*
            |--------------------------------------------------------------------------
            | JUMLAH RETUR
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('quantity');

            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL RETUR
            |--------------------------------------------------------------------------
            */

            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('purchase_return_id');
            $table->index('purchase_item_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};