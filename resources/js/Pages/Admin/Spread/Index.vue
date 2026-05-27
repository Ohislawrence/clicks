<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    totals: Object,
    pendingSpread: Number,
    spreadByOffer: Array,
    dailySpread: Array,
    modelBreakdown: Array,
    recentConversions: Array,
    range: Number,
});

const dateRange = ref(props.range);

const changeRange = () => {
    router.get(route('admin.spread.index'), { range: dateRange.value }, { preserveState: true });
};

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '₦0.00';
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-NG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatPct = (value) => {
    if (!value && value !== 0) return '—';
    return Number(value).toFixed(1) + '%';
};

const spreadRate = computed(() => {
    const payout = parseFloat(props.totals?.total_advertiser_payout || 0);
    const spread = parseFloat(props.totals?.total_spread || 0);
    if (payout === 0) return 0;
    return ((spread / payout) * 100).toFixed(1);
});

const modelLabel = (model) => {
    const map = { revshare: 'RevShare', cpa: 'CPA', cpl: 'CPL', pps: 'PPS', ppl: 'PPL', fixed: 'Fixed' };
    return map[model] || model;
};
</script>

<template>
    <AppLayout title="Platform Spread">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Platform Spread</h2>
                    <p class="mt-1 text-sm text-gray-600">Admin revenue from the spread between advertiser payouts and affiliate commissions</p>
                </div>
                <div>
                    <select
                        v-model="dateRange"
                        @change="changeRange"
                        class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="60">Last 60 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Top KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Spread Earned -->
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow">
                        <p class="text-sm opacity-90 font-medium">Total Spread Earned</p>
                        <p class="text-3xl font-bold mt-2">{{ formatCurrency(totals?.total_spread || 0) }}</p>
                        <p class="text-xs opacity-75 mt-2">Approved &amp; paid conversions</p>
                    </div>

                    <!-- Pending Spread -->
                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-6 text-white shadow">
                        <p class="text-sm opacity-90 font-medium">Pending Spread</p>
                        <p class="text-3xl font-bold mt-2">{{ formatCurrency(pendingSpread) }}</p>
                        <p class="text-xs opacity-75 mt-2">From pending conversions</p>
                    </div>

                    <!-- Spread Rate -->
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white shadow">
                        <p class="text-sm opacity-90 font-medium">Average Spread Rate</p>
                        <p class="text-3xl font-bold mt-2">{{ spreadRate }}%</p>
                        <p class="text-xs opacity-75 mt-2">Of total advertiser payouts</p>
                    </div>

                    <!-- Total Conversions -->
                    <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-6 text-white shadow">
                        <p class="text-sm opacity-90 font-medium">Paid Conversions</p>
                        <p class="text-3xl font-bold mt-2">{{ totals?.total_conversions || 0 }}</p>
                        <p class="text-xs opacity-75 mt-2">With spread earnings</p>
                    </div>
                </div>

                <!-- Revenue Breakdown -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Revenue Breakdown</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500 font-medium">Gross Sale Revenue</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(totals?.total_revenue || 0) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Total conversion value</p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <p class="text-sm text-orange-600 font-medium">Affiliate Commissions</p>
                            <p class="text-2xl font-bold text-orange-600 mt-1">{{ formatCurrency(totals?.total_affiliate_commission || 0) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Paid to affiliates</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 font-medium">Admin Spread</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ formatCurrency(totals?.total_spread || 0) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Platform margin</p>
                        </div>
                    </div>

                    <!-- Visual bar -->
                    <div class="mt-6" v-if="(totals?.total_advertiser_payout || 0) > 0">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Commission split (of advertiser payout)</p>
                        <div class="flex rounded-full overflow-hidden h-5 bg-gray-200">
                            <div
                                class="bg-orange-400 flex items-center justify-center text-xs text-white font-semibold"
                                :style="{ width: (((totals?.total_affiliate_commission || 0) / (totals?.total_advertiser_payout || 1)) * 100).toFixed(1) + '%' }"
                            >
                                Aff
                            </div>
                            <div
                                class="bg-green-500 flex items-center justify-center text-xs text-white font-semibold"
                                :style="{ width: (((totals?.total_spread || 0) / (totals?.total_advertiser_payout || 1)) * 100).toFixed(1) + '%' }"
                            >
                                Admin
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Affiliate {{ (((totals?.total_affiliate_commission || 0) / (totals?.total_advertiser_payout || 1)) * 100).toFixed(1) }}%</span>
                            <span>Admin Spread {{ spreadRate }}%</span>
                        </div>
                    </div>
                </div>

                <!-- By Commission Model -->
                <div v-if="modelBreakdown.length > 0" class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Spread by Commission Model</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Affiliate Commission</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Admin Spread</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="row in modelBreakdown" :key="row.commission_model" class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ modelLabel(row.commission_model) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ row.conversions_count }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ formatCurrency(row.total_revenue) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-orange-600 font-medium">{{ formatCurrency(row.total_commissions) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-green-600 font-bold">{{ formatCurrency(row.total_spread) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Spread by Offer -->
                <div v-if="spreadByOffer.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Spread by Offer</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Spread %</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Advertiser Payout</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliate Commission</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Spread</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="offer in spreadByOffer" :key="offer.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ offer.name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ modelLabel(offer.commission_model) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold text-gray-700">
                                            {{ formatPct(offer.platform_spread_percentage) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ offer.conversions_count }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">{{ formatCurrency(offer.total_advertiser_payout) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-orange-600 font-medium">{{ formatCurrency(offer.total_affiliate_commission) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-green-600">{{ formatCurrency(offer.total_spread) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-700">Totals</td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                        {{ formatCurrency(spreadByOffer.reduce((s, o) => s + parseFloat(o.total_advertiser_payout || 0), 0)) }}
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-orange-600">
                                        {{ formatCurrency(spreadByOffer.reduce((s, o) => s + parseFloat(o.total_affiliate_commission || 0), 0)) }}
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-green-600">
                                        {{ formatCurrency(spreadByOffer.reduce((s, o) => s + parseFloat(o.total_spread || 0), 0)) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Daily Trend -->
                <div v-if="dailySpread.length > 0" class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Spread Trend</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Conversions</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Commissions</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Spread</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-for="day in [...dailySpread].reverse()" :key="day.date" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-700">{{ formatDate(day.date) }}</td>
                                    <td class="px-4 py-2 text-right text-gray-900">{{ day.conversions }}</td>
                                    <td class="px-4 py-2 text-right text-gray-900">{{ formatCurrency(day.revenue) }}</td>
                                    <td class="px-4 py-2 text-right text-orange-600">{{ formatCurrency(day.commissions) }}</td>
                                    <td class="px-4 py-2 text-right font-semibold text-green-600">{{ formatCurrency(day.spread) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- No data state -->
                <div v-if="!spreadByOffer.length && !dailySpread.length" class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="mt-4 text-lg font-medium text-gray-500">No spread data for this period</p>
                    <p class="text-sm text-gray-400 mt-1">Spread is earned when conversions are approved on offers with a platform spread set.</p>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
