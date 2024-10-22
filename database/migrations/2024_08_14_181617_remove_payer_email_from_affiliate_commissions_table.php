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
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            // まず外部キー制約を削除
            $table->dropForeign(['payer_id']);
            // 次にカラムを削除
            $table->dropColumn('payer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            // カラムと外部キー制約を元に戻す処理
            $table->string('payer_email')->nullable();
            $table->foreign('payer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
