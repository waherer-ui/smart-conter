<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->time('work_start_time')
                ->default('08:00:00')
                ->after('attendance_radius');

            $table->time('work_end_time')
                ->default('17:00:00')
                ->after('work_start_time');

            $table->unsignedInteger('late_tolerance')
                ->default(15)
                ->after('work_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'work_start_time',
                'work_end_time',
                'late_tolerance',
            ]);
        });
    }
};