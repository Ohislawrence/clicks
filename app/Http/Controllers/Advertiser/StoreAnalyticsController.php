<?php

namespace App\Http\Controllers\Advertiser;

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
     * Display store analytics dashboard.
     */
    public function index(Request $request, $storeId): Response
    {
        $store = $request->user()->stores()->findOrFail($storeId);

        if (!$store) {
            return Inertia::render('Advertiser/Store/NoStore');
        }

        $period = $request->get('period', '30days');

        $analytics = $this->analyticsService->getStoreAnalytics($store, $period);

        return Inertia::render('Advertiser/Store/Analytics', [
            'store' => $store->load('plan'),
            'analytics' => $analytics,
            'period' => $period,
        ]);
    }
}
