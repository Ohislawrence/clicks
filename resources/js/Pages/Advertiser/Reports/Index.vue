<template>
    <AppLayout title="Campaign Reports">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Campaign Performance Reports
                </h2>
                <div class="flex gap-3">
                    <select v-model="dateRange" @change="applyFilters" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="7days">Last 7 Days</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="thismonth">This Month</option>
                        <option value="lastmonth">Last Month</option>
                    </select>
                    <button @click="exportReport" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Export Report
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Performance Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats.total_clicks) }}</p>
                                <p :class="['text-sm mt-2', getChangeClass(stats.clicks_change)]">
                                    {{ formatChange(stats.clicks_change) }}
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
                                <p class="text-sm text-gray-600 mt-2">CR: {{ stats.conversion_rate.toFixed(2) }}%</p>
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
                                <p class="text-sm font-medium text-gray-600">Total Spend</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ formatNumber(stats.total_spend) }}</p>
                                <p class="text-sm text-gray-600 mt-2">CPA: ₦{{ stats.cpa.toFixed(2) }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 rounded-full">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">ROI</p>
                                <p :class="['text-3xl font-bold mt-2', stats.roi >= 0 ? 'text-green-600' : 'text-red-600']">
                                    {{ stats.roi >= 0 ? '+' : '' }}{{ stats.roi.toFixed(1) }}%
                                </p>
                                <p class="text-sm text-gray-600 mt-2">{{ getRoiLabel(stats.roi) }}</p>
                            </div>
                            <div :class="['p-3 rounded-full', stats.roi >= 0 ? 'bg-green-100' : 'bg-red-100']">
                                <svg :class="['w-8 h-8', stats.roi >= 0 ? 'text-green-600' : 'text-red-600']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget & Caps Status -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Budget Status</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Used Today</span>
                                    <span class="font-semibold text-gray-900">₦{{ formatNumber(budgetStatus.daily_used) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        :class="['h-2 rounded-full transition-all', getBudgetColor(budgetStatus.daily_percentage)]"
                                        :style="{ width: `${Math.min(budgetStatus.daily_percentage, 100)}%` }"
                                    ></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Limit: ₦{{ formatNumber(budgetStatus.daily_limit) }} ({{ budgetStatus.daily_percentage.toFixed(0) }}%)</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Budget Status</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Used This Month</span>
                                    <span class="font-semibold text-gray-900">₦{{ formatNumber(budgetStatus.monthly_used) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        :class="['h-2 rounded-full transition-all', getBudgetColor(budgetStatus.monthly_percentage)]"
                                        :style="{ width: `${Math.min(budgetStatus.monthly_percentage, 100)}%` }"
                                    ></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Limit: ₦{{ formatNumber(budgetStatus.monthly_limit) }} ({{ budgetStatus.monthly_percentage.toFixed(0) }}%)</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Caps</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Today's Conversions</span>
                                    <span class="font-semibold text-gray-900">{{ formatNumber(capStatus.daily_conversions) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        :class="['h-2 rounded-full transition-all', getBudgetColor(capStatus.daily_percentage)]"
                                        :style="{ width: `${Math.min(capStatus.daily_percentage, 100)}%` }"
                                    ></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Cap: {{ formatNumber(capStatus.daily_cap) }} ({{ capStatus.daily_percentage.toFixed(0) }}%)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Performance Trend -->
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

                    <!-- Spend vs Revenue -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Spend vs Revenue</h3>
                        <apexchart
                            v-if="spendChartOptions"
                            type="bar"
                            height="300"
                            :options="spendChartOptions"
                            :series="spendChartSeries"
                        />
                    </div>
                </div>

                <!-- Offer Performance -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Offer Performance</h3>
                        <Link :href="route('advertiser.offers.index')" class="text-sm text-blue-600 hover:text-blue-800">
                            Manage Offers →
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offer</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">CR</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Spend</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">CPA</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Budget Used</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="offer in offerPerformance" :key="offer.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ offer.name }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ offer.id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ formatNumber(offer.clicks) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ formatNumber(offer.conversions) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span :class="['text-sm font-semibold', getCRColor(offer.cr)]">
                                            {{ offer.cr.toFixed(2) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        ₦{{ formatNumber(offer.spend) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
                                        ₦{{ offer.cpa.toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 bg-gray-200 rounded-full h-1.5">
                                                <div
                                                    :class="['h-1.5 rounded-full', getBudgetColor(offer.budget_percentage)]"
                                                    :style="{ width: `${Math.min(offer.budget_percentage, 100)}%` }"
                                                ></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ offer.budget_percentage.toFixed(0) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="[
                                            'px-2 py-1 text-xs font-semibold rounded-full',
                                            offer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        ]">
                                            {{ offer.is_active ? 'Active' : 'Paused' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Affiliates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Affiliates</h3>
                    <div class="space-y-4">
                        <div v-for="(affiliate, index) in topAffiliates" :key="affiliate.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-4">
                                <span class="text-2xl font-bold text-gray-400">#{{ index + 1 }}</span>
                                <div>
                                    <p class="font-semibold text-gray-900 font-mono">{{ affiliate.affiliate_code || 'Affiliate #' + affiliate.id }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <TierBadge :tier="affiliate.tier" size="sm" />
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ formatNumber(affiliate.conversions) }}</p>
                                <p class="text-sm text-gray-600">conversions • CR: {{ affiliate.cr.toFixed(2) }}%</p>
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
    budgetStatus: Object,
    capStatus: Object,
    performanceTrend: Array,
    spendTrend: Array,
    offerPerformance: Array,
    topAffiliates: Array,
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
    dataLabels: { enabled: false }
}));

const performanceChartSeries = computed(() => [
    {
        name: 'Clicks',
        data: props.performanceTrend?.map(d => d.clicks) || []
    },
    {
        name: 'Conversions',
        data: props.performanceTrend?.map(d => d.conversions) || []
    }
]);

const spendChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false }
    },
    colors: ['#f59e0b', '#10b981'],
    xaxis: {
        categories: props.spendTrend?.map(d => d.date) || [],
        labels: { rotate: -45 }
    },
    yaxis: {
        labels: { formatter: (val) => `₦${formatNumber(val)}` }
    },
    legend: { position: 'top' },
    dataLabels: { enabled: false },
    plotOptions: {
        bar: { columnWidth: '60%' }
    }
}));

const spendChartSeries = computed(() => [
    {
        name: 'Spend',
        data: props.spendTrend?.map(d => d.spend) || []
    },
    {
        name: 'Revenue',
        data: props.spendTrend?.map(d => d.revenue) || []
    }
]);

// Methods
const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num?.toLocaleString() || '0';
};

const formatChange = (change) => {
    const prefix = change >= 0 ? '↑' : '↓';
    return `${prefix} ${Math.abs(change).toFixed(1)}% vs last period`;
};

const getChangeClass = (change) => {
    if (change > 0) return 'text-green-600';
    if (change < 0) return 'text-red-600';
    return 'text-gray-600';
};

const getCRColor = (cr) => {
    if (cr >= 5) return 'text-green-600';
    if (cr >= 3) return 'text-blue-600';
    if (cr >= 1) return 'text-yellow-600';
    return 'text-red-600';
};

const getBudgetColor = (percentage) => {
    if (percentage >= 90) return 'bg-red-500';
    if (percentage >= 75) return 'bg-orange-500';
    if (percentage >= 50) return 'bg-yellow-500';
    return 'bg-green-500';
};

const getRoiLabel = (roi) => {
    if (roi >= 100) return 'Excellent ROI';
    if (roi >= 50) return 'Good ROI';
    if (roi >= 0) return 'Profitable';
    return 'Loss';
};

const applyFilters = () => {
    router.get(route('advertiser.reports.index'), { range: dateRange.value }, { preserveState: true });
};

const exportReport = () => {
    window.location.href = route('advertiser.reports.export', { range: dateRange.value });
};
</script>
