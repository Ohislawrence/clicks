<template>
    <AppLayout title="Advertiser Wallets">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Advertiser Wallets</h2>
                    <p class="mt-1 text-sm text-gray-600">Monitor deposits, allocations, and refunds across all advertisers</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-500 mb-1">Total Deposited</p>
                        <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.total_deposited) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-500 mb-1">Total Allocated</p>
                        <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(stats.total_allocated) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-500 mb-1">Total Refunded</p>
                        <p class="text-2xl font-bold text-purple-600">{{ formatCurrency(stats.total_refunded) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-500 mb-1">Pending Deposits</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ stats.pending_deposits }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <input
                            v-model="searchForm.search"
                            type="text"
                            placeholder="Search by name, email or ref..."
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                        <select v-model="searchForm.advertiser_id" @change="applyFilters" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Advertisers</option>
                            <option v-for="a in advertisers" :key="a.id" :value="a.id">{{ a.name }}</option>
                        </select>
                        <select v-model="searchForm.type" @change="applyFilters" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="deposit">Deposit</option>
                            <option value="offer_allocation">Offer Allocation</option>
                            <option value="offer_topup">Offer Top-up</option>
                            <option value="offer_refund">Offer Refund</option>
                        </select>
                        <select v-model="searchForm.status" @change="applyFilters" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div v-if="transactions.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advertiser</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="txn in transactions.data" :key="txn.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ txn.user?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ txn.user?.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="typeBadgeClass(txn.type)">
                                            {{ formatType(txn.type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold" :class="isCredit(txn.type) ? 'text-green-600' : 'text-red-600'">
                                            {{ isCredit(txn.type) ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="statusBadgeClass(txn.status)">
                                            {{ txn.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ txn.offer?.name || '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-mono text-gray-500">{{ txn.reference || '—' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(txn.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        <Link :href="route('admin.wallets.show', txn.user?.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Wallet
                                        </Link>
                                        <button
                                            v-if="txn.type === 'deposit' && txn.status === 'pending'"
                                            @click="failDeposit(txn)"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Mark Failed
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ transactions.from || 0 }} to {{ transactions.to || 0 }} of {{ transactions.total || 0 }} transactions
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    v-for="(link, index) in transactions.links"
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || undefined"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm transition-colors',
                                        link.active ? 'bg-blue-600 text-white' : link.url ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    transactions: Object,
    stats: Object,
    advertisers: Array,
    filters: Object,
});

const searchForm = reactive({
    search: props.filters?.search || '',
    advertiser_id: props.filters?.advertiser_id || '',
    type: props.filters?.type || '',
    status: props.filters?.status || '',
});

let searchTimer = null;
const debouncedSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 400);
};

const applyFilters = () => {
    router.get(route('admin.wallets.index'), searchForm, { preserveState: true, preserveScroll: true });
};

const failDeposit = (txn) => {
    if (!confirm('Mark this deposit as failed?')) return;
    router.post(route('admin.wallets.deposits.fail', txn.id));
};

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount ?? 0);

const formatDate = (date) =>
    new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const formatType = (type) => ({
    deposit: 'Deposit',
    offer_allocation: 'Offer Allocation',
    offer_topup: 'Offer Top-up',
    offer_refund: 'Offer Refund',
}[type] ?? type);

const isCredit = (type) => ['deposit', 'offer_refund'].includes(type);

const typeBadgeClass = (type) => ({
    deposit: 'bg-green-100 text-green-800',
    offer_allocation: 'bg-blue-100 text-blue-800',
    offer_topup: 'bg-indigo-100 text-indigo-800',
    offer_refund: 'bg-purple-100 text-purple-800',
}[type] ?? 'bg-gray-100 text-gray-800');

const statusBadgeClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-800');
</script>
