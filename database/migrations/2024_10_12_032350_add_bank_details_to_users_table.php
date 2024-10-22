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
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable(); // 銀行名
            $table->string('branch_name')->nullable(); // 支店名
            $table->string('account_number')->nullable(); // 口座番号
            $table->string('account_type')->nullable(); // 口座の種類（普通預金、当座預金など）
            $table->string('account_holder')->nullable(); // 口座名義
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'branch_name', 'account_number', 'account_type', 'account_holder']);
        });
    }
};
