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
                    <!-- Row 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="md:col-span-2 relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                            </span>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search offers by name or description..."
                                class="w-full pl-9 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                @input="debouncedSearch"
                            />
                        </div>

                        <select
                            v-model="searchForm.category"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Categories</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>

                        <select
                            v-model="searchForm.model"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Models</option>
                            <option value="pps">Pay Per Sale (PPS)</option>
                            <option value="ppl">Pay Per Lead (PPL)</option>
                            <option value="revshare">Revenue Share</option>
                        </select>
                        <select
                            v-model="searchForm.source"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Sources</option>
                            <option value="cpalead">CPAlead</option>
                            <option value="native">Native Offers</option>
                        </select>
                    </div>

                    <!-- Row 2 -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
                        <select
                            v-model="searchForm.sort"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="newest">Newest First</option>
                            <option value="commission_high">Highest Commission</option>
                            <option value="commission_low">Lowest Commission</option>
                            <option value="popular">Most Clicks</option>
                            <option value="converting">Best Converting</option>
                        </select>

                        <select
                            v-model="searchForm.access_type"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Access Types</option>
                            <option value="open">Open (Join Instantly)</option>
                            <option value="private">Invite Only</option>
                        </select>

                        <select
                            v-model="searchForm.country"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">Any Country</option>
                            <option v-for="c in countries" :key="c.code" :value="c.code">{{ c.flag }} {{ c.name }}</option>
                        </select>

                        <select
                            v-model="searchForm.device"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Devices</option>
                            <option value="desktop">🖥️ Desktop</option>
                            <option value="mobile">📱 Mobile</option>
                            <option value="tablet">📋 Tablet</option>
                        </select>

                        <div class="flex gap-2">
                            <div class="flex-1 flex gap-1">
                                <input
                                    v-model="searchForm.min_commission"
                                    type="number"
                                    min="0"
                                    placeholder="Min ₦"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    @change="applyFilters"
                                />
                                <input
                                    v-model="searchForm.max_commission"
                                    type="number"
                                    min="0"
                                    placeholder="Max ₦"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                    @change="applyFilters"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Active filter chips + reset -->
                    <div v-if="activeFilterCount > 0" class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                        <span class="text-xs text-gray-500 font-medium">Active filters:</span>
                        <span v-if="searchForm.search" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            "{{ searchForm.search }}"
                            <button @click="clearFilter('search')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.category" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ categoryName(searchForm.category) }}
                            <button @click="clearFilter('category')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.model" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ searchForm.model.toUpperCase() }}
                            <button @click="clearFilter('model')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.access_type" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ searchForm.access_type === 'open' ? 'Open' : 'Invite Only' }}
                            <button @click="clearFilter('access_type')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.country" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ countryLabel(searchForm.country) }}
                            <button @click="clearFilter('country')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.device" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            {{ searchForm.device }}
                            <button @click="clearFilter('device')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.min_commission" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            Min: {{ searchForm.min_commission }}
                            <button @click="clearFilter('min_commission')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <span v-if="searchForm.max_commission" class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs">
                            Max: {{ searchForm.max_commission }}
                            <button @click="clearFilter('max_commission')" class="ml-0.5 hover:text-blue-900">×</button>
                        </span>
                        <button
                            @click="resetFilters"
                            class="ml-auto text-xs text-red-500 hover:text-red-700 font-medium underline underline-offset-2"
                        >
                            Clear all
                        </button>
                    </div>
                </div>

                <!-- Offers Table -->
                <div v-if="offers.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Targeting</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-for="offer in offers.data" :key="offer.id" class="hover:bg-gray-50 transition-colors">
                                    <!-- Offer -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <img v-if="offer.product_image || offer.thumbnail" :src="offer.product_image || offer.thumbnail" :alt="offer.name" class="h-full w-full object-cover" />
                                                <span v-else class="text-white text-sm font-bold">{{ offer.name.charAt(0) }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ offer.name }}</p>
                                                <div class="flex flex-wrap gap-2 mt-1">
                                                    <span
                                                        class="inline-block px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                                        :style="{ backgroundColor: offer.category?.color || '#3B82F6' }"
                                                    >{{ offer.category?.name }}</span>
                                                    <span
                                                        v-if="offer.is_cpalead"
                                                        class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold text-white bg-slate-600"
                                                    >CPAlead</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Commission -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">
                                            <template v-if="offer.commission_model === 'revshare'">{{ offer.commission_rate }}%</template>
                                            <template v-else>{{ formatCurrency(offer.commission_rate) }}</template>
                                        </span>
                                    </td>
                                    <!-- Model -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded w-fit">
                                                {{ offer.commission_model.toUpperCase() }}
                                            </span>
                                            <span v-if="offer.commission_model === 'ppl'"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded w-fit"
                                                title="No purchase required — you earn on lead capture">
                                                📋 Lead
                                            </span>
                                            <span v-else
                                                class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded w-fit"
                                                title="You earn when a purchase is completed">
                                                🛒 Sale
                                            </span>
                                        </div>
                                    </td>
                                    <!-- Targeting -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span v-if="offer.target_countries?.length" class="text-xs text-gray-600 truncate" :title="offer.target_countries.join(', ')">
                                                🌍 {{ offer.target_countries.slice(0, 3).join(', ') }}{{ offer.target_countries.length > 3 ? ` +${offer.target_countries.length - 3}` : '' }}
                                            </span>
                                            <span v-else class="text-xs text-gray-400">🌍 Worldwide</span>
                                            <span v-if="offer.target_devices?.length" class="text-xs text-gray-500">
                                                {{ offer.target_devices.map(d => d === 'desktop' ? '🖥️' : d === 'mobile' ? '📱' : '📋').join(' ') }}
                                            </span>
                                            <span v-if="offer.require_unique_ip" class="text-xs text-amber-600" title="Unique IP per conversion required">🔒 Unique IP</span>
                                        </div>
                                    </td>
                                    <!-- Clicks -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ offer.total_clicks }}</td>
                                    <!-- Conversions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ offer.total_conversions }}</td>
                                    <!-- Action -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <Link
                                            :href="route('affiliate.offers.show', offer.id)"
                                            class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors"
                                        >
                                            View Details
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                                    <template v-for="link in offers.links" :key="link.label">
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                link.active
                                                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                            ]"
                                        />
                                        <span
                                            v-else
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-default"
                                        />
                                    </template>
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
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    offers: Object,
    categories: Array,
    filters: Object
});

