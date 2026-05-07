<template>
    <AppLayout title="Conversion Management">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Conversion Management</h2>
                    <p class="mt-1 text-sm text-gray-600">Review and approve affiliate conversions</p>
                </div>
                <div>
                    <a
                        :href="route('advertiser.conversions.create')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Report Manual Conversion
                    </a>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search by transaction ID..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                @input="debouncedSearch"
                            />
                        </div>

                        <select
                            v-model="searchForm.status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="paid">Paid</option>
                        </select>

                        <select
                            v-model="searchForm.offer_id"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Offers</option>
                            <option v-for="offer in offers" :key="offer.id" :value="offer.id">
                                {{ offer.name }}
                            </option>
                        </select>
                    </div>

                    <div v-if="selectedConversions.length > 0" class="mt-4 flex items-center justify-between bg-blue-50 rounded-lg p-3">
                        <span class="text-sm text-blue-900">
                            {{ selectedConversions.length }} conversion(s) selected
                        </span>
                        <button
                            @click="bulkApprove"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                        >
                            Approve Selected
                        </button>
                    </div>
                </div>

                <!-- Conversions Table -->
                <div v-if="conversions.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            @change="toggleAll"
                                            :checked="isAllSelected"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Transaction
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Affiliate
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Offer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Value
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Commission
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="conversion in conversions.data" :key="conversion.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <input
                                            v-if="conversion.status === 'pending'"
                                            type="checkbox"
                                            :value="conversion.id"
                                            v-model="selectedConversions"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ conversion.transaction_id || 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ conversion.tracking_method }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-600 font-mono font-semibold">{{ conversion.affiliate?.affiliate_code || 'Affiliate #' + conversion.affiliate_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ conversion.offer?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(conversion.conversion_value) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-orange-600">
                                            {{ formatCurrency(conversion.commission_amount) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': conversion.status === 'pending',
                                                'bg-green-100 text-green-800': conversion.status === 'approved',
                                                'bg-red-100 text-red-800': conversion.status === 'rejected',
                                                'bg-blue-100 text-blue-800': conversion.status === 'paid'
                                            }"
                                        >
                                            {{ conversion.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(conversion.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button
                                                v-if="conversion.status === 'pending'"
                                                @click="approveConversion(conversion)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Approve
                                            </button>
                                            <button
                                                v-if="conversion.status === 'pending'"
                                                @click="openRejectModal(conversion)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Reject
                                            </button>
                                            <span v-if="conversion.status === 'rejected'" class="text-xs text-gray-500">
                                                {{ conversion.rejection_reason }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="conversions.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                        <nav class="flex items-center justify-between">
                            <div class="hidden sm:block">
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ conversions.from }}</span> to
                                    <span class="font-medium">{{ conversions.to }}</span> of
                                    <span class="font-medium">{{ conversions.total }}</span> results
                                </p>
                            </div>
                            <div class="flex-1 flex justify-end">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="link in conversions.links"
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

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No conversions found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <DialogModal :show="showRejectModal" @close="closeRejectModal">
            <template #title>
                Reject Conversion
            </template>

            <template #content>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for rejecting this conversion:
                </p>
                <textarea
                    v-model="rejectForm.rejection_reason"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="e.g., Invalid transaction, fraudulent activity, etc."
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
                    Reject Conversion
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { debounce } from 'lodash';

const props = defineProps({
    conversions: Object,
    offers: Array,
    filters: Object
});

const searchForm = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    offer_id: props.filters.offer_id || ''
});

const selectedConversions = ref([]);
const showRejectModal = ref(false);
const currentConversion = ref(null);

const rejectForm = useForm({
    rejection_reason: ''
});

const isAllSelected = computed(() => {
    const pendingIds = props.conversions.data
        .filter(c => c.status === 'pending')
        .map(c => c.id);
    return pendingIds.length > 0 && pendingIds.every(id => selectedConversions.value.includes(id));
});

const applyFilters = () => {
    router.get(route('advertiser.conversions.index'), searchForm, {
        preserveState: true,
        preserveScroll: true
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const toggleAll = () => {
    if (isAllSelected.value) {
        selectedConversions.value = [];
    } else {
        selectedConversions.value = props.conversions.data
            .filter(c => c.status === 'pending')
            .map(c => c.id);
    }
};

const approveConversion = (conversion) => {
    if (confirm('Are you sure you want to approve this conversion?')) {
        router.post(route('advertiser.conversions.approve', conversion.id));
    }
};

const openRejectModal = (conversion) => {
    currentConversion.value = conversion;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    currentConversion.value = null;
    rejectForm.reset();
};

const submitReject = () => {
    rejectForm.post(route('advertiser.conversions.reject', currentConversion.value.id), {
        onSuccess: () => {
            closeRejectModal();
        }
    });
};

const bulkApprove = () => {
    if (confirm(`Are you sure you want to approve ${selectedConversions.value.length} conversion(s)?`)) {
        router.post(route('advertiser.conversions.bulk-approve'), {
            conversion_ids: selectedConversions.value
        }, {
            onSuccess: () => {
                selectedConversions.value = [];
            }
        });
    }
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
