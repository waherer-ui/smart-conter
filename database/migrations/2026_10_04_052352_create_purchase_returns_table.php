<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | STORE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PEMBELIAN ASAL
            |--------------------------------------------------------------------------
            */

            $table->foreignId('purchase_id')
                ->constrained('purchases')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SUPPLIER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | USER YANG MELAKUKAN RETUR
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | NOMOR RETUR
            |--------------------------------------------------------------------------
            */

            $table->string('return_number')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | TANGGAL RETUR
            |--------------------------------------------------------------------------
            */

            $table->date('return_date');

            /*
            |--------------------------------------------------------------------------
            | TOTAL NILAI RETUR
            |--------------------------------------------------------------------------
            */

            $table->decimal('total', 15, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | ALASAN & CATATAN
            |--------------------------------------------------------------------------
            */

            $table->string('reason')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index([
                'store_id',
                'return_date',
            ]);

            $table->index([
                'store_id',
                'supplier_id',
            ]);

            $table->index('purchase_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};