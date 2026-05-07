<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use App\Services\TierService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportingService $reportingService;
    protected TierService $tierService;

    public function __construct(ReportingService $reportingService, TierService $tierService)
    {
        $this->reportingService = $reportingService;
        $this->tierService = $tierService;
    }

    /**
     * Display affiliate performance reports
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
            'role' => 'affiliate',
        ];

        // Get current and previous stats
        $currentStats = $this->reportingService->getStats($filters);
        $previousStats = $this->reportingService->getStats([
            'date_from' => Carbon::parse($dateRange['from'])->subDays($dateRange['days']),
            'date_to' => Carbon::parse($dateRange['to'])->subDays($dateRange['days']),
            'user_id' => $user->id,
            'role' => 'affiliate',
        ]);

        // Build stats
        $stats = [
            'total_clicks' => $currentStats['clicks']['total'],
            'clicks_change' => $this->calculateChange($currentStats['clicks']['total'], $previousStats['clicks']['total']),
            'total_conversions' => $currentStats['conversions']['total'],
            'conversion_rate' => $currentStats['conversions']['rate'],
            'total_earnings' => $currentStats['commissions']['total'],
            'epc' => $currentStats['performance']['epc'],
            'pending_balance' => $user->balance ?? 0,
            'available_balance' => $user->available_balance ?? 0,
        ];

        // Get tier info
        $tierInfo = $this->tierService->getTierInfo($user);

        // Get earnings trend
        $earningsTrend = $this->reportingService->getDailyStats($filters);

        // Get CR trend (same data, different format)
        $crTrend = collect($earningsTrend)->map(fn($day) => [
            'date' => $day['date'],
            'cr' => $day['clicks'] > 0 ? ($day['conversions'] / $day['clicks']) * 100 : 0,
        ])->toArray();

        // Get top links
        $topLinks = \App\Models\AffiliateLink::where('affiliate_id', $user->id)
            ->withCount(['clicks' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withCount(['conversions' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withSum(['conversions as earnings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }], 'commission_amount')
            ->with('offer:id,name')
            ->orderBy('earnings', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($link) => [
                'id' => $link->id,
                'name' => $link->name ?? 'Link #' . $link->id,
                'tracking_code' => $link->tracking_code,
                'offer_name' => $link->offer->name ?? 'N/A',
                'clicks' => $link->clicks_count ?? 0,
                'conversions' => $link->conversions_count ?? 0,
                'cr' => $link->clicks_count > 0 ? ($link->conversions_count / $link->clicks_count) * 100 : 0,
                'earnings' => $link->earnings ?? 0,
                'epc' => $link->clicks_count > 0 ? ($link->earnings / $link->clicks_count) : 0,
            ]);

        // Get referral stats
        $referralStats = null;
        if ($user->referral_code) {
            $referralStats = [
                'referral_code' => $user->referral_code,
                'total_referrals' => $user->referral_count ?? 0,
                'active_referrals' => \App\Models\User::where('parent_affiliate_id', $user->id)
                    ->where('status', 'active')
                    ->count(),
                'total_earnings' => $user->referral_earnings ?? 0,
            ];
        }

        return Inertia::render('Affiliate/Reports/Index', [
            'stats' => $stats,
            'tierInfo' => $tierInfo,
            'earningsTrend' => $earningsTrend,
            'crTrend' => $crTrend,
            'topLinks' => $topLinks,
            'referralStats' => $referralStats,
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
}
