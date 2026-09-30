<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            // bank / ewallet
            $table->string('type', 20);

            // BCA, BRI, DANA, GoPay, OVO, dll.
            $table->string('provider', 50);

            // Nama pemilik rekening / akun
            $table->string('account_name', 100);

            // Nomor rekening / nomor HP e-wallet
            $table->string('account_number', 50);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['store_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};