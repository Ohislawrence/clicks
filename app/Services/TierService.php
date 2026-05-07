<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralEarning;
use App\Notifications\TierUpgradedNotification;
use App\Notifications\ReferralEarnedNotification;
use App\Notifications\ReferralCapWarningNotification;
use App\Notifications\ReferralCapReachedNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TierService
{
    // Tier configuration
    protected array $tierRequirements = [
        'bronze' => [
            'min_conversions' => 0,
            'min_earnings' => 0,
            'bonus_percentage' => 0,
            'color' => 'gray',
        ],
        'silver' => [
            'min_conversions' => 50,
            'min_earnings' => 5000,
            'bonus_percentage' => 5,
            'color' => 'slate',
        ],
        'gold' => [
            'min_conversions' => 200,
            'min_earnings' => 25000,
            'bonus_percentage' => 10,
            'color' => 'yellow',
        ],
        'platinum' => [
            'min_conversions' => 500,
            'min_earnings' => 100000,
            'bonus_percentage' => 15,
            'color' => 'purple',
        ],
    ];

    /**
     * Calculate and update affiliate tier based on performance
     */
    public function updateAffiliateTier(User $affiliate): bool
    {
        if (!$affiliate->hasRole('affiliate')) {
            return false;
        }

        $currentTier = $affiliate->tier;
        $newTier = $this->calculateTier($affiliate);

        if ($newTier !== $currentTier) {
            $newBonus = $this->tierRequirements[$newTier]['bonus_percentage'];

            $affiliate->update([
                'tier' => $newTier,
                'tier_commission_bonus' => $newBonus,
                'tier_updated_at' => now(),
            ]);

            // Clear cached tier data
            Cache::forget("affiliate_tier:{$affiliate->id}");

            // Send tier upgrade notification
            $tierStats = [
                'conversions' => $affiliate->total_conversions,
                'earnings' => $affiliate->lifetime_earnings,
                'conversion_rate' => $affiliate->conversion_rate,
            ];
            $affiliate->notify(new TierUpgradedNotification($currentTier, $newTier, $newBonus, $tierStats));

            return true;
        }

        return false;
    }

    /**
     * Calculate appropriate tier for affiliate
     */
    public function calculateTier(User $affiliate): string
    {
        $totalConversions = $affiliate->total_conversions;
        $totalEarnings = $affiliate->lifetime_earnings;

        // Check from highest to lowest tier
        foreach (array_reverse(array_keys($this->tierRequirements)) as $tier) {
            $requirements = $this->tierRequirements[$tier];

            if ($totalConversions >= $requirements['min_conversions'] &&
                $totalEarnings >= $requirements['min_earnings']) {
                return $tier;
            }
        }

        return 'bronze';
    }

    /**
     * Get tier requirements
     */
    public function getTierRequirements(): array
    {
        return $this->tierRequirements;
    }

    /**
     * Get tier info for an affiliate
     */
    public function getTierInfo(User $affiliate): array
    {
        $currentTier = $affiliate->tier;
        $nextTier = $this->getNextTier($currentTier);

        $info = [
            'current_tier' => $currentTier,
            'current_bonus' => $this->tierRequirements[$currentTier]['bonus_percentage'],
            'current_color' => $this->tierRequirements[$currentTier]['color'],
            'next_tier' => $nextTier,
            'progress' => null,
        ];

        if ($nextTier) {
            $nextRequirements = $this->tierRequirements[$nextTier];
            $info['next_requirements'] = $nextRequirements;

            // Calculate progress to next tier
            $conversionProgress = ($affiliate->total_conversions / $nextRequirements['min_conversions']) * 100;
            $earningsProgress = ($affiliate->lifetime_earnings / $nextRequirements['min_earnings']) * 100;

            $info['progress'] = [
                'conversions' => min($conversionProgress, 100),
                'earnings' => min($earningsProgress, 100),
                'overall' => min(($conversionProgress + $earningsProgress) / 2, 100),
            ];
        }

        return $info;
    }

    /**
     * Get next tier level
     */
    protected function getNextTier(string $currentTier): ?string
    {
        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        $currentIndex = array_search($currentTier, $tiers);

        if ($currentIndex !== false && $currentIndex < count($tiers) - 1) {
            return $tiers[$currentIndex + 1];
        }

        return null;
    }

    /**
     * Generate unique referral code for affiliate
     */
    public function generateReferralCode(User $affiliate): string
    {
        $code = strtoupper(Str::random(8));

        // Ensure uniqueness
        while (User::where('referral_code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        $affiliate->update(['referral_code' => $code]);

        return $code;
    }

    /**
     * Process sub-affiliate referral with cap enforcement
     */
    public function processReferral(User $affiliate, User $parentAffiliate, float $commission, ?int $commissionId = null): void
    {
        // Check if parent's referral is still active
        if (!$parentAffiliate->isReferralActive()) {
            // Log that referral was blocked due to cap
            ReferralEarning::create([
                'parent_affiliate_id' => $parentAffiliate->id,
                'sub_affiliate_id' => $affiliate->id,
                'commission_id' => $commissionId,
                'amount' => 0,
                'is_capped' => true,
                'cap_reason' => 'Referral cap already reached',
            ]);
            return; // Don't process referral
        }

        // Calculate referral commission
        $referralPercentage = Cache::get('settings.referral_commission_percentage', 10);
        $referralCommission = ($commission * $referralPercentage) / 100;

        // Check amount cap
        $capType = $parentAffiliate->referral_cap_type;
        $capAmount = $parentAffiliate->referral_cap_amount;
        $capReached = false;
        $capReason = null;

        if ($capType === 'amount' || $capType === 'both') {
            if ($capAmount) {
                $totalEarned = $parentAffiliate->referral_earnings;
                $remainingCap = $capAmount - $totalEarned;

                if ($remainingCap <= 0) {
                    // Cap reached
                    $parentAffiliate->update(['referral_cap_reached_at' => now()]);

                    ReferralEarning::create([
                        'parent_affiliate_id' => $parentAffiliate->id,
                        'sub_affiliate_id' => $affiliate->id,
                        'commission_id' => $commissionId,
                        'amount' => 0,
                        'is_capped' => true,
                        'cap_reason' => 'Amount cap reached',
                    ]);

                    // Send notification
                    $parentAffiliate->notify(new ReferralCapReachedNotification('amount', $capAmount));
                    return;
                }

                // If this commission would exceed cap, limit it
                if ($referralCommission > $remainingCap) {
                    $referralCommission = $remainingCap;
                    $capReached = true;
                    $capReason = 'Amount cap reached with this earning';
                    $parentAffiliate->update(['referral_cap_reached_at' => now()]);
                }

                // Send warning at 80% of cap
                $capProgress = ($totalEarned / $capAmount) * 100;
                if ($capProgress >= 80 && $capProgress < 100) {
                    $parentAffiliate->notify(new ReferralCapWarningNotification('amount', $remainingCap, $capAmount));
                }
            }
        }

        // Check time cap
        if ($capType === 'time' || $capType === 'both') {
            $capMonths = $parentAffiliate->referral_cap_months;
            $startedAt = $parentAffiliate->referral_started_at;

            if ($startedAt && $capMonths) {
                $monthsActive = now()->diffInMonths($startedAt);

                if ($monthsActive >= $capMonths) {
                    $parentAffiliate->update(['referral_cap_reached_at' => now()]);

                    ReferralEarning::create([
                        'parent_affiliate_id' => $parentAffiliate->id,
                        'sub_affiliate_id' => $affiliate->id,
                        'commission_id' => $commissionId,
                        'amount' => 0,
                        'is_capped' => true,
                        'cap_reason' => 'Time cap reached',
                    ]);

                    // Send notification
                    $parentAffiliate->notify(new ReferralCapReachedNotification('time', $capMonths));
                    return;
                }

                // Send warning at 80% of time
                $timeProgress = ($monthsActive / $capMonths) * 100;
                if ($timeProgress >= 80 && $timeProgress < 100) {
                    $remainingMonths = $capMonths - $monthsActive;
                    $parentAffiliate->notify(new ReferralCapWarningNotification('time', $remainingMonths, $capMonths));
                }
            }
        }

        // Set referral start time if first commission
        if (!$parentAffiliate->referral_started_at) {
            $parentAffiliate->update(['referral_started_at' => now()]);
        }

        // Process the referral commission
        $parentAffiliate->increment('referral_earnings', $referralCommission);
        $parentAffiliate->increment('balance', $referralCommission);

        // Log referral commission in commissions table
        DB::table('commissions')->insert([
            'affiliate_id' => $parentAffiliate->id,
            'conversion_id' => null,
            'offer_id' => null,
            'amount' => $referralCommission,
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log the referral earning with detailed tracking
        ReferralEarning::create([
            'parent_affiliate_id' => $parentAffiliate->id,
            'sub_affiliate_id' => $affiliate->id,
            'commission_id' => $commissionId,
            'amount' => $referralCommission,
            'is_capped' => false,
            'cap_reason' => $capReached ? $capReason : null,
        ]);

        // Send referral notification (only if not capped)
        if (!$capReached) {
            $parentAffiliate->notify(new ReferralEarnedNotification($affiliate, $referralCommission, 'commission'));
        }
    }

    /**
     * Get leaderboard of top affiliates
     */
    public function getLeaderboard(int $limit = 10): array
    {
        return User::role('affiliate')
            ->select('id', 'name', 'tier', 'total_conversions', 'lifetime_earnings', 'conversion_rate')
            ->orderBy('lifetime_earnings', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
