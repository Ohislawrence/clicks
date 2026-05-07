<template>
    <AppLayout title="Reports Dashboard">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Reports Dashboard
                </h2>
                <div class="flex gap-3">
                    <select v-model="dateRange" @change="applyFilters" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="90days">Last 90 Days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <button @click="exportReport" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Export CSV
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_clicks) }}</p>
                                <p :class="['text-sm mt-2', getChangeClass(stats.clicks_change)]">
                                    {{ formatChange(stats.clicks_change) }} vs previous period
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Conversions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_conversions) }}</p>
                                <p :class="['text-sm mt-2', getChangeClass(stats.conversions_change)]">
                                    {{ formatChange(stats.conversions_change) }} vs previous period
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.conversion_rate.toFixed(2) }}%</p>
                                <p :class="['text-sm mt-2', getChangeClass(stats.cr_change)]">
                                    {{ formatChange(stats.cr_change) }} vs previous period
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ formatNumber(stats.total_revenue) }}</p>
                                <p :class="['text-sm mt-2', getChangeClass(stats.revenue_change)]">
                                    {{ formatChange(stats.revenue_change) }} vs previous period
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Platform Margin</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ formatNumber(stats.platform_margin) }}</p>
                                <p class="text-xs text-gray-500 mt-1">Avg: {{ stats.avg_margin_percentage.toFixed(1) }}%</p>
                                <p :class="['text-sm mt-1', getChangeClass(stats.platform_margin_change)]">
                                    {{ formatChange(stats.platform_margin_change) }} vs previous period
                                </p>
                            </div>
                            <div class="p-3 bg-emerald-100 rounded-full">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Clicks & Conversions Trend -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trend</h3>
                        <apexchart
                            v-if="performanceChartOptions"
                            type="line"
                            height="300"
                            :options="performanceChartOptions"
                            :series="performanceChartSeries"
                        />
                    </div>

                    <!-- Revenue by Source -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Distribution</h3>
                        <apexchart
                            v-if="revenueChartOptions"
                            type="donut"
                            height="300"
                            :options="revenueChartOptions"
                            :series="revenueChartSeries"
                        />
                    </div>
                </div>

                <!-- Top Performers -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Affiliates -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Affiliates</h3>
                        <div class="space-y-4">
                            <div v-for="(affiliate, index) in topAffiliates" :key="affiliate.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl font-bold text-gray-400">#{{ index + 1 }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ affiliate.name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <TierBadge :tier="affiliate.tier" size="sm" />
                                            <span class="text-xs text-gray-600">{{ affiliate.email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">₦{{ formatNumber(affiliate.total_earnings) }}</p>
                                    <p class="text-sm text-gray-600">{{ formatNumber(affiliate.total_conversions) }} conversions</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Offers -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Offers</h3>
                        <div class="space-y-4">
                            <div v-for="(offer, index) in topOffers" :key="offer.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl font-bold text-gray-400">#{{ index + 1 }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ offer.name }}</p>
                                        <p class="text-sm text-gray-600">CR: {{ offer.conversion_rate.toFixed(2) }}%</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ formatNumber(offer.total_conversions) }}</p>
                                    <p class="text-sm text-gray-600">conversions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traffic Sources & Fraud Stats -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Traffic Sources -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Traffic Sources</h3>
                        <div class="space-y-3">
                            <div v-for="source in trafficSources" :key="source.name" class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-2 h-2 rounded-full" :style="{ backgroundColor: source.color }"></div>
                                    <span class="text-sm text-gray-700">{{ source.name }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-48 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full transition-all duration-500"
                                            :style="{ width: `${source.percentage}%`, backgroundColor: source.color }"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-16 text-right">{{ formatNumber(source.count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fraud & Blacklist Stats -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Security Overview</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                                <p class="text-sm text-red-600 font-medium">Blocked Clicks</p>
                                <p class="text-2xl font-bold text-red-700 mt-2">{{ formatNumber(securityStats.blocked_clicks) }}</p>
                                <p class="text-xs text-red-600 mt-1">{{ securityStats.block_rate.toFixed(2) }}% of total</p>
                            </div>
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-600 font-medium">Quality Flags</p>
                                <p class="text-2xl font-bold text-yellow-700 mt-2">{{ formatNumber(securityStats.flagged_clicks) }}</p>
                                <p class="text-xs text-yellow-600 mt-1">{{ securityStats.flag_rate.toFixed(2) }}% of total</p>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-600 font-medium">Active Rules</p>
                                <p class="text-2xl font-bold text-blue-700 mt-2">{{ formatNumber(securityStats.active_blacklists) }}</p>
                                <Link :href="route('admin.blacklists.index')" class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                    Manage →
                                </Link>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <p class="text-sm text-green-600 font-medium">Avg Quality Score</p>
                                <p class="text-2xl font-bold text-green-700 mt-2">{{ securityStats.avg_quality_score }}/100</p>
                                <p class="text-xs text-green-600 mt-1">{{ getQualityLabel(securityStats.avg_quality_score) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import TierBadge from '@/Components/TierBadge.vue';
import { Link, router } from '@inertiajs/vue3';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

const props = defineProps({
    stats: Object,
    topAffiliates: Array,
    topOffers: Array,
    performanceTrend: Array,
    revenueByCategory: Array,
    trafficSources: Array,
    securityStats: Object,
});

const dateRange = ref('7days');

// Chart Options
const performanceChartOptions = computed(() => ({
    chart: {
        type: 'line',
        toolbar: { show: true },
        zoom: { enabled: true }
    },
    stroke: { width: [3, 3], curve: 'smooth' },
    colors: ['#3b82f6', '#10b981'],
    xaxis: {
        categories: props.performanceTrend?.map(d => d.date) || [],
        labels: { rotate: -45 }
    },
    yaxis: [
        {
            title: { text: 'Clicks' },
            labels: { formatter: (val) => formatNumber(val) }
        },
        {
            opposite: true,
            title: { text: 'Conversions' },
            labels: { formatter: (val) => formatNumber(val) }
        }
    ],
    legend: { position: 'top' },
    dataLabels: { enabled: false },
    tooltip: {
        shared: true,
        intersect: false,
        y: { formatter: (val) => formatNumber(val) }
    }
}));

const performanceChartSeries = computed(() => [
    {
        name: 'Clicks',
        type: 'line',
        data: props.performanceTrend?.map(d => d.clicks) || []
    },
    {
        name: 'Conversions',
        type: 'line',
        data: props.performanceTrend?.map(d => d.conversions) || []
    }
]);

const revenueChartOptions = computed(() => ({
    chart: { type: 'donut' },
    labels: props.revenueByCategory?.map(c => c.name) || [],
    colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
    legend: { position: 'bottom' },
    dataLabels: {
        enabled: true,
        formatter: (val) => `${val.toFixed(1)}%`
    },
    tooltip: {
        y: {
            formatter: (val) => `₦${formatNumber(val)}`
        }
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Revenue',
                        formatter: () => `₦${formatNumber(props.stats.total_revenue)}`
                    }
                }
            }
        }
    }
}));

const revenueChartSeries = computed(() =>
    props.revenueByCategory?.map(c => c.revenue) || []
);

// Methods
const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num?.toLocaleString() || '0';
};

const formatChange = (change) => {
    const prefix = change >= 0 ? '+' : '';
    return `${prefix}${change.toFixed(1)}%`;
};

const getChangeClass = (change) => {
    if (change > 0) return 'text-green-600';
    if (change < 0) return 'text-red-600';
    return 'text-gray-600';
};

const getQualityLabel = (score) => {
    if (score >= 80) return 'Excellent';
    if (score >= 60) return 'Good';
    if (score >= 40) return 'Fair';
    return 'Poor';
};

const applyFilters = () => {
    router.get(route('admin.reports.index'), { range: dateRange.value }, { preserveState: true });
};

const exportReport = () => {
    window.location.href = route('admin.reports.export', { range: dateRange.value });
};
</script>
