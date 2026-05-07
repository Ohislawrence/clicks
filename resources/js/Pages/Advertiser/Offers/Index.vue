<template>
    <AppLayout title="My Offers">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">My Offers</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage all your affiliate offers</p>
                </div>
                <Link
                    :href="route('advertiser.offers.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create Offer</span>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Offers Grid -->
                <div v-if="offers.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="offer in offers.data"
                        :key="offer.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 relative overflow-hidden">
                            <img
                                v-if="offer.thumbnail"
                                :src="offer.thumbnail"
                                :alt="offer.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="flex items-center justify-center h-full">
                                <span class="text-white text-4xl font-bold">{{ offer.name.charAt(0) }}</span>
                            </div>
                            <div class="absolute top-4 right-4 flex items-center space-x-2">
                                <!-- Approval Status Badge -->
                                <span
                                    v-if="offer.approval_status === 'pending'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500 text-white"
                                    title="Pending admin approval"
                                >
                                    Pending Approval
                                </span>
                                <span
                                    v-else-if="offer.approval_status === 'rejected'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white"
                                    title="Rejected by admin"
                                >
                                    Rejected
                                </span>
                                <span
                                    v-else-if="offer.approval_status === 'approved'"
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="offer.is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white'"
                                >
                                    {{ offer.is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold text-white bg-black bg-opacity-50"
                                >
                                    {{ offer.access_type === 'open' ? 'Open' : 'Request' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ offer.name }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ offer.description }}</p>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500">Commission</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        <span v-if="offer.commission_model === 'revshare'">
                                            {{ offer.commission_rate }}%
                                        </span>
                                        <span v-else>
                                            {{ formatCurrency(offer.commission_rate) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Model</p>
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                        {{ offer.commission_model.toUpperCase() }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-lg font-bold text-gray-900">{{ offer.total_clicks }}</p>
                                    <p class="text-xs text-gray-500">Clicks</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-lg font-bold text-gray-900">{{ offer.total_conversions }}</p>
                                    <p class="text-xs text-gray-500">Conversions</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-lg font-bold text-gray-900">{{ formatShortCurrency(offer.total_revenue) }}</p>
                                    <p class="text-xs text-gray-500">Revenue</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <Link
                                    :href="route('advertiser.offers.show', offer.id)"
                                    class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors"
                                >
                                    View Details
                                </Link>
                                <Link
                                    :href="route('advertiser.offers.edit', offer.id)"
                                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="toggleOffer(offer)"
                                    class="px-4 py-2 rounded-lg transition-colors"
                                    :class="offer.is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                                >
                                    {{ offer.is_active ? 'Pause' : 'Activate' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No offers yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first offer</p>
                    <div class="mt-6">
                        <Link
                            :href="route('advertiser.offers.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Create Offer
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="offers.data.length > 0 && offers.links.length > 3" class="mt-8">
                    <nav class="flex items-center justify-between">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ offers.from }}</span> to
                                <span class="font-medium">{{ offers.to }}</span> of
                                <span class="font-medium">{{ offers.total }}</span> results
                            </p>
                        </div>
                        <div class="flex-1 flex justify-end">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <Link
                                    v-for="link in offers.links"
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
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    offers: Object
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};

const formatShortCurrency = (amount) => {
    if (amount >= 1000000) {
        return '₦' + (amount / 1000000).toFixed(1) + 'M';
    } else if (amount >= 1000) {
        return '₦' + (amount / 1000).toFixed(1) + 'K';
    }
    return formatCurrency(amount);
};

const toggleOffer = (offer) => {
    if (confirm(`Are you sure you want to ${offer.is_active ? 'pause' : 'activate'} this offer?`)) {
        router.patch(route('advertiser.offers.toggle', offer.id));
    }
};
</script>
