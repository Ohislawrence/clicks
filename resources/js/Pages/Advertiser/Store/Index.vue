<template>
    <AppLayout title="My Stores">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">My Stores</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage all your online stores</p>
                </div>
                <Link :href="route('advertiser.store.setup')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Store
                    </Link>
                </div>
            </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Stores Grid -->
                <div v-if="stores.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="store in stores" :key="store.id"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-blue-400 hover:shadow-md transition-all shadow-sm">
                        <!-- Store Header -->
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-xl font-semibold text-gray-900">{{ store.name }}</h3>
                                <span :class="[
                                    'px-2 py-1 text-xs font-medium rounded',
                                    store.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                ]">
                                    {{ store.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 line-clamp-2">{{ store.description || 'No description' }}</p>
                            <div class="mt-3">
                                <a :href="`/store/${store.slug}`" target="_blank" class="text-sm text-blue-600 hover:underline">
                                    /store/{{ store.slug }}
                                </a>
                            </div>
                        </div>

                        <!-- Store Stats -->
                        <div class="p-6 bg-gray-50">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500">Plan</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ store.plan.name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Theme</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ store.theme.name }}</p>
                                </div>
                            </div>
                            
                            <!-- Subscription Status -->
                            <div v-if="store.subscription_status === 'expired'" 
                                class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-700 font-medium">Subscription Expired</p>
                                <p class="text-xs text-red-500 mt-1">Renew to reactivate your store</p>
                            </div>
                            <div v-else-if="store.days_until_expiry <= 7" 
                                class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-700 font-medium">Expires in {{ store.days_until_expiry }} days</p>
                                <p class="text-xs text-yellow-600 mt-1">Renew soon to avoid interruption</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
                            <Link :href="route('advertiser.store.dashboard', store.id)"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Dashboard
                            </Link>
                            <Link :href="route('advertiser.store.edit', store.id)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </Link>
                            <Link :href="route('advertiser.store.products.index', store.id)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Stores Yet</h3>
                    <p class="text-gray-500 mb-6">Create your first online store to start selling</p>
                    <Link :href="route('advertiser.store.setup')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Your First Store
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stores: Array,
});
</script>
