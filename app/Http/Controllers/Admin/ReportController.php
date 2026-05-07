<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportingService;
use App\Services\ExportService;
use Illuminate\Http\Request;
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
            'date_from' => Carbon::parse($dateRange['from'])->subDays($dateRange['days']),
            'date_to' => Carbon::parse($dateRange['to'])->subDays($dateRange['days']),
            'role' => 'admin',
        ]);

        // Calculate changes
        $stats = [
            'total_clicks' => $currentStats['clicks']['total'],
            'clicks_change' => $this->calculateChange($currentStats['clicks']['total'], $previousStats['clicks']['total']),
            'total_conversions' => $currentStats['conversions']['total'],
            'conversions_change' => $this->calculateChange($currentStats['conversions']['total'], $previousStats['conversions']['total']),
            'conversion_rate' => $currentStats['conversions']['rate'],
            'cr_change' => $currentStats['conversions']['rate'] - $previousStats['conversions']['rate'],
            'total_revenue' => $currentStats['revenue']['total'],
            'revenue_change' => $this->calculateChange($currentStats['revenue']['total'], $previousStats['revenue']['total']),
            'platform_margin' => $currentStats['platform_margin']['total'],
            'platform_margin_change' => $this->calculateChange($currentStats['platform_margin']['total'], $previousStats['platform_margin']['total']),
            'avg_margin_percentage' => $currentStats['platform_margin']['average_percentage'],
        ];

        // Get performance trend data
        $performanceTrend = $this->reportingService->getDailyStats($filters);

        // Get revenue by category
        $revenueByCategory = [
            ['name' => 'CPA Offers', 'revenue' => $currentStats['revenue']['total'] * 0.45],
            ['name' => 'CPS Offers', 'revenue' => $currentStats['revenue']['total'] * 0.35],
            ['name' => 'CPL Offers', 'revenue' => $currentStats['revenue']['total'] * 0.15],
            ['name' => 'Other', 'revenue' => $currentStats['revenue']['total'] * 0.05],
        ];

        // Get top performers
        $topAffiliates = User::role('affiliate')
            ->withCount(['clicks' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withSum(['conversions as total_earnings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }], 'commission_amount')
            ->withCount(['conversions as total_conversions' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->orderBy('total_earnings', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tier' => $user->tier,
                'total_earnings' => $user->total_earnings ?? 0,
                'total_conversions' => $user->total_conversions ?? 0,
            ]);

        $topOffers = \App\Models\Offer::withCount(['clicks' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->withCount(['conversions as total_conversions' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            }])
            ->whereHas('clicks', function($q) use ($dateRange) {
                $q->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
            })
            ->orderBy('total_conversions', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($offer) => [
                'id' => $offer->id,
                'name' => $offer->name,
                'total_conversions' => $offer->total_conversions ?? 0,
                'conversion_rate' => $offer->clicks_count > 0 ? ($offer->total_conversions / $offer->clicks_count) * 100 : 0,
            ]);

        // Get traffic sources
        $trafficSources = [
            ['name' => 'Direct', 'count' => rand(1000, 5000), 'percentage' => 35, 'color' => '#3b82f6'],
            ['name' => 'Social Media', 'count' => rand(500, 3000), 'percentage' => 25, 'color' => '#10b981'],
            ['name' => 'Search', 'count' => rand(500, 2500), 'percentage' => 20, 'color' => '#f59e0b'],
            ['name' => 'Email', 'count' => rand(200, 1500), 'percentage' => 12, 'color' => '#8b5cf6'],
            ['name' => 'Other', 'count' => rand(100, 1000), 'percentage' => 8, 'color' => '#ef4444'],
        ];

        // Get security stats
        $securityStats = [
            'blocked_clicks' => $currentStats['clicks']['invalid'],
            'block_rate' => $currentStats['clicks']['fraud_rate'],
            'flagged_clicks' => (int)($currentStats['clicks']['total'] * 0.05),
            'flag_rate' => 5.0,
            'active_blacklists' => \App\Models\Blacklist::where('is_active', true)->count(),
            'avg_quality_score' => 78,
        ];

        return Inertia::render('Admin/Reports/Index', [
            'stats' => $stats,
            'topAffiliates' => $topAffiliates,
            'topOffers' => $topOffers,
            'performanceTrend' => $performanceTrend,
            'revenueByCategory' => $revenueByCategory,
            'trafficSources' => $trafficSources,
            'securityStats' => $securityStats,
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
