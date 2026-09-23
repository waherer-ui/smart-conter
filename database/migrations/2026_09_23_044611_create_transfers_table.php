<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('source_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('destination_store_id')
                ->constrained('stores')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('transfer_date');

            $table->string('status', 30)
                ->default('pending');

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index('source_store_id');
            $table->index('destination_store_id');
            $table->index('user_id');
            $table->index('transfer_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};