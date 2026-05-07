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
            // Affiliate tier system
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('is_active');
            $table->decimal('tier_commission_bonus', 5, 2)->default(0)->after('tier'); // Percentage bonus (0-100)
            $table->integer('total_clicks')->default(0)->after('tier_commission_bonus');
            $table->integer('total_conversions')->default(0)->after('total_clicks');
            $table->decimal('conversion_rate', 5, 2)->default(0)->after('total_conversions'); // Percentage
            $table->timestamp('tier_updated_at')->nullable()->after('conversion_rate');
            
            // Sub-affiliate referral system
            $table->foreignId('parent_affiliate_id')->nullable()->constrained('users')->onDelete('set null')->after('tier_updated_at');
            $table->string('referral_code')->unique()->nullable()->after('parent_affiliate_id');
            $table->integer('referral_count')->default(0)->after('referral_code');
            $table->decimal('referral_earnings', 12, 2)->default(0)->after('referral_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_affiliate_id']);
            $table->dropColumn([
                'tier', 'tier_commission_bonus', 'total_clicks', 'total_conversions',
                'conversion_rate', 'tier_updated_at', 'parent_affiliate_id',
                'referral_code', 'referral_count', 'referral_earnings'
            ]);
        });
    }
};
