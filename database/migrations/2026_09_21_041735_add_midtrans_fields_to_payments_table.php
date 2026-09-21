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
        Schema::table('payments', function (Blueprint $table) {

            $table->string('midtrans_order_id')
                ->nullable()
                ->unique()
                ->after('reference');

            $table->text('midtrans_snap_token')
                ->nullable()
                ->after('midtrans_order_id');

            $table->string('midtrans_transaction_status')
                ->nullable()
                ->after('midtrans_snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique([
                'midtrans_order_id'
            ]);

            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_transaction_status',
            ]);
        });
    }
};