<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('type');
            // reseller / promotion

            $table->unsignedInteger('min_quantity')
                ->nullable();

            $table->string('discount_type')
                ->nullable();
            // nominal / percent

            $table->decimal('discount_value', 12, 2)
                ->nullable();

            $table->decimal('special_price', 12, 2)
                ->nullable();

            $table->dateTime('start_at')
                ->nullable();

            $table->dateTime('end_at')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'store_id',
                'type',
                'is_active'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_rules');
    }
};