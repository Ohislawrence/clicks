<template>
    <AppLayout title="Store Refunds">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Store Refunds</h2>
                    <p class="mt-1 text-sm text-gray-600">Review and manage refund requests from platform-mode store orders</p>
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
                        <p class="text-sm text-gray-500">Requested</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ stats.requested }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Approved</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.approved }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Rejected</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ stats.rejected }}</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div v-if="orders.data.length === 0" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        No refund requests found.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Order</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Store / Advertiser</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Affiliate</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Refund Note</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Requested</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-mono font-medium text-gray-900">{{ order.order_number }}</p>
                                        <p class="text-xs text-gray-400">{{ order.payment_reference }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-medium text-gray-900">{{ order.store?.name }}</p>
                                        <p class="text-xs text-gray-400">{{ order.store?.user?.name }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-semibold text-gray-900">₦{{ Number(order.total_amount).toLocaleString() }}</p>
                                        <p class="text-xs text-gray-400">Net: ₦{{ Number(order.advertiser_net_amount).toLocaleString() }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        <span v-if="order.affiliate">{{ order.affiliate?.name }}</span>
                                        <span v-else class="text-gray-400">–</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600 max-w-xs">
                                        <p class="truncate" :title="order.refund_note">{{ order.refund_note || '–' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500">{{ order.refund_requested_at ? formatDate(order.refund_requested_at) : '–' }}</td>
                                    <td class="py-3 px-4">
                                        <span :class="refundBadgeClass(order.refund_status)">{{ order.refund_status }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div v-if="order.refund_status === 'requested'" class="flex items-center justify-end gap-2">
                                            <button @click="approveRefund(order)" class="px-3 py-1 text-xs bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                                                Approve
                                            </button>
                                            <button @click="openRejectModal(order)" class="px-3 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
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
                    <div v-if="orders.last_page > 1" class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                        <p class="text-sm text-gray-500">Page {{ orders.current_page }} of {{ orders.last_page }}</p>
                        <div class="flex gap-2">
                            <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Previous</Link>
                            <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">Next</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="rejectModal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Reject Refund Request</h3>
                <p class="text-sm text-gray-500 mb-4">Provide a note explaining why this refund is being rejected.</p>
                <textarea
                    v-model="rejectModal.note"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-red-400 focus:ring-red-400 text-sm"
                    placeholder="Reason for rejection..."
                ></textarea>
                <div class="flex justify-end gap-3 mt-4">
                    <button @click="rejectModal.open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">Cancel</button>
                    <button @click="submitReject" :disabled="rejectForm.processing" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition disabled:opacity-50">
                        {{ rejectForm.processing ? 'Rejecting...' : 'Reject Refund' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    orders: Object,
    stats: Object,
});

const rejectModal = ref({ open: false, order: null, note: '' });
const rejectForm = useForm({ refund_note: '' });

const approveRefund = (order) => {
    if (confirm(`Approve refund for order ${order.order_number}? This will reverse wallet credits and attempt a Paystack refund.`)) {
        router.post(route('admin.store-refunds.approve', order.id));
    }
};

const openRejectModal = (order) => {
    rejectModal.value = { open: true, order, note: '' };
};

const submitReject = () => {
    rejectForm.refund_note = rejectModal.value.note;
    rejectForm.post(route('admin.store-refunds.reject', rejectModal.value.order.id), {
        onSuccess: () => { rejectModal.value.open = false; },
    });
};

const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' });

const refundBadgeClass = (status) => {
    const map = {
        requested: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700',
        approved: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-green-100 text-green-700',
        rejected: 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700',
    };
    return map[status] || 'inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-gray-100 text-gray-500';
};
</script>
