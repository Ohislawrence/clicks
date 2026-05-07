<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingService
{
    /**
     * Get comprehensive stats for a given period
     */
    public function getStats(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30);
        $dateTo = $filters['date_to'] ?? now();
        $userId = $filters['user_id'] ?? null;
        $offerId = $filters['offer_id'] ?? null;
        $role = $filters['role'] ?? null;

        // Base queries
        $clicksQuery = Click::whereBetween('created_at', [$dateFrom, $dateTo]);
        $conversionsQuery = Conversion::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply filters
        if ($userId) {
            if ($role === 'affiliate') {
                $clicksQuery->where('affiliate_id', $userId);
                $conversionsQuery->where('affiliate_id', $userId);
            } elseif ($role === 'advertiser') {
                $clicksQuery->whereHas('offer', fn($q) => $q->where('advertiser_id', $userId));
                $conversionsQuery->whereHas('offer', fn($q) => $q->where('advertiser_id', $userId));
            }
        }

        if ($offerId) {
            $clicksQuery->where('offer_id', $offerId);
            $conversionsQuery->where('offer_id', $offerId);
        }

        // Get basic metrics
        $totalClicks = $clicksQuery->count();
        $validClicks = (clone $clicksQuery)->where('is_valid', true)->count();
        $totalConversions = $conversionsQuery->count();
        $approvedConversions = (clone $conversionsQuery)->where('status', 'approved')->count();

        // Get financial metrics
        $totalRevenue = (clone $conversionsQuery)->sum('conversion_value');
        $totalCommissions = (clone $conversionsQuery)->sum('commission_amount');
        $approvedCommissions = (clone $conversionsQuery)->where('status', 'approved')->sum('commission_amount');
        $totalPlatformMargin = (clone $conversionsQuery)->sum('platform_margin');
        $approvedPlatformMargin = (clone $conversionsQuery)->where('status', 'approved')->sum('platform_margin');
        $totalAdvertiserPayout = (clone $conversionsQuery)->sum('advertiser_payout');

        // Calculate derived metrics
        $conversionRate = $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0;
        $epc = $totalClicks > 0 ? $totalCommissions / $totalClicks : 0; // Earnings Per Click
        $epl = $totalConversions > 0 ? $totalCommissions / $totalConversions : 0; // Earnings Per Lead
        $roi = $totalCommissions > 0 ? (($totalRevenue - $totalCommissions) / $totalCommissions) * 100 : 0;
        $averageOrderValue = $totalConversions > 0 ? $totalRevenue / $totalConversions : 0;
        $averageMarginPercentage = $totalAdvertiserPayout > 0 ? ($totalPlatformMargin / $totalAdvertiserPayout) * 100 : 0;

        return [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
                'days' => Carbon::parse($dateFrom)->diffInDays($dateTo) + 1,
            ],
            'clicks' => [
                'total' => $totalClicks,
                'valid' => $validClicks,
                'invalid' => $totalClicks - $validClicks,
                'fraud_rate' => $totalClicks > 0 ? (($totalClicks - $validClicks) / $totalClicks) * 100 : 0,
            ],
            'conversions' => [
                'total' => $totalConversions,
                'approved' => $approvedConversions,
                'pending' => $totalConversions - $approvedConversions,
                'rate' => round($conversionRate, 2),
            ],
            'revenue' => [
                'total' => round($totalRevenue, 2),
                'average_order_value' => round($averageOrderValue, 2),
            ],
            'commissions' => [
                'total' => round($totalCommissions, 2),
                'approved' => round($approvedCommissions, 2),
                'pending' => round($totalCommissions - $approvedCommissions, 2),
            ],
            'platform_margin' => [
                'total' => round($totalPlatformMargin, 2),
                'approved' => round($approvedPlatformMargin, 2),
                'average_percentage' => round($averageMarginPercentage, 2),
                'total_advertiser_payout' => round($totalAdvertiserPayout, 2),
            ],
            'performance' => [
                'epc' => round($epc, 2), // Earnings Per Click
                'epl' => round($epl, 2), // Earnings Per Lead/Sale
                'roi' => round($roi, 2), // Return on Investment (%)
                'cr' => round($conversionRate, 2), // Conversion Rate (%)
            ],
        ];
    }

    /**
     * Get daily stats for charting
     */
    public function getDailyStats(array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30);
        $dateTo = $filters['date_to'] ?? now();
        $userId = $filters['user_id'] ?? null;
        $offerId = $filters['offer_id'] ?? null;
        $role = $filters['role'] ?? null;

        // Get daily clicks
        $clicksQuery = Click::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(CASE WHEN is_valid = 1 THEN 1 ELSE 0 END) as valid_count')
        )
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->groupBy(DB::raw('DATE(created_at)'));

        // Get daily conversions
        $conversionsQuery = Conversion::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(conversion_value) as revenue'),
            DB::raw('SUM(commission_amount) as commissions')
        )
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->groupBy(DB::raw('DATE(created_at)'));

        // Apply filters
        if ($userId) {
            if ($role === 'affiliate') {
                $clicksQuery->where('affiliate_id', $userId);
                $conversionsQuery->where('affiliate_id', $userId);
            } elseif ($role === 'advertiser') {
                $clicksQuery->whereHas('offer', fn($q) => $q->where('advertiser_id', $userId));
                $conversionsQuery->whereHas('offer', fn($q) => $q->where('advertiser_id', $userId));
            }
        }

        if ($offerId) {
            $clicksQuery->where('offer_id', $offerId);
            $conversionsQuery->where('offer_id', $offerId);
        }

        $dailyClicks = $clicksQuery->get()->keyBy('date');
        $dailyConversions = $conversionsQuery->get()->keyBy('date');

        // Merge data by date
        $days = [];
        $currentDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $clicks = $dailyClicks->get($dateStr);
            $conversions = $dailyConversions->get($dateStr);

            $clickCount = $clicks->count ?? 0;
            $conversionCount = $conversions->count ?? 0;
            $commissions = $conversions->commissions ?? 0;

            $days[] = [
                'date' => $dateStr,
                'clicks' => $clickCount,
                'valid_clicks' => $clicks->valid_count ?? 0,
                'conversions' => $conversionCount,
                'revenue' => $conversions->revenue ?? 0,
                'commissions' => $commissions,
                'cr' => $clickCount > 0 ? round(($conversionCount / $clickCount) * 100, 2) : 0,
                'epc' => $clickCount > 0 ? round($commissions / $clickCount, 2) : 0,
            ];

            $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Get top performing offers
     */
    public function getTopOffers(int $limit = 10, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30);
        $dateTo = $filters['date_to'] ?? now();

        return Offer::select('offers.*')
            ->withCount([
                'conversions as period_conversions' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]),
                'clicks as period_clicks' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]),
            ])
            ->withSum([
                'conversions as period_revenue' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo])
            ], 'conversion_value')
            ->withSum([
                'conversions as period_commissions' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo])
            ], 'commission_amount')
            ->where('is_active', true)
            ->orderByDesc('period_revenue')
            ->limit($limit)
            ->get()
            ->map(function ($offer) {
                $epc = $offer->period_clicks > 0 ? $offer->period_commissions / $offer->period_clicks : 0;
                $cr = $offer->period_clicks > 0 ? ($offer->period_conversions / $offer->period_clicks) * 100 : 0;

                return [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'clicks' => $offer->period_clicks,
                    'conversions' => $offer->period_conversions,
                    'revenue' => round($offer->period_revenue ?? 0, 2),
                    'commissions' => round($offer->period_commissions ?? 0, 2),
                    'epc' => round($epc, 2),
                    'cr' => round($cr, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get top performing affiliates
     */
    public function getTopAffiliates(int $limit = 10, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30);
        $dateTo = $filters['date_to'] ?? now();

        return User::role('affiliate')
            ->select('users.*')
            ->withCount([
                'conversions as period_conversions' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]),
                'clicks as period_clicks' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo]),
            ])
            ->withSum([
                'conversions as period_commissions' => fn($q) => $q->whereBetween('created_at', [$dateFrom, $dateTo])
            ], 'commission_amount')
            ->orderByDesc('period_commissions')
            ->limit($limit)
            ->get()
            ->map(function ($affiliate) {
                $epc = $affiliate->period_clicks > 0 ? $affiliate->period_commissions / $affiliate->period_clicks : 0;
                $cr = $affiliate->period_clicks > 0 ? ($affiliate->period_conversions / $affiliate->period_clicks) * 100 : 0;

                return [
                    'id' => $affiliate->id,
                    'name' => $affiliate->name,
                    'tier' => $affiliate->tier,
                    'clicks' => $affiliate->period_clicks,
                    'conversions' => $affiliate->period_conversions,
                    'commissions' => round($affiliate->period_commissions ?? 0, 2),
                    'epc' => round($epc, 2),
                    'cr' => round($cr, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Export stats to array (for CSV/Excel)
     */
    public function exportStats(array $filters = []): array
    {
        $stats = $this->getStats($filters);
        $daily = $this->getDailyStats($filters);

        return [
            'summary' => $stats,
            'daily' => $daily,
        ];
    }
}
