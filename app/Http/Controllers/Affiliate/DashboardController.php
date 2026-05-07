<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $dateRange = $request->get('range', '30'); // Default 30 days

        $startDate = now()->subDays((int)$dateRange);

        // Total Clicks
        $totalClicks = Click::where('affiliate_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalClicksAllTime = Click::where('affiliate_id', $user->id)->count();

        // Conversions
        $totalConversions = Conversion::where('affiliate_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $pendingConversions = Conversion::where('affiliate_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedConversions = Conversion::where('affiliate_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // Earnings
        $pendingEarnings = Commission::where('affiliate_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $approvedEarnings = Commission::where('affiliate_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $paidEarnings = Commission::where('affiliate_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $totalEarnings = $pendingEarnings + $approvedEarnings + $paidEarnings;

        // Conversion Rate
        $conversionRate = $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0;

        // Top Performing Offers
        $topOffers = Click::where('clicks.affiliate_id', $user->id)
            ->where('clicks.created_at', '>=', $startDate)
            ->join('offers', 'clicks.offer_id', '=', 'offers.id')
            ->select(
                'offers.id',
                'offers.name',
                'offers.thumbnail',
                DB::raw('COUNT(clicks.id) as clicks'),
                DB::raw('SUM(clicks.is_converted) as conversions')
            )
            ->groupBy('offers.id', 'offers.name', 'offers.thumbnail')
            ->orderByDesc('clicks')
            ->limit(5)
            ->get();

        // Traffic Sources
        $trafficSources = Click::where('affiliate_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('referrer')
            ->select(
                DB::raw('COALESCE(referrer, "Direct") as source'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Geographic Performance
        $geoPerformance = Click::where('affiliate_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('country_name')
            ->select(
                'country_name',
                'country_code',
                DB::raw('COUNT(*) as clicks'),
                DB::raw('SUM(is_converted) as conversions')
            )
            ->groupBy('country_name', 'country_code')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get();

        // Daily Stats for Chart (last 30 days)
        $dailyStats = Click::where('affiliate_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as clicks'),
                DB::raw('SUM(is_converted) as conversions')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Referral Cap Status
        $referralCapData = null;
        if ($user->parent_affiliate_id !== null || $user->referral_earnings > 0) {
            $referralCapData = [
                'cap_type' => $user->referral_cap_type,
                'cap_amount' => $user->referral_cap_amount,
                'cap_months' => $user->referral_cap_months,
                'current_earnings' => $user->referral_earnings,
                'started_at' => $user->referral_started_at,
                'cap_reached_at' => $user->referral_cap_reached_at,
                'is_active' => $user->isReferralActive(),
                'has_reached_cap' => $user->hasReachedReferralCap(),
                'progress' => $user->getReferralCapProgress(),
                'remaining_amount' => $user->getRemainingReferralCap(),
                'remaining_months' => $user->getRemainingReferralMonths(),
            ];
        }

        return Inertia::render('Affiliate/Dashboard', [
            'stats' => [
                'totalClicks' => $totalClicks,
                'totalClicksAllTime' => $totalClicksAllTime,
                'totalConversions' => $totalConversions,
                'pendingConversions' => $pendingConversions,
                'approvedConversions' => $approvedConversions,
                'conversionRate' => round($conversionRate, 2),
                'pendingEarnings' => $pendingEarnings,
                'approvedEarnings' => $approvedEarnings,
                'paidEarnings' => $paidEarnings,
                'totalEarnings' => $totalEarnings,
                'balance' => $user->balance,
                'lifetimeEarnings' => $user->lifetime_earnings,
            ],
            'topOffers' => $topOffers,
            'trafficSources' => $trafficSources,
            'geoPerformance' => $geoPerformance,
            'dailyStats' => $dailyStats,
            'dateRange' => $dateRange,
            'referralCapData' => $referralCapData,
        ]);
    }
}
