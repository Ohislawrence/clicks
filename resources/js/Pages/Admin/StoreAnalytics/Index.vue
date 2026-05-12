<template>
    <AppLayout title="Store Analytics - Platform">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-neutral-100">
                    Store Analytics - Platform Overview
                </h2>
                <select
                    v-model="selectedPeriod"
                    @change="changePeriod"
                    class="rounded-md border-neutral-700 bg-neutral-800 text-neutral-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
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
                        title="Active Stores"
                        :value="formatNumber(analytics.overview.active_stores)"
                        icon="store"
                    />
                    <StatCard
                        title="Average Order Value"
                        :value="`₦${formatNumber(analytics.overview.average_order_value)}`"
                        icon="chart-bar"
                    />
                </div>

                <!-- Store Status Breakdown -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-4">
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-neutral-400">Total Stores</p>
                            <p class="mt-2 text-3xl font-bold text-neutral-100">
                                {{ formatNumber(analytics.store_status.total) }}
                            </p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-neutral-400">Active</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-400">
                                {{ formatNumber(analytics.store_status.active) }}
                            </p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-neutral-400">Expiring Soon</p>
                            <p class="mt-2 text-3xl font-bold text-yellow-400">
                                {{ formatNumber(analytics.store_status.expiring_soon) }}
                            </p>
                        </div>
                    </div>
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-neutral-400">Expired</p>
                            <p class="mt-2 text-3xl font-bold text-red-400">
                                {{ formatNumber(analytics.store_status.expired) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="mb-8 overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-neutral-100">Platform Revenue Over Time</h3>
                        <apexchart
                            type="area"
                            height="350"
                            :options="revenueChartOptions"
                            :series="revenueChartSeries"
                        />
                    </div>
                </div>

                <!-- Orders Chart and Subscription Revenue -->
                <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                    <!-- Orders Chart -->
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-neutral-100">Orders Over Time</h3>
                            <apexchart
                                type="bar"
                                height="300"
                                :options="ordersChartOptions"
                                :series="ordersChartSeries"
                            />
                        </div>
                    </div>

                    <!-- Subscription Revenue -->
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-neutral-100">Subscription Revenue</h3>
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-neutral-800">
                                    <p class="text-sm text-neutral-400">Total Subscriptions</p>
                                    <p class="mt-1 text-2xl font-bold text-neutral-100">
                                        {{ formatNumber(analytics.subscription_revenue.total_subscriptions) }}
                                    </p>
                                </div>
                                <div class="p-4 rounded-lg bg-neutral-800">
                                    <p class="text-sm text-neutral-400">Total Revenue</p>
                                    <p class="mt-1 text-2xl font-bold text-emerald-400">
                                        ₦{{ formatNumber(analytics.subscription_revenue.total_revenue) }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 rounded-lg bg-neutral-800">
                                        <p class="text-sm text-neutral-400">Monthly</p>
                                        <p class="mt-1 text-lg font-semibold text-neutral-100">
                                            ₦{{ formatNumber(analytics.subscription_revenue.monthly_revenue) }}
                                        </p>
                                    </div>
                                    <div class="p-4 rounded-lg bg-neutral-800">
                                        <p class="text-sm text-neutral-400">Yearly</p>
                                        <p class="mt-1 text-lg font-semibold text-neutral-100">
                                            ₦{{ formatNumber(analytics.subscription_revenue.yearly_revenue) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Stores and Recent Orders -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Top Stores -->
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-neutral-100">Top Stores by Revenue</h3>
                            <div v-if="analytics.top_stores.length > 0" class="space-y-4">
                                <div
                                    v-for="(store, index) in analytics.top_stores"
                                    :key="store.id"
                                    class="flex items-center justify-between p-3 rounded-lg bg-neutral-800"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-neutral-100">{{ store.name }}</p>
                                            <p class="text-sm text-neutral-400">
                                                {{ store.orders_count }} orders · ₦{{ formatNumber(store.average_order_value) }} avg
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-emerald-400">
                                            ₦{{ formatNumber(store.total_revenue) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-center text-neutral-400">No store data available</p>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-medium text-neutral-100">Recent Orders (Platform-wide)</h3>
                            <div v-if="analytics.recent_orders.length > 0" class="space-y-3">
                                <div
                                    v-for="order in analytics.recent_orders"
                                    :key="order.id"
                                    class="flex items-center justify-between p-3 rounded-lg bg-neutral-800"
                                >
                                    <div>
                                        <p class="font-medium text-neutral-100">{{ order.order_number }}</p>
                                        <p class="text-sm text-neutral-400">
                                            {{ order.store_name }} · {{ order.created_at }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-emerald-400">₦{{ formatNumber(order.total) }}</p>
                                        <span :class="statusClass(order.payment_status)" class="text-xs">
                                            {{ order.payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-center text-neutral-400">No recent orders</p>
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

const props = defineProps({
    analytics: Object,
    period: String,
});

const selectedPeriod = ref(props.period);

const changePeriod = () => {
    router.get(route('admin.store-analytics'), { period: selectedPeriod.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-NG').format(value || 0);
};

const statusClass = (status) => {
    const classes = {
        paid: 'text-emerald-400',
        pending: 'text-yellow-400',
        failed: 'text-red-400',
    };
    return classes[status] || 'text-neutral-400';
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
    theme: { mode: 'dark' },
    colors: ['#10B981'],
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
        labels: { style: { colors: '#9CA3AF' } },
    },
    yaxis: {
        labels: {
            style: { colors: '#9CA3AF' },
            formatter: (value) => `₦${formatNumber(value)}`,
        },
    },
    tooltip: {
        theme: 'dark',
        y: { formatter: (value) => `₦${formatNumber(value)}` },
    },
    grid: {
        borderColor: '#374151',
        strokeDashArray: 4,
    },
}));

// Orders Chart Configuration
const ordersChartSeries = computed(() => [
    {
        name: 'Paid',
        data: props.analytics.orders_chart.map(item => item.paid),
    },
    {
        name: 'Pending',
        data: props.analytics.orders_chart.map(item => item.pending),
    },
    {
        name: 'Failed',
        data: props.analytics.orders_chart.map(item => item.failed),
    },
]);

const ordersChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        stacked: true,
        toolbar: { show: false },
        background: 'transparent',
    },
    theme: { mode: 'dark' },
    colors: ['#10B981', '#EAB308', '#EF4444'],
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.analytics.orders_chart.map(item => item.date),
        labels: { style: { colors: '#9CA3AF' } },
    },
    yaxis: {
        labels: { style: { colors: '#9CA3AF' } },
    },
    tooltip: {
        theme: 'dark',
        y: { formatter: (value) => `${value} orders` },
    },
    legend: {
        position: 'top',
        labels: { colors: '#9CA3AF' },
    },
    grid: {
        borderColor: '#374151',
        strokeDashArray: 4,
    },
}));
</script>
