<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            // OWNER YANG MEMBUAT TIKET
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // TOKO YANG TERKAIT DENGAN MASALAH
            $table->foreignId('store_id')
                ->nullable()
                ->constrained('stores')
                ->nullOnDelete();

            // ADMIN YANG MENANGANI TIKET
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // NOMOR TIKET
            $table->string('ticket_number')->unique();

            // JUDUL MASALAH
            $table->string('subject');

            // KATEGORI MASALAH
            $table->string('category')->default('lainnya');

            // PRIORITAS
            // low, normal, high, urgent
            $table->string('priority')->default('normal');

            // STATUS TIKET
            // open, processing, waiting, resolved, closed
            $table->string('status')->default('open');

            // DETAIL MASALAH
            $table->text('description');

            // WAKTU TIKET DISELESAIKAN
            $table->dateTime('closed_at')->nullable();

            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};