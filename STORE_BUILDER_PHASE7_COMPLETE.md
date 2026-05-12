# Store Builder - Phase 7 Complete
## Analytics & Reporting for Store Performance

**Status:** ✅ Complete  
**Implementation Date:** May 8, 2026  
**Phase Duration:** ~3 hours

---

## Overview

Phase 7 implements comprehensive analytics and reporting dashboards for both advertisers and admin, providing real-time insights into store performance, revenue trends, order analytics, and product performance using ApexCharts for beautiful visualizations.

This phase enables:
- **Advertiser Analytics**: Individual store performance metrics and insights
- **Admin Analytics**: Platform-wide store analytics and revenue tracking
- **Data-Driven Decisions**: Visual charts and key performance indicators (KPIs)
- **Period Filtering**: 7 days, 30 days, 90 days, or yearly comparisons

---

## Features Implemented

### 1. Analytics Service (StoreAnalyticsService)

Centralized service that calculates all analytics metrics for both individual stores and platform-wide data.

**Key Methods:**

**For Individual Stores:**
- `getStoreAnalytics()` - Complete analytics data for a specific store
- `getStoreOverview()` - Revenue, orders, average order value with comparisons
- `getRevenueChart()` - Time-series revenue data
- `getOrdersChart()` - Orders by payment status over time
- `getTopProducts()` - Best-selling products by revenue
- `getRecentOrders()` - Latest orders with status
- `getFulfillmentStats()` - Breakdown by fulfillment status

**For Platform (Admin):**
- `getPlatformAnalytics()` - Complete platform-wide analytics
- `getPlatformOverview()` - Total revenue, orders, active stores
- `getPlatformRevenueChart()` - Platform revenue trends
- `getPlatformOrdersChart()` - Platform order trends
- `getTopStores()` - Top performing stores by revenue
- `getStoreStatusBreakdown()` - Active, expiring, expired counts
- `getSubscriptionRevenue()` - Store subscription revenue tracking
- `getPlatformRecentOrders()` - Recent orders across all stores

**Smart Features:**
- Automatic date range calculations with previous period comparisons
- Percentage change calculations (↑ increase / ↓ decrease)
- Dynamic grouping (daily for short periods, weekly for long periods)
- Gap-filling for missing data points in charts
- Efficient database queries with aggregations

### 2. Advertiser Analytics Dashboard

**Route:** `/advertiser/store/analytics`  
**Controller:** `Advertiser\StoreAnalyticsController`  
**Page:** `Advertiser/Store/Analytics.vue`

**Features:**

**Overview Statistics (Top Cards):**
- Total Revenue (₦) with % change vs previous period
- Total Orders with % change
- Average Order Value (₦)
- Pending Orders (warning badge)

**Revenue Chart:**
- Area chart showing revenue over time
- Smooth gradient fill
- Hover tooltips with formatted values
- Responsive to period selection

**Orders Chart:**
- Donut chart showing fulfillment status breakdown
- Color-coded: Pending (yellow), Processing (blue), Shipped (purple), Delivered (green), Cancelled (red)
- Center displays total orders count
- Interactive legend

**Top Products Section:**
- Ranked list (1-5) of best-selling products
- Shows quantity sold, order count, and total revenue
- Visual ranking with colored badges

**Fulfillment Status Breakdown:**
- Progress bars for each fulfillment status
- Color-coded by status
- Shows order counts

**Recent Orders:**
- List of last 10 orders
- Shows order number, customer, amount, status
- Quick access to order details

**Period Filter:**
- Dropdown to change time period
- Options: 7 days, 30 days, 90 days, 1 year
- Instant refresh without page reload (preserveState)

### 3. Admin Analytics Dashboard

**Route:** `/admin/store-analytics`  
**Controller:** `Admin\StoreAnalyticsController`  
**Page:** `Admin/StoreAnalytics/Index.vue`

**Features:**

