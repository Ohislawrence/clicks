<?php

namespace App\Jobs;

use App\Models\AffiliateLink;
use App\Models\Click;
use App\Models\Commission;
use App\Models\Conversion;
use App\Notifications\NewConversionNotification;
use App\Services\OfferCapService;
use App\Services\TierService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $trackingCode,
        public ?string $transactionId,
        public float $conversionValue,
        public ?array $postbackData = null,
        public string $ipAddress = '',
        public string $trackingMethod = 'postback'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $click = $this->findClick();

            if (!$click) {
                Log::warning('Conversion tracking failed: Click not found', [
                    'tracking_code' => $this->trackingCode,
                    'transaction_id' => $this->transactionId,
                ]);
                return;
            }

            // Check if already converted
            if ($click->is_converted) {
                Log::info('Click already converted', ['click_id' => $click->id]);
                return;
            }

            $affiliateLink = $click->affiliateLink;
            $offer = $affiliateLink->offer;
            $affiliate = $affiliateLink->affiliate;

            // Check if offer has reached caps
            $capService = app(OfferCapService::class);
            if ($capService->hasReachedCap($offer)) {
                Log::warning('Conversion rejected: Offer has reached cap', [
                    'offer_id' => $offer->id,
                    'tracking_code' => $this->trackingCode,
                ]);
                return;
            }

            // Calculate commission based on pricing model
            if ($offer->pricing_model === 'spread') {
                // Spread model: Use affiliate payout directly (platform margin already built in)
                $commissionAmount = $offer->affiliate_payout ?? 0;
            } else {
                // Legacy flat_fee model: Calculate from commission rate
                $commissionAmount = $this->calculateCommission(
                    $offer->commission_model,
                    $offer->commission_rate,
                    $this->conversionValue
                );
            }

            // Apply tier bonus for affiliate
            $tierBonus = $affiliate->tier_commission_bonus ?? 0;
            if ($tierBonus > 0) {
                $bonusAmount = ($commissionAmount * $tierBonus) / 100;
                $commissionAmount += $bonusAmount;
            }

            // Apply platform fee only for flat_fee model (spread model has margin built in)
            if ($offer->pricing_model === 'flat_fee') {
                $platformFee = Cache::get('settings.platform_fee_percentage', 0);
                $platformFeeAmount = ($commissionAmount * $platformFee) / 100;
                $finalCommission = $commissionAmount - $platformFeeAmount;
            } else {
                $finalCommission = $commissionAmount;
            }

            // Apply commission cap if configured
            $commissionCap = Cache::get('settings.commission_cap', 0);
            if ($commissionCap > 0 && $finalCommission > $commissionCap) {
                $finalCommission = $commissionCap;
            }

            // Check auto-approval settings
            $autoApprove = Cache::get('settings.auto_approve_conversions', false);
            $conversionStatus = $autoApprove ? 'approved' : 'pending';

            // Calculate advertiser payout and platform margin
            $advertiserPayout = null;
            $platformMargin = 0;

            if ($offer->pricing_model === 'spread') {
                $advertiserPayout = $offer->advertiser_payout;
                // Platform margin = advertiser pays - affiliate receives
                $platformMargin = $advertiserPayout - $finalCommission;
            }

            // Create conversion
            $conversion = Conversion::create([
                'click_id' => $click->id,
                'affiliate_id' => $affiliateLink->affiliate_id,
                'offer_id' => $offer->id,
                'affiliate_link_id' => $affiliateLink->id,
                'transaction_id' => $this->transactionId ?? Str::uuid(),
                'conversion_value' => $this->conversionValue,
                'advertiser_payout' => $advertiserPayout,
                'platform_margin' => $platformMargin,
                'commission_amount' => $finalCommission,
                'commission_model' => $offer->commission_model,
                'status' => $conversionStatus,
                'tracking_method' => $this->trackingMethod,
                'postback_data' => $this->postbackData ? json_encode($this->postbackData) : null,
            ]);

            // Create commission
            $commission = Commission::create([
                'affiliate_id' => $affiliateLink->affiliate_id,
                'conversion_id' => $conversion->id,
                'offer_id' => $offer->id,
                'amount' => $finalCommission,
                'status' => $conversionStatus,
            ]);

            // Mark click as converted
            $click->update([
                'is_converted' => true,
                'converted_at' => now(),
            ]);

            // Update link stats
            $affiliateLink->increment('conversion_count');
            $affiliateLink->increment('total_earnings', $finalCommission);

            // Update offer stats and caps
            $offer->increment('total_conversions');
            $offer->increment('total_revenue', $this->conversionValue);

            // Increment cap counters and check auto-pause
            $capService->incrementConversion($offer, $finalCommission);

            // Update affiliate balance
            if ($autoApprove) {
                DB::table('users')
                    ->where('id', $affiliateLink->affiliate_id)
                    ->increment('balance', $finalCommission);
            } else {
                DB::table('users')
                    ->where('id', $affiliateLink->affiliate_id)
                    ->increment('pending_balance', $finalCommission);
            }

            // Update affiliate lifetime stats
            $affiliate->increment('lifetime_earnings', $finalCommission);
            $affiliate->increment('total_conversions');
            $affiliate->increment('total_clicks', 1); // Assuming each conversion has 1 click

            // Recalculate conversion rate
            if ($affiliate->total_clicks > 0) {
                $affiliate->conversion_rate = ($affiliate->total_conversions / $affiliate->total_clicks) * 100;
                $affiliate->save();
            }

            // Check and update affiliate tier
            $tierService = app(TierService::class);
            $tierService->updateAffiliateTier($affiliate);

            // Process sub-affiliate referral commission with cap enforcement
            if ($affiliate->parent_affiliate_id) {
                $parentAffiliate = $affiliate->parentAffiliate;
                if ($parentAffiliate) {
                    $tierService->processReferral($affiliate, $parentAffiliate, $finalCommission, $commission->id);
                }
            }

            DB::commit();

            // Send notifications
            // Notify affiliate about new conversion
            $affiliate->notify(new NewConversionNotification($conversion, 'affiliate'));

            // Notify advertiser about new conversion
            $advertiser = $offer->advertiser;
            if ($advertiser) {
                $advertiser->notify(new NewConversionNotification($conversion, 'advertiser'));
            }

            // Dispatch postback to advertiser if configured
            if ($offer->postback_url) {
                SendPostbackJob::dispatch(
                    $offer->postback_url,
                    $conversion,
                    $click
                )->delay(now()->addSeconds(5));
            }

            Log::info('Conversion processed successfully', [
                'conversion_id' => $conversion->id,
                'click_id' => $click->id,
                'commission' => $finalCommission,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Conversion processing failed', [
                'error' => $e->getMessage(),
                'tracking_code' => $this->trackingCode,
            ]);
            throw $e;
        }
    }

    /**
     * Find the click record
     */
    protected function findClick(): ?Click
    {
        // Try cache first
        $cacheKey = "click:{$this->trackingCode}:{$this->ipAddress}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData && isset($cachedData['click_id'])) {
            $click = Click::find($cachedData['click_id']);
            if ($click && !$click->is_converted) {
                return $click;
            }
        }

        // Try finding by tracking code
        $affiliateLink = AffiliateLink::where('tracking_code', $this->trackingCode)->first();

        if (!$affiliateLink) {
            return null;
        }

        // Find most recent valid unconverted click
        return Click::where('affiliate_link_id', $affiliateLink->id)
            ->where('is_valid', true)
            ->where('is_converted', false)
            ->latest()
            ->first();
    }

    /**
     * Calculate commission based on model
     */
    protected function calculateCommission(string $model, float $rate, float $value): float
    {
        return match ($model) {
            'pps' => $rate, // Pay Per Sale - fixed amount
            'ppl' => $rate, // Pay Per Lead - fixed amount
            'revshare' => ($value * $rate) / 100, // Revenue Share - percentage
            default => 0,
        };
    }
}
