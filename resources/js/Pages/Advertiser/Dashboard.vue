<template>
    <AppLayout title="Advertiser Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                    <p class="mt-1 text-sm text-gray-600">Monitor your offers and conversions</p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('advertiser.offers.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Create Offer
                    </Link>
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
                        title="Total Offers"
                        :value="stats.totalOffers"
                        :subtitle="`${stats.activeOffers} active`"
                        icon="chart"
                        color="blue"
                    />

                    <MetricCard
                        title="Total Clicks"
                        :value="stats.totalClicks"
                        :subtitle="`${stats.totalClicksAllTime.toLocaleString()} all time`"
                        icon="click"
                        color="purple"
                    />

                    <MetricCard
                        title="Conversions"
                        :value="stats.totalConversions"
                        :subtitle="`${stats.pendingConversions} pending approval`"
                        icon="check"
                        color="green"
                    />

                    <MetricCard
                        title="Total Revenue"
                        :value="stats.totalRevenue"
                        :subtitle="`CR: ${stats.conversionRate}%`"
                        format="currency"
                        icon="money"
                        color="orange"
                    />
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Performance Chart -->
                    <ChartCard
                        title="Revenue & Conversions"
                        type="area"
                        :series="performanceSeries"
                        :categories="performanceCategories"
                        :colors="['#3B82F6', '#10B981', '#EF4444']"
                    />

                    <!-- Revenue Breakdown -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Revenue & Commissions</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalRevenue) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Commissions Paid</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalCommissions) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.pendingRevenue) }}</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Three Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Top Offers -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Top Offers</h3>
                            <Link :href="route('advertiser.offers.index')" class="text-sm text-blue-600 hover:text-blue-700">
                                View all
                            </Link>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="offer in topOffers.slice(0, 5)"
                                :key="offer.id"
                                class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer"
                                @click="$inertia.visit(route('advertiser.offers.show', offer.id))"
                            >
                                <img
                                    :src="offer.thumbnail || '/images/placeholder.png'"
                                    :alt="offer.name"
                                    class="w-10 h-10 rounded-lg object-cover"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ offer.name }}</p>
                                    <p class="text-xs text-gray-500">{{ offer.conversions }} conv · {{ formatCurrency(offer.revenue) }}</p>
                                </div>
                            </div>
                            <div v-if="topOffers.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No offers yet
                            </div>
                        </div>
                    </div>

                    <!-- Top Affiliates -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Affiliates</h3>
                        <div class="space-y-3">
                            <div
                                v-for="affiliate in topAffiliates.slice(0, 5)"
                                :key="affiliate.id"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-600 font-mono font-semibold">{{ affiliate.affiliate_code || 'Affiliate #' + affiliate.id }}</p>
                                    <p class="text-xs text-gray-500">Tier: {{ affiliate.tier }}</p>
                                </div>
                                <div class="text-right ml-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ affiliate.conversions }}</p>
                                    <p class="text-xs text-gray-500">conversions</p>
                                </div>
                            </div>
                            <div v-if="topAffiliates.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No affiliates yet
                            </div>
                        </div>
                    </div>

                    <!-- Recent Conversions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pending Conversions</h3>
                            <Link :href="route('advertiser.conversions.index', { status: 'pending' })" class="text-sm text-blue-600 hover:text-blue-700">
                                View all
                            </Link>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="conversion in recentConversions.slice(0, 5)"
                                :key="conversion.id"
                                class="p-3 border border-yellow-200 bg-yellow-50 rounded-lg"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-medium text-gray-900">{{ conversion.offer?.name }}</p>
                                    <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ formatCurrency(conversion.conversion_value) }}</p>
                                <p class="text-xs text-gray-500">{{ conversion.affiliate?.name }} · {{ formatDate(conversion.created_at) }}</p>
                            </div>
                            <div v-if="recentConversions.length === 0" class="text-center py-8 text-gray-500 text-sm">
                                No pending conversions
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
    topAffiliates: Array,
    recentConversions: Array,
    dailyStats: Array,
    dateRange: String
});

const selectedRange = ref(props.dateRange);

const updateRange = () => {
    router.get(route('advertiser.dashboard'), { range: selectedRange.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const performanceSeries = computed(() => [
    {
        name: 'Conversions',
        data: props.dailyStats.map(stat => stat.conversions)
    },
    {
        name: 'Revenue',
        data: props.dailyStats.map(stat => parseFloat(stat.revenue))
    },
    {
        name: 'Commissions',
        data: props.dailyStats.map(stat => parseFloat(stat.commissions))
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
