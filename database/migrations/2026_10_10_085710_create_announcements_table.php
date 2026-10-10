<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->longText('content');

            // Target penerima pengumuman.
            $table->string('target_audience', 30)->default('all');

            // draft, published, archived.
            $table->string('status', 20)->default('draft');

            $table->timestamp('published_at')->nullable();

            // Pembuat pengumuman dari akun Super Admin.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('target_audience');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};