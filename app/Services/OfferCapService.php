<?php

namespace App\Services;

use App\Models\Offer;
use App\Notifications\OfferBudgetWarningNotification;
use App\Notifications\OfferCapWarningNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OfferCapService
{
    /**
     * Check if offer has reached its caps
     */
    public function hasReachedCap(Offer $offer): bool
    {
        // Check daily cap
        if ($offer->daily_conversion_cap && $offer->today_conversions >= $offer->daily_conversion_cap) {
            return true;
        }

        // Check monthly cap
        if ($offer->monthly_conversion_cap && $offer->month_conversions >= $offer->monthly_conversion_cap) {
            return true;
        }

        // Check total cap
        if ($offer->total_conversion_cap && $offer->total_conversions >= $offer->total_conversion_cap) {
            return true;
        }

        // Check budget limit
        if ($offer->budget_limit && $offer->spent_budget >= $offer->budget_limit) {
            return true;
        }

        return false;
    }

    /**
     * Check caps and auto-pause if needed
     */
    public function checkAndPause(Offer $offer): bool
    {
        if (!$offer->auto_pause_on_cap || !$offer->is_active) {
            return false;
        }

        $reason = null;

        // Check daily cap
        if ($offer->daily_conversion_cap && $offer->today_conversions >= $offer->daily_conversion_cap) {
            $reason = 'Daily conversion cap reached';
        }

        // Check monthly cap
        if (!$reason && $offer->monthly_conversion_cap && $offer->month_conversions >= $offer->monthly_conversion_cap) {
            $reason = 'Monthly conversion cap reached';
        }

        // Check total cap
        if (!$reason && $offer->total_conversion_cap && $offer->total_conversions >= $offer->total_conversion_cap) {
            $reason = 'Total conversion cap reached';
        }

        // Check budget limit
        if (!$reason && $offer->budget_limit && $offer->spent_budget >= $offer->budget_limit) {
            $reason = 'Budget limit reached';
        }

        if ($reason) {
            $offer->update([
                'is_active' => false,
                'pause_reason' => $reason,
            ]);

            // Clear offer cache
            Cache::forget("offer:{$offer->id}");

            return true;
        }

        return false;
    }

    /**
     * Reset daily caps for all offers
     */
    public function resetDailyCaps(): int
    {
        return Offer::whereNotNull('daily_conversion_cap')
            ->update([
                'today_conversions' => 0,
                'last_cap_reset_date' => now()->toDateString(),
            ]);
    }

    /**
     * Reset monthly caps for all offers
     */
    public function resetMonthlyCaps(): int
    {
        return Offer::whereNotNull('monthly_conversion_cap')
            ->update([
                'month_conversions' => 0,
                'last_cap_reset_date' => now()->toDateString(),
            ]);
    }

    /**
     * Increment conversion counters
     */
    public function incrementConversion(Offer $offer, float $commission): void
    {
        // Check if we need to reset daily counter (new day)
        if ($offer->last_cap_reset_date && $offer->last_cap_reset_date < now()->toDateString()) {
            $offer->today_conversions = 0;
        }

        // Check if we need to reset monthly counter (new month)
        if ($offer->last_cap_reset_date && now()->startOfMonth() > $offer->last_cap_reset_date) {
            $offer->month_conversions = 0;
        }

        // Increment counters
        $offer->increment('today_conversions');
        $offer->increment('month_conversions');
        $offer->increment('spent_budget', $commission);
        $offer->last_cap_reset_date = now()->toDateString();
        $offer->save();

        // Check warnings before pausing
        $this->checkWarnings($offer);

        // Check if we need to auto-pause
        $this->checkAndPause($offer);
    }

    /**
     * Get cap status for offer
     */
    public function getCapStatus(Offer $offer): array
    {
        $status = [
            'daily' => [
                'current' => $offer->today_conversions,
                'cap' => $offer->daily_conversion_cap,
                'remaining' => $offer->daily_conversion_cap ? max(0, $offer->daily_conversion_cap - $offer->today_conversions) : null,
                'percentage' => $offer->daily_conversion_cap ? min(100, ($offer->today_conversions / $offer->daily_conversion_cap) * 100) : 0,
            ],
            'monthly' => [
                'current' => $offer->month_conversions,
                'cap' => $offer->monthly_conversion_cap,
                'remaining' => $offer->monthly_conversion_cap ? max(0, $offer->monthly_conversion_cap - $offer->month_conversions) : null,
                'percentage' => $offer->monthly_conversion_cap ? min(100, ($offer->month_conversions / $offer->monthly_conversion_cap) * 100) : 0,
            ],
            'total' => [
                'current' => $offer->total_conversions,
                'cap' => $offer->total_conversion_cap,
                'remaining' => $offer->total_conversion_cap ? max(0, $offer->total_conversion_cap - $offer->total_conversions) : null,
                'percentage' => $offer->total_conversion_cap ? min(100, ($offer->total_conversions / $offer->total_conversion_cap) * 100) : 0,
            ],
            'budget' => [
                'spent' => $offer->spent_budget,
                'limit' => $offer->budget_limit,
                'remaining' => $offer->budget_limit ? max(0, $offer->budget_limit - $offer->spent_budget) : null,
                'percentage' => $offer->budget_limit ? min(100, ($offer->spent_budget / $offer->budget_limit) * 100) : 0,
            ],
            'has_caps' => $offer->daily_conversion_cap || $offer->monthly_conversion_cap || $offer->total_conversion_cap || $offer->budget_limit,
            'is_capped' => $this->hasReachedCap($offer),
        ];

        return $status;
    }

    /**
     * Check and send warning notifications for caps/budget
     */
    public function checkWarnings(Offer $offer): void
    {
        $advertiser = $offer->advertiser;
        if (!$advertiser) {
            return;
        }

        // Check budget warnings
        if ($offer->budget && $offer->total_spent > 0) {
            $budgetPercentage = ($offer->total_spent / $offer->budget) * 100;
            
            // Send warning at 75%, 90%, and 95%
            if ($budgetPercentage >= 95 && !$this->hasRecentWarning($offer, 'budget', 95)) {
                $advertiser->notify(new OfferBudgetWarningNotification($offer, $budgetPercentage, '95%'));
                $this->markWarningAsSent($offer, 'budget', 95);
            } elseif ($budgetPercentage >= 90 && !$this->hasRecentWarning($offer, 'budget', 90)) {
                $advertiser->notify(new OfferBudgetWarningNotification($offer, $budgetPercentage, '90%'));
                $this->markWarningAsSent($offer, 'budget', 90);
            } elseif ($budgetPercentage >= 75 && !$this->hasRecentWarning($offer, 'budget', 75)) {
                $advertiser->notify(new OfferBudgetWarningNotification($offer, $budgetPercentage, '75%'));
                $this->markWarningAsSent($offer, 'budget', 75);
            }
        }

        // Check daily cap warnings
        if ($offer->daily_conversion_cap && $offer->today_conversions > 0) {
            $dailyPercentage = ($offer->today_conversions / $offer->daily_conversion_cap) * 100;
            
            if ($dailyPercentage >= 90 && !$this->hasRecentWarning($offer, 'daily_cap', 90)) {
                $advertiser->notify(new OfferCapWarningNotification($offer, 'daily', $offer->today_conversions, $offer->daily_conversion_cap));
                $this->markWarningAsSent($offer, 'daily_cap', 90);
            } elseif ($dailyPercentage >= 75 && !$this->hasRecentWarning($offer, 'daily_cap', 75)) {
                $advertiser->notify(new OfferCapWarningNotification($offer, 'daily', $offer->today_conversions, $offer->daily_conversion_cap));
                $this->markWarningAsSent($offer, 'daily_cap', 75);
            }
        }

        // Check total cap warnings
        if ($offer->total_conversion_cap && $offer->total_conversions > 0) {
            $totalPercentage = ($offer->total_conversions / $offer->total_conversion_cap) * 100;
            
            if ($totalPercentage >= 90 && !$this->hasRecentWarning($offer, 'total_cap', 90)) {
                $advertiser->notify(new OfferCapWarningNotification($offer, 'total', $offer->total_conversions, $offer->total_conversion_cap));
                $this->markWarningAsSent($offer, 'total_cap', 90);
            } elseif ($totalPercentage >= 75 && !$this->hasRecentWarning($offer, 'total_cap', 75)) {
                $advertiser->notify(new OfferCapWarningNotification($offer, 'total', $offer->total_conversions, $offer->total_conversion_cap));
                $this->markWarningAsSent($offer, 'total_cap', 75);
            }
        }
    }

    /**
     * Check if warning was recently sent
     */
    protected function hasRecentWarning(Offer $offer, string $type, int $threshold): bool
    {
        $cacheKey = "offer_warning:{$offer->id}:{$type}:{$threshold}";
        return Cache::has($cacheKey);
    }

    /**
     * Mark warning as sent (cache for 24 hours)
     */
    protected function markWarningAsSent(Offer $offer, string $type, int $threshold): void
    {
        $cacheKey = "offer_warning:{$offer->id}:{$type}:{$threshold}";
        Cache::put($cacheKey, true, now()->addHours(24));
    }
}
