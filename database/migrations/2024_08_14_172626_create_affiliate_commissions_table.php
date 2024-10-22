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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // 紹介者のID（リファラー）
            $table->foreignId('affiliate_link_id')->constrained(); // アフィリエイトリンクのID
            $table->foreignId('payer_id')->constrained('users'); // 決済を行ったユーザーのID
            $table->decimal('amount', 10, 2); // 報酬額
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
