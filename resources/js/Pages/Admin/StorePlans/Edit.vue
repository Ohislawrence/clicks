<template>
    <AppLayout title="Edit Store Plan">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Store Plan</h2>
                    <p class="mt-1 text-sm text-gray-600">Update subscription plan details</p>
                </div>
                <Link :href="route('admin.store-plans.index')" class="text-gray-600 hover:text-gray-900">
                    ← Back to Plans
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-md p-6 space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Plan Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., Single Product Store"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug (optional)
                                </label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="auto-generated if empty"
                                />
                                <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Store Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.store_type"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">Select type...</option>
                                    <option value="single">Single Product</option>
                                    <option value="multi">Multi Product</option>
                                </select>
                                <p v-if="form.errors.store_type" class="mt-1 text-sm text-red-600">{{ form.errors.store_type }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product Limit
                                </label>
                                <input
                                    v-model.number="form.product_limit"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Leave empty for unlimited"
                                    :disabled="form.store_type === 'single'"
                                />
                                <p v-if="form.errors.product_limit" class="mt-1 text-sm text-red-600">{{ form.errors.product_limit }}</p>
                                <p class="mt-1 text-xs text-gray-500">Single product stores are limited to 1 product</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Monthly Price (₦) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model.number="form.monthly_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="5000"
                                />
                                <p v-if="form.errors.monthly_price" class="mt-1 text-sm text-red-600">{{ form.errors.monthly_price }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Yearly Price (₦) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model.number="form.yearly_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="50000"
                                />
                                <p v-if="form.errors.yearly_price" class="mt-1 text-sm text-red-600">{{ form.errors.yearly_price }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Yearly Discount (%)
                                </label>
                                <input
                                    v-model.number="form.yearly_discount_percent"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Auto-calculated"
                                />
                                <p v-if="form.errors.yearly_discount_percent" class="mt-1 text-sm text-red-600">{{ form.errors.yearly_discount_percent }}</p>
                                <p v-if="calculatedDiscount" class="mt-1 text-xs text-gray-500">
                                    Calculated: {{ calculatedDiscount }}%
                                </p>
                            </div>
                        </div>

                        <div v-if="yearlySavings > 0" class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <p class="text-sm text-emerald-800">
                                <strong>Yearly Savings:</strong> ₦{{ formatNumber(yearlySavings) }}
                                ({{ calculatedDiscount }}% discount)
                            </p>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Features</h3>
                            <button
                                type="button"
                                @click="addFeature"
                                class="text-sm text-emerald-600 hover:text-emerald-700 font-medium"
                            >
                                + Add Feature
                            </button>
                        </div>

                        <div v-if="form.features.length === 0" class="text-sm text-gray-500 italic">
                            No features added yet. Click "Add Feature" to get started.
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="(feature, index) in form.features"
                                :key="index"
                                class="flex items-center space-x-3"
                            >
                                <input
                                    v-model="form.features[index]"
                                    type="text"
                                    class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Enter feature description"
                                />
                                <button
                                    type="button"
                                    @click="removeFeature(index)"
                                    class="text-red-600 hover:text-red-700"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p v-if="form.errors.features" class="mt-2 text-sm text-red-600">{{ form.errors.features }}</p>
                    </div>

                    <!-- Settings -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    />
                                    <span class="ml-2 text-sm font-medium text-gray-700">Active Plan</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Inactive plans won't be available for selection</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sort Order
                                </label>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="0"
                                />
                                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
                        <Link
                            :href="route('admin.store-plans.index')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Plan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    plan: Object,
});

const form = useForm({
    name: props.plan.name,
    slug: props.plan.slug,
    store_type: props.plan.store_type,
    product_limit: props.plan.product_limit,
    monthly_price: props.plan.monthly_price,
    yearly_price: props.plan.yearly_price,
    yearly_discount_percent: props.plan.yearly_discount_percent,
    features: props.plan.features || [],
    is_active: props.plan.is_active,
    sort_order: props.plan.sort_order,
});

// Auto-set product_limit to 1 for single product stores
watch(() => form.store_type, (newType) => {
    if (newType === 'single') {
        form.product_limit = 1;
    }
});

const calculatedDiscount = computed(() => {
    if (!form.monthly_price || !form.yearly_price) return null;

    const monthlyTotal = form.monthly_price * 12;
    if (monthlyTotal <= 0 || form.yearly_price >= monthlyTotal) return 0;

    return Math.round(((monthlyTotal - form.yearly_price) / monthlyTotal) * 100);
});

const yearlySavings = computed(() => {
    if (!form.monthly_price || !form.yearly_price) return 0;

    const monthlyTotal = form.monthly_price * 12;
    return Math.max(0, monthlyTotal - form.yearly_price);
});

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-NG').format(value);
};

const addFeature = () => {
    form.features.push('');
};

const removeFeature = (index) => {
    form.features.splice(index, 1);
};

const submit = () => {
    form.put(route('admin.store-plans.update', props.plan.id));
};
</script>
