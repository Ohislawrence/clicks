<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Conversion;
use App\Models\Click;
use App\Notifications\WeeklyPerformanceSummaryNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendWeeklyPerformanceSummaries implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();

        // Send summaries to affiliates
        $this->sendAffiliatesSummaries($startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek);

        // Send summaries to advertisers
        $this->sendAdvertisersSummaries($startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek);

        Log::info('Weekly performance summaries sent successfully');
    }

    /**
     * Send weekly summaries to affiliates
     */
    protected function sendAffiliatesSummaries($startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek): void
    {
        $affiliates = User::role('affiliate')
            ->where('is_active', true)
            ->get();

        foreach ($affiliates as $affiliate) {
            try {
                // Get current week stats
                $currentWeekConversions = Conversion::where('affiliate_id', $affiliate->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->get();

                $currentWeekClicks = Click::whereHas('affiliateLink', function ($query) use ($affiliate) {
                    $query->where('affiliate_id', $affiliate->id);
                })->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();

                // Get last week stats
                $lastWeekConversions = Conversion::where('affiliate_id', $affiliate->id)
                    ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
                    ->sum('commission_amount');

                // Calculate stats
                $earnings = $currentWeekConversions->sum('commission_amount');
                $conversions = $currentWeekConversions->count();
                $clicks = $currentWeekClicks;
                $conversionRate = $clicks > 0 ? ($conversions / $clicks) * 100 : 0;
                $earningsChange = $lastWeekConversions > 0 
                    ? (($earnings - $lastWeekConversions) / $lastWeekConversions) * 100 
                    : 0;

                // Get top offers
                $topOffers = $currentWeekConversions
                    ->groupBy('offer_id')
                    ->map(function ($group) {
                        $offer = $group->first()->offer;
                        return [
                            'name' => $offer->name,
                            'conversions' => $group->count(),
                        ];
                    })
                    ->sortByDesc('conversions')
                    ->values()
                    ->toArray();

                // Skip if no activity this week
                if ($earnings == 0 && $conversions == 0) {
                    continue;
                }

                $stats = [
                    'earnings' => $earnings,
                    'previous_earnings' => $lastWeekConversions,
                    'earnings_change' => $earningsChange,
                    'conversions' => $conversions,
                    'clicks' => $clicks,
                    'conversion_rate' => $conversionRate,
                    'top_offers' => $topOffers,
                ];

                $affiliate->notify(new WeeklyPerformanceSummaryNotification($stats, 'affiliate'));

            } catch (\Exception $e) {
                Log::error('Failed to send weekly summary to affiliate ' . $affiliate->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Send weekly summaries to advertisers
     */
    protected function sendAdvertisersSummaries($startOfWeek, $endOfWeek, $startOfLastWeek, $endOfLastWeek): void
    {
        $advertisers = User::role('advertiser')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->get();

        foreach ($advertisers as $advertiser) {
            try {
                $offerIds = $advertiser->offers()->pluck('id');

                // Get current week conversions
                $currentWeekConversions = Conversion::whereIn('offer_id', $offerIds)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->get();

                // Get last week conversions
                $lastWeekConversions = Conversion::whereIn('offer_id', $offerIds)
                    ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
                    ->count();

                // Calculate stats
                $conversions = $currentWeekConversions->count();
                $revenue = $currentWeekConversions->sum('conversion_value');
                $spent = $currentWeekConversions->sum('commission_amount');
                $roi = $spent > 0 ? (($revenue - $spent) / $spent) * 100 : 0;
                $conversionsChange = $lastWeekConversions > 0 
                    ? (($conversions - $lastWeekConversions) / $lastWeekConversions) * 100 
                    : 0;

                // Get top offers
                $topOffers = $currentWeekConversions
                    ->groupBy('offer_id')
                    ->map(function ($group) {
                        $offer = $group->first()->offer;
                        return [
                            'name' => $offer->name,
                            'conversions' => $group->count(),
                        ];
                    })
                    ->sortByDesc('conversions')
                    ->values()
                    ->toArray();

                // Skip if no activity this week
                if ($conversions == 0) {
                    continue;
                }

                $stats = [
                    'conversions' => $conversions,
                    'previous_conversions' => $lastWeekConversions,
                    'conversions_change' => $conversionsChange,
                    'revenue' => $revenue,
                    'spent' => $spent,
                    'roi' => $roi,
                    'top_offers' => $topOffers,
                ];

                $advertiser->notify(new WeeklyPerformanceSummaryNotification($stats, 'advertiser'));

            } catch (\Exception $e) {
                Log::error('Failed to send weekly summary to advertiser ' . $advertiser->id . ': ' . $e->getMessage());
            }
        }
    }
}
