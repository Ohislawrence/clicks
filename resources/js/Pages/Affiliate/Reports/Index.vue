<template>
    <AppLayout title="My Reports">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Performance Reports
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
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Tier Progress Card -->
                <div class="bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-2xl font-bold">Your Tier Status</h3>
                            <p class="text-purple-100 mt-1">Keep growing to unlock higher commissions!</p>
                        </div>
                        <TierBadge :tier="tierInfo.current_tier" size="lg" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <p class="text-purple-100 text-sm">Current Bonus</p>
                            <p class="text-3xl font-bold mt-1">+{{ tierInfo.bonus_percentage }}%</p>
                        </div>
                        <div>
                            <p class="text-purple-100 text-sm">Progress to {{ tierInfo.next_tier }}</p>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ tierInfo.conversions_progress }}/{{ tierInfo.conversions_required }}</span>
                                    <span>{{ tierInfo.progress_percentage }}%</span>
                                </div>
                                <div class="w-full bg-purple-300 rounded-full h-3">
                                    <div 
                                        class="bg-white h-3 rounded-full transition-all duration-500" 
                                        :style="{ width: `${Math.min(tierInfo.progress_percentage, 100)}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-purple-100 text-sm">Earnings to Next Tier</p>
                            <p class="text-2xl font-bold mt-1">₦{{ formatNumber(tierInfo.earnings_remaining) }}</p>
                            <p class="text-sm text-purple-100 mt-1">of ₦{{ formatNumber(tierInfo.earnings_required) }} needed</p>
                        </div>
                    </div>
                </div>

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
                                <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ formatNumber(stats.total_earnings) }}</p>
                                <p class="text-sm text-gray-600 mt-2">EPC: ₦{{ stats.epc.toFixed(2) }}</p>
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
                                <p class="text-sm font-medium text-gray-600">Pending Balance</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ formatNumber(stats.pending_balance) }}</p>
                                <p class="text-sm text-green-600 mt-2">Available: ₦{{ formatNumber(stats.available_balance) }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Earnings Trend -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Earnings Trend</h3>
                        <apexchart 
                            v-if="earningsChartOptions"
                            type="area" 
                            height="300" 
                            :options="earningsChartOptions" 
                            :series="earningsChartSeries"
                        />
                    </div>

                    <!-- Performance Breakdown -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Rate by Day</h3>
                        <apexchart 
                            v-if="crChartOptions"
                            type="bar" 
                            height="300" 
                            :options="crChartOptions" 
                            :series="crChartSeries"
                        />
                    </div>
                </div>

                <!-- Top Performing Links -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Top Performing Links</h3>
                        <Link :href="route('affiliate.links.index')" class="text-sm text-blue-600 hover:text-blue-800">
                            View All →
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offer</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">CR</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Earnings</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">EPC</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="link in topLinks" :key="link.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ link.name }}</div>
                                        <div class="text-xs text-gray-500">{{ link.tracking_code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ link.offer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ formatNumber(link.clicks) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ formatNumber(link.conversions) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span :class="['text-sm font-semibold', getCRColor(link.cr)]">
                                            {{ link.cr.toFixed(2) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        ₦{{ formatNumber(link.earnings) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
                                        ₦{{ link.epc.toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Referral Stats (if has referrals) -->
                <div v-if="referralStats && referralStats.total_referrals > 0" class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Referral Program</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 font-medium">Total Referrals</p>
                            <p class="text-2xl font-bold text-blue-700 mt-2">{{ referralStats.total_referrals }}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 font-medium">Active Referrals</p>
                            <p class="text-2xl font-bold text-green-700 mt-2">{{ referralStats.active_referrals }}</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-purple-600 font-medium">Referral Earnings</p>
                            <p class="text-2xl font-bold text-purple-700 mt-2">₦{{ formatNumber(referralStats.total_earnings) }}</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <p class="text-sm text-yellow-600 font-medium">Your Referral Code</p>
                            <div class="flex items-center gap-2 mt-2">
                                <code class="text-lg font-mono font-bold text-yellow-700">{{ referralStats.referral_code }}</code>
                                <button @click="copyReferralCode" class="p-1 text-yellow-600 hover:text-yellow-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
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
    tierInfo: Object,
    earningsTrend: Array,
    crTrend: Array,
    topLinks: Array,
    referralStats: Object,
});

const dateRange = ref('7days');

// Chart Options
const earningsChartOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        sparkline: { enabled: false }
    },
    stroke: { width: 2, curve: 'smooth' },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.2,
        }
    },
    colors: ['#8b5cf6'],
    xaxis: {
        categories: props.earningsTrend?.map(d => d.date) || [],
        labels: { rotate: -45 }
    },
    yaxis: {
        labels: { formatter: (val) => `₦${formatNumber(val)}` }
    },
    dataLabels: { enabled: false },
    tooltip: {
        y: { formatter: (val) => `₦${formatNumber(val)}` }
    }
}));

const earningsChartSeries = computed(() => [{
    name: 'Earnings',
    data: props.earningsTrend?.map(d => d.earnings) || []
}]);

const crChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false }
    },
    colors: ['#10b981'],
    xaxis: {
        categories: props.crTrend?.map(d => d.date) || [],
        labels: { rotate: -45 }
    },
    yaxis: {
        labels: { formatter: (val) => `${val.toFixed(1)}%` }
    },
    dataLabels: {
        enabled: true,
        formatter: (val) => `${val.toFixed(1)}%`,
        style: { fontSize: '10px' }
    },
    plotOptions: {
        bar: { columnWidth: '60%', distributed: false }
    },
    tooltip: {
        y: { formatter: (val) => `${val.toFixed(2)}%` }
    }
}));

const crChartSeries = computed(() => [{
    name: 'Conversion Rate',
    data: props.crTrend?.map(d => d.cr) || []
}]);

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

const applyFilters = () => {
    router.get(route('affiliate.reports.index'), { range: dateRange.value }, { preserveState: true });
};

const copyReferralCode = () => {
    navigator.clipboard.writeText(props.referralStats.referral_code);
    // Could add a toast notification here
};
</script>
