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
        Schema::table('offers', function (Blueprint $table) {
            // Conversion caps
            $table->integer('daily_conversion_cap')->nullable()->after('is_featured');
            $table->integer('monthly_conversion_cap')->nullable()->after('daily_conversion_cap');
            $table->integer('total_conversion_cap')->nullable()->after('monthly_conversion_cap');
            
            // Budget limits
            $table->decimal('budget_limit', 12, 2)->nullable()->after('total_conversion_cap');
            $table->decimal('spent_budget', 12, 2)->default(0)->after('budget_limit');
            
            // Cap status tracking
            $table->integer('today_conversions')->default(0)->after('spent_budget');
            $table->integer('month_conversions')->default(0)->after('today_conversions');
            $table->date('last_cap_reset_date')->nullable()->after('month_conversions');
            $table->boolean('auto_pause_on_cap')->default(true)->after('last_cap_reset_date');
            $table->string('pause_reason')->nullable()->after('auto_pause_on_cap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'daily_conversion_cap', 'monthly_conversion_cap', 'total_conversion_cap',
                'budget_limit', 'spent_budget', 'today_conversions', 'month_conversions',
                'last_cap_reset_date', 'auto_pause_on_cap', 'pause_reason'
            ]);
        });
    }
};
