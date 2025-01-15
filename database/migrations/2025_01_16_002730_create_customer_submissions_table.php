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
        Schema::create('customer_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // 顧客情報との関連
            $table->foreignId('product_id')->constrained(); // 商材ID
            $table->string('affiliate_ref'); // アフィリエイト参照
            $table->string('action'); // アクション（例: 資料請求）
            $table->timestamp('timestamp'); // 送信時刻
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_submissions');
    }
};
