<template>
    <AppLayout title="Payout Management">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Payout Management</h2>
                <p class="mt-1 text-sm text-gray-600">Process affiliate payout requests</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <select 
                        v-model="searchForm.status"
                        @change="applyFilters"
                        class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Payouts List -->
                <div v-if="payouts.data.length > 0" class="space-y-4">
                    <div
                        v-for="payout in payouts.data"
                        :key="payout.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900">#{{ payout.id }} - {{ payout.affiliate?.name }}</h3>
                                        <span 
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': payout.status === 'pending',
                                                'bg-blue-100 text-blue-800': payout.status === 'processing',
                                                'bg-green-100 text-green-800': payout.status === 'completed',
                                                'bg-red-100 text-red-800': payout.status === 'failed' || payout.status === 'rejected',
                                                'bg-gray-100 text-gray-800': payout.status === 'cancelled'
                                            }"
                                        >
                                            {{ payout.status }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Amount</p>
                                            <p class="text-xl font-bold text-gray-900">{{ formatCurrency(payout.amount) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Payment Method</p>
                                            <p class="text-sm font-medium text-gray-900 capitalize">{{ payout.payment_method.replace('_', ' ') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Request Date</p>
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(payout.created_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Payment Details -->
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Payment Details</p>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div v-if="payout.payment_details.account_number" class="space-y-1 text-sm">
                                                <p><span class="font-medium">Account Name:</span> {{ payout.payment_details.account_name }}</p>
                                                <p><span class="font-medium">Account Number:</span> {{ payout.payment_details.account_number }}</p>
                                                <p><span class="font-medium">Bank:</span> {{ payout.payment_details.bank_name || payout.payment_details.bank_code }}</p>
                                            </div>
                                            <div v-else-if="payout.payment_details.email" class="text-sm">
                                                <p><span class="font-medium">Email:</span> {{ payout.payment_details.email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Affiliate Info -->
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500">Affiliate Email</p>
                                        <p class="text-sm font-medium text-gray-900">{{ payout.affiliate?.email }}</p>
                                    </div>

                                    <!-- Rejection/Failure Reason -->
                                    <div v-if="(payout.status === 'rejected' || payout.status === 'failed') && (payout.rejection_reason || payout.gateway_response?.error)" class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Reason</p>
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                            <p class="text-sm text-red-700">{{ payout.rejection_reason || payout.gateway_response?.error }}</p>
                                        </div>
                                    </div>

                                    <!-- Processing Info -->
                                    <div v-if="payout.processed_at" class="text-xs text-gray-500">
                                        Processed {{ formatDate(payout.processed_at) }}
                                        <span v-if="payout.completed_at"> • Completed {{ formatDate(payout.completed_at) }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="ml-6 flex flex-col space-y-2">
                                    <button
                                        v-if="payout.status === 'pending'"
                                        @click="processPayout(payout)"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                                    >
                                        Process
                                    </button>
                                    <button
                                        v-if="payout.status === 'pending'"
                                        @click="openRejectModal(payout)"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium"
                                    >
                                        Reject
                                    </button>
                                    <button
                                        v-if="payout.status === 'processing'"
                                        @click="markCompleted(payout)"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                                    >
                                        Mark Complete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payout requests</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchForm.status ? 'No requests match your filters' : 'No payout requests have been made yet' }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="payouts.data.length > 0 && payouts.links.length > 3" class="mt-8">
                    <nav class="flex items-center justify-between">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ payouts.from }}</span> to 
                                <span class="font-medium">{{ payouts.to }}</span> of 
                                <span class="font-medium">{{ payouts.total }}</span> results
                            </p>
                        </div>
                        <div class="flex-1 flex justify-end">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <Link
                                    v-for="link in payouts.links"
                                    :key="link.label"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active 
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                    ]"
                                />
                            </nav>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <DialogModal :show="showRejectModal" @close="closeRejectModal">
            <template #title>
                Reject Payout Request
            </template>

            <template #content>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for rejecting this payout request:
                </p>
                <textarea
                    v-model="rejectForm.rejection_reason"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="e.g., Invalid account details, suspicious activity, etc."
                ></textarea>
                <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-sm text-red-600">
                    {{ rejectForm.errors.rejection_reason }}
                </p>
            </template>

            <template #footer>
                <SecondaryButton @click="closeRejectModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="submitReject"
                    :disabled="rejectForm.processing"
                >
                    Reject Payout
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    payouts: Object,
    filters: Object
});

const searchForm = reactive({
    status: props.filters.status || ''
});

const showRejectModal = ref(false);
const currentPayout = ref(null);

const rejectForm = useForm({
    rejection_reason: ''
});

const applyFilters = () => {
    router.get(route('admin.payouts.index'), searchForm, {
        preserveState: true,
        preserveScroll: true
    });
};

const processPayout = (payout) => {
    if (confirm('Are you sure you want to process this payout? This will initiate the payment via the selected gateway.')) {
        router.post(route('admin.payouts.process', payout.id));
    }
};

const markCompleted = (payout) => {
    if (confirm('Mark this payout as completed?')) {
        router.post(route('admin.payouts.mark-completed', payout.id));
    }
};

const openRejectModal = (payout) => {
    currentPayout.value = payout;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    currentPayout.value = null;
    rejectForm.reset();
};

const submitReject = () => {
    rejectForm.post(route('admin.payouts.reject', currentPayout.value.id), {
        onSuccess: () => {
            closeRejectModal();
        }
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
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
