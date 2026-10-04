<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_histories', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('store_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('description')
                ->nullable()
                ->after('status_type');
        });
    }

    public function down(): void
    {
        Schema::table('product_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'user_id',
                'description',
            ]);
        });
    }
};