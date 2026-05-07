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
        Schema::table('conversions', function (Blueprint $table) {
            // Track what advertiser pays for this conversion (for spread model)
            $table->decimal('advertiser_payout', 12, 2)->nullable()->after('conversion_value');

            // Track platform margin/profit for this conversion
            $table->decimal('platform_margin', 12, 2)->default(0)->after('advertiser_payout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            $table->dropColumn(['advertiser_payout', 'platform_margin']);
        });
    }
};
