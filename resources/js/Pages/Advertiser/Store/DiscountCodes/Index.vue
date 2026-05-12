<template>
    <AppLayout :title="store.name + ' - Discount Codes'">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Discount Codes</h2>
                    <p class="mt-1 text-sm text-gray-600">Create coupon codes for your customers</p>
                </div>
                <Link :href="route('advertiser.store.discount-codes.create', store.id)"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    + New Code
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success"
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="codes.length === 0"
                    class="bg-white border border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-gray-600 font-medium">No discount codes yet</p>
                    <p class="text-sm text-gray-400 mt-1">Create coupon codes to offer discounts to your customers</p>
                    <Link :href="route('advertiser.store.discount-codes.create', store.id)"
                        class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        Create First Code
                    </Link>
                </div>

                <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Code</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Discount</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Min Order</th>
                                <th class="text-center px-5 py-3 font-medium text-gray-600">Uses</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Expires</th>
                                <th class="text-center px-5 py-3 font-medium text-gray-600">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="code in codes" :key="code.id" class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <span class="font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-0.5 rounded text-xs">{{ code.code }}</span>
                                    <p v-if="code.description" class="text-xs text-gray-400 mt-0.5">{{ code.description }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    <span v-if="code.type === 'percentage'">{{ code.value }}% off</span>
                                    <span v-else>₦{{ Number(code.value).toLocaleString() }} off</span>
                                    <span v-if="code.max_discount_amount" class="text-xs text-gray-400 ml-1">(max ₦{{ Number(code.max_discount_amount).toLocaleString() }})</span>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    <span v-if="code.min_order_amount">₦{{ Number(code.min_order_amount).toLocaleString() }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-5 py-3 text-center text-gray-700">
                                    {{ code.uses_count }}<span v-if="code.max_uses" class="text-gray-400"> / {{ code.max_uses }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs">
                                    <span v-if="code.expires_at">{{ formatDate(code.expires_at) }}</span>
                                    <span v-else class="text-gray-400">Never</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <button @click="toggleCode(code)"
                                        :class="code.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                        class="px-2 py-0.5 rounded-full text-xs font-medium transition-colors">
                                        {{ code.is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                                    <Link :href="route('advertiser.store.discount-codes.edit', [store.id, code.id])"
                                        class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</Link>
                                    <button @click="deleteCode(code)"
                                        class="text-red-500 hover:text-red-600 text-xs font-medium">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <Link :href="route('advertiser.store.dashboard', store.id)"
                        class="text-sm text-gray-500 hover:text-gray-700">← Back to Store Dashboard</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    codes: Array,
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const toggleCode = (code) => {
    router.patch(route('advertiser.store.discount-codes.toggle', [props.store.id, code.id]));
};

const deleteCode = (code) => {
    if (!confirm(`Delete discount code "${code.code}"?`)) return;
    router.delete(route('advertiser.store.discount-codes.destroy', [props.store.id, code.id]));
};
</script>
