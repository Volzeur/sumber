<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_posts', function (Blueprint $table) {
            $table->unsignedInteger('repayment_period')->default(1)->after('collateral_description');
            $table->enum('repayment_unit', ['days', 'weeks', 'months', 'years'])->default('months')->after('repayment_period');
        });
    }

    public function down(): void
    {
        Schema::table('loan_posts', function (Blueprint $table) {
            $table->dropColumn(['repayment_period', 'repayment_unit']);
        });
    }
};