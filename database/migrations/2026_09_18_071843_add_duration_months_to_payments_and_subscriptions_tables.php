<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('duration_months')
                ->default(1)
                ->after('plan_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('duration_months')
                ->default(1)
                ->after('plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('duration_months');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('duration_months');
        });
    }
};