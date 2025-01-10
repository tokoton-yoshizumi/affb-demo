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
            // nullable に変更
            $table->text('description')->nullable()->change();
            $table->integer('price')->nullable()->change();
            $table->string('url')->nullable()->change();

            // 不要なカラムの削除
            $table->dropColumn('commission_rate');
            $table->dropColumn('fixed_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // nullable 解除
            $table->text('description')->nullable(false)->change();
            $table->integer('price')->nullable(false)->change();
            $table->string('url')->nullable(false)->change();

            // 削除したカラムを元に戻す
            $table->decimal('commission_rate', 5, 2)->nullable()->after('price');
            $table->integer('fixed_commission')->nullable()->after('commission_rate');
        });
    }
};
