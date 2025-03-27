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
            $table->integer('fixed_commission_on_form')->nullable()->after('fixed_commission');
            $table->integer('fixed_commission_on_payment')->nullable()->after('fixed_commission_on_form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_commissions', function (Blueprint $table) {
            $table->dropColumn('fixed_commission_on_form');
            $table->dropColumn('fixed_commission_on_payment');
        });
    }
};
