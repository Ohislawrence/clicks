<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Click;
use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $dateRange = $request->get('range', '30'); // Default 30 days

        $startDate = now()->subDays((int)$dateRange);

        $cacheKey = "advertiser_dashboard_{$user->id}_{$dateRange}";

        $cached = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user, $startDate) {
            // Get all user's offers
            $offerIds = Offer::where('advertiser_id', $user->id)->pluck('id');

            // Total Offers
            $totalOffers = $offerIds->count();
            $activeOffers = Offer::where('advertiser_id', $user->id)
                ->where('is_active', true)
                ->count();

            // Total Clicks
            $totalClicks = Click::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', $startDate)
                ->count();

            $totalClicksAllTime = Click::whereIn('offer_id', $offerIds)->count();

            // Conversions
            $totalConversions = Conversion::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', $startDate)
                ->count();

            $pendingConversions = Conversion::whereIn('offer_id', $offerIds)
                ->where('status', 'pending')
                ->count();

            $approvedConversions = Conversion::whereIn('offer_id', $offerIds)
                ->where('status', 'approved')
                ->count();

            // Revenue
            $totalRevenue = Conversion::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', $startDate)
                ->sum('conversion_value');

            $pendingRevenue = Conversion::whereIn('offer_id', $offerIds)
                ->where('status', 'pending')
                ->sum('conversion_value');

            $approvedRevenue = Conversion::whereIn('offer_id', $offerIds)
                ->where('status', 'approved')
                ->sum('conversion_value');

            // Commissions Paid (Total Payout by Advertiser)
            // If some older conversions have null advertiser_payout, fallback to commission_amount
            $payoutSum = Conversion::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', $startDate)
                ->where('status', 'approved')
                ->whereNotNull('advertiser_payout')
                ->sum('advertiser_payout');

            $fallbackSum = Conversion::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', $startDate)
                ->where('status', 'approved')
                ->whereNull('advertiser_payout')
                ->sum('commission_amount');

            $totalCommissions = $payoutSum + $fallbackSum;

            // Conversion Rate
            $conversionRate = $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0;

            // Top Performing Offers
            $topOffers = Offer::where('advertiser_id', $user->id)
                ->select([
                    'offers.id',
                    'offers.name',
                    'offers.thumbnail',
                    'offers.total_clicks as clicks',
                    'offers.total_conversions as conversions',
                    'offers.total_revenue as revenue'
                ])
                ->orderByDesc('total_conversions')
                ->limit(5)
                ->get();

            // Top Affiliates
            $topAffiliates = Conversion::whereIn('conversions.offer_id', $offerIds)
                ->where('conversions.created_at', '>=', $startDate)
                ->join('users', 'conversions.affiliate_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COUNT(conversions.id) as conversions'),
                    DB::raw('SUM(conversions.conversion_value) as revenue')
                )
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderByDesc('conversions')
                ->limit(5)
                ->get();

            // Daily Stats for Chart (last 30 days)
            $dailyStats = Conversion::whereIn('offer_id', $offerIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as conversions'),
                    DB::raw('SUM(conversion_value) as revenue'),
                    DB::raw('SUM(commission_amount) as commissions')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return compact(
                'totalOffers', 'activeOffers', 'totalClicks', 'totalClicksAllTime',
                'totalConversions', 'pendingConversions', 'approvedConversions',
                'conversionRate', 'totalRevenue', 'pendingRevenue', 'approvedRevenue',
                'totalCommissions', 'topOffers', 'topAffiliates', 'dailyStats'
            );
        });

        // Recent pending conversions are always fetched fresh (actionable items)
        $offerIds = Offer::where('advertiser_id', $user->id)->pluck('id');
        $recentConversions = Conversion::with(['offer', 'click', 'affiliate:id,affiliate_code'])
            ->whereIn('offer_id', $offerIds)
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return Inertia::render('Advertiser/Dashboard', [
            'stats' => [
                'totalOffers' => $cached['totalOffers'],
                'activeOffers' => $cached['activeOffers'],
                'totalClicks' => $cached['totalClicks'],
                'totalClicksAllTime' => $cached['totalClicksAllTime'],
                'totalConversions' => $cached['totalConversions'],
                'pendingConversions' => $cached['pendingConversions'],
                'approvedConversions' => $cached['approvedConversions'],
                'conversionRate' => round($cached['conversionRate'], 2),
                'totalRevenue' => $cached['totalRevenue'],
                'pendingRevenue' => $cached['pendingRevenue'],
                'approvedRevenue' => $cached['approvedRevenue'],
                'totalCommissions' => $cached['totalCommissions'],
            ],
            'topOffers' => $cached['topOffers'],
            'topAffiliates' => $cached['topAffiliates'],
            'recentConversions' => $recentConversions,
            'dailyStats' => $cached['dailyStats'],
            'dateRange' => $dateRange,
        ]);
    }
}

