<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display advertiser performance reports
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $range = $request->input('range', '7days');
        $dateRange = $this->getDateRange($range);

        $filters = [
            'date_from' => $dateRange['from'],
            'date_to' => $dateRange['to'],
            'user_id' => $user->id,
            'role' => 'advertiser',
        ];

        // Get current and previous stats
        $currentStats = $this->reportingService->getStats($filters);
        $previousStats = $this->reportingService->getStats([
            'date_from' => Carbon::parse($dateRange['from'])->subDays($dateRange['days']),
            'date_to' => Carbon::parse($dateRange['to'])->subDays($dateRange['days']),
            'user_id' => $user->id,
            'role' => 'advertiser',
        ]);

        // Build stats
        $stats = [
            'total_clicks' => $currentStats['clicks']['total'],
            'clicks_change' => $this->calculateChange($currentStats['clicks']['total'], $previousStats['clicks']['total']),
            'total_conversions' => $currentStats['conversions']['total'],
            'conversion_rate' => $currentStats['conversions']['rate'],
            'total_spend' => $currentStats['commissions']['total'],
            'cpa' => $currentStats['conversions']['total'] > 0 ? $currentStats['commissions']['total'] / $currentStats['conversions']['total'] : 0,
            'roi' => $currentStats['performance']['roi'],
        ];

        // Get budget status
        $offers = \App\Models\Offer::where('advertiser_id', $user->id)->get();
        $totalDailyBudget = $offers->sum('daily_budget');
        $totalMonthlyBudget = $offers->sum('monthly_budget');

        $budgetStatus = [
            'daily_used' => $currentStats['commissions']['total'] / $dateRange['days'],
            'daily_limit' => $totalDailyBudget,
            'daily_percentage' => $totalDailyBudget > 0 ? ($currentStats['commissions']['total'] / $dateRange['days'] / $totalDailyBudget) * 100 : 0,
            'monthly_used' => $currentStats['commissions']['total'],
            'monthly_limit' => $totalMonthlyBudget,
            'monthly_percentage' => $totalMonthlyBudget > 0 ? ($currentStats['commissions']['total'] / $totalMonthlyBudget) * 100 : 0,
        ];

        // Get cap status
        $totalDailyCap = $offers->sum('daily_cap');
        $capStatus = [
            'daily_conversions' => $currentStats['conversions']['total'] / $dateRange['days'],
            'daily_cap' => $totalDailyCap,
            'daily_percentage' => $totalDailyCap > 0 ? ($currentStats['conversions']['total'] / $dateRange['days'] / $totalDailyCap) * 100 : 0,
        ];

        // Get performance trend
        $performanceTrend = $this->reportingService->getDailyStats($filters);

        // Get spend trend (calculate revenue from conversions)
        $spendTrend = collect($performanceTrend)->map(fn($day) => [
            'date' => $day['date'],
            'spend' => $day['commission'] ?? 0,
            'revenue' => ($day['commission'] ?? 0) * 1.5, // Assuming 50% profit margin
        ])->toArray();

        // Get offer performance
        $offerPerformance = \App\Models\Offer::where('advertiser_id', $user->id)
            ->withCount(['clicks' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withCount(['conversions as conversions' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withSum(['conversions as spend' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }], 'commission_amount')
            ->get()
            ->map(fn($offer) => [
                'id' => $offer->id,
                'name' => $offer->name,
                'clicks' => $offer->clicks_count ?? 0,
                'conversions' => $offer->conversions ?? 0,
                'cr' => $offer->clicks_count > 0 ? ($offer->conversions / $offer->clicks_count) * 100 : 0,
                'spend' => $offer->spend ?? 0,
                'cpa' => $offer->conversions > 0 ? ($offer->spend / $offer->conversions) : 0,
                'budget_percentage' => $offer->monthly_budget > 0 ? ($offer->spend / $offer->monthly_budget) * 100 : 0,
                'is_active' => $offer->is_active,
            ]);

        // Get top affiliates (ID only for privacy)
        $topAffiliates = \App\Models\User::role('affiliate')
            ->whereHas('conversions', function($q) use ($user, $dateRange) {
                $q->whereHas('offer', fn($q2) => $q2->where('advertiser_id', $user->id))
                  ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            })
            ->withCount(['conversions' => function($q) use ($user, $dateRange) {
                $q->whereHas('offer', fn($q2) => $q2->where('advertiser_id', $user->id))
                  ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->with(['clicks' => function($q) use ($user, $dateRange) {
                $q->whereHas('offer', fn($q2) => $q2->where('advertiser_id', $user->id))
                  ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->orderBy('conversions_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($affiliate) => [
                'id' => $affiliate->id,
                'affiliate_code' => $affiliate->affiliate_code,
                'tier' => $affiliate->tier,
                'conversions' => $affiliate->conversions_count ?? 0,
                'cr' => $affiliate->clicks->count() > 0 ? ($affiliate->conversions_count / $affiliate->clicks->count()) * 100 : 0,
            ]);

        return Inertia::render('Advertiser/Reports/Index', [
            'stats' => $stats,
            'budgetStatus' => $budgetStatus,
            'capStatus' => $capStatus,
            'performanceTrend' => $performanceTrend,
            'spendTrend' => $spendTrend,
            'offerPerformance' => $offerPerformance,
            'topAffiliates' => $topAffiliates,
        ]);
    }

    protected function getDateRange($range)
    {
        $to = now();
        switch ($range) {
            case 'today':
                $from = now()->startOfDay();
                break;
            case 'yesterday':
                $from = now()->subDay()->startOfDay();
                $to = now()->subDay()->endOfDay();
                break;
            case '7days':
                $from = now()->subDays(7);
                break;
            case '30days':
                $from = now()->subDays(30);
                break;
            case 'thismonth':
                $from = now()->startOfMonth();
                break;
            case 'lastmonth':
                $from = now()->subMonth()->startOfMonth();
                $to = now()->subMonth()->endOfMonth();
                break;
            default:
                $from = now()->subDays(7);
        }

        return [
            'from' => $from,
            'to' => $to,
            'days' => $from->diffInDays($to),
        ];
    }

    protected function calculateChange($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $range = $request->input('range', '7days');
        // Implementation here
        return response()->download(storage_path('app/reports/export.csv'));
    }
}
