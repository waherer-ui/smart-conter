<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('debt_id')
                ->constrained('debts')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->date('payment_date');

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index([
                'debt_id',
                'payment_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};