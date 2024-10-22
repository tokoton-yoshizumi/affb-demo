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
        Schema::table('affiliate_programs', function (Blueprint $table) {
            $table->decimal('commission_rate', 8, 2)->nullable()->change(); // commission_rateをNULL許容に変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_programs', function (Blueprint $table) {
            $table->decimal('commission_rate', 8, 2)->nullable(false)->change(); // 元に戻す場合はNULL不可に戻す
        });
    }
};
