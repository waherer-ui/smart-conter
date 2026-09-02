<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel pengeluaran.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // User yang mencatat pengeluaran
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Kategori pengeluaran
            $table->string('category');

            // Keterangan pengeluaran
            $table->text('description')->nullable();

            // Nominal pengeluaran
            $table->decimal('amount', 12, 2)->default(0);

            // Tanggal pengeluaran
            $table->date('expense_date');

            $table->timestamps();

            // Index untuk mempercepat laporan
            $table->index('user_id');
            $table->index('category');
            $table->index('expense_date');
        });
    }

    /**
     * Menghapus tabel pengeluaran.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};