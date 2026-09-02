<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku');
            $table->string('name');
            $table->integer('added_stock');
            $table->string('status_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};