**Platform Overview Statistics:**
- Total Revenue across all stores with % change
- Total Orders across all stores with % change
- Active Stores (stores with orders in period)
- Average Order Value platform-wide

**Store Status Dashboard:**
- Total Stores count
- Active Stores (with valid subscriptions)
- Stores Expiring Soon (within 7 days)
- Expired Stores

**Platform Revenue Chart:**
- Area chart of total platform revenue over time
- Shows combined revenue from all stores
- Formatted currency tooltips

**Orders Over Time Chart:**
- Stacked bar chart showing orders by status
- Paid (green), Pending (yellow), Failed (red)
- Shows order volume trends
- Legend toggle for filtering

**Subscription Revenue Breakdown:**
- Total Subscriptions count
- Total Subscription Revenue (₦)
- Monthly Revenue breakdown
- Yearly Revenue breakdown
- Visual cards with formatted values

**Top Stores by Revenue:**
- Ranked list of top 10 performing stores
- Store name, order count, average order value
- Total revenue earned
- Visual ranking badges

**Recent Orders (Platform-wide):**
- Latest 10 orders from all stores
- Shows store name, order number, customer, amount
- Status indicators
- Quick store identification

**Period Filter:**
- Same as advertiser dashboard
- Affects all platform metrics

### 4. Visualization Components

**StatCard Component:**
- Reusable KPI card
- Icon support (currency, shopping cart, chart, clock, store)
- Color-coded change indicators (↑ green / ↓ red)
- Variant support (primary, warning, danger)
- Consistent styling with theme

**FulfillmentBar Component:**
- Horizontal progress bar
- Color-coded by status
- Shows count and percentage
- Smooth animations

**ApexCharts Integration:**
- Area charts for revenue trends
- Donut charts for status breakdowns
- Bar charts for order volumes
- Stacked charts for multi-series data
- Dark theme configuration
- Responsive design
- Interactive tooltips
- Formatted values (currency, numbers)

---

## Files Created

### Services
```
app/Services/StoreAnalyticsService.php  (584 lines)
```

### Controllers
```
app/Http/Controllers/Advertiser/StoreAnalyticsController.php  (42 lines)
app/Http/Controllers/Admin/StoreAnalyticsController.php       (38 lines)
```

### Pages (Vue Components)
```
resources/js/Pages/Advertiser/Store/Analytics.vue         (281 lines)
resources/js/Pages/Admin/StoreAnalytics/Index.vue         (308 lines)
```

### Reusable Components
```
resources/js/Components/Analytics/StatCard.vue             (71 lines)
resources/js/Components/Analytics/FulfillmentBar.vue       (43 lines)
```

---

## Files Modified

### routes/web.php
**Added analytics routes:**

**Advertiser Route:**
```php
Route::get('/store/analytics', [StoreAnalyticsController::class, 'index'])
    ->name('store.analytics');
```

**Admin Route:**
```php
Route::get('/store-analytics', [Admin\StoreAnalyticsController::class, 'index'])
    ->name('store-analytics');
```

---

## Database Queries

### Performance Optimizations

**1. Aggregated Queries:**
- All stats calculated in single queries using SQL aggregations
- `COUNT()`, `SUM()`, `AVG()` used for efficiency
- `CASE WHEN` for conditional aggregations

**2. Eager Loading:**
- Store relationships loaded efficiently
- Recent orders include store data

**3. Indexed Fields:**
- `store_id` indexed in store_orders
- `payment_status` for filtering
- `created_at` for date range queries

**4. Query Example (Revenue Overview):**
```sql
SELECT 
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as average_order_value
FROM store_orders
WHERE store_id = ? 
    AND payment_status = 'paid'
    AND created_at BETWEEN ? AND ?
```

---

## Period Filtering

### Available Periods

**7 Days:**
- Shows daily data points
- Compares to previous 7 days
- Best for recent trends

**30 Days (Default):**
- Shows daily data points
- Compares to previous 30 days
- Standard monthly view

