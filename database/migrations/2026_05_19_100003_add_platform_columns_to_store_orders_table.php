<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            // Platform split fields
            $table->enum('payment_mode', ['direct', 'platform'])->default('direct')->after('payment_status');
            $table->decimal('platform_fee_amount', 12, 2)->nullable()->after('payment_mode');
            $table->decimal('affiliate_commission_amount', 12, 2)->nullable()->after('platform_fee_amount');
            $table->decimal('advertiser_net_amount', 12, 2)->nullable()->after('affiliate_commission_amount');

            // Affiliate attribution
            $table->foreignId('affiliate_id')->nullable()->constrained('users')->nullOnDelete()->after('advertiser_net_amount');
            $table->foreignId('affiliate_link_id')->nullable()->constrained('affiliate_links')->nullOnDelete()->after('affiliate_id');
            $table->foreignId('conversion_id')->nullable()->constrained('conversions')->nullOnDelete()->after('affiliate_link_id');

            // Refund flow
            $table->enum('refund_status', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('conversion_id');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_status');
            $table->timestamp('refund_approved_at')->nullable()->after('refund_requested_at');
            $table->text('refund_note')->nullable()->after('refund_approved_at');

            $table->index(['affiliate_id']);
            $table->index(['refund_status']);
        });
    }

    public function down(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            $table->dropForeign(['affiliate_id']);
            $table->dropForeign(['affiliate_link_id']);
            $table->dropForeign(['conversion_id']);
            $table->dropIndex(['affiliate_id']);
            $table->dropIndex(['refund_status']);
            $table->dropColumn([
                'payment_mode', 'platform_fee_amount', 'affiliate_commission_amount',
                'advertiser_net_amount', 'affiliate_id', 'affiliate_link_id', 'conversion_id',
                'refund_status', 'refund_requested_at', 'refund_approved_at', 'refund_note',
            ]);
        });
    }
};
