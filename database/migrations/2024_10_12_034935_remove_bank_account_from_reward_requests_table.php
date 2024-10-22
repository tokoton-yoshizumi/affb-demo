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
        Schema::table('reward_requests', function (Blueprint $table) {
            $table->dropColumn('bank_account'); // bank_accountカラムを削除

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_requests', function (Blueprint $table) {
            $table->string('bank_account')->nullable(); // 逆に戻す場合の処理

        });
    }
};
