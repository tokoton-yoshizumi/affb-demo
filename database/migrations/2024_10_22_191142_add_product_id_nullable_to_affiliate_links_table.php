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
        Schema::table('affiliate_links', function (Blueprint $table) {
            // product_idをnullableで追加し、外部キー制約を設定
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_links', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // 外部キー制約の削除
            $table->dropColumn('product_id');    // カラムの削除
        });
    }
};
