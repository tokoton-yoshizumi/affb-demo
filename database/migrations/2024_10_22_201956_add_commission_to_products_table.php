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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('price'); // 購入金額に対する報酬率
            $table->integer('fixed_commission')->nullable()->after('commission_rate'); // 固定の報酬額（整数）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
            $table->dropColumn('fixed_commission');
        });
    }
};
