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
            // Pricing model: 'flat_fee' (legacy) or 'spread' (new model)
            $table->enum('pricing_model', ['flat_fee', 'spread'])->default('flat_fee')->after('commission_rate');

            // Advertiser payout: What the advertiser pays per conversion
            $table->decimal('advertiser_payout', 12, 2)->nullable()->after('pricing_model');

            // Affiliate payout: What the affiliate receives per conversion
            $table->decimal('affiliate_payout', 12, 2)->nullable()->after('advertiser_payout');

            // Add index for filtering by pricing model
            $table->index('pricing_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['pricing_model']);
            $table->dropColumn(['pricing_model', 'advertiser_payout', 'affiliate_payout']);
        });
    }
};
