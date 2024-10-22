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
            $table->decimal('amount', 10, 0)->change(); // amountカラムをdecimal(10, 0)に変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change(); // 元に戻す場合はdecimal(10, 2)に変更
        });
    }
};
