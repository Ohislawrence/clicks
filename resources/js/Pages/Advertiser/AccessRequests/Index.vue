<template>
    <AppLayout title="Access Requests">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Access Requests</h2>
                <p class="mt-1 text-sm text-gray-600">Review affiliate access requests for private offers</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <select 
                            v-model="searchForm.status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
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
                </div>

                <!-- Access Requests List -->
                <div v-if="accessRequests.data.length > 0" class="space-y-4">
                    <div
                        v-for="request in accessRequests.data"
                        :key="request.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ request.affiliate?.name }}</h3>
                                        <span 
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': request.status === 'pending',
                                                'bg-green-100 text-green-800': request.status === 'approved',
                                                'bg-red-100 text-red-800': request.status === 'rejected'
                                            }"
                                        >
                                            {{ request.status }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Affiliate Email</p>
                                            <p class="text-sm font-medium text-gray-900">{{ request.affiliate?.email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Requested Offer</p>
                                            <p class="text-sm font-medium text-gray-900">{{ request.offer?.name }}</p>
                                        </div>
                                        <div v-if="request.affiliate?.follower_count">
                                            <p class="text-sm text-gray-500">Followers</p>
                                            <p class="text-sm font-medium text-gray-900">{{ request.affiliate.follower_count.toLocaleString() }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Request Date</p>
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(request.created_at) }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Message</p>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm text-gray-700">{{ request.message }}</p>
                                        </div>
                                    </div>

                                    <!-- Social Media Links -->
                                    <div v-if="request.affiliate?.instagram_handle || request.affiliate?.tiktok_handle || request.affiliate?.youtube_channel" class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Social Media</p>
                                        <div class="flex items-center space-x-4">
                                            <a
                                                v-if="request.affiliate.instagram_handle"
                                                :href="`https://instagram.com/${request.affiliate.instagram_handle}`"
                                                target="_blank"
                                                class="text-sm text-blue-600 hover:text-blue-700 flex items-center space-x-1"
                                            >
                                                <span>📷</span>
                                                <span>@{{ request.affiliate.instagram_handle }}</span>
                                            </a>
                                            <a
                                                v-if="request.affiliate.tiktok_handle"
                                                :href="`https://tiktok.com/@${request.affiliate.tiktok_handle}`"
                                                target="_blank"
                                                class="text-sm text-blue-600 hover:text-blue-700 flex items-center space-x-1"
                                            >
                                                <span>🎵</span>
                                                <span>@{{ request.affiliate.tiktok_handle }}</span>
                                            </a>
                                            <a
                                                v-if="request.affiliate.youtube_channel"
                                                :href="`https://youtube.com/${request.affiliate.youtube_channel}`"
                                                target="_blank"
                                                class="text-sm text-blue-600 hover:text-blue-700 flex items-center space-x-1"
                                            >
                                                <span>📺</span>
                                                <span>{{ request.affiliate.youtube_channel }}</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Rejection Reason -->
                                    <div v-if="request.status === 'rejected' && request.rejection_reason" class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Rejection Reason</p>
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                            <p class="text-sm text-red-700">{{ request.rejection_reason }}</p>
                                        </div>
                                    </div>

                                    <!-- Review Info -->
                                    <div v-if="request.reviewed_at" class="text-xs text-gray-500">
                                        Reviewed {{ formatDate(request.reviewed_at) }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div v-if="request.status === 'pending'" class="ml-6 flex flex-col space-y-2">
                                    <button
                                        @click="approveRequest(request)"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
                                    >
                                        Approve
                                    </button>
                                    <button
                                        @click="openRejectModal(request)"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium"
                                    >
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No access requests</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ searchForm.status ? 'No requests match your filters' : 'You haven\'t received any access requests yet' }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="accessRequests.data.length > 0 && accessRequests.links.length > 3" class="mt-8">
                    <nav class="flex items-center justify-between">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ accessRequests.from }}</span> to 
                                <span class="font-medium">{{ accessRequests.to }}</span> of 
                                <span class="font-medium">{{ accessRequests.total }}</span> results
                            </p>
                        </div>
                        <div class="flex-1 flex justify-end">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <Link
                                    v-for="link in accessRequests.links"
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
                Reject Access Request
            </template>

            <template #content>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for rejecting this access request:
                </p>
                <textarea
                    v-model="rejectForm.rejection_reason"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="e.g., Low follower count, not aligned with target audience, etc."
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
                    Reject Request
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
    accessRequests: Object,
    offers: Array,
    filters: Object
});

const searchForm = reactive({
    status: props.filters.status || '',
    offer_id: props.filters.offer_id || ''
});

const showRejectModal = ref(false);
const currentRequest = ref(null);

const rejectForm = useForm({
    rejection_reason: ''
});

const applyFilters = () => {
    router.get(route('advertiser.access-requests.index'), searchForm, {
        preserveState: true,
        preserveScroll: true
    });
};

const approveRequest = (request) => {
    if (confirm('Are you sure you want to approve this access request?')) {
        router.post(route('advertiser.access-requests.approve', request.id));
    }
};

const openRejectModal = (request) => {
    currentRequest.value = request;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    currentRequest.value = null;
    rejectForm.reset();
};

const submitReject = () => {
    rejectForm.post(route('advertiser.access-requests.reject', currentRequest.value.id), {
        onSuccess: () => {
            closeRejectModal();
        }
    });
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