const countries = [
    { code: 'NG', name: 'Nigeria', flag: '🇳🇬' },
    { code: 'GH', name: 'Ghana', flag: '🇬🇭' },
    { code: 'KE', name: 'Kenya', flag: '🇰🇪' },
    { code: 'ZA', name: 'South Africa', flag: '🇿🇦' },
    { code: 'EG', name: 'Egypt', flag: '🇪🇬' },
    { code: 'TZ', name: 'Tanzania', flag: '🇹🇿' },
    { code: 'UG', name: 'Uganda', flag: '🇺🇬' },
    { code: 'ET', name: 'Ethiopia', flag: '🇪🇹' },
    { code: 'CI', name: "Côte d'Ivoire", flag: '🇨🇮' },
    { code: 'SN', name: 'Senegal', flag: '🇸🇳' },
    { code: 'US', name: 'United States', flag: '🇺🇸' },
    { code: 'GB', name: 'United Kingdom', flag: '🇬🇧' },
    { code: 'CA', name: 'Canada', flag: '🇨🇦' },
    { code: 'AU', name: 'Australia', flag: '🇦🇺' },
    { code: 'DE', name: 'Germany', flag: '🇩🇪' },
    { code: 'FR', name: 'France', flag: '🇫🇷' },
    { code: 'IN', name: 'India', flag: '🇮🇳' },
    { code: 'BR', name: 'Brazil', flag: '🇧🇷' },
    { code: 'PH', name: 'Philippines', flag: '🇵🇭' },
    { code: 'PK', name: 'Pakistan', flag: '🇵🇰' },
    { code: 'BD', name: 'Bangladesh', flag: '🇧🇩' },
    { code: 'ID', name: 'Indonesia', flag: '🇮🇩' },
    { code: 'MY', name: 'Malaysia', flag: '🇲🇾' },
    { code: 'ZW', name: 'Zimbabwe', flag: '🇿🇼' },
    { code: 'RW', name: 'Rwanda', flag: '🇷🇼' },
    { code: 'CM', name: 'Cameroon', flag: '🇨🇲' },
    { code: 'ZM', name: 'Zambia', flag: '🇿🇲' },
];

const searchForm = reactive({
    search: props.filters.search || '',
    category: props.filters.category || '',
    model: props.filters.model || '',
    source: props.filters.source || '',
    sort: props.filters.sort || 'newest',
    access_type: props.filters.access_type || '',
    country: props.filters.country || '',
    device: props.filters.device || '',
    min_commission: props.filters.min_commission || '',
    max_commission: props.filters.max_commission || '',
});

const activeFilterCount = computed(() => {
    return [
        searchForm.search,
        searchForm.category,
        searchForm.model,
        searchForm.access_type,
        searchForm.country,
        searchForm.device,
        searchForm.min_commission,
        searchForm.max_commission,
    ].filter(Boolean).length;
});

const categoryName = (id) => {
    const cat = props.categories.find(c => String(c.id) === String(id));
    return cat ? cat.name : id;
};

const countryLabel = (code) => {
    const c = countries.find(c => c.code === code);
    return c ? `${c.flag} ${c.name}` : code;
};

const applyFilters = () => {
    router.get(route('affiliate.offers.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const clearFilter = (key) => {
    searchForm[key] = '';
    applyFilters();
};

const resetFilters = () => {
    Object.assign(searchForm, {
        search: '',
        category: '',
        model: '',
        sort: 'newest',
        access_type: '',
        country: '',
        device: '',
        min_commission: '',
        max_commission: '',
    });
    applyFilters();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>
