<template>
    <AppLayout title="Store Management">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Store Management</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage all advertiser stores on the platform</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Total Stores</p>
                                <p class="text-white text-3xl font-bold mt-2">{{ stats.total.toLocaleString() }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Active Stores</p>
                                <p class="text-white text-3xl font-bold mt-2">{{ stats.active.toLocaleString() }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Expired</p>
                                <p class="text-white text-3xl font-bold mt-2">{{ stats.expired.toLocaleString() }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Subscription Revenue (Month)</p>
                                <p class="text-white text-3xl font-bold mt-2">₦{{ formatNumber(stats.revenue_this_month) }}</p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search stores..."
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
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <select
                            v-model="searchForm.subscription_status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Subscriptions</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <select
                            v-model="searchForm.plan"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Plans</option>
                            <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                {{ plan.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Stores Table -->
                <div v-if="stores.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Store
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Owner
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Plan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Products/Orders
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subscription
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
                                <tr v-for="store in stores.data" :key="store.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ store.name }}</div>
                                            <div class="text-sm text-gray-500">/store/{{ store.slug }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm text-gray-900">{{ store.user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ store.user.email }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">
                                            {{ store.plan.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ store.products_count }} / {{ store.orders_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-700': store.subscription_status === 'active',
                                                'bg-red-100 text-red-700': store.subscription_status === 'expired',
                                                'bg-gray-100 text-gray-500': store.subscription_status === 'cancelled',
                                            }"
                                        >
                                            {{ store.subscription_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-700': store.is_active,
                                                'bg-gray-100 text-gray-500': !store.is_active,
                                            }"
                                        >
                                            {{ store.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link
                                                :href="route('admin.stores.show', store.id)"
                                                class="text-blue-600 hover:text-blue-800 font-medium"
                                            >
                                                View
                                            </Link>
                                            <button
                                                @click="toggleStoreStatus(store)"
                                                class="font-medium"
                                                :class="store.is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'"
                                            >
                                                {{ store.is_active ? 'Suspend' : 'Activate' }}
                                            </button>
                                            <Link
                                                :href="route('admin.stores.edit', store.id)"
                                                class="text-gray-500 hover:text-gray-700 font-medium"
                                            >
                                                Edit
                                            </Link>
                                            <a
                                                :href="`/store/${store.slug}`"
                                                target="_blank"
                                                class="text-purple-500 hover:text-purple-400 font-medium"
                                            >
                                                Visit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="stores.links.length > 3" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing {{ stores.from }} to {{ stores.to }} of {{ stores.total }} stores
                            </div>
                            <div class="flex space-x-1">
                                <Link
                                    v-for="(link, index) in stores.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-lg',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200',
                                        !link.url && 'opacity-50 cursor-not-allowed',
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No stores found</h3>
                    <p class="mt-2 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    stores: Object,
    plans: Array,
    stats: Object,
    filters: Object,
});

const searchForm = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    subscription_status: props.filters.subscription_status || '',
    plan: props.filters.plan || '',
});

const applyFilters = () => {
    router.get(route('admin.stores.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

const toggleStoreStatus = (store) => {
    const action = store.is_active ? 'suspend' : 'activate';
    if (confirm(`Are you sure you want to ${action} "${store.name}"?`)) {
        router.post(route('admin.stores.toggle-status', store.id), {}, {
            preserveScroll: true,
        });
    }
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-NG').format(num || 0);
};
</script>