**90 Days:**
- Shows daily data points (or weekly if too many)
- Compares to previous 90 days
- Quarterly overview

**1 Year:**
- Shows weekly data points
- Compares to previous year
- Long-term trends

### Automatic Grouping

The service intelligently groups data:
- ≤7 days: Daily grouping
- ≤90 days: Daily grouping
- >90 days: Weekly grouping

This prevents chart overcrowding while maintaining detail.

---

## Chart Configurations

### Area Chart (Revenue Over Time)

**Configuration:**
```javascript
{
    type: 'area',
    colors: ['#10B981'], // Emerald
    stroke: { curve: 'smooth', width: 2 },
    fill: { 
        type: 'gradient',
        gradient: { opacityFrom: 0.4, opacityTo: 0.1 }
    },
    theme: { mode: 'dark' },
    tooltip: { y: { formatter: (value) => ₦{formatted} } }
}
```

**Features:**
- Smooth curves for better visualization
- Gradient fill for emphasis
- Dark theme matching app design
- Currency-formatted tooltips

### Donut Chart (Fulfillment Status)

**Configuration:**
```javascript
{
    type: 'donut',
    colors: ['#EAB308', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
    labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
    donut: { 
        size: '65%',
        labels: { total: { show: true } }
    }
}
```

**Features:**
- Color-coded by status
- Center shows total count
- Interactive legend
- Percentage labels

### Bar Chart (Orders Over Time)

**Configuration:**
```javascript
{
    type: 'bar',
    stacked: true,
    colors: ['#10B981', '#EAB308', '#EF4444'],
    dataLabels: { enabled: false }
}
```

**Features:**
- Stacked for comparison
- Status color coding
- Clean visualization
- Hover tooltips

---

## Testing Guide

### 1. Test Advertiser Analytics

**Prerequisites:**
- Create store with products
- Create test orders (mix of paid/pending/failed)
- Vary order dates across different periods

**Test Steps:**

**Access Dashboard:**
```
1. Log in as advertiser with store
2. Navigate to /advertiser/store/analytics
3. Verify page loads without errors
```

**Verify Overview Stats:**
```
1. Check Total Revenue displays correctly
2. Check Total Orders count
3. Check Average Order Value calculation
4. Check Pending Orders count
5. Verify percentage changes (should be 0% if no previous data)
```

**Test Revenue Chart:**
```
1. Verify chart renders
2. Hover over data points to see tooltips
3. Check currency formatting (₦)
4. Verify dates on x-axis
```

**Test Period Filter:**
```
1. Change period to "Last 7 Days"
2. Verify data updates
3. Change to "Last 90 Days"
4. Verify chart adjusts
5. Try "Last Year"
6. Check weekly grouping for long periods
```

**Test Top Products:**
```
1. Verify products are ranked by revenue
2. Check quantity sold is correct
3. Check order count matches
4. Verify revenue formatting
```

**Test Recent Orders:**
```
1. Verify last 10 orders displayed
2. Check order numbers are correct
3. Verify customer names shown
4. Check status badges colored correctly
```

### 2. Test Admin Analytics

**Prerequisites:**
- Multiple stores with orders
- Mix of active/expired stores
- Subscription payments recorded

**Test Steps:**

**Access Dashboard:**
```
1. Log in as admin
2. Navigate to /admin/store-analytics
3. Verify page loads
```

**Verify Platform Overview:**
```
1. Check Total Revenue (all stores)
2. Check Total Orders count
3. Check Active Stores count
4. Check Average Order Value
```

**Verify Store Status:**
```
1. Check Total Stores count matches database
2. Check Active count (subscriptions valid)
3. Check Expiring Soon count (within 7 days)
4. Check Expired count
```

**Test Platform Charts:**
```
1. Verify revenue chart shows combined data
2. Check orders chart with stacked bars
3. Test tooltips
4. Verify legends
```

