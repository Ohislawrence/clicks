<template>
    <AppLayout title="Advertiser Payouts">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Advertiser Payouts</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage withdrawal requests from advertisers</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $page.props.flash.error }}
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ stats.pending }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Processing</p>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ stats.processing }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Total Paid Out</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">₦{{ Number(stats.total_paid).toLocaleString() }}</p>
                    </div>
                </div>

                <!-- Filter -->
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600">Filter:</span>
                    <div class="flex gap-2 flex-wrap">
                        <Link
                            v-for="s in ['all', 'pending', 'processing', 'completed', 'rejected']"
                            :key="s"
                            :href="route('admin.advertiser-payouts.index', { status: s })"
                            :class="[
                                'px-3 py-1 text-sm rounded-full font-medium transition',
                                (currentStatus === s || (s === 'all' && !currentStatus))
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            ]"
                        >{{ s.charAt(0).toUpperCase() + s.slice(1) }}</Link>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div v-if="payouts.data.length === 0" class="px-6 py-12 text-center text-gray-500">
                        No payout requests match this filter.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Advertiser</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Bank Details</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Requested</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="payout in payouts.data" :key="payout.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-medium text-gray-900">{{ payout.user?.name }}</p>
                                        <p class="text-xs text-gray-400">{{ payout.user?.email }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900">₦{{ Number(payout.amount).toLocaleString() }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <div v-if="payout.payment_details" class="text-xs space-y-0.5">
                                            <p class="font-medium">{{ payout.payment_details.bank_name }}</p>
                                            <p>{{ payout.payment_details.account_number }}</p>
                                            <p class="text-gray-400">{{ payout.payment_details.account_name }}</p>
                                        </div>
                                        <span v-else class="text-gray-400">–</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="statusBadgeClass(payout.status)">{{ payout.status }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500">{{ formatDate(payout.created_at) }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <div v-if="payout.status === 'pending' || payout.status === 'processing'" class="flex items-center justify-end gap-2">
                                            <button @click="approvePayout(payout)" class="px-3 py-1 text-xs bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                                                Approve
                                            </button>
                                            <button @click="openRejectModal(payout)" class="px-3 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
                                                Reject
                                            </button>
                                        </div>
                                        <span v-else class="text-xs text-gray-400">–</span>
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

        <!-- Reject Modal -->
        <div v-if="rejectModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Reject Payout</h3>
                <p class="text-sm text-gray-500 mb-4">Provide a reason for rejecting this withdrawal request. The balance will be refunded to the advertiser.</p>
                <textarea
                    v-model="rejectModal.notes"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-red-400 text-sm"
                    placeholder="Reason for rejection..."
                ></textarea>
                <div class="flex justify-end gap-3 mt-4">
                    <button @click="rejectModal.open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button @click="submitReject" :disabled="rejectForm.processing" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition disabled:opacity-50">
                        {{ rejectForm.processing ? 'Rejecting...' : 'Reject Payout' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    payouts: Object,
    stats: Object,
});

const page = usePage();
const currentStatus = computed(() => page.props.ziggy?.query?.status);

const rejectModal = ref({ open: false, payout: null, notes: '' });
const rejectForm = useForm({ notes: '' });

const approvePayout = (payout) => {
    if (confirm(`Approve ₦${Number(payout.amount).toLocaleString()} withdrawal for ${payout.user?.name}?`)) {
        router.post(route('admin.advertiser-payouts.approve', payout.id));
    }
};

const openRejectModal = (payout) => {
    rejectModal.value = { open: true, payout, notes: '' };
};

const submitReject = () => {
    rejectForm.notes = rejectModal.value.notes;
    rejectForm.post(route('admin.advertiser-payouts.reject', rejectModal.value.payout.id), {
        onSuccess: () => { rejectModal.value.open = false; },
    });
};

const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' });

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
