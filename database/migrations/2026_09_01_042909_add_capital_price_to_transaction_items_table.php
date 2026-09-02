<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan snapshot harga modal / HPP.
     */
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->decimal('capital_price', 12, 2)
                ->default(0)
                ->after('sku');
        });
    }

    /**
     * Menghapus kolom harga modal / HPP.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn('capital_price');
        });
    }
};