<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\Commission;
use App\Models\Conversion;
use App\Models\StoreSaleTransaction;
use App\Models\StoreOrder;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StorePaymentSplitService
{
    /**
     * Settle a platform-managed store order after payment verification.
     *
     * 1. Calculate platform fee from plan's platform_fee_percentage.
     * 2. If affiliate link is present, calculate and credit affiliate commission.
     * 3. Credit the remainder to the advertiser's wallet.
     * 4. Create a StoreSaleTransaction audit record.
     * 5. Lock the store's payment mode (has_orders = true).
     * 6. Auto-approve the conversion if attributed.
     */
    public function settle(StoreOrder $order, ?AffiliateLink $affiliateLink): void
    {
        $store = $order->store()->with('plan')->first();
        $advertiser = $store->user;

        // --- Amounts ---
        // `order->total` is already net of discounts (subtotal + shipping - discount_amount).
        // We record the discount separately in the audit trail but the fee is on the final total.
        $grossAmount    = (float) $order->total;
        $discountAmount = (float) ($order->discount_amount ?? 0.0);
        $netAmount      = $grossAmount; // total is already the payable net amount

        $feePct = (float) ($store->plan->platform_fee_percentage ?? 0);
        $platformFee = round($netAmount * ($feePct / 100), 2);

        // Affiliate commission (only for platform mode with an attributed link)
        $affiliateCommission = 0.0;
        $affiliateId = null;
        $affiliateLinkId = null;
        $conversionId = null;

        if ($affiliateLink) {
            $affiliateId = $affiliateLink->affiliate_id;
            $affiliateLinkId = $affiliateLink->id;

            // The affiliate link belongs to an Offer — use its commission settings
            $offer = $affiliateLink->offer;
            if ($offer) {
                if ($offer->commission_model === 'percentage') {
                    $affiliateCommission = round($netAmount * ($offer->affiliate_payout / 100), 2);
                } else {
                    // Fixed CPS payout
                    $affiliateCommission = (float) $offer->affiliate_payout;
                }
                // Cap commission so it never exceeds net
                $affiliateCommission = min($affiliateCommission, $netAmount - $platformFee);
            }
        }

        $advertiserNet = $netAmount - $platformFee - $affiliateCommission;
        if ($advertiserNet < 0) {
            Log::warning('StorePaymentSplitService: advertiser net is negative, flooring to 0', [
                'order_id'        => $order->id,
                'net_amount'      => $netAmount,
                'platform_fee'    => $platformFee,
                'affiliate_comm'  => $affiliateCommission,
                'advertiser_net'  => $advertiserNet,
            ]);
        }
        $advertiserNet = max(0, round($advertiserNet, 2));

        DB::transaction(function () use (
            $order, $store, $advertiser, $affiliateLink,
            $grossAmount, $discountAmount, $netAmount,
            $platformFee, $affiliateCommission, $advertiserNet,
            $affiliateId, $affiliateLinkId, &$conversionId
        ) {
            // 1. Update order split amounts
            $order->update([
                'platform_fee_amount'          => $platformFee,
                'affiliate_commission_amount'  => $affiliateCommission,
                'advertiser_net_amount'        => $advertiserNet,
                'affiliate_id'                 => $affiliateId,
                'affiliate_link_id'            => $affiliateLinkId,
            ]);

            // 2. Credit advertiser wallet
            $advertiser->increment('advertiser_balance', $advertiserNet);

            WalletTransaction::create([
                'user_id'     => $advertiser->id,
                'amount'      => $advertiserNet,
                'type'        => 'store_sale',
                'status'      => 'success',
                'reference'   => 'SALE-' . strtoupper(Str::random(12)),
                'description' => 'Store sale: order ' . $order->order_number,
                'metadata'    => [
                    'order_id'         => $order->id,
                    'store_id'         => $store->id,
                    'gross_amount'     => $grossAmount,
                    'platform_fee'     => $platformFee,
                    'affiliate_commission' => $affiliateCommission,
                ],
            ]);

            // 3. Credit affiliate if applicable
            if ($affiliateId && $affiliateCommission > 0) {
                $affiliateUser = \App\Models\User::find($affiliateId);
                if ($affiliateUser) {
                    $affiliateUser->increment('balance', $affiliateCommission);

                    // Create conversion record
                    $conversion = Conversion::create([
                        'affiliate_id'      => $affiliateId,
                        'offer_id'          => $affiliateLink->offer_id,
                        'affiliate_link_id' => $affiliateLinkId,
                        'transaction_id'    => $order->order_number,
                        'conversion_value'  => $netAmount,
                        'conversion_amount' => $affiliateCommission,
                        'advertiser_payout' => $advertiserNet,
                        'platform_margin'   => $platformFee,
                        'commission_amount' => $affiliateCommission,
                        'commission_model'  => $affiliateLink->offer->commission_model ?? 'fixed',
                        'status'            => 'approved', // Auto-approve store purchases
                        'tracking_method'   => 'cookie',
                        'approved_at'       => now(),
                    ]);

                    $conversionId = $conversion->id;
                    $order->update(['conversion_id' => $conversionId]);

                    // Create a Commission record so the affiliate dashboard earnings
                    // breakdown (which reads from the commissions table) reflects this
                    // store-sale payment correctly.
                    Commission::create([
                        'affiliate_id'  => $affiliateId,
                        'conversion_id' => $conversionId,
                        'offer_id'      => $affiliateLink->offer_id,
                        'amount'        => $affiliateCommission,
                        'status'        => 'approved',
                        'approved_at'   => now(),
                    ]);

                    // Update affiliate link stats
                    $affiliateLink->increment('conversion_count');
                    $affiliateLink->increment('total_earnings', $affiliateCommission);
                }
            }

            // 4. Create audit record
            StoreSaleTransaction::create([
                'order_id'                   => $order->id,
                'store_id'                   => $store->id,
                'advertiser_id'              => $advertiser->id,
                'affiliate_id'               => $affiliateId,
                'gross_amount'               => $grossAmount,
                'discount_amount'            => $discountAmount,
                'net_amount'                 => $netAmount,
                'platform_fee_amount'        => $platformFee,
                'affiliate_commission_amount' => $affiliateCommission,
                'advertiser_net_amount'      => $advertiserNet,
                'platform_reference'         => $order->payment_reference,
            ]);

            // 5. Lock store payment mode (has_orders = true) so mode can't be changed
            if (!$store->has_orders) {
                $store->update(['has_orders' => true]);
            }
        });

        Log::info('StorePaymentSplitService: settled', [
            'order_id'        => $order->id,
            'advertiser_net'  => $advertiserNet,
            'platform_fee'    => $platformFee,
            'affiliate_comm'  => $affiliateCommission,
        ]);
    }
}

