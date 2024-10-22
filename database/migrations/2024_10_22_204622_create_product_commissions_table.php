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
        Schema::create('product_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // 商材ID
            $table->enum('affiliate_type', ['general', 'partner', 'agent']); // アフィリエイタータイプ
            $table->integer('fixed_commission')->nullable(); // 固定報酬
            $table->decimal('commission_rate', 5, 2)->nullable(); // パーセンテージ報酬
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_commissions');
    }
};
