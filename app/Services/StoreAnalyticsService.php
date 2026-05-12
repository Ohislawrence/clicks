<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\StoreProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreAnalyticsService
{
    /**
     * Get analytics for a specific store.
     */
    public function getStoreAnalytics(Store $store, string $period = '30days'): array
    {
        $dates = $this->getDateRange($period);

        return [
            'overview' => $this->getStoreOverview($store, $dates),
            'revenue_chart' => $this->getRevenueChart($store, $dates),
            'orders_chart' => $this->getOrdersChart($store, $dates),
            'top_products' => $this->getTopProducts($store, $dates),
            'recent_orders' => $this->getRecentOrders($store, 10),
            'fulfillment_stats' => $this->getFulfillmentStats($store, $dates),
        ];
    }

    /**
     * Get platform-wide analytics for admin (all stores).
     */
    public function getPlatformAnalytics(string $period = '30days'): array
    {
        $dates = $this->getDateRange($period);

        return [
            'overview' => $this->getPlatformOverview($dates),
            'revenue_chart' => $this->getPlatformRevenueChart($dates),
            'orders_chart' => $this->getPlatformOrdersChart($dates),
            'top_stores' => $this->getTopStores($dates),
            'store_status' => $this->getStoreStatusBreakdown(),
            'subscription_revenue' => $this->getSubscriptionRevenue($dates),
            'recent_orders' => $this->getPlatformRecentOrders(10),
        ];
    }

    /**
     * Get store overview statistics.
     */
    protected function getStoreOverview(Store $store, array $dates): array
    {
        $currentPeriod = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value
            ')
            ->first();

        $previousPeriod = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['previous_start'], $dates['previous_end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue
            ')
            ->first();

        return [
            'total_revenue' => (float) ($currentPeriod->total_revenue ?? 0),
            'total_orders' => (int) ($currentPeriod->total_orders ?? 0),
            'average_order_value' => (float) ($currentPeriod->average_order_value ?? 0),
            'revenue_change' => $this->calculatePercentageChange(
                $previousPeriod->total_revenue ?? 0,
                $currentPeriod->total_revenue ?? 0
            ),
            'orders_change' => $this->calculatePercentageChange(
                $previousPeriod->total_orders ?? 0,
                $currentPeriod->total_orders ?? 0
            ),
            'total_products' => StoreProduct::where('store_id', $store->id)
                ->where('is_active', true)
                ->count(),
            'pending_orders' => StoreOrder::where('store_id', $store->id)
                ->where('payment_status', 'paid')
                ->where('fulfillment_status', 'pending')
                ->count(),
        ];
    }

    /**
     * Get platform overview statistics (all stores).
     */
    protected function getPlatformOverview(array $dates): array
    {
        $currentPeriod = StoreOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value,
                COUNT(DISTINCT store_id) as active_stores
            ')
            ->first();

        $previousPeriod = StoreOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['previous_start'], $dates['previous_end']])
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue
            ')
            ->first();

        return [
            'total_revenue' => (float) ($currentPeriod->total_revenue ?? 0),
            'total_orders' => (int) ($currentPeriod->total_orders ?? 0),
            'average_order_value' => (float) ($currentPeriod->average_order_value ?? 0),
            'active_stores' => (int) ($currentPeriod->active_stores ?? 0),
            'revenue_change' => $this->calculatePercentageChange(
                $previousPeriod->total_revenue ?? 0,
                $currentPeriod->total_revenue ?? 0
            ),
            'orders_change' => $this->calculatePercentageChange(
                $previousPeriod->total_orders ?? 0,
                $currentPeriod->total_orders ?? 0
            ),
            'total_stores' => Store::count(),
            'active_subscriptions' => Store::where('is_active', true)
                ->whereDate('subscription_end_date', '>=', now())
                ->count(),
        ];
    }

    /**
     * Get revenue chart data for a store.
     */
    protected function getRevenueChart(Store $store, array $dates): array
    {
        $groupBy = $this->getGroupByFormat($dates['start'], $dates['end']);

        $data = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw("
                {$groupBy['select']} as date,
                SUM(total) as revenue,
                COUNT(*) as orders
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->formatChartData($data, $dates, $groupBy['format']);
    }

    /**
     * Get platform revenue chart data (all stores).
     */
    protected function getPlatformRevenueChart(array $dates): array
    {
        $groupBy = $this->getGroupByFormat($dates['start'], $dates['end']);

        $data = StoreOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw("
                {$groupBy['select']} as date,
                SUM(total) as revenue,
                COUNT(*) as orders,
                COUNT(DISTINCT store_id) as stores
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->formatChartData($data, $dates, $groupBy['format']);
    }

    /**
     * Get orders chart data for a store.
     */
    protected function getOrdersChart(Store $store, array $dates): array
    {
        $groupBy = $this->getGroupByFormat($dates['start'], $dates['end']);

        $data = StoreOrder::where('store_id', $store->id)
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw("
                {$groupBy['select']} as date,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->formatChartData($data, $dates, $groupBy['format']);
    }

    /**
     * Get platform orders chart data (all stores).
     */
    protected function getPlatformOrdersChart(array $dates): array
    {
        $groupBy = $this->getGroupByFormat($dates['start'], $dates['end']);

        $data = StoreOrder::whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw("
                {$groupBy['select']} as date,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->formatChartData($data, $dates, $groupBy['format']);
    }

    /**
     * Get top products for a store.
     */
    protected function getTopProducts(Store $store, array $dates, int $limit = 5): array
    {
        return StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->get()
            ->flatMap(function ($order) {
                return collect($order->items)->map(function ($item) {
                    return [
                        'product_id' => $item['product_id'] ?? null,
                        'name' => $item['name'] ?? 'Unknown',
                        'quantity' => $item['quantity'] ?? 0,
                        'revenue' => ($item['price'] ?? 0) * ($item['quantity'] ?? 0),
                    ];
                });
            })
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product_id' => $items->first()['product_id'],
                    'name' => $items->first()['name'],
                    'total_quantity' => $items->sum('quantity'),
                    'total_revenue' => $items->sum('revenue'),
                    'orders_count' => $items->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get top stores (platform-wide).
     */
    protected function getTopStores(array $dates, int $limit = 10): array
    {
        return Store::select('stores.*')
            ->join('store_orders', 'stores.id', '=', 'store_orders.store_id')
            ->where('store_orders.payment_status', 'paid')
            ->whereBetween('store_orders.created_at', [$dates['start'], $dates['end']])
            ->selectRaw('
                stores.id,
                stores.name,
                stores.slug,
                COUNT(store_orders.id) as orders_count,
                SUM(store_orders.total) as total_revenue,
                AVG(store_orders.total) as average_order_value
            ')
            ->groupBy('stores.id', 'stores.name', 'stores.slug')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'orders_count' => (int) $store->orders_count,
                    'total_revenue' => (float) $store->total_revenue,
                    'average_order_value' => (float) $store->average_order_value,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent orders for a store.
     */
    protected function getRecentOrders(Store $store, int $limit = 10): array
    {
        return StoreOrder::where('store_id', $store->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total' => (float) $order->total,
                    'payment_status' => $order->payment_status,
                    'fulfillment_status' => $order->fulfillment_status,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get recent orders (platform-wide).
     */
    protected function getPlatformRecentOrders(int $limit = 10): array
    {
        return StoreOrder::with('store:id,name,slug')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'store_name' => $order->store->name ?? 'Unknown',
                    'store_slug' => $order->store->slug ?? null,
                    'customer_name' => $order->customer_name,
                    'total' => (float) $order->total,
                    'payment_status' => $order->payment_status,
                    'fulfillment_status' => $order->fulfillment_status,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get fulfillment statistics for a store.
     */
    protected function getFulfillmentStats(Store $store, array $dates): array
    {
        $stats = StoreOrder::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw('
                SUM(CASE WHEN fulfillment_status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN fulfillment_status = "processing" THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN fulfillment_status = "shipped" THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN fulfillment_status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN fulfillment_status = "cancelled" THEN 1 ELSE 0 END) as cancelled
            ')
            ->first();

        return [
            'pending' => (int) ($stats->pending ?? 0),
            'processing' => (int) ($stats->processing ?? 0),
            'shipped' => (int) ($stats->shipped ?? 0),
            'delivered' => (int) ($stats->delivered ?? 0),
            'cancelled' => (int) ($stats->cancelled ?? 0),
        ];
    }

    /**
     * Get store status breakdown (platform-wide).
     */
    protected function getStoreStatusBreakdown(): array
    {
        $total = Store::count();
        $active = Store::where('is_active', true)
            ->whereDate('subscription_end_date', '>=', now())
            ->count();
        $expiringSoon = Store::where('is_active', true)
            ->whereDate('subscription_end_date', '>=', now())
            ->whereDate('subscription_end_date', '<=', now()->addDays(7))
            ->count();
        $expired = Store::where('is_active', false)
            ->orWhereDate('subscription_end_date', '<', now())
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'expiring_soon' => $expiringSoon,
            'expired' => $expired,
        ];
    }

    /**
     * Get subscription revenue (platform-wide).
     */
    protected function getSubscriptionRevenue(array $dates): array
    {
        $revenue = DB::table('store_subscriptions')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->selectRaw('
                COUNT(*) as total_subscriptions,
                SUM(amount) as total_revenue,
                SUM(CASE WHEN billing_cycle = "monthly" THEN amount ELSE 0 END) as monthly_revenue,
                SUM(CASE WHEN billing_cycle = "yearly" THEN amount ELSE 0 END) as yearly_revenue
            ')
            ->first();

        return [
            'total_subscriptions' => (int) ($revenue->total_subscriptions ?? 0),
            'total_revenue' => (float) ($revenue->total_revenue ?? 0),
            'monthly_revenue' => (float) ($revenue->monthly_revenue ?? 0),
            'yearly_revenue' => (float) ($revenue->yearly_revenue ?? 0),
        ];
    }

    /**
     * Get date range based on period.
     */
    protected function getDateRange(string $period): array
    {
        $end = now();

        switch ($period) {
            case '7days':
                $start = now()->subDays(7);
                $previousStart = now()->subDays(14);
                $previousEnd = now()->subDays(7);
                break;
            case '30days':
                $start = now()->subDays(30);
                $previousStart = now()->subDays(60);
                $previousEnd = now()->subDays(30);
                break;
            case '90days':
                $start = now()->subDays(90);
                $previousStart = now()->subDays(180);
                $previousEnd = now()->subDays(90);
                break;
            case 'year':
                $start = now()->subYear();
                $previousStart = now()->subYears(2);
                $previousEnd = now()->subYear();
                break;
            default:
                $start = now()->subDays(30);
                $previousStart = now()->subDays(60);
                $previousEnd = now()->subDays(30);
        }

        return [
            'start' => $start,
            'end' => $end,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    /**
     * Get appropriate grouping format based on date range.
     */
    protected function getGroupByFormat(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end);

        if ($days <= 7) {
            // Group by day
            return [
                'select' => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                'format' => 'Y-m-d',
            ];
        } elseif ($days <= 90) {
            // Group by day
            return [
                'select' => "DATE_FORMAT(created_at, '%Y-%m-%d')",
                'format' => 'Y-m-d',
            ];
        } else {
            // Group by week
            return [
                'select' => "DATE_FORMAT(created_at, '%Y-%u')",
                'format' => 'Y-W',
            ];
        }
    }

    /**
     * Format chart data with all dates in range.
     */
    protected function formatChartData($data, array $dates, string $format): array
    {
        $formatted = [];
        $dataByDate = $data->keyBy('date');

        $current = $dates['start']->copy();
        while ($current->lte($dates['end'])) {
            $dateKey = $current->format($format);
            $item = $dataByDate->get($dateKey);

            $formatted[] = [
                'date' => $current->format('M d, Y'),
                'revenue' => (float) ($item->revenue ?? 0),
                'orders' => (int) ($item->orders ?? 0),
                'paid' => (int) ($item->paid ?? 0),
                'pending' => (int) ($item->pending ?? 0),
                'failed' => (int) ($item->failed ?? 0),
                'stores' => (int) ($item->stores ?? 0),
            ];

            if ($format === 'Y-W') {
                $current->addWeek();
            } else {
                $current->addDay();
            }
        }

        return $formatted;
    }

    /**
     * Calculate percentage change.
     */
    protected function calculatePercentageChange($old, $new): float
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return round((($new - $old) / $old) * 100, 2);
    }
}
