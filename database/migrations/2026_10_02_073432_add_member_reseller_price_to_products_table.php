<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('member_price', 12, 2)
                ->nullable()
                ->after('price');

            $table->decimal('reseller_price', 12, 2)
                ->nullable()
                ->after('member_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'member_price',
                'reseller_price',
            ]);
        });
    }
};