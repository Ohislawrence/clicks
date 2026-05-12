<template>
    <AppLayout title="Store Analytics">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Store Analytics - {{ store.name }}
                </h2>
                <select
                    v-model="selectedPeriod"
                    @change="changePeriod"
                    class="rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="90days">Last 90 Days</option>
                    <option value="year">Last Year</option>
                </select>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Overview Stats -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Total Revenue"
                        :value="`₦${formatNumber(analytics.overview.total_revenue)}`"
                        :change="analytics.overview.revenue_change"
                        icon="currency-naira"
                    />
                    <StatCard
                        title="Total Orders"
                        :value="formatNumber(analytics.overview.total_orders)"
                        :change="analytics.overview.orders_change"
                        icon="shopping-cart"
                    />
                    <StatCard
                        title="Average Order Value"
                        :value="`₦${formatNumber(analytics.overview.average_order_value)}`"
                        icon="chart-bar"
                    />
                    <StatCard
                        title="Pending Orders"
                        :value="formatNumber(analytics.overview.pending_orders)"
                        icon="clock"
                        variant="warning"
                    />
                </div>

                <!-- Revenue Chart -->
                <div class="mb-8 overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Revenue Over Time</h3>
                        <apexchart
                            type="area"
                            height="350"
                            :options="revenueChartOptions"
                            :series="revenueChartSeries"
                        />
                    </div>
                </div>

                <!-- Orders Chart and Top Products -->
                <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                    <!-- Orders Chart -->
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Orders by Status</h3>
                            <apexchart
                                type="donut"
                                height="300"
                                :options="ordersChartOptions"
                                :series="ordersChartSeries"
                            />
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Top Products</h3>
                            <div v-if="analytics.top_products.length > 0" class="space-y-4">
                                <div
                                    v-for="(product, index) in analytics.top_products"
                                    :key="product.product_id"
                                    class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ product.name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ product.total_quantity }} sold · {{ product.orders_count }} orders
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-blue-600">
                                            ₦{{ formatNumber(product.total_revenue) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-center text-gray-500">No product data available</p>
                        </div>
                    </div>
                </div>

                <!-- Fulfillment Stats and Recent Orders -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Fulfillment Stats -->
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Fulfillment Status</h3>
                            <div class="space-y-3">
                                <FulfillmentBar label="Pending" :count="analytics.fulfillment_stats.pending" color="yellow" />
                                <FulfillmentBar label="Processing" :count="analytics.fulfillment_stats.processing" color="blue" />
                                <FulfillmentBar label="Shipped" :count="analytics.fulfillment_stats.shipped" color="purple" />
                                <FulfillmentBar label="Delivered" :count="analytics.fulfillment_stats.delivered" color="green" />
                                <FulfillmentBar label="Cancelled" :count="analytics.fulfillment_stats.cancelled" color="red" />
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-xl">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Recent Orders</h3>
                            <div v-if="analytics.recent_orders.length > 0" class="space-y-3">
                                <div
                                    v-for="order in analytics.recent_orders"
                                    :key="order.id"
                                    class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">{{ order.order_number }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ order.customer_name }} · {{ order.created_at }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-blue-600">₦{{ formatNumber(order.total) }}</p>
                                        <span :class="statusClass(order.payment_status)" class="text-xs">
                                            {{ order.payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-center text-gray-500">No recent orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/Analytics/StatCard.vue';
import FulfillmentBar from '@/Components/Analytics/FulfillmentBar.vue';

const props = defineProps({
    store: Object,
    analytics: Object,
    period: String,
});

const selectedPeriod = ref(props.period);

const changePeriod = () => {
    router.get(route('advertiser.store.analytics'), { period: selectedPeriod.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-NG').format(value || 0);
};

const statusClass = (status) => {
    const classes = {
        paid: 'text-green-600',
        pending: 'text-yellow-600',
        failed: 'text-red-600',
    };
    return classes[status] || 'text-gray-500';
};

// Revenue Chart Configuration
const revenueChartSeries = computed(() => [{
    name: 'Revenue',
    data: props.analytics.revenue_chart.map(item => item.revenue),
}]);

const revenueChartOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        background: 'transparent',
    },
    theme: { mode: 'light' },
    colors: ['#2563eb'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
        },
    },
    xaxis: {
        categories: props.analytics.revenue_chart.map(item => item.date),
        labels: { style: { colors: '#6B7280' } },
    },
    yaxis: {
        labels: {
            style: { colors: '#6B7280' },
            formatter: (value) => `₦${formatNumber(value)}`,
        },
    },
    tooltip: {
        theme: 'light',
        y: { formatter: (value) => `₦${formatNumber(value)}` },
    },
    grid: {
        borderColor: '#E5E7EB',
        strokeDashArray: 4,
    },
}));

// Orders Chart Configuration
const ordersChartSeries = computed(() => {
    const stats = props.analytics.fulfillment_stats;
    return [stats.pending, stats.processing, stats.shipped, stats.delivered, stats.cancelled];
});

const ordersChartOptions = computed(() => ({
    chart: {
        type: 'donut',
        background: 'transparent',
    },
    theme: { mode: 'light' },
    colors: ['#EAB308', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
    labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
    legend: {
        position: 'bottom',
        labels: { colors: '#6B7280' },
    },
    dataLabels: {
        enabled: true,
        style: { fontSize: '14px', colors: ['#fff'] },
    },
    tooltip: {
        theme: 'light',
        y: { formatter: (value) => `${value} orders` },
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Orders',
                        color: '#6B7280',
                        formatter: () => formatNumber(props.analytics.overview.total_orders),
                    },
                },
            },
        },
    },
}));
</script>
