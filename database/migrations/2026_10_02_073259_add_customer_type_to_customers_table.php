<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_type')
                ->default('umum')
                ->after('name');

            $table->index(['store_id', 'customer_type']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex([
                'store_id',
                'customer_type',
            ]);

            $table->dropColumn('customer_type');
        });
    }
};