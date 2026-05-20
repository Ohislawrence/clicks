<template>
    <AppLayout title="Sales Payouts">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Sales Payouts</h2>
                    <p class="mt-1 text-sm text-gray-600">Withdraw your store sales earnings</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Balance Card -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white shadow-lg">
                    <p class="text-sm text-emerald-100 mb-1">Available Balance</p>
                    <p class="text-4xl font-bold">₦{{ Number(balance).toLocaleString() }}</p>
                    <p class="text-sm text-emerald-100 mt-2">Earnings from platform-managed store sales</p>
                </div>

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $page.props.flash.error }}
                </div>

                <!-- Withdrawal Request Form -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Withdrawal</h3>

                    <div v-if="hasPending" class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-3 text-sm mb-4">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        You have a pending withdrawal request. Cancel it or wait for it to be processed before submitting a new one.
                    </div>

                    <form v-else @submit.prevent="submitWithdrawal" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                                <input
                                    v-model.number="form.amount"
                                    type="number"
                                    min="500"
                                    :max="balance"
                                    step="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Minimum ₦500"
                                />
                                <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                <input
                                    v-model="form.bank_name"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g. Zenith Bank"
                                />
                                <p v-if="form.errors.bank_name" class="mt-1 text-sm text-red-600">{{ form.errors.bank_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                <input
                                    v-model="form.account_number"
                                    type="text"
                                    maxlength="10"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="10-digit account number"
                                />
                                <p v-if="form.errors.account_number" class="mt-1 text-sm text-red-600">{{ form.errors.account_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                <input
                                    v-model="form.account_name"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Account holder name"
                                />
                                <p v-if="form.errors.account_name" class="mt-1 text-sm text-red-600">{{ form.errors.account_name }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing || balance < 500"
                                class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'Submitting...' : 'Request Withdrawal' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payout History -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Payout History</h3>
                    </div>

                    <div v-if="payouts.data.length === 0" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        No payout requests yet.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Bank Details</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Reference</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="payout in payouts.data" :key="payout.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ formatDate(payout.created_at) }}</td>
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900">₦{{ Number(payout.amount).toLocaleString() }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <div v-if="payout.payment_details" class="text-xs space-y-0.5">
                                            <p class="font-medium text-gray-800">{{ payout.payment_details.bank_name }}</p>
                                            <p>{{ payout.payment_details.account_number }} · {{ payout.payment_details.account_name }}</p>
                                        </div>
                                        <span v-else class="text-gray-400">–</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="statusBadgeClass(payout.status)">{{ payout.status }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-gray-400 font-mono">{{ payout.reference || '–' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <button
                                            v-if="payout.status === 'pending'"
                                            @click="cancelPayout(payout)"
                                            class="text-xs text-red-500 hover:text-red-700 font-medium"
                                        >
                                            Cancel
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="payouts.last_page > 1" class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                        <p class="text-sm text-gray-500">Page {{ payouts.current_page }} of {{ payouts.last_page }}</p>
                        <div class="flex gap-2">
                            <Link v-if="payouts.prev_page_url" :href="payouts.prev_page_url" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Previous</Link>
                            <Link v-if="payouts.next_page_url" :href="payouts.next_page_url" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Next</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    balance: Number,
    payouts: Object,
});

const hasPending = computed(() => props.payouts.data.some(p => p.status === 'pending'));

const form = useForm({
    amount: null,
    bank_name: '',
    account_number: '',
    account_name: '',
});

const submitWithdrawal = () => {
    form.post(route('advertiser.payouts.sales.store'), {
        onSuccess: () => form.reset(),
    });
};

const cancelPayout = (payout) => {
    if (confirm('Are you sure you want to cancel this withdrawal request?')) {
        router.delete(route('advertiser.payouts.sales.cancel', payout.id));
    }
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' });
};

const statusBadgeClass = (status) => {
    const map = {
        pending: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700',
        processing: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-blue-100 text-blue-700',
        completed: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-green-100 text-green-700',
        failed: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700',
        rejected: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700',
    };
    return map[status] || map.pending;
};
</script>
