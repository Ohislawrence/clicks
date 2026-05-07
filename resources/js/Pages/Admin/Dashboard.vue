<template>
    <AppLayout title="Admin Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
                    <p class="mt-1 text-sm text-gray-600">Platform overview and statistics</p>
                </div>
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
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Platform Stats -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <MetricCard
                            title="Total Users"
                            :value="stats.totalUsers"
                            :subtitle="`${stats.totalAffiliates} Affiliates, ${stats.totalAdvertisers} Advertisers`"
                            icon="users"
                            color="blue"
                            format="number"
                        />
                        <MetricCard
                            title="Total Offers"
                            :value="stats.totalOffers"
                            :subtitle="`${stats.activeOffers} Active`"
                            icon="chart"
                            color="green"
                            format="number"
                        />
                        <MetricCard
                            title="Conversion Rate"
                            :value="stats.conversionRate"
                            :subtitle="`${stats.totalConversions.toLocaleString()} Conversions`"
                            icon="check"
                            color="purple"
                            format="percentage"
                        />
                        <MetricCard
                            title="Total Revenue"
                            :value="stats.totalRevenue"
                            :subtitle="`${stats.totalCommissions.toLocaleString('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 })} Commissions`"
                            icon="money"
                            color="orange"
                            format="currency"
                        />
                    </div>
                </div>

                <!-- Pending Items -->
                <div v-if="stats.pendingPayouts > 0 || stats.pendingConversions > 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-yellow-400 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-yellow-800">Pending Actions Required</h3>
                            <div class="mt-2 text-sm text-yellow-700 space-y-1">
                                <p v-if="stats.pendingPayouts > 0">
                                    <Link :href="route('admin.payouts.index', { status: 'pending' })" class="font-medium underline hover:text-yellow-900">
                                        {{ stats.pendingPayouts }} payout request(s) pending
                                    </Link>
                                </p>
                                <p v-if="stats.pendingConversions > 0">
                                    {{ stats.pendingConversions }} conversion(s) awaiting approval
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-6">
                    <ChartCard
                        title="Platform Performance (30 Days)"
                        type="area"
                        :series="performanceSeries"
                        :categories="performanceCategories"
                        :height="350"
                        :colors="['#3b82f6', '#10b981', '#f59e0b']"
                    />
                </div>

                <!-- Top Performers -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top Affiliates -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Top Affiliates</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="(affiliate, index) in topAffiliates"
                                :key="affiliate.id"
                                class="px-6 py-4 hover:bg-gray-50"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-bold text-blue-600">#{{ index + 1 }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ affiliate.name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ affiliate.email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <p class="text-sm font-bold text-green-600">{{ formatCurrency(affiliate.earnings) }}</p>
                                        <p class="text-xs text-gray-500">{{ affiliate.conversions }} sales</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Advertisers -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Top Advertisers</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="(advertiser, index) in topAdvertisers"
                                :key="advertiser.id"
                                class="px-6 py-4 hover:bg-gray-50"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-bold text-purple-600">#{{ index + 1 }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ advertiser.name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ advertiser.email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <p class="text-sm font-bold text-blue-600">{{ formatCurrency(advertiser.revenue) }}</p>
                                        <p class="text-xs text-gray-500">{{ advertiser.offers_count }} offers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Offers -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Top Offers</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <div
                                v-for="(offer, index) in topOffers"
                                :key="offer.id"
                                class="px-6 py-4 hover:bg-gray-50"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                                            <span class="text-sm font-bold text-orange-600">#{{ index + 1 }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ offer.name }}</p>
                                            <p class="text-xs text-gray-500 truncate">by {{ offer.advertiser_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <p class="text-sm font-bold text-orange-600">{{ formatCurrency(offer.revenue) }}</p>
                                        <p class="text-xs text-gray-500">{{ offer.conversions }} sales</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Conversions -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Conversions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Affiliate</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="conversion in recentConversions" :key="conversion.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ conversion.offer?.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ conversion.affiliate?.name }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(conversion.conversion_value) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-orange-600">{{ formatCurrency(conversion.commission_amount) }}</td>
                                    <td class="px-6 py-4">
                                        <span 
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': conversion.status === 'pending',
                                                'bg-green-100 text-green-800': conversion.status === 'approved',
                                                'bg-red-100 text-red-800': conversion.status === 'rejected'
                                            }"
                                        >
                                            {{ conversion.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(conversion.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MetricCard from '@/Components/MetricCard.vue';
import ChartCard from '@/Components/ChartCard.vue';

const props = defineProps({
    stats: Object,
    topAffiliates: Array,
    topAdvertisers: Array,
    topOffers: Array,
    recentConversions: Array,
    dailyStats: Array,
    dateRange: Number
});

const selectedRange = computed({
    get: () => props.dateRange,
    set: () => {}
});

const performanceSeries = computed(() => [
    {
        name: 'Clicks',
        data: props.dailyStats.map(stat => stat.clicks)
    },
    {
        name: 'Conversions',
        data: props.dailyStats.map(stat => stat.conversions)
    },
    {
        name: 'Revenue (₦)',
        data: props.dailyStats.map(stat => Math.round(stat.revenue))
    }
]);

const performanceCategories = computed(() => 
    props.dailyStats.map(stat => new Date(stat.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }))
);

const updateRange = () => {
    router.get(route('admin.dashboard'), { range: selectedRange.value }, {
        preserveState: true
    });
};

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
