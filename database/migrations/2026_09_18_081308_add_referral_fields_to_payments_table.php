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

            // Kode referral yang digunakan saat pembayaran
            $table->string('referral_code', 50)
                ->nullable()
                ->after('duration_months');

            // Persentase diskon referral
            $table->unsignedTinyInteger('referral_discount_percent')
                ->default(0)
                ->after('referral_code');

            // Nominal diskon referral
            $table->decimal('referral_discount_amount', 12, 2)
                ->default(0)
                ->after('referral_discount_percent');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropColumn([
                'referral_code',
                'referral_discount_percent',
                'referral_discount_amount',
            ]);

        });
    }
};