<template>
    <AppLayout title="Offer Management">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Offer Management</h2>
                <p class="mt-1 text-sm text-gray-600">Manage all platform offers across advertisers</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search offers..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                @input="debouncedSearch"
                            />
                        </div>

                        <select
                            v-model="searchForm.category_id"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>

                        <select
                            v-model="searchForm.status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <select
                            v-model="searchForm.approval_status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Approvals</option>
                            <option value="pending">Pending ({{ approvalCounts.pending }})</option>
                            <option value="approved">Approved ({{ approvalCounts.approved }})</option>
                            <option value="rejected">Rejected ({{ approvalCounts.rejected }})</option>
                        </select>

                        <select
                            v-model="searchForm.advertiser_id"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Advertisers</option>
                            <option v-for="advertiser in advertisers" :key="advertiser.id" :value="advertiser.id">
                                {{ advertiser.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Offers Table -->
                <div v-if="offers.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Offer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Advertiser
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Commission
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Performance
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="offer in offers.data" :key="offer.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <img
                                                    v-if="offer.thumbnail"
                                                    :src="offer.thumbnail"
                                                    :alt="offer.name"
                                                    class="h-12 w-12 rounded-lg object-cover"
                                                />
                                                <span v-else class="text-white font-bold text-lg">{{ offer.name.charAt(0) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="flex items-center space-x-2">
                                                    <div class="text-sm font-medium text-gray-900">{{ offer.name }}</div>
                                                    <svg v-if="offer.is_featured" class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <span
                                                        class="inline-block px-2 py-0.5 rounded text-xs"
                                                        :style="{ backgroundColor: offer.category?.color || '#E5E7EB', color: '#1F2937' }"
                                                    >
                                                        {{ offer.category?.name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ offer.advertiser?.name }}</div>
                                        <div class="text-sm text-gray-500">{{ offer.advertiser?.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <span v-if="offer.commission_model === 'revshare'">
                                                {{ offer.commission_rate }}%
                                            </span>
                                            <span v-else>
                                                {{ formatCurrency(offer.commission_rate) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 uppercase">{{ offer.commission_model }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <span class="font-medium">{{ offer.clicks_count || 0 }}</span>
                                                    <span class="text-gray-500 text-xs"> clicks</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium">{{ offer.conversions_count || 0 }}</span>
                                                    <span class="text-gray-500 text-xs"> conv</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <!-- Approval Status Badge -->
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-yellow-100 text-yellow-800': offer.approval_status === 'pending',
                                                    'bg-green-100 text-green-800': offer.approval_status === 'approved',
                                                    'bg-red-100 text-red-800': offer.approval_status === 'rejected'
                                                }"
                                            >
                                                {{ offer.approval_status === 'pending' ? 'Pending Review' :
                                                   offer.approval_status === 'approved' ? 'Approved' : 'Rejected' }}
                                            </span>
                                            <!-- Active Status Badge -->
                                            <span
                                                v-if="offer.approval_status === 'approved'"
                                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-green-100 text-green-800': offer.is_active,
                                                    'bg-gray-100 text-gray-800': !offer.is_active
                                                }"
                                            >
                                                {{ offer.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span
                                                v-if="offer.is_featured"
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"
                                            >
                                                Featured
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Approval Buttons (only for pending offers) -->
                                            <button
                                                v-if="offer.approval_status === 'pending'"
                                                @click="openApproveModal(offer)"
                                                class="text-green-600 hover:text-green-900"
                                                title="Approve Offer"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <button
                                                v-if="offer.approval_status === 'pending'"
                                                @click="openRejectModal(offer)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Reject Offer"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <!-- View Details Button -->
                                            <Link
                                                :href="route('admin.offers.show', offer.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="View Details"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>

                                            <button
                                                @click="toggleStatus(offer)"
                                                class="text-yellow-600 hover:text-yellow-900"
                                                :title="offer.is_active ? 'Deactivate Offer' : 'Activate Offer'"
                                            >
                                                <svg v-if="offer.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="toggleFeatured(offer)"
                                                :class="[
                                                    'hover:text-yellow-900',
                                                    offer.is_featured ? 'text-yellow-500' : 'text-gray-400'
                                                ]"
                                                title="Toggle Featured"
                                            >
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="confirmDelete(offer)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete Offer"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ offers.from || 0 }} to {{ offers.to || 0 }} of {{ offers.total || 0 }} offers
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    v-for="(link, index) in offers.links"
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || undefined"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm transition-colors',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : link.url
                                                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No offers found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="closeDeleteModal">
            <template #title>
                Delete Offer
            </template>

            <template #content>
                <p>Are you sure you want to delete <strong>{{ offerToDelete?.name }}</strong>? This action cannot be undone.</p>
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteOffer"
                    :disabled="deleteForm.processing"
                >
                    Delete Offer
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Reject Offer Modal -->
        <DialogModal :show="showRejectModal" @close="closeRejectModal">
            <template #title>
                Reject Offer
            </template>

            <template #content>
                <div class="space-y-4">
                    <p>You are about to reject the offer <strong>{{ offerToReject?.name }}</strong>.</p>
                    <p class="text-sm text-gray-600">The advertiser will be notified and can edit and resubmit the offer.</p>

                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="rejection_reason"
                            v-model="rejectForm.rejection_reason"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                            placeholder="Explain why this offer is being rejected (pricing issues, policy violations, etc.)"
                            required
                        />
                        <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-sm text-red-600">
                            {{ rejectForm.errors.rejection_reason }}
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeRejectModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="rejectOffer"
                    :disabled="rejectForm.processing || !rejectForm.rejection_reason"
                >
                    Reject Offer
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Approve Offer Modal -->
        <DialogModal :show="showApproveModal" @close="closeApproveModal">
            <template #title>
                Approve Offer
            </template>

            <template #content>
                <div class="space-y-4">
                    <p>You are about to approve the offer <strong>{{ offerToApprove?.name }}</strong>.</p>
                    <p class="text-sm text-gray-600">Set the platform spread percentage to determine platform profit margin.</p>

                    <!-- Offer Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Offer Details</h4>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Commission Model:</dt>
                                <dd class="font-medium text-gray-900">{{ offerToApprove?.commission_model?.toUpperCase() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Commission Rate:</dt>
                                <dd class="font-medium text-gray-900">
                                    <span v-if="offerToApprove?.commission_model === 'revshare'">
                                        {{ offerToApprove?.commission_rate }}%
                                    </span>
                                    <span v-else>
                                        ₦{{ parseFloat(offerToApprove?.commission_rate || 0).toLocaleString('en-NG', { minimumFractionDigits: 2 }) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Platform Spread Input -->
                    <div>
                        <label for="platform_spread" class="block text-sm font-medium text-gray-700 mb-2">
                            Platform Spread Percentage <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <input
                                id="platform_spread"
                                v-model="approveForm.platform_spread_percentage"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                class="flex-1 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                placeholder="25"
                                required
                            />
                            <span class="text-gray-600">%</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Recommended: 20-30% for optimal profitability. Minimum 10%.
                        </p>
                        <p v-if="approveForm.errors.platform_spread_percentage" class="mt-1 text-sm text-red-600">
                            {{ approveForm.errors.platform_spread_percentage }}
                        </p>
                    </div>

                    <!-- Spread Calculation Preview -->
                    <div v-if="approveForm.platform_spread_percentage && offerToApprove"
                        class="rounded-lg border p-4"
                        :class="{
                            'border-green-300 bg-green-50': spreadWarning === 'success',
                            'border-yellow-300 bg-yellow-50': spreadWarning === 'warning',
                            'border-red-300 bg-red-50': spreadWarning === 'danger'
                        }"
                    >
                        <h4 class="text-sm font-semibold mb-2"
                            :class="{
                                'text-green-900': spreadWarning === 'success',
                                'text-yellow-900': spreadWarning === 'warning',
                                'text-red-900': spreadWarning === 'danger'
                            }"
                        >
                            Payout Calculation Preview
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Affiliate Receives:</span>
                                <span class="font-medium">₦{{ parseFloat(calculatedAffiliatePayout).toLocaleString('en-NG', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Platform Margin:</span>
                                <span class="font-medium">₦{{ parseFloat(calculatedPlatformMargin).toLocaleString('en-NG', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 font-semibold"
                                :class="{
                                    'text-green-700': spreadWarning === 'success',
                                    'text-yellow-700': spreadWarning === 'warning',
                                    'text-red-700': spreadWarning === 'danger'
                                }"
                            >
                                <span>Advertiser Pays:</span>
                                <span>₦{{ parseFloat(calculatedAdvertiserPayout).toLocaleString('en-NG', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                        </div>
                        <p v-if="spreadWarning === 'danger'" class="mt-2 text-xs text-red-700">
                            ⚠️ Warning: Spread below 10% - Consider increasing for better profitability
                        </p>
                        <p v-else-if="spreadWarning === 'warning'" class="mt-2 text-xs text-yellow-700">
                            ⚠️ Notice: Spread below 20% - You may want to increase for optimal margins
                        </p>
                        <p v-else class="mt-2 text-xs text-green-700">
                            ✓ Good spread - Platform keeps {{ approveForm.platform_spread_percentage }}% margin
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeApproveModal">
                    Cancel
                </SecondaryButton>

                <button
                    class="ml-3 inline-flex justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    @click="approveOffer"
                    :disabled="approveForm.processing || !approveForm.platform_spread_percentage"
                >
                    Approve Offer
                </button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { debounce } from 'lodash';

const props = defineProps({
    offers: Object,
    categories: Array,
    advertisers: Array,
    approvalCounts: Object,
    filters: Object,
});

const searchForm = reactive({
    search: props.filters.search || '',
    category_id: props.filters.category_id || '',
    status: props.filters.status || '',
    approval_status: props.filters.approval_status || '',
    advertiser_id: props.filters.advertiser_id || '',
});

const showDeleteModal = ref(false);
const offerToDelete = ref(null);
const showRejectModal = ref(false);
const offerToReject = ref(null);
const showApproveModal = ref(false);
const offerToApprove = ref(null);

const deleteForm = useForm({});
const rejectForm = useForm({
    rejection_reason: '',
});
const approveForm = useForm({
    platform_spread_percentage: 25, // Default to 25%
});

// Calculated values for approve modal
const calculatedAffiliatePayout = computed(() => {
    if (!offerToApprove.value || !offerToApprove.value.commission_rate) {
        return 0;
    }
    // Affiliate gets the base commission rate
    return parseFloat(offerToApprove.value.commission_rate);
});

const calculatedPlatformMargin = computed(() => {
    if (!approveForm.platform_spread_percentage || !calculatedAffiliatePayout.value) {
        return 0;
    }
    return (calculatedAffiliatePayout.value * parseFloat(approveForm.platform_spread_percentage)) / 100;
});

const calculatedAdvertiserPayout = computed(() => {
    return calculatedAffiliatePayout.value + calculatedPlatformMargin.value;
});

const spreadWarning = computed(() => {
    if (!approveForm.platform_spread_percentage) return null;
    const spread = parseFloat(approveForm.platform_spread_percentage);
    if (spread < 10) return 'danger';
    if (spread < 20) return 'warning';
    return 'success';
});

const applyFilters = () => {
    router.get(route('admin.offers.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const toggleStatus = (offer) => {
    router.post(route('admin.offers.toggle', offer.id), {}, {
        preserveScroll: true,
    });
};

const toggleFeatured = (offer) => {
    router.post(route('admin.offers.featured', offer.id), {}, {
        preserveScroll: true,
    });
};

const confirmDelete = (offer) => {
    offerToDelete.value = offer;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    offerToDelete.value = null;
};

const deleteOffer = () => {
    deleteForm.delete(route('admin.offers.destroy', offerToDelete.value.id), {
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};

const openApproveModal = (offer) => {
    offerToApprove.value = offer;
    approveForm.platform_spread_percentage = 25; // Reset to default
    showApproveModal.value = true;
};

const closeApproveModal = () => {
    showApproveModal.value = false;
    offerToApprove.value = null;
    approveForm.reset();
};

const approveOffer = () => {
    approveForm.post(route('admin.offers.approve', offerToApprove.value.id), {
        onSuccess: () => {
            closeApproveModal();
        },
        preserveScroll: true,
    });
};

const openRejectModal = (offer) => {
    offerToReject.value = offer;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    offerToReject.value = null;
    rejectForm.reset();
};

const rejectOffer = () => {
    rejectForm.post(route('admin.offers.reject', offerToReject.value.id), {
        onSuccess: () => {
            closeRejectModal();
        },
        preserveScroll: true,
    });
};

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) {
        return '₦0.00';
    }
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount);
};
</script>
