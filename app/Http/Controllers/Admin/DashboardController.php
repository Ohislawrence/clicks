<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Offer;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date range
        $dateRange = $request->get('range', 30);
        $startDate = now()->subDays($dateRange);

        // Platform stats
        $totalUsers = User::count();
        $totalAffiliates = User::role('affiliate')->count();
        $totalAdvertisers = User::role('advertiser')->count();
        $totalOffers = Offer::count();
        $activeOffers = Offer::where('is_active', true)->count();

        // Performance stats
        $totalClicks = Click::where('created_at', '>=', $startDate)->count();
        $totalConversions = Conversion::where('created_at', '>=', $startDate)->count();
        $totalRevenue = Conversion::where('created_at', '>=', $startDate)
            ->where('status', '!=', 'rejected')
            ->sum('conversion_value');
        
        $totalCommissions = Conversion::where('created_at', '>=', $startDate)
            ->where('status', 'approved')
            ->sum('commission_amount');

        $conversionRate = $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 2) : 0;

        // Pending items
        $pendingPayouts = PayoutRequest::where('status', 'pending')->count();
        $pendingConversions = Conversion::where('status', 'pending')->count();

        // Top Affiliates (by total earnings)
        $topAffiliates = User::role('affiliate')
            ->select('users.id', 'users.name', 'users.email', 'users.lifetime_earnings')
            ->orderBy('lifetime_earnings', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($affiliate) {
                return [
                    'id' => $affiliate->id,
                    'name' => $affiliate->name,
                    'email' => $affiliate->email,
                    'earnings' => $affiliate->lifetime_earnings,
                    'conversions' => Conversion::where('affiliate_id', $affiliate->id)
                        ->where('status', 'approved')
                        ->count(),
                ];
            });

        // Top Advertisers (by total revenue)
        $topAdvertisers = User::role('advertiser')
            ->select('users.id', 'users.name', 'users.email')
            ->withCount(['offers'])
            ->get()
            ->map(function ($advertiser) {
                $revenue = Conversion::whereHas('offer', function ($query) use ($advertiser) {
                    $query->where('advertiser_id', $advertiser->id);
                })->where('status', '!=', 'rejected')->sum('conversion_value');

                return [
                    'id' => $advertiser->id,
                    'name' => $advertiser->name,
                    'email' => $advertiser->email,
                    'offers_count' => $advertiser->offers_count,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        // Top Offers
        $topOffers = Offer::with('advertiser:id,name')
            ->withCount(['conversions as conversions_count' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->where('status', '!=', 'rejected');
            }])
            ->having('conversions_count', '>', 0)
            ->orderBy('conversions_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($offer) use ($startDate) {
                $revenue = Conversion::where('offer_id', $offer->id)
                    ->where('created_at', '>=', $startDate)
                    ->where('status', '!=', 'rejected')
                    ->sum('conversion_value');

                return [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'advertiser_name' => $offer->advertiser->name,
                    'conversions' => $offer->conversions_count,
                    'revenue' => $revenue,
                ];
            });

        // Recent Activity
        $recentConversions = Conversion::with(['offer:id,name', 'affiliate:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        // Daily Stats for Charts (last 30 days)
        $dailyStats = DB::table('clicks')
            ->select(DB::raw('DATE(created_at) as date'))
            ->selectRaw('COUNT(*) as clicks')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($stat) {
                $conversions = DB::table('conversions')
                    ->whereDate('created_at', $stat->date)
                    ->count();
                
                $revenue = DB::table('conversions')
                    ->whereDate('created_at', $stat->date)
                    ->where('status', '!=', 'rejected')
                    ->sum('conversion_value');
                
                return (object) [
                    'date' => $stat->date,
                    'clicks' => $stat->clicks,
                    'conversions' => $conversions,
                    'revenue' => $revenue ?? 0,
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalAffiliates' => $totalAffiliates,
                'totalAdvertisers' => $totalAdvertisers,
                'totalOffers' => $totalOffers,
                'activeOffers' => $activeOffers,
                'totalClicks' => $totalClicks,
                'totalConversions' => $totalConversions,
                'conversionRate' => $conversionRate,
                'totalRevenue' => $totalRevenue,
                'totalCommissions' => $totalCommissions,
                'pendingPayouts' => $pendingPayouts,
                'pendingConversions' => $pendingConversions,
            ],
            'topAffiliates' => $topAffiliates,
            'topAdvertisers' => $topAdvertisers,
            'topOffers' => $topOffers,
            'recentConversions' => $recentConversions,
            'dailyStats' => $dailyStats,
            'dateRange' => $dateRange,
        ]);
    }
}
