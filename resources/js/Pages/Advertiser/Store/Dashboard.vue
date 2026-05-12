<template>
    <AppLayout title="Store Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ store.name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ store.description || 'Manage your online store' }}</p>
                </div>
                <div class="flex gap-3">
                        <a :href="storeUrl" target="_blank"
                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Store
                        </a>
                        <Link :href="route('advertiser.store.edit', store.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Edit Store
                        </Link>
                        <Link :href="route('advertiser.store.theme', store.id)"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Customize Theme
                        </Link>
                    </div>
                </div>
            </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Subscription Status Alert -->
                <div v-if="store.subscription_status !== 'active'" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-yellow-800 font-semibold">Store Not Active</h4>
                            <p class="text-yellow-700 text-sm mt-1">Your store is not published yet. Complete payment to activate your store.</p>
                            <Link :href="route('advertiser.store.subscription.index', store.id)"
                                class="mt-2 inline-block px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium">
                                Complete Payment
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else-if="stats.days_until_expiry !== null && stats.days_until_expiry >= 0 && stats.days_until_expiry <= 7" class="mb-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-orange-800 font-semibold">Subscription Expiring Soon</h4>
                            <p class="text-orange-700 text-sm mt-1">Your subscription expires in {{ stats.days_until_expiry }} day(s). Renew to keep your store active.</p>
                            <Link :href="route('advertiser.store.subscription.index', store.id)"
                                class="mt-2 inline-block px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium">
                                Renew Subscription
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Payment Not Configured Warning -->
                <div v-if="!store.payment_provider || !store.payment_public_key" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm0 6a1 1 0 011-1h5a1 1 0 010 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-red-800 font-semibold">Payment Not Configured</h4>
                            <p class="text-red-700 text-sm mt-1">Your store cannot accept orders until you add your Paystack or Flutterwave API keys.</p>
                            <Link :href="route('advertiser.store.edit', store.id)"
                                class="mt-2 inline-block px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                Add Payment Settings →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Products</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_products }}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ stats.active_products }} active</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_orders }}</p>
                            </div>
                            <div class="bg-indigo-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ stats.pending_orders }} pending</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ stats.total_revenue.toLocaleString() }}</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">All time</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Subscription</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ store.plan.name }}</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" v-if="stats.days_until_expiry !== null">
                            {{ stats.days_until_expiry }} days remaining
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <Link :href="route('advertiser.store.products.index', store.id)"
                        class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-400 hover:shadow-md transition-all shadow-sm group">
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-100 p-3 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Manage Products</h3>
                                <p class="text-sm text-gray-500 mt-1">Add and edit products</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('advertiser.store.orders.index', store.id)"
                        class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-400 hover:shadow-md transition-all shadow-sm group">
                        <div class="flex items-center gap-4">
                            <div class="bg-indigo-100 p-3 rounded-lg group-hover:bg-indigo-200 transition-colors">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">View Orders</h3>
                                <p class="text-sm text-gray-500 mt-1">Process customer orders</p>
                            </div>
                        </div>
                    </Link>

                    <Link :href="route('advertiser.store.subscription.index', store.id)"
                        class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-400 hover:shadow-md transition-all shadow-sm group">
                        <div class="flex items-center gap-4">
                            <div class="bg-purple-100 p-3 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Subscription</h3>
                                <p class="text-sm text-gray-500 mt-1">Manage your plan</p>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">Recent Orders</h3>
                        <Link :href="route('advertiser.store.orders.index', store.id)"
                            class="text-sm text-blue-600 hover:text-blue-800">
                            View All →
                        </Link>
                    </div>

                    <div v-if="recentOrders.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Order #</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Customer</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Amount</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Payment</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in recentOrders" :key="order.id" class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ order.order_number }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ order.customer_name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">₦{{ order.total.toLocaleString() }}</td>
                                    <td class="py-3 px-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                        ]">
                                            {{ order.payment_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            order.fulfillment_status === 'delivered' ? 'bg-green-100 text-green-700' :
                                            order.fulfillment_status === 'shipped' ? 'bg-blue-100 text-blue-700' :
                                            order.fulfillment_status === 'processing' ? 'bg-purple-100 text-purple-700' :
                                            'bg-gray-100 text-gray-500'
                                        ]">
                                            {{ order.fulfillment_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p>No orders yet</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    store: Object,
    stats: Object,
    recentOrders: Array,
    storeUrl: String,
});
</script>
