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
        Schema::table('product_commissions', function (Blueprint $table) {
            $table->foreignId('affiliate_type_id')->nullable()->constrained('affiliate_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_commissions', function (Blueprint $table) {
            // 外部キーとカラムの削除
            $table->dropForeign(['affiliate_type_id']);
            $table->dropColumn('affiliate_type_id');
        });
    }
};
