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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // 顧客の名前
            $table->string('email')->unique();  // 顧客のメールアドレス
            $table->string('phone')->nullable();  // 顧客の電話番号
            $table->foreignId('affiliate_link_id')->constrained()->onDelete('cascade');  // アフィリエイトリンクとの関連
            $table->timestamps();  // 作成日時と更新日時
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
