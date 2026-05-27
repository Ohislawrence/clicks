<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SpreadController extends Controller
{
    public function index(Request $request)
    {
        $range = (int) $request->get('range', 30);
        $startDate = now()->subDays($range)->startOfDay();

        // Overall totals
        $totals = Conversion::whereIn('status', ['approved', 'paid'])
            ->where('created_at', '>=', $startDate)
            ->select([
                DB::raw('SUM(conversion_value) as total_revenue'),
                DB::raw('SUM(advertiser_payout) as total_advertiser_payout'),
                DB::raw('SUM(commission_amount) as total_affiliate_commission'),
                DB::raw('SUM(platform_margin) as total_spread'),
                DB::raw('COUNT(*) as total_conversions'),
            ])
            ->first();

        // Pending spread (from pending conversions — not yet earned)
        $pendingSpread = Conversion::where('status', 'pending')
            ->where('created_at', '>=', $startDate)
            ->sum('platform_margin');

        // Spread by offer
        $spreadByOffer = Conversion::whereIn('status', ['approved', 'paid'])
            ->where('conversions.created_at', '>=', $startDate)
            ->join('offers', 'conversions.offer_id', '=', 'offers.id')
            ->select([
                'offers.id',
                'offers.name',
                'offers.commission_model',
                'offers.platform_spread_percentage',
                DB::raw('COUNT(conversions.id) as conversions_count'),
                DB::raw('SUM(conversions.conversion_value) as total_revenue'),
                DB::raw('SUM(conversions.advertiser_payout) as total_advertiser_payout'),
                DB::raw('SUM(conversions.commission_amount) as total_affiliate_commission'),
                DB::raw('SUM(conversions.platform_margin) as total_spread'),
            ])
            ->groupBy('offers.id', 'offers.name', 'offers.commission_model', 'offers.platform_spread_percentage')
            ->orderByDesc('total_spread')
            ->get();

        // Daily spread trend
        $dailySpread = Conversion::whereIn('status', ['approved', 'paid'])
            ->where('created_at', '>=', $startDate)
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(platform_margin) as spread'),
                DB::raw('SUM(commission_amount) as commissions'),
                DB::raw('SUM(conversion_value) as revenue'),
                DB::raw('COUNT(*) as conversions'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Commission model breakdown
        $modelBreakdown = Conversion::whereIn('status', ['approved', 'paid'])
            ->where('created_at', '>=', $startDate)
            ->select([
                'commission_model',
                DB::raw('COUNT(*) as conversions_count'),
                DB::raw('SUM(conversion_value) as total_revenue'),
                DB::raw('SUM(platform_margin) as total_spread'),
                DB::raw('SUM(commission_amount) as total_commissions'),
            ])
            ->groupBy('commission_model')
            ->get();

        // Recent conversions with spread detail
        $recentConversions = Conversion::with(['offer:id,name,commission_model,platform_spread_percentage', 'affiliate:id,name'])
            ->whereIn('status', ['approved', 'paid'])
            ->where('created_at', '>=', $startDate)
            ->where('platform_margin', '>', 0)
            ->latest()
            ->limit(50)
            ->get();

        return Inertia::render('Admin/Spread/Index', [
            'totals'             => $totals,
            'pendingSpread'      => (float) $pendingSpread,
            'spreadByOffer'      => $spreadByOffer,
            'dailySpread'        => $dailySpread,
            'modelBreakdown'     => $modelBreakdown,
            'recentConversions'  => $recentConversions,
            'range'              => $range,
        ]);
    }
}
