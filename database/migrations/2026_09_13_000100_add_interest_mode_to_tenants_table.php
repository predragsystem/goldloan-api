<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // flat_monthly: a full month's interest is charged for any part of a
            // month elapsed (minimum 1 month) — the common pawnbroker convention.
            // daily_proportional: interest accrues day-by-day (monthly rate ÷ 30 × days).
            $table->string('interest_calculation_mode')->default('flat_monthly')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('interest_calculation_mode');
        });
    }
};
