<template>
    <AppLayout :title="offer.name">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('affiliate.offers.index')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ offer.name }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ offer.category?.name }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Offer Image -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="h-64 bg-gradient-to-br from-blue-500 to-purple-600 relative">
                                <img
                                    v-if="offer.thumbnail"
                                    :src="offer.thumbnail"
                                    :alt="offer.name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="flex items-center justify-center h-full">
                                    <span class="text-white text-6xl font-bold">{{ offer.name.charAt(0) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">About this Offer</h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {{ offer.description }}
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div v-if="offer.terms_and_conditions" class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Terms & Conditions</h3>
                            <div class="prose prose-sm max-w-none text-gray-600">
                                {{ offer.terms_and_conditions }}
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Commission Card -->
                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
                            <p class="text-sm opacity-90 mb-2">Commission</p>
                            <h3 class="text-4xl font-bold mb-4">
                                <span v-if="offer.commission_model === 'revshare'">
                                    {{ offer.commission_rate }}%
                                </span>
                                <span v-else>
                                    {{ formatCurrency(offer.commission_rate) }}
                                </span>
                            </h3>
                            <div class="flex items-center justify-between text-sm opacity-90">
                                <span>Model:</span>
                                <span class="font-semibold">{{ offer.commission_model.toUpperCase() }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm opacity-90 mt-2">
                                <span>Cookie Duration:</span>
                                <span class="font-semibold">{{ offer.cookie_duration }} days</span>
                            </div>
                        </div>

                        <!-- Stats Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total Clicks</span>
                                    <span class="text-lg font-bold text-gray-900">{{ offer.total_clicks.toLocaleString() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Conversions</span>
                                    <span class="text-lg font-bold text-gray-900">{{ offer.total_conversions.toLocaleString() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Conversion Rate</span>
                                    <span class="text-lg font-bold text-gray-900">{{ conversionRate }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <!-- Has Access - Show Link -->
                            <div v-if="hasAccess">
                                <div v-if="affiliateLink">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Affiliate Link</h3>

                                    <!-- Discount Badge -->
                                    <div v-if="affiliateLink.discount_enabled && affiliateLink.discount_percentage" class="mb-4">
                                        <div class="bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-300 rounded-lg p-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-2xl">💰</span>
                                                    <div>
                                                        <p class="text-sm font-bold text-green-900">{{ affiliateLink.discount_percentage }}% Discount Active</p>
                                                        <p class="text-xs text-green-700">Your customers save on every purchase!</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <p class="text-xs text-gray-600 mb-2">Tracking URL</p>
                                        <div class="flex items-center space-x-2">
                                            <input
                                                :value="affiliateLink.tracking_url"
                                                readonly
                                                class="flex-1 text-sm bg-white border-gray-300 rounded-lg"
                                            />
                                            <button
                                                @click="copyToClipboard(affiliateLink.tracking_url)"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                            >
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-center">
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-2xl font-bold text-blue-600">{{ affiliateLink.click_count }}</p>
                                            <p class="text-xs text-gray-600">Clicks</p>
                                        </div>
                                        <div class="bg-green-50 rounded-lg p-3">
                                            <p class="text-2xl font-bold text-green-600">{{ affiliateLink.conversion_count }}</p>
                                            <p class="text-xs text-gray-600">Conversions</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Get Started</h3>
                                    <form @submit.prevent="generateLink" class="space-y-4">
                                        <!-- Discount Option -->
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                            <div class="flex items-start space-x-3">
                                                <input
                                                    type="checkbox"
                                                    v-model="linkForm.discount_enabled"
                                                    id="discount_enabled"
                                                    class="mt-1 rounded text-green-600 focus:ring-green-500"
                                                />
                                                <div class="flex-1">
                                                    <label for="discount_enabled" class="block text-sm font-semibold text-gray-900 cursor-pointer">
                                                        💰 Offer Discount to Customers
                                                    </label>
                                                    <p class="text-xs text-gray-600 mt-1">
                                                        Give your customers a discount to boost conversions!
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Discount Percentage Input -->
                                            <div v-if="linkForm.discount_enabled" class="mt-3 pl-7">
                                                <label for="discount_percentage" class="block text-xs font-medium text-gray-700 mb-1">
                                                    Discount Percentage
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <input
                                                        type="number"
                                                        v-model="linkForm.discount_percentage"
                                                        id="discount_percentage"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        class="flex-1 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                                        placeholder="e.g., 10"
                                                    />
                                                    <span class="text-gray-600 font-medium">%</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Your commission will be calculated on the discounted price
                                                </p>
                                            </div>
                                        </div>

                                        <button
                                            type="submit"
                                            class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors"
                                        >
                                            Generate Affiliate Link
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Pending Access Request -->
                            <div v-else-if="accessRequest && accessRequest.status === 'pending'">
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Request Pending</h3>
                                    <p class="mt-1 text-sm text-gray-600">Your access request is being reviewed</p>
                                </div>
                            </div>

                            <!-- Rejected Access Request -->
                            <div v-else-if="accessRequest && accessRequest.status === 'rejected'">
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-900">Request Rejected</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ accessRequest.rejection_reason }}</p>
                                </div>
                            </div>

                            <!-- Request Access Form -->
                            <div v-else>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Access</h3>
                                <p class="text-sm text-gray-600 mb-4">This is a private offer. Please tell the advertiser why you'd like to promote it.</p>
                                <form @submit.prevent="submitAccessRequest">
                                    <textarea
                                        v-model="form.message"
                                        rows="4"
                                        placeholder="Tell us about your traffic source, audience, and why you're interested..."
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 mb-4"
                                        required
                                    ></textarea>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                                    >
                                        Submit Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    offer: Object,
    hasAccess: Boolean,
    accessRequest: Object,
    affiliateLink: Object
});

const form = useForm({
    message: ''
});

const linkForm = reactive({
    discount_enabled: false,
    discount_percentage: null
});

const conversionRate = computed(() => {
    if (props.offer.total_clicks === 0) return 0;
    return ((props.offer.total_conversions / props.offer.total_clicks) * 100).toFixed(2);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};

const submitAccessRequest = () => {
    form.post(route('affiliate.offers.request-access', props.offer.id));
};

const generateLink = () => {
    router.post(route('affiliate.links.store'), {
        offer_id: props.offer.id,
        discount_enabled: linkForm.discount_enabled,
        discount_percentage: linkForm.discount_enabled ? linkForm.discount_percentage : null
    });
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copied to clipboard!');
    });
};
</script>
