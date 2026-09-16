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
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('store_id')
            ->constrained('stores')
            ->cascadeOnDelete();

        $table->foreignId('plan_id')
            ->constrained('plans')
            ->restrictOnDelete();

        $table->dateTime('starts_at');
        $table->dateTime('ends_at')->nullable();

        $table->string('status')->default('active');

        $table->timestamps();

        $table->index(['store_id', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
