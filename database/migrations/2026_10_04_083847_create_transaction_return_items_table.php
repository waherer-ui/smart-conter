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
        Schema::create('transaction_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_return_id')
                ->constrained('transaction_returns')
                ->cascadeOnDelete();

            $table->foreignId('transaction_item_id')
                ->constrained('transaction_items')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('product_name');

            $table->string('sku')->nullable();

            $table->decimal('price', 15, 2)->default(0);

            $table->unsignedInteger('quantity');

            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_return_items');
    }
};