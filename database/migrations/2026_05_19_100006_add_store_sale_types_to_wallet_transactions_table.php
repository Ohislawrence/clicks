<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add store_sale (credit to advertiser wallet from sale)
        // store_withdrawal (debit when advertiser requests payout)
        // store_refund_clawback (debit when affiliate commission is reversed on refund)
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
            'deposit',
            'offer_allocation',
            'offer_refund',
            'offer_topup',
            'store_sale',
            'store_withdrawal',
            'store_refund_clawback'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
            'deposit',
            'offer_allocation',
            'offer_refund',
            'offer_topup'
        ) NOT NULL");
    }
};
