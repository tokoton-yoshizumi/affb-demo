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
        Schema::table('customer_submissions', function (Blueprint $table) {
            $table->json('other_data')->nullable();  // JSON型のカラムを追加

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_submissions', function (Blueprint $table) {
            $table->dropColumn('other_data');  // 追加したカラムを削除

        });
    }
};
