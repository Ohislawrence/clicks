<template>
    <AppLayout title="Store Plans">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Store Plans</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage subscription plans for advertiser stores</p>
                </div>
                <Link :href="route('admin.store-plans.create')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    Create New Plan
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash?.error" class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition"
                    >
                        <!-- Plan Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ plan.name }}</h3>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="plan.store_type === 'single' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                                        {{ plan.store_type === 'single' ? 'Single Product' : 'Multi Product' }}
                                    </span>
                                </div>
                                <button
                                    @click="toggleActive(plan)"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                    :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </div>

                            <div class="mt-4">
                                <div class="text-3xl font-bold text-gray-900">
                                    ₦{{ formatNumber(plan.monthly_price) }}
                                    <span class="text-base font-normal text-gray-600">/month</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    ₦{{ formatNumber(plan.yearly_price) }}/year
                                    <span class="text-emerald-600 font-medium">({{ plan.yearly_discount_percent }}% off)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Details -->
                        <div class="p-6">
                            <div class="space-y-2 mb-4">
                                <div v-if="plan.product_limit" class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Up to {{ plan.product_limit }} products
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    {{ plan.features?.length || 0 }} features
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ plan.stores_count }} active stores
                                </div>
                            </div>

                            <!-- Features List -->
                            <div v-if="plan.features && plan.features.length > 0" class="border-t border-gray-200 pt-4 mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Features</p>
                                <ul class="space-y-1">
                                    <li v-for="(feature, index) in plan.features.slice(0, 3)" :key="index" class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ feature }}
                                    </li>
                                    <li v-if="plan.features.length > 3" class="text-sm text-gray-500 italic">
                                        +{{ plan.features.length - 3 }} more...
                                    </li>
                                </ul>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('admin.store-plans.edit', plan.id)"
                                    class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deletePlan(plan)"
                                    :disabled="plan.stores_count > 0"
                                    class="flex-1 text-center bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    :title="plan.stores_count > 0 ? 'Cannot delete plan in use' : 'Delete plan'"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="plans.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No store plans</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new subscription plan.</p>
                    <div class="mt-6">
                        <Link :href="route('admin.store-plans.create')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                            Create New Plan
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    plans: Array,
});

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-NG').format(value);
};

const toggleActive = (plan) => {
    if (confirm(`Are you sure you want to ${plan.is_active ? 'deactivate' : 'activate'} this plan?`)) {
        router.patch(route('admin.store-plans.toggle', plan.id), {}, {
            preserveScroll: true,
        });
    }
};

const deletePlan = (plan) => {
    if (plan.stores_count > 0) {
        alert('Cannot delete a plan that is currently in use by stores.');
        return;
    }

    if (confirm(`Are you sure you want to delete "${plan.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.store-plans.destroy', plan.id));
    }
};
</script>