**Test Subscription Revenue:**
```
1. Check total subscriptions count
2. Verify total subscription revenue
3. Check monthly/yearly breakdown
4. Verify calculations match store_subscriptions table
```

**Test Top Stores:**
```
1. Verify stores ranked by revenue
2. Check order counts are correct
3. Verify average order values
4. Check store names display
```

**Test Period Filtering:**
```
1. Change period to different options
2. Verify all metrics update
3. Check charts re-render
4. Verify data consistency
```

### 3. Test Edge Cases

**Empty Store (No Orders):**
```
1. Create new store with no orders
2. Access analytics page
3. Verify graceful handling (shows 0 for all metrics)
4. Verify charts display empty state or zeros
5. Check no errors in console
```

**Single Order:**
```
1. Create store with only 1 order
2. Check analytics display correctly
3. Verify percentage change handles division by zero
4. Check charts render with minimal data
```

**Large Dataset:**
```
1. Create store with 1000+ orders
2. Test analytics load time
3. Verify charts render without lag
4. Check pagination or limiting works
```

**Different Date Ranges:**
```
1. Create orders with various dates
2. Test each period filter
3. Verify correct date ranges applied
4. Check comparison periods calculated correctly
```

---

## Performance Considerations

### 1. Query Optimization

**Current Implementation:**
- Single aggregated queries per metric
- Indexed fields for fast filtering
- Eager loading for relationships
- No N+1 query problems

**Expected Performance:**
- <200ms for individual store analytics
- <500ms for platform-wide analytics (100 stores)
- Chart data processing: <50ms

### 2. Caching Strategy (Optional Future Enhancement)

**Recommended Caching:**
```php
Cache::remember("store-analytics-{$store->id}-{$period}", 3600, function () {
    return $this->analyticsService->getStoreAnalytics($store, $period);
});
```

**Benefits:**
- Reduces database load
- Faster page loads
- 1-hour cache duration reasonable for analytics
- Cache invalidation on new orders

### 3. Frontend Performance

**Optimizations:**
- ApexCharts lazy loaded
- Charts only render visible data
- Period changes use preserveState (no full reload)
- Responsive breakpoints for mobile

---

## Mobile Responsiveness

### Breakpoints

**Stats Cards:**
- Mobile (sm): 1 column
- Tablet (md): 2 columns
- Desktop (lg): 4 columns

**Charts:**
- Mobile: Full width, reduced height
- Tablet: 2 columns for side-by-side charts
- Desktop: 2 columns with larger charts

**Tables/Lists:**
- Mobile: Stacked layout
- Tablet: Flex layout
- Desktop: Grid layout

### Touch Interactions

**Charts:**
- Touch to view tooltips
- Pinch to zoom (ApexCharts native)
- Swipe for long charts

---

## Troubleshooting

### Analytics Page Blank

**Check 1: Verify route registered**
```bash
php artisan route:list | grep -i analytics
# Should show advertiser.store.analytics and admin.store-analytics
```

**Check 2: Check controller**
```bash
# Test controller can be instantiated
php artisan tinker
app(App\Http\Controllers\Advertiser\StoreAnalyticsController::class);
```

**Check 3: Check service**
```bash
php artisan tinker
$service = app(App\Services\StoreAnalyticsService::class);
$store = App\Models\Store::first();
$analytics = $service->getStoreAnalytics($store, '30days');
dump($analytics);
```

**Solution:** Verify all files created and no syntax errors

### Charts Not Rendering

**Check 1: ApexCharts installed**
```bash
npm list vue3-apexcharts
# Should show version 1.11.1 or higher
```

**Check 2: Build successful**
```bash
npm run build
# Should complete without errors
```

**Check 3: Check browser console**
```javascript
// Open DevTools Console
// Look for ApexCharts errors
```

**Solution:** Clear cache and rebuild: `npm run build`

### Wrong Data Displayed

**Check 1: Verify period parameter**
```
URL should have ?period=30days
Check dropdown value matches
```

