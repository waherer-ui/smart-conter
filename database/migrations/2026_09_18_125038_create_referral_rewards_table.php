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
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();

            /*
             * Referral yang memberikan reward.
             */
            $table->foreignId('referral_id')
                ->constrained('referrals')
                ->cascadeOnDelete();

            /*
             * Payment yang menghasilkan reward.
             * Satu payment hanya boleh menghasilkan
             * satu reward referral.
             */
            $table->foreignId('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete()
                ->unique();

            /*
             * Owner pemilik referral.
             */
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Jumlah bulan masa aktif yang diberikan.
             */
            $table->unsignedInteger('reward_months');

            /*
             * Status reward.
             */
            $table->string('status', 20)
                ->default('granted');

            $table->timestamps();

            /*
             * Mempermudah pencarian histori reward
             * berdasarkan owner dan status.
             */
            $table->index([
                'owner_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};