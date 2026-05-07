<template>
    <AppLayout title="Affiliate Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                    <p class="mt-1 text-sm text-gray-600">Welcome back! Here's your performance overview.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select
                        v-model="selectedRange"
                        @change="updateRange"
                        class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <MetricCard
                        title="Total Clicks"
                        :value="stats.totalClicks"
                        :subtitle="`${stats.totalClicksAllTime.toLocaleString()} all time`"
                        icon="click"
                        color="blue"
                    />

                    <MetricCard
                        title="Conversions"
                        :value="stats.totalConversions"
                        :subtitle="`${stats.pendingConversions} pending, ${stats.approvedConversions} approved`"
                        icon="check"
                        color="green"
                    />

                    <MetricCard
                        title="Conversion Rate"
                        :value="stats.conversionRate"
                        format="percentage"
                        icon="chart"
                        color="purple"
                    />

                    <MetricCard
                        title="Total Earnings"
                        :value="stats.totalEarnings"
                        :subtitle="`₦${stats.balance.toLocaleString()} available`"
                        format="currency"
                        icon="money"
                        color="orange"
                    />
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Performance Chart -->
                    <ChartCard
                        title="Performance Overview"
                        type="area"
                        :series="performanceSeries"
                        :categories="performanceCategories"
                        :colors="['#3B82F6', '#10B981']"
                    />

                    <!-- Earnings Breakdown -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Earnings Breakdown</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pending</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.pendingEarnings) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Approved</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.approvedEarnings) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Paid</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.paidEarnings) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Commission Cap Status (if applicable) -->
                <div v-if="referralCapData" class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Referral Commission Status</h3>
                            <p class="text-sm text-gray-500 mt-1">Your referral earning limits and progress</p>
                        </div>
                        <div
                            v-if="referralCapData.has_reached_cap"
                            class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium"
                        >
                            Cap Reached
                        </div>
                        <div
                            v-else-if="referralCapData.progress.overall_percentage >= 80"
                            class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium"
                        >
                            {{ Math.round(referralCapData.progress.overall_percentage) }}% Used
                        </div>
                        <div
                            v-else
                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"
                        >
                            Active
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Current Earnings -->
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-600 mb-1">Referral Earnings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(referralCapData.current_earnings) }}</p>
                        </div>

                        <!-- Amount Cap Progress (if applicable) -->
                        <div v-if="referralCapData.cap_type === 'amount' || referralCapData.cap_type === 'both'" class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-600 mb-1">Amount Limit</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(referralCapData.cap_amount) }}</p>
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>{{ Math.round(referralCapData.progress.amount_percentage) }}%</span>
                                    <span>{{ formatCurrency(referralCapData.remaining_amount) }} left</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full transition-all"
                                        :class="{
                                            'bg-green-500': referralCapData.progress.amount_percentage < 80,
                                            'bg-yellow-500': referralCapData.progress.amount_percentage >= 80 && referralCapData.progress.amount_percentage < 100,
                                            'bg-red-500': referralCapData.progress.amount_percentage >= 100
                                        }"
                                        :style="{ width: Math.min(referralCapData.progress.amount_percentage, 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Cap Progress (if applicable) -->
                        <div v-if="referralCapData.cap_type === 'time' || referralCapData.cap_type === 'both'" class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-600 mb-1">Time Limit</p>
                            <p class="text-2xl font-bold text-gray-900">{{ referralCapData.cap_months }} Months</p>
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>{{ Math.round(referralCapData.progress.time_percentage) }}%</span>
                                    <span>{{ referralCapData.remaining_months }} months left</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full transition-all"
                                        :class="{
                                            'bg-green-500': referralCapData.progress.time_percentage < 80,
                                            'bg-yellow-500': referralCapData.progress.time_percentage >= 80 && referralCapData.progress.time_percentage < 100,
                                            'bg-red-500': referralCapData.progress.time_percentage >= 100
                                        }"
                                        :style="{ width: Math.min(referralCapData.progress.time_percentage, 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="p-4 bg-gradient-to-br from-gray-50 to-slate-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-600 mb-1">Status</p>
                            <p v-if="referralCapData.has_reached_cap" class="text-lg font-bold text-red-600">Cap Reached</p>
                            <p v-else-if="referralCapData.progress.overall_percentage >= 80" class="text-lg font-bold text-yellow-600">Approaching Limit</p>
                            <p v-else class="text-lg font-bold text-green-600">Active</p>
                            <p v-if="referralCapData.started_at" class="text-xs text-gray-500 mt-1">
                                Started {{ new Date(referralCapData.started_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>

                    <!-- Warning/Info Messages -->
                    <div v-if="!referralCapData.has_reached_cap && referralCapData.progress.overall_percentage >= 80" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Approaching Referral Cap Limit</p>
                                <p class="text-xs text-yellow-700 mt-1">You're getting close to your referral commission limit. Once reached, you'll no longer earn from sub-affiliates.</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="referralCapData.has_reached_cap" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-red-800">Referral Commission Cap Reached</p>
                                <p class="text-xs text-red-700 mt-1">You've reached your referral commission limit. You will no longer earn commissions from sub-affiliates. Your sub-affiliates can continue earning normally.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Three Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top Offers -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Top Offers</h3>
                            <Link :href="route('affiliate.offers.index')" class="text-sm text-blue-600 hover:text-blue-700">
                                View all
                            </Link>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="offer in topOffers.slice(0, 5)"
                                :key="offer.id"
                                class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                <img
                                    :src="offer.thumbnail || '/images/placeholder.png'"
                                    :alt="offer.name"
                                    class="w-10 h-10 rounded-lg object-cover"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ offer.name }}</p>
                                    <p class="text-xs text-gray-500">{{ offer.clicks }} clicks · {{ offer.conversions }} conversions</p>
                                </div>
                            </div>
                            <div v-if="topOffers.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No data yet. Start promoting offers!
                            </div>
                        </div>
                    </div>

                    <!-- Traffic Sources -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Traffic Sources</h3>
                        <div class="space-y-3">
                            <div
                                v-for="source in trafficSources.slice(0, 5)"
                                :key="source.source"
                                class="flex items-center justify-between"
                            >
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ source.source || 'Direct' }}</p>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full"
                                            :style="{ width: `${(source.count / trafficSources[0]?.count * 100) || 0}%` }"
                                        ></div>
                                    </div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-600">{{ source.count }}</span>
                            </div>
                            <div v-if="trafficSources.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No traffic data available
                            </div>
                        </div>
                    </div>

                    <!-- Geographic Performance -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Countries</h3>
                        <div class="space-y-3">
                            <div
                                v-for="geo in geoPerformance.slice(0, 5)"
                                :key="geo.country_code"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">{{ getCountryFlag(geo.country_code) }}</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ geo.country_name }}</p>
                                        <p class="text-xs text-gray-500">{{ geo.conversions }} conversions</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ geo.clicks }}</span>
                            </div>
                            <div v-if="geoPerformance.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No geographic data yet
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
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MetricCard from '@/Components/MetricCard.vue';
import ChartCard from '@/Components/ChartCard.vue';

const props = defineProps({
    stats: Object,
    topOffers: Array,
    trafficSources: Array,
    geoPerformance: Array,
    dailyStats: Array,
    dateRange: String,
    referralCapData: Object
});

const selectedRange = ref(props.dateRange);

const updateRange = () => {
    router.get(route('affiliate.dashboard'), { range: selectedRange.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const performanceSeries = computed(() => [
    {
        name: 'Clicks',
        data: props.dailyStats.map(stat => stat.clicks)
    },
    {
        name: 'Conversions',
        data: props.dailyStats.map(stat => stat.conversions)
    }
]);

const performanceCategories = computed(() =>
    props.dailyStats.map(stat => new Date(stat.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }))
);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};

const getCountryFlag = (code) => {
    if (!code) return '🌍';
    return String.fromCodePoint(...[...code.toUpperCase()].map(c => c.charCodeAt(0) + 127397));
};
</script>
