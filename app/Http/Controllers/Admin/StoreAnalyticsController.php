<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StoreAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(StoreAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display platform-wide store analytics dashboard.
     */
    public function index(Request $request): Response
    {
        $period = $request->get('period', '30days');

        $analytics = $this->analyticsService->getPlatformAnalytics($period);

        return Inertia::render('Admin/StoreAnalytics/Index', [
            'analytics' => $analytics,
            'period' => $period,
        ]);
    }
}
