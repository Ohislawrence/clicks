<template>
    <AppLayout title="Manual Conversions">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manual Conversions (WhatsApp)</h2>
                <p class="mt-1 text-sm text-gray-600">Report conversions from WhatsApp chats using tracking IDs</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Report New Conversion -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Report New Conversion</h3>
                            <p class="text-sm text-gray-500 mt-1">Enter the tracking ID from the WhatsApp message</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitConversion" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Click ID (Tracking Reference) *
                                </label>
                                <input
                                    v-model="conversionForm.click_id"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono"
                                    placeholder="CLK-X7K92M"
                                />
                                <p v-if="conversionForm.errors.click_id" class="mt-1 text-sm text-red-600">
                                    {{ conversionForm.errors.click_id }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Find this in the WhatsApp message after "Ref:"
                                </p>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Conversion Amount (Optional)
                                </label>
                                <input
                                    v-model="conversionForm.conversion_amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                    placeholder="0.00"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Transaction value (for reporting)
                                </p>
                            </div>

                            <div class="md:col-span-1 flex items-end">
                                <button
                                    type="submit"
                                    :disabled="conversionForm.processing"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 font-medium"
                                >
                                    {{ conversionForm.processing ? 'Reporting...' : 'Report Conversion' }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (Optional)
                            </label>
                            <textarea
                                v-model="conversionForm.notes"
                                rows="2"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                placeholder="Additional details about this conversion..."
                            ></textarea>
                        </div>
                    </form>

                    <!-- Help Box -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">💡 How to find the Click ID:</p>
                                <ol class="list-decimal list-inside space-y-1 text-xs">
                                    <li>Open the WhatsApp conversation with the customer</li>
                                    <li>Look for their first message (it should have "Ref: CLK-XXXXXXX")</li>
                                    <li>Copy the Click ID and paste it above</li>
                                    <li>Submit to credit the affiliate automatically!</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Import Section -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Bulk Import Conversions</h3>
                            <p class="text-sm text-gray-500 mt-1">Upload CSV file with multiple conversions</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitBulkImport" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                CSV File *
                            </label>
                            <input
                                type="file"
                                @change="handleFileChange"
                                accept=".csv"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                CSV format: click_id, amount, notes
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="bulkForm.processing"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 font-medium"
                        >
                            {{ bulkForm.processing ? 'Importing...' : 'Import Conversions' }}
                        </button>
                    </form>

                    <!-- CSV Example -->
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900 mb-2">CSV Example:</p>
                        <pre class="text-xs text-gray-700 font-mono bg-white p-3 rounded border border-gray-200">click_id,amount,notes
CLK-X7K92M,50000,iPhone 15 Pro sale
CLK-P2Q8R1,35000,AirPods Pro sale
CLK-M4N8T3,120000,MacBook Air M2</pre>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Offer</label>
                            <select
                                v-model="filters.offer_id"
                                @change="applyFilters"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">All Offers</option>
                                <option v-for="offer in offers" :key="offer.id" :value="offer.id">
                                    {{ offer.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Click ID</label>
                            <input
                                v-model="filters.search"
                                @input="debouncedSearch"
                                type="text"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="CLK-X7K92M"
                            />
                        </div>

                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Conversions List -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Manual Conversions</h3>
                    </div>

                    <div v-if="conversions.data.length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Click ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Offer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Affiliate
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="conversion in conversions.data" :key="conversion.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <code class="text-sm font-mono text-gray-900">{{ conversion.click?.click_id || 'N/A' }}</code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 font-medium">{{ conversion.offer?.name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600 font-mono font-semibold">{{ conversion.affiliate?.affiliate_code || 'Affiliate #' + conversion.affiliate_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ formatCurrency(conversion.conversion_amount || 0) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800': conversion.status === 'approved',
                                                    'bg-yellow-100 text-yellow-800': conversion.status === 'pending',
                                                    'bg-red-100 text-red-800': conversion.status === 'rejected'
                                                }"
                                            >
                                                {{ conversion.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(conversion.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="conversions.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                            <nav class="flex items-center justify-between">
                                <div class="hidden sm:block">
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ conversions.from }}</span> to
                                        <span class="font-medium">{{ conversions.to }}</span> of
                                        <span class="font-medium">{{ conversions.total }}</span> results
                                    </p>
                                </div>
                                <div class="flex-1 flex justify-end">
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-for="link in conversions.links"
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

                    <!-- Empty State -->
                    <div v-else class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No manual conversions yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start reporting conversions from WhatsApp chats above</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    conversions: Object,
    offers: Array,
    filters: Object
});

const conversionForm = useForm({
    click_id: '',
    conversion_amount: '',
    notes: ''
});

const bulkForm = useForm({
    file: null
});

const filters = reactive({
    offer_id: props.filters.offer_id || '',
    search: props.filters.search || ''
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-NG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const submitConversion = () => {
    conversionForm.post(route('advertiser.manual-conversions.store'), {
        onSuccess: () => {
            conversionForm.reset();
        }
    });
};

const handleFileChange = (event) => {
    bulkForm.file = event.target.files[0];
};

const submitBulkImport = () => {
    bulkForm.post(route('advertiser.manual-conversions.bulk-import'), {
        onSuccess: () => {
            bulkForm.reset();
            // Reset file input
            document.querySelector('input[type="file"]').value = '';
        }
    });
};

const applyFilters = () => {
    router.get(route('advertiser.manual-conversions.index'), filters, {
        preserveState: true,
        preserveScroll: true
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const clearFilters = () => {
    filters.offer_id = '';
    filters.search = '';
    applyFilters();
};
</script>
