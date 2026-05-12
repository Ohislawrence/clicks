<template>
    <AppLayout title="Orders">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Orders</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage your store orders</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Pending Payment</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_payment }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Paid Orders</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.paid }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Pending Fulfillment</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_fulfillment }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Revenue</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ formatNumber(stats.total_revenue) }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search orders..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @input="applyFilters"
                            />
                        </div>
                        <div>
                            <select
                                v-model="form.payment_status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @change="applyFilters"
                            >
                                <option value="">All Payment Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div>
                            <select
                                v-model="form.fulfillment_status"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @change="applyFilters"
                            >
                                <option value="">All Fulfillment Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <button
                                @click="clearFilters"
                                class="w-full px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div v-if="orders.data.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Order</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Customer</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Date</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Payment</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Fulfillment</th>
                                    <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Total</th>
                                    <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders.data" :key="order.id"
                                    class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <p class="text-gray-900 font-medium">#{{ order.order_number }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div>
                                            <p class="text-gray-900 font-medium">{{ order.customer_name }}</p>
                                            <p class="text-sm text-gray-500">{{ order.customer_email }}</p>
                                            <p v-if="order.customer_phone" class="text-sm text-gray-500">{{ order.customer_phone }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-gray-900">{{ formatDate(order.created_at) }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            getPaymentStatusClass(order.payment_status)
                                        ]">
                                            {{ formatStatus(order.payment_status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            getFulfillmentStatusClass(order.fulfillment_status)
                                        ]">
                                            {{ formatStatus(order.fulfillment_status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <p class="text-gray-900 font-medium">₦{{ formatNumber(order.total) }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                v-if="order.payment_status === 'pending'"
                                                @click="markAsPaid(order)"
                                                class="inline-block px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                                title="Mark as Paid">
                                                Mark Paid
                                            </button>
                                            <Link :href="route('advertiser.store.orders.show', [order.store_id, order.id])"
                                                class="inline-block p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-16 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-lg mb-2">No orders yet</p>
                        <p class="text-sm">Orders will appear here when customers make purchases</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="orders.data.length > 0" class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Showing {{ orders.from }} to {{ orders.to }} of {{ orders.total }} orders
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-for="(link, index) in orders.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-3 py-1 rounded-lg text-sm transition-colors',
                                link.active 
                                    ? 'bg-blue-600 text-white' 
                                    : link.url 
                                        ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' 
                                        : 'bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed'
                            ]"
                            :disabled="!link.url"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    orders: Object,
    store: Object,
    stats: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters.search || '',
    payment_status: props.filters.payment_status || '',
    fulfillment_status: props.filters.fulfillment_status || '',
});

const applyFilters = () => {
    router.get(route('advertiser.store.orders.index', props.store.id), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.payment_status = '';
    form.fulfillment_status = '';
    applyFilters();
};

const markAsPaid = (order) => {
    if (!confirm(`Mark order #${order.order_number} as paid?`)) return;
    router.patch(route('advertiser.store.orders.mark-paid', [props.store.id, order.id]), {}, {
        preserveScroll: true,
    });
};

const formatNumber = (number) => {
    return new Intl.NumberFormat('en-NG').format(number || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const getPaymentStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        paid: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        refunded: 'bg-purple-100 text-purple-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-500';
};

const getFulfillmentStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        processing: 'bg-blue-100 text-blue-700',
        shipped: 'bg-purple-100 text-purple-700',
        delivered: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-500';
};
</script>
