<template>
    <AppLayout :title="`Store: ${store.name}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <Link :href="route('admin.stores.index')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h2 class="text-2xl font-bold text-gray-900">{{ store.name }}</h2>
                        <span
                            class="px-3 py-1 text-sm rounded-full font-medium"
                            :class="store.is_active
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'"
                        >
                            {{ store.is_active ? 'Active' : 'Suspended' }}
                        </span>
                        <span
                            class="px-3 py-1 text-sm rounded-full font-medium"
                            :class="{
                                'bg-blue-100 text-blue-700': store.subscription_status === 'active',
                                'bg-red-100 text-red-700': store.subscription_status === 'expired',
                                'bg-gray-100 text-gray-500': store.subscription_status === 'cancelled',
                            }"
                        >
                            Sub: {{ store.subscription_status }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">/store/{{ store.slug }} &mdash; Owner: {{ store.user.name }} ({{ store.user.email }})</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a
                        :href="`/store/${store.slug}`"
                        target="_blank"
                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Visit Store
                    </a>
                    <button
                        @click="toggleStatus"
                        class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium border"
                        :class="store.is_active
                            ? 'bg-red-50 text-red-700 hover:bg-red-100 border-red-200'
                            : 'bg-green-50 text-green-700 hover:bg-green-100 border-green-200'"
                    >
                        {{ store.is_active ? 'Suspend Store' : 'Activate Store' }}
                    </button>
                    <Link
                        :href="route('admin.stores.edit', store.id)"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 rounded-lg text-white hover:bg-blue-700 text-sm shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Products</p>
                        <p class="text-gray-900 text-3xl font-bold mt-2">{{ stats.total_products }}</p>
                        <p class="text-blue-600 text-sm mt-1">{{ stats.active_products }} active</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Orders</p>
                        <p class="text-gray-900 text-3xl font-bold mt-2">{{ stats.total_orders }}</p>
                        <p class="text-orange-600 text-sm mt-1">{{ stats.pending_orders }} pending</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Revenue</p>
                        <p class="text-gray-900 text-3xl font-bold mt-2">&#8358;{{ formatNumber(stats.total_revenue) }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">This Month</p>
                        <p class="text-gray-900 text-3xl font-bold mt-2">&#8358;{{ formatNumber(stats.revenue_this_month) }}</p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-6">
                        <button
                            v-for="tab in tabs"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-3 px-1 text-sm font-medium border-b-2 transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        >
                            {{ tab.label }}
                            <span v-if="tab.count !== undefined" class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">{{ tab.count }}</span>
                        </button>
                    </nav>
                </div>

                <!-- Tab: Overview -->
                <div v-if="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Store Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Store Information</h3>
                        <dl class="space-y-0 divide-y divide-gray-100">
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Owner</dt>
                                <dd class="text-sm text-gray-900 font-medium">{{ store.user.name }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ store.user.email }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Plan</dt>
                                <dd class="text-sm text-gray-900">{{ store.plan.name }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Billing Cycle</dt>
                                <dd class="text-sm text-gray-900 capitalize">{{ store.billing_cycle }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Theme</dt>
                                <dd class="text-sm text-gray-900">{{ store.theme.name }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">{{ formatDate(store.created_at) }}</dd>
                            </div>
                            <div class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Published</dt>
                                <dd class="text-sm" :class="store.published_at ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    {{ store.published_at ? formatDate(store.published_at) : 'Not published' }}
                                </dd>
                            </div>
                            <div v-if="store.phone || store.whatsapp_number" class="flex justify-between py-2.5">
                                <dt class="text-sm text-gray-500">Contact</dt>
                                <dd class="text-sm text-gray-900">{{ store.phone || store.whatsapp_number }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Subscription Actions -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Subscription Management</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-500">Status</span>
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-700': store.subscription_status === 'active',
                                        'bg-red-100 text-red-700': store.subscription_status === 'expired',
                                        'bg-gray-100 text-gray-500': store.subscription_status === 'cancelled',
                                    }"
                                >{{ store.subscription_status }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-500">Start Date</span>
                                <span class="text-sm text-gray-900">{{ formatDate(store.subscription_start_date) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-500">Expiry Date</span>
                                <span
                                    class="text-sm font-medium"
                                    :class="isExpiringSoon ? 'text-orange-600' : 'text-gray-900'"
                                >
                                    {{ formatDate(store.subscription_end_date) }}
                                    <span v-if="isExpiringSoon" class="ml-1 text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded">expiring soon</span>
                                </span>
                            </div>

                            <div class="pt-2 space-y-2">
                                <button
                                    @click="showExtendModal = true"
                                    class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium"
                                >
                                    Extend Subscription
                                </button>
                                <button
                                    @click="togglePublished"
                                    class="w-full px-4 py-2 text-sm rounded-lg font-medium border"
                                    :class="store.published_at
                                        ? 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100'
                                        : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'"
                                >
                                    {{ store.published_at ? 'Unpublish Store' : 'Publish Store' }}
                                </button>
                                <button
                                    v-if="store.subscription_status === 'active'"
                                    @click="cancelSubscription"
                                    class="w-full px-4 py-2 bg-red-50 text-red-700 border border-red-200 text-sm rounded-lg hover:bg-red-100 font-medium"
                                >
                                    Cancel Subscription
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Products -->
                <div v-if="activeTab === 'products'">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Products ({{ store.products.length }})</h3>
                            <span class="text-xs text-gray-500">{{ stats.active_products }} active &middot; {{ stats.total_products - stats.active_products }} inactive</span>
                        </div>
                        <div v-if="store.products.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in store.products" :key="product.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div v-if="product.images && product.images.length > 0" class="flex-shrink-0">
                                                    <img :src="`/storage/${product.images[0]}`" class="w-10 h-10 rounded-lg object-cover border border-gray-200" alt="" />
                                                </div>
                                                <div v-else class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                                    <div v-if="product.sku" class="text-xs text-gray-500">SKU: {{ product.sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">&#8358;{{ formatNumber(product.price) }}</div>
                                            <div v-if="product.compare_at_price" class="text-xs text-gray-400 line-through">&#8358;{{ formatNumber(product.compare_at_price) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            :class="product.stock_quantity === 0 ? 'text-red-600' : product.stock_quantity <= 5 ? 'text-orange-600' : 'text-gray-700'">
                                            {{ product.stock_quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 capitalize">
                                                {{ product.product_type || 'physical' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full"
                                                :class="product.is_active
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-gray-100 text-gray-500'"
                                            >{{ product.is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="product.is_featured" class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-medium">Featured</span>
                                            <span v-else class="text-xs text-gray-400">&mdash;</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(product.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500">No products added to this store yet.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab: Orders -->
                <div v-if="activeTab === 'orders'">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-base font-semibold text-gray-900">Recent Orders (last 20)</h3>
                        </div>
                        <div v-if="store.orders.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fulfillment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in store.orders" :key="order.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ order.order_number }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ order.customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ order.customer_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">&#8358;{{ formatNumber(order.total) }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full capitalize"
                                                :class="{
                                                'bg-green-100 text-green-700': order.payment_status === 'paid',
                                                'bg-yellow-100 text-yellow-700': order.payment_status === 'pending',
                                                'bg-red-100 text-red-700': order.payment_status === 'failed',
                                                }"
                                            >{{ order.payment_status }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full capitalize"
                                                :class="{
                                                'bg-green-100 text-green-700': order.fulfillment_status === 'fulfilled',
                                                'bg-orange-100 text-orange-700': order.fulfillment_status === 'pending',
                                                'bg-purple-100 text-purple-700': order.fulfillment_status === 'processing',
                                                }"
                                            >{{ order.fulfillment_status }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12 text-gray-500">
                            No orders yet.
                        </div>
                    </div>
                </div>

                <!-- Tab: Subscriptions -->
                <div v-if="activeTab === 'subscriptions'">
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Subscription History</h3>
                            <button
                                @click="showExtendModal = true"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 font-medium"
                            >
                                + Extend Subscription
                            </button>
                        </div>
                        <div v-if="store.subscriptions && store.subscriptions.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Billing</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="sub in store.subscriptions" :key="sub.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ sub.plan ? sub.plan.name : '&mdash;' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 capitalize">{{ sub.billing_cycle }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">&#8358;{{ formatNumber(sub.amount) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ formatDate(sub.period_start) }} &ndash; {{ formatDate(sub.period_end) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full capitalize"
                                                :class="{
                                                'bg-green-100 text-green-700': sub.status === 'paid',
                                                'bg-yellow-100 text-yellow-700': sub.status === 'pending',
                                                'bg-red-100 text-red-700': sub.status === 'failed',
                                                }"
                                            >{{ sub.status }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ sub.paid_at ? formatDate(sub.paid_at) : '&mdash;' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12 text-gray-500">
                            No subscription records found.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Extend Subscription Modal -->
        <div v-if="showExtendModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showExtendModal = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/75"></div>
                <div class="relative bg-white rounded-xl p-6 max-w-md w-full border border-gray-200 shadow-2xl">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Extend Subscription</h3>
                    <p class="text-sm text-gray-500 mb-5">{{ store.name }}</p>
                    <form @submit.prevent="extendSubscription">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                                <select
                                    v-model="extendForm.duration"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="1_month">1 Month</option>
                                    <option value="3_months">3 Months</option>
                                    <option value="6_months">6 Months</option>
                                    <option value="1_year">1 Year</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                                <textarea
                                    v-model="extendForm.reason"
                                    rows="3"
                                    placeholder="Admin note..."
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showExtendModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium"
                            >
                                Extend Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    stats: Object,
});

const activeTab = ref('overview');
const showExtendModal = ref(false);
const extendForm = reactive({
    duration: '1_month',
    reason: '',
});

const tabs = computed(() => [
    { key: 'overview', label: 'Overview' },
    { key: 'products', label: 'Products', count: props.stats.total_products },
    { key: 'orders', label: 'Orders', count: props.stats.total_orders },
    { key: 'subscriptions', label: 'Subscriptions', count: props.store.subscriptions ? props.store.subscriptions.length : 0 },
]);

const isExpiringSoon = computed(() => {
    if (!props.store.subscription_end_date) return false;
    const end = new Date(props.store.subscription_end_date);
    const diff = (end - new Date()) / (1000 * 60 * 60 * 24);
    return diff >= 0 && diff <= 7;
});

const toggleStatus = () => {
    const action = props.store.is_active ? 'suspend' : 'activate';
    if (confirm(`Are you sure you want to ${action} "${props.store.name}"?`)) {
        router.post(route('admin.stores.toggle-status', props.store.id));
    }
};

const togglePublished = () => {
    const action = props.store.published_at ? 'unpublish' : 'publish';
    if (confirm(`Are you sure you want to ${action} this store?`)) {
        router.post(route('admin.stores.toggle-published', props.store.id));
    }
};

const cancelSubscription = () => {
    if (confirm("Cancel this store's subscription? The store will also be deactivated.")) {
        router.post(route('admin.stores.cancel-subscription', props.store.id));
    }
};

const extendSubscription = () => {
    router.post(route('admin.stores.extend-subscription', props.store.id), extendForm, {
        onSuccess: () => {
            showExtendModal.value = false;
            extendForm.duration = '1_month';
            extendForm.reason = '';
        },
    });
};

const formatNumber = (num) => new Intl.NumberFormat('en-NG').format(num || 0);

const formatDate = (date) => {
    if (!date) return '\u2014';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>