**Check 2: Check date ranges**
```bash
php artisan tinker
use Carbon\Carbon;
$start = Carbon::now()->subDays(30);
$end = Carbon::now();
$orders = App\Models\StoreOrder::whereBetween('created_at', [$start, $end])->count();
```

**Check 3: Check data in database**
```sql
SELECT COUNT(*), SUM(total) 
FROM store_orders 
WHERE store_id = 1 AND payment_status = 'paid';
```

**Solution:** Verify orders exist and have correct dates

### Slow Loading

**Check 1: Check number of stores/orders**
```bash
php artisan tinker
App\Models\Store::count();
App\Models\StoreOrder::count();
```

**Check 2: Enable query logging**
```php
// Add to controller temporarily
DB::enableQueryLog();
// ... analytics call ...
dd(DB::getQueryLog());
```

**Check 3: Add indexes**
```bash
php artisan make:migration add_indexes_to_store_orders_table
```

**Solution:** Add caching for large datasets

---

## Future Enhancements

### Phase 7.1: Advanced Analytics (Optional)

**Customer Analytics:**
- Customer lifetime value (CLV)
- Repeat customer rate
- Customer acquisition cost
- Geographic distribution

**Product Analytics:**
- Product conversion rates
- Inventory turnover
- Product margin analysis
- Category performance

**Marketing Analytics:**
- Traffic sources
- Conversion funnels
- Attribution tracking
- Campaign performance

### Phase 7.2: Export & Reporting

**PDF Reports:**
- Generate PDF analytics reports
- Email scheduled reports
- Custom date ranges
- Brand customization

**CSV Exports:**
- Export order data
- Export product performance
- Export customer data
- Scheduled exports

**Email Digests:**
- Weekly performance summaries
- Monthly revenue reports
- Alert on significant changes
- Custom report scheduling

### Phase 7.3: Real-Time Analytics

**Live Dashboard:**
- WebSocket integration
- Real-time order notifications
- Live revenue counter
- Active visitors tracking

**Push Notifications:**
- New order alerts
- Daily/weekly summaries
- Milestone achievements
- Performance warnings

---

## Summary

Phase 7 successfully implements comprehensive analytics for the Store Builder:

✅ **Service Layer:** StoreAnalyticsService with 20+ methods  
✅ **Advertiser Dashboard:** Complete store performance analytics  
✅ **Admin Dashboard:** Platform-wide store analytics  
✅ **Visualizations:** ApexCharts integration with 3 chart types  
✅ **Reusable Components:** StatCard, FulfillmentBar  
✅ **Period Filtering:** 4 time periods with comparison  
✅ **Performance:** Optimized queries with aggregations  
✅ **Responsive Design:** Mobile-friendly layouts  
✅ **Build Successful:** No errors, production-ready

**Total Implementation Time:** ~3 hours  
**Lines of Code Added:** ~1,400 lines  
**Files Created:** 6  
**Files Modified:** 1 (routes)

---

## Related Documentation

- [STORE_BUILDER_PHASE1_COMPLETE.md](STORE_BUILDER_PHASE1_COMPLETE.md) - Database models and migrations
- [STORE_BUILDER_PHASE4_COMPLETE.md](STORE_BUILDER_PHASE4_COMPLETE.md) - Public storefront
- [STORE_BUILDER_PHASE5_COMPLETE.md](STORE_BUILDER_PHASE5_COMPLETE.md) - Payment integration
- [STORE_BUILDER_PHASE6_COMPLETE.md](STORE_BUILDER_PHASE6_COMPLETE.md) - Jobs & notifications
- [PAYMENT_GATEWAY_SETUP_GUIDE.md](PAYMENT_GATEWAY_SETUP_GUIDE.md) - Payment configuration guide

**Phase 7 Status:** ✅ COMPLETE

**Store Builder Feature:** 100% COMPLETE (All 7 Phases Done!)
