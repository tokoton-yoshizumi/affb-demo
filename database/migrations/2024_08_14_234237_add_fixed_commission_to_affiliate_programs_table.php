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
            $table->integer('fixed_commission')->nullable()->after('commission_rate'); // 固定報酬額を保存
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_programs', function (Blueprint $table) {
            $table->integer('fixed_commission')->nullable()->after('commission_rate'); // 固定報酬額を保存
        });
    }
};
