<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\User;
use App\Services\ReportingService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected ReportingService $reportingService;
    protected ExportService $exportService;

    public function __construct(ReportingService $reportingService, ExportService $exportService)
    {
        $this->reportingService = $reportingService;
        $this->exportService = $exportService;
    }

    /**
     * Display comprehensive reporting dashboard
     */
    public function index(Request $request)
    {
        $range = $request->input('range', '7days');
        $dateRange = $this->getDateRange($range);

        $filters = [
            'date_from' => $dateRange['from'],
            'date_to' => $dateRange['to'],
            'role' => 'admin',
        ];

        // Get period stats
        $currentStats = $this->reportingService->getStats($filters);
        $previousStats = $this->reportingService->getStats([
            'date_from' => Carbon::parse($dateRange['from'])->subDays(max($dateRange['days'], 1)),
            'date_to' => Carbon::parse($dateRange['to'])->subDays(max($dateRange['days'], 1)),
            'role' => 'admin',
        ]);

        // Stats summary with period-over-period changes
        $stats = [
            'total_clicks'             => $currentStats['clicks']['total'],
            'clicks_change'            => $this->calculateChange($currentStats['clicks']['total'], $previousStats['clicks']['total']),
            'total_conversions'        => $currentStats['conversions']['total'],
            'conversions_change'       => $this->calculateChange($currentStats['conversions']['total'], $previousStats['conversions']['total']),
            'conversion_rate'          => $currentStats['conversions']['rate'],
            'cr_change'                => round($currentStats['conversions']['rate'] - $previousStats['conversions']['rate'], 2),
            'total_revenue'            => $currentStats['revenue']['total'],
            'revenue_change'           => $this->calculateChange($currentStats['revenue']['total'], $previousStats['revenue']['total']),
            'platform_margin'          => $currentStats['platform_margin']['total'],
            'platform_margin_change'   => $this->calculateChange($currentStats['platform_margin']['total'], $previousStats['platform_margin']['total']),
            'avg_margin_percentage'    => $currentStats['platform_margin']['average_percentage'],
            'total_affiliate_commissions' => $currentStats['commissions']['total'],
        ];

        // Performance trend (daily)
        $performanceTrend = $this->reportingService->getDailyStats($filters);

        // Revenue by commission model — real data
        $revenueByModel = Conversion::whereBetween('created_at', [$dateRange['from'], $dateRange['to']])
            ->whereIn('status', ['approved', 'paid'])
            ->select('commission_model', DB::raw('SUM(conversion_value) as revenue'))
            ->groupBy('commission_model')
            ->get();

        $modelLabels = [
            'revshare' => 'RevShare',
            'cpa'      => 'CPA',
            'cpl'      => 'CPL',
            'pps'      => 'PPS',
            'ppl'      => 'PPL',
            'fixed'    => 'Fixed',
        ];
        $revenueByCategory = $revenueByModel->map(fn($row) => [
            'name'    => $modelLabels[$row->commission_model] ?? ucfirst($row->commission_model),
            'revenue' => (float) $row->revenue,
        ])->values()->toArray();

        // Fallback if no data
        if (empty($revenueByCategory)) {
            $revenueByCategory = [['name' => 'No Data', 'revenue' => 0]];
        }

        // Top affiliates by earnings in period
        $topAffiliates = User::role('affiliate')
            ->withCount(['clicks' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])])
            ->withSum(['conversions as total_earnings' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])->whereIn('status', ['approved', 'paid'])], 'commission_amount')
            ->withCount(['conversions as total_conversions' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])->whereIn('status', ['approved', 'paid'])])
            ->having(DB::raw('COALESCE(total_earnings, 0)'), '>', 0)
            ->orderByDesc('total_earnings')
            ->limit(5)
            ->get()
            ->map(fn($user) => [
                'id'               => $user->id,
                'name'             => $user->name,
                'email'            => $user->email,
                'tier'             => $user->tier,
                'total_earnings'   => (float) ($user->total_earnings ?? 0),
                'total_conversions'=> (int) ($user->total_conversions ?? 0),
            ]);

        // Top offers by conversions in period
        $topOffers = \App\Models\Offer::withCount(['clicks as clicks_count' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])])
            ->withCount(['conversions as total_conversions' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])->whereIn('status', ['approved', 'paid'])])
            ->withSum(['conversions as total_spread' => fn($q) => $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']])->whereIn('status', ['approved', 'paid'])], 'platform_margin')
            ->having(DB::raw('COALESCE(total_conversions, 0)'), '>', 0)
            ->orderByDesc('total_conversions')
            ->limit(5)
            ->get()
            ->map(fn($offer) => [
                'id'               => $offer->id,
                'name'             => $offer->name,
                'total_conversions'=> (int) ($offer->total_conversions ?? 0),
                'conversion_rate'  => ($offer->clicks_count ?? 0) > 0
                    ? round(($offer->total_conversions / $offer->clicks_count) * 100, 2)
                    : 0,
                'total_spread'     => (float) ($offer->total_spread ?? 0),
            ]);

        // Traffic sources by device type (real data)
        $deviceCounts = Click::whereBetween('created_at', [$dateRange['from'], $dateRange['to']])
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get();

        $totalDeviceClicks = $deviceCounts->sum('count') ?: 1;
        $deviceColors = ['desktop' => '#3b82f6', 'mobile' => '#10b981', 'tablet' => '#f59e0b', 'unknown' => '#9ca3af'];
        $trafficSources = $deviceCounts->map(fn($row) => [
            'name'       => ucfirst($row->device_type ?? 'Unknown'),
            'count'      => (int) $row->count,
            'percentage' => round(($row->count / $totalDeviceClicks) * 100, 1),
            'color'      => $deviceColors[$row->device_type ?? 'unknown'] ?? '#8b5cf6',
        ])->values()->toArray();

        if (empty($trafficSources)) {
            $trafficSources = [['name' => 'No Data', 'count' => 0, 'percentage' => 0, 'color' => '#9ca3af']];
        }

        // Security stats — real data
        $totalClicks = $currentStats['clicks']['total'];
        $invalidClicks = $currentStats['clicks']['invalid'];
        $validClicks = $currentStats['clicks']['valid'];
        $qualityScore = $totalClicks > 0 ? round(($validClicks / $totalClicks) * 100) : 100;

        $securityStats = [
            'blocked_clicks'    => $invalidClicks,
            'block_rate'        => (float) $currentStats['clicks']['fraud_rate'],
            'flagged_clicks'    => $invalidClicks, // invalid clicks ARE flagged/blocked
            'flag_rate'         => (float) $currentStats['clicks']['fraud_rate'],
            'active_blacklists' => \App\Models\Blacklist::where('is_active', true)->count(),
            'avg_quality_score' => $qualityScore,
        ];

        return Inertia::render('Admin/Reports/Index', [
            'stats'            => $stats,
            'topAffiliates'    => $topAffiliates,
            'topOffers'        => $topOffers,
            'performanceTrend' => $performanceTrend,
            'revenueByCategory'=> $revenueByCategory,
            'trafficSources'   => $trafficSources,
            'securityStats'    => $securityStats,
            'selectedRange'    => $range,
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
            case '90days':
                $from = now()->subDays(90);
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

    /**
     * Export stats as CSV
     */
    public function exportStats(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', now()->subDays(30)),
            'date_to' => $request->input('date_to', now()),
        ];

        $stats = $this->reportingService->getStats($filters);
        $csv = $this->exportService->exportReportStats($stats);
        $filename = $this->exportService->getExportFilename('stats');

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Export daily stats as CSV
     */
    public function exportDailyStats(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', now()->subDays(30)),
            'date_to' => $request->input('date_to', now()),
        ];

        $dailyStats = $this->reportingService->getDailyStats($filters);
        $csv = $this->exportService->exportDailyStats($dailyStats);
        $filename = $this->exportService->getExportFilename('daily_stats');

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Export clicks data
     */
    public function exportClicks(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'offer_id' => $request->input('offer_id'),
        ];

        $csv = $this->exportService->exportClicks($filters);
        $filename = $this->exportService->getExportFilename('clicks');

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Export conversions data
     */
    public function exportConversions(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'offer_id' => $request->input('offer_id'),
            'status' => $request->input('status'),
        ];

        $csv = $this->exportService->exportConversions($filters);
        $filename = $this->exportService->getExportFilename('conversions');

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}

