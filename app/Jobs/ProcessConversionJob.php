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
        public string $trackingMethod = 'postback',
        public ?string $customerId = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            // --- Recurring RevShare renewal path ---
            // When a subscription renews, the original click is already converted.
            // We look up attribution from the initial conversion using customer_id.
            if ($this->customerId) {
                $recurringHandled = $this->handleRecurringIfApplicable();
                if ($recurringHandled) {
                    DB::commit();
                    return;
                }
            }

            // --- Normal (initial) conversion path ---
            $click = $this->findClick();

            if (!$click) {
                Log::warning('Conversion tracking failed: Click not found', [
                    'tracking_code' => $this->trackingCode,
                    'transaction_id' => $this->transactionId,
                ]);
                DB::rollBack();
                return;
            }

            // Check if already converted
            if ($click->is_converted) {
                Log::info('Click already converted', ['click_id' => $click->id]);
                DB::rollBack();
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
                DB::rollBack();
                return;
            }

            // Calculate commission based on pricing model
            if ($offer->pricing_model === 'spread') {
                if ($offer->commission_model === 'revshare') {
                    // For revshare + spread, affiliate_payout is a percentage rate (e.g. 30 = 30%)
                    $commissionAmount = ($this->conversionValue * ($offer->affiliate_payout ?? 0)) / 100;
                } else {
                    // For fixed models (pps/ppl), affiliate_payout is a currency amount
                    $commissionAmount = $offer->affiliate_payout ?? 0;
                }
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
                if ($offer->commission_model === 'revshare') {
                    // For revshare, advertiser_payout is a percentage rate (e.g. 33 = 33%)
                    $advertiserPayout = ($this->conversionValue * ($offer->advertiser_payout ?? 0)) / 100;
                } else {
                    $advertiserPayout = $offer->advertiser_payout;
                }
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
                'customer_id' => $this->customerId,
                'recurring_occurrence' => 1,
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
            // For spread model, we deduct the advertiser_payout. For flat_fee, we deduct the base commissionAmount.
            $payoutToDeduct = $advertiserPayout ?? $commissionAmount;
            $capService->incrementConversion($offer, $payoutToDeduct);

            // Deduct from advertiser balance if it's a native offer and not the placeholder admin account
            if ($offer->advertiser_id && $payoutToDeduct > 0) {
                DB::table('users')
                    ->where('id', $offer->advertiser_id)
                    ->decrement('advertiser_balance', $payoutToDeduct);
            }

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

    /**
     * Handle a recurring RevShare renewal.
     *
     * Called when customer_id is present. Looks up the original conversion for
     * this customer on this offer and creates a linked renewal conversion without
     * requiring a new click (the customer is not re-clicking an affiliate link).
     *
     * @return bool true if the renewal was processed (caller should commit), false if not applicable
     */
    protected function handleRecurringIfApplicable(): bool
    {
        $affiliateLink = AffiliateLink::where('tracking_code', $this->trackingCode)
            ->with('offer')
            ->first();

        if (!$affiliateLink) {
            return false;
        }

        $offer = $affiliateLink->offer;

        // Only applies to recurring RevShare offers
        if ($offer->commission_model !== 'revshare' || $offer->revshare_type !== 'recurring') {
            return false;
        }

        // Find the original (occurrence #1) conversion for this customer
        $originalConversion = Conversion::where('offer_id', $offer->id)
            ->where('customer_id', $this->customerId)
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->orderBy('id')
            ->first();

        // No previous conversion → fall through to normal initial-sale flow
        if (!$originalConversion) {
            return false;
        }

        // Count existing occurrences (already checked cap in TrackingController, but guard here too)
        $occurrenceCount = Conversion::where('offer_id', $offer->id)
            ->where('customer_id', $this->customerId)
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->count();

        if ($offer->revshare_recurring_duration !== null && $occurrenceCount >= $offer->revshare_recurring_duration) {
            Log::info('ProcessConversionJob: recurring duration cap reached', [
                'offer_id'    => $offer->id,
                'customer_id' => $this->customerId,
            ]);
            return true; // handled (by rejection) — do not fall through to normal path
        }

        // Re-use the original affiliate's link for attribution
        $renewalAffiliateLink = $originalConversion->affiliateLink ?? $affiliateLink;
        $affiliate = $renewalAffiliateLink->affiliate;

        // Commission calculation
        if ($offer->pricing_model === 'spread') {
            $commissionAmount = ($this->conversionValue * ($offer->affiliate_payout ?? 0)) / 100;
            $advertiserPayout = ($this->conversionValue * ($offer->advertiser_payout ?? 0)) / 100;
            $platformMargin   = $advertiserPayout - $commissionAmount;
        } else {
            $commissionAmount = ($this->conversionValue * $offer->commission_rate) / 100;
            $advertiserPayout = null;
            $platformMargin   = 0;
        }

        // Tier bonus
        $tierBonus = $affiliate->tier_commission_bonus ?? 0;
        if ($tierBonus > 0) {
            $commissionAmount += ($commissionAmount * $tierBonus) / 100;
        }

        $autoApprove     = Cache::get('settings.auto_approve_conversions', false);
        $conversionStatus = $autoApprove ? 'approved' : 'pending';

        $renewalConversion = Conversion::create([
            'click_id'              => $originalConversion->click_id,
            'affiliate_id'          => $originalConversion->affiliate_id,
            'offer_id'              => $offer->id,
            'affiliate_link_id'     => $originalConversion->affiliate_link_id,
            'transaction_id'        => $this->transactionId ?? Str::uuid(),
            'customer_id'           => $this->customerId,
            'recurring_occurrence'  => $occurrenceCount + 1,
            'parent_conversion_id'  => $originalConversion->id,
            'conversion_value'      => $this->conversionValue,
            'advertiser_payout'     => $advertiserPayout,
            'platform_margin'       => $platformMargin,
            'commission_amount'     => $commissionAmount,
            'commission_model'      => $offer->commission_model,
            'status'                => $conversionStatus,
            'tracking_method'       => $this->trackingMethod,
            'postback_data'         => $this->postbackData ? json_encode($this->postbackData) : null,
        ]);

        Commission::create([
            'affiliate_id'  => $originalConversion->affiliate_id,
            'conversion_id' => $renewalConversion->id,
            'offer_id'      => $offer->id,
            'amount'        => $commissionAmount,
            'status'        => $conversionStatus,
        ]);

        // Update stats
        $renewalAffiliateLink->increment('conversion_count');
        $renewalAffiliateLink->increment('total_earnings', $commissionAmount);
        $offer->increment('total_conversions');
        $offer->increment('total_revenue', $this->conversionValue);

        if ($autoApprove) {
            DB::table('users')->where('id', $originalConversion->affiliate_id)->increment('balance', $commissionAmount);
        } else {
            DB::table('users')->where('id', $originalConversion->affiliate_id)->increment('pending_balance', $commissionAmount);
        }

        $affiliate->increment('lifetime_earnings', $commissionAmount);
        $affiliate->increment('total_conversions');

        $affiliate->notify(new NewConversionNotification($renewalConversion, 'affiliate'));

        Log::info('Recurring RevShare renewal processed', [
            'offer_id'             => $offer->id,
            'customer_id'          => $this->customerId,
            'recurring_occurrence' => $occurrenceCount + 1,
            'commission'           => $commissionAmount,
        ]);

        return true;
    }
}

