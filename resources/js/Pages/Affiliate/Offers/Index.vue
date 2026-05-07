<template>
    <AppLayout title="Browse Offers">
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">Browse Offers</h2>
            <p class="mt-1 text-sm text-gray-600">Find the perfect offers to promote and earn commissions</p>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search offers..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                @input="debouncedSearch"
                            />
                        </div>
                        
                        <select 
                            v-model="searchForm.category"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>

                        <select 
                            v-model="searchForm.model"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Models</option>
                            <option value="pps">Pay Per Sale (PPS)</option>
                            <option value="ppl">Pay Per Lead (PPL)</option>
                            <option value="revshare">Revenue Share</option>
                        </select>
                    </div>
                </div>

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
                            <div class="absolute top-4 right-4">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-semibold text-white"
                                    :style="{ backgroundColor: offer.category?.color || '#3B82F6' }"
                                >
                                    {{ offer.category?.name }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ offer.name }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ offer.description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs text-gray-500">Commission</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        <span v-if="offer.commission_model === 'revshare'">
                                            {{ offer.commission_rate }}%
                                        </span>
                                        <span v-else>
                                            {{ formatCurrency(offer.commission_rate) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Model</p>
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                        {{ offer.commission_model.toUpperCase() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <span>{{ offer.total_clicks }} clicks</span>
                                <span>{{ offer.total_conversions }} conversions</span>
                            </div>

                            <Link
                                :href="route('affiliate.offers.show', offer.id)"
                                class="block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors"
                            >
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No offers found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>

                <!-- Pagination -->
                <div v-if="offers.data.length > 0" class="mt-8">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="offers.prev_page_url"
                                :href="offers.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link
                                v-if="offers.next_page_url"
                                :href="offers.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ offers.from }}</span> to <span class="font-medium">{{ offers.to }}</span> of
                                    <span class="font-medium">{{ offers.total }}</span> results
                                </p>
                            </div>
                            <div>
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
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    offers: Object,
    categories: Array,
    filters: Object
});

const searchForm = reactive({
    search: props.filters.search || '',
    category: props.filters.category || '',
    model: props.filters.model || ''
});

const applyFilters = () => {
    router.get(route('affiliate.offers.index'), searchForm, {
        preserveState: true,
        preserveScroll: true
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { 
        style: 'currency', 
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>
