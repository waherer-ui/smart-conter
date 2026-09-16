<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('description')->nullable();

            $table->decimal('amount', 15, 2);

            $table->decimal('paid_amount', 15, 2)
                ->default(0);

            $table->date('debt_date');

            $table->date('due_date')->nullable();

            $table->string('status')
                ->default('unpaid');

            $table->timestamps();

            $table->index([
                'store_id',
                'customer_id',
            ]);

            $table->index([
                'store_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};