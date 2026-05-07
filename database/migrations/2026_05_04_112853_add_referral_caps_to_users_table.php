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
            // Referral commission cap configuration
            $table->enum('referral_cap_type', ['unlimited', 'amount', 'time', 'both'])->default('unlimited')->after('referral_earnings');

            // Maximum amount cap (e.g., ₦1,000,000)
            $table->decimal('referral_cap_amount', 12, 2)->nullable()->after('referral_cap_type');

            // Maximum duration cap in months (e.g., 12 months)
            $table->integer('referral_cap_months')->nullable()->after('referral_cap_amount');

            // When the first referral commission was earned
            $table->timestamp('referral_started_at')->nullable()->after('referral_cap_months');

            // When the cap was reached
            $table->timestamp('referral_cap_reached_at')->nullable()->after('referral_started_at');

            // Add index for performance
            $table->index(['referral_cap_type', 'referral_cap_reached_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['referral_cap_type', 'referral_cap_reached_at']);
            $table->dropColumn([
                'referral_cap_type',
                'referral_cap_amount',
                'referral_cap_months',
                'referral_started_at',
                'referral_cap_reached_at'
            ]);
        });
    }
};
