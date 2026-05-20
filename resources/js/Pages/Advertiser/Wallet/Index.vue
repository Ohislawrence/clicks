<template>
    <AppLayout title="My Wallet">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">My Wallet</h2>
                <p class="mt-1 text-sm text-gray-600">Fund your ad budget and track all transactions</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-700 text-sm">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Balance Card + Deposit Form -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Balance -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-8 text-white">
                        <p class="text-blue-100 text-sm font-medium">Available Ad Budget</p>
                        <p class="text-5xl font-bold mt-2">{{ formatCurrency(balance) }}</p>
                        <p class="text-blue-100 text-xs mt-4">Funds here are used to allocate budgets for your campaigns.</p>
                    </div>

                    <!-- Deposit Form -->
                    <div class="bg-white rounded-2xl shadow-sm p-8">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Add Funds</h3>
                        <form @submit.prevent="submitDeposit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                                <input
                                    v-model="depositForm.amount"
                                    type="number"
                                    min="1000"
                                    step="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Minimum ₦1,000"
                                />
                                <p v-if="depositForm.errors.amount" class="text-red-600 text-sm mt-1">{{ depositForm.errors.amount }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" v-for="preset in presets" :key="preset" @click="depositForm.amount = preset"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-colors">
                                    {{ formatCurrency(preset) }}
                                </button>
                            </div>
                            <button
                                type="submit"
                                :disabled="depositForm.processing"
                                class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-colors disabled:opacity-50"
                            >
                                <span v-if="depositForm.processing">Redirecting to Paystack...</span>
                                <span v-else>Pay via Paystack</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Transaction History</h3>
                    </div>

                    <div v-if="transactions.data.length > 0" class="divide-y divide-gray-100">
                        <div v-for="txn in transactions.data" :key="txn.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="isCredit(txn.type) ? 'bg-green-100' : 'bg-red-100'">
                                    <svg class="w-4 h-4" :class="isCredit(txn.type) ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isCredit(txn.type) ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ formatType(txn.type) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ txn.description }}</p>
                                    <p v-if="txn.offer?.name" class="text-xs text-blue-600 font-medium mt-0.5">{{ txn.offer.name }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-bold" :class="isCredit(txn.type) ? 'text-green-600' : 'text-red-600'">
                                    {{ isCredit(txn.type) ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                                </p>
                                <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block" :class="statusBadgeClass(txn.status)">{{ txn.status }}</span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(txn.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No transactions yet. Add funds to get started.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="transactions.data.length > 0 && transactions.links" class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                        <component
                            v-for="(link, i) in transactions.links"
                            :key="i"
                            :is="link.url ? Link : 'span'"
                            :href="link.url || undefined"
                            :class="['px-3 py-1.5 rounded-lg text-sm', link.active ? 'bg-blue-600 text-white' : link.url ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed']"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    balance: Number,
    transactions: Object,
});

const presets = [5000, 10000, 25000, 50000, 100000, 250000];

const depositForm = useForm({ amount: '' });

const submitDeposit = () => {
    depositForm.post(route('advertiser.wallet.deposit'));
};

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount ?? 0);

const formatDate = (date) =>
    new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const formatType = (type) => ({
    deposit: 'Deposit',
    offer_allocation: 'Campaign Budget Allocation',
    offer_topup: 'Campaign Top-up',
    offer_refund: 'Campaign Refund',
}[type] ?? type);

const isCredit = (type) => ['deposit', 'offer_refund'].includes(type);

const statusBadgeClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-800');
</script>
