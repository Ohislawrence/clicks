<template>
    <AppLayout :title="discountCode ? 'Edit Discount Code' : 'New Discount Code'">
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">{{ discountCode ? 'Edit Discount Code' : 'New Discount Code' }}</h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Code & Type -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5">
                        <h3 class="text-lg font-semibold text-gray-900">Code Details</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Code *</label>
                            <input type="text" v-model="form.code"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 uppercase font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g. SAVE20" @input="form.code = form.code.toUpperCase()">
                            <p class="mt-1 text-xs text-gray-400">Letters, numbers and hyphens only. Automatically uppercased.</p>
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-500">{{ form.errors.code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" v-model="form.description"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Internal note about this code">
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label :class="['flex items-center gap-3 p-3 rounded-lg border cursor-pointer', form.type === 'percentage' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300']">
                                    <input type="radio" v-model="form.type" value="percentage" class="hidden">
                                    <div :class="['w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0', form.type === 'percentage' ? 'border-blue-500' : 'border-gray-400']">
                                        <div v-if="form.type === 'percentage'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Percentage (%)</span>
                                </label>
                                <label :class="['flex items-center gap-3 p-3 rounded-lg border cursor-pointer', form.type === 'fixed' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300']">
                                    <input type="radio" v-model="form.type" value="fixed" class="hidden">
                                    <div :class="['w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0', form.type === 'fixed' ? 'border-blue-500' : 'border-gray-400']">
                                        <div v-if="form.type === 'fixed'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Fixed Amount (₦)</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ form.type === 'percentage' ? 'Percentage Off *' : 'Amount Off (₦) *' }}
                                </label>
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                                    <span class="px-3 py-2 bg-gray-50 text-gray-500 text-sm border-r border-gray-300">
                                        {{ form.type === 'percentage' ? '%' : '₦' }}
                                    </span>
                                    <input type="number" v-model.number="form.value" step="0.01" min="0.01"
                                        :max="form.type === 'percentage' ? 100 : undefined"
                                        class="flex-1 px-3 py-2 text-gray-900 text-sm focus:outline-none">
                                </div>
                                <p v-if="form.errors.value" class="mt-1 text-sm text-red-500">{{ form.errors.value }}</p>
                            </div>
                            <div v-if="form.type === 'percentage'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max Discount Cap (₦)</label>
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                                    <span class="px-3 py-2 bg-gray-50 text-gray-500 text-sm border-r border-gray-300">₦</span>
                                    <input type="number" v-model.number="form.max_discount_amount" step="0.01" min="0"
                                        class="flex-1 px-3 py-2 text-gray-900 text-sm focus:outline-none"
                                        placeholder="No cap">
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Maximum discount regardless of order size</p>
                            </div>
                        </div>
                    </div>

                    <!-- Restrictions -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5">
                        <h3 class="text-lg font-semibold text-gray-900">Restrictions</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order (₦)</label>
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                                    <span class="px-3 py-2 bg-gray-50 text-gray-500 text-sm border-r border-gray-300">₦</span>
                                    <input type="number" v-model.number="form.min_order_amount" step="0.01" min="0"
                                        class="flex-1 px-3 py-2 text-gray-900 text-sm focus:outline-none"
                                        placeholder="No minimum">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max Uses</label>
                                <input type="number" v-model.number="form.max_uses" min="1"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Unlimited">
                                <p v-if="form.errors.max_uses" class="mt-1 text-sm text-red-500">{{ form.errors.max_uses }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="datetime-local" v-model="form.starts_at"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="datetime-local" v-model="form.expires_at"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p v-if="form.errors.expires_at" class="mt-1 text-sm text-red-500">{{ form.errors.expires_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" v-model="form.is_active" class="rounded">
                            <span class="text-sm text-gray-700">Active (customers can use this code)</span>
                        </label>
                    </div>

                    <div class="flex justify-between items-center">
                        <Link :href="route('advertiser.store.discount-codes.index', store.id)"
                            class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : (discountCode ? 'Update Code' : 'Create Code') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    discountCode: Object,
});

const toLocalDatetime = (d) => {
    if (!d) return '';
    const date = new Date(d);
    const offset = date.getTimezoneOffset();
    const local = new Date(date.getTime() - offset * 60000);
    return local.toISOString().slice(0, 16);
};

const form = useForm({
    code: props.discountCode?.code ?? '',
    description: props.discountCode?.description ?? '',
    type: props.discountCode?.type ?? 'percentage',
    value: props.discountCode?.value ?? '',
    min_order_amount: props.discountCode?.min_order_amount ?? '',
    max_discount_amount: props.discountCode?.max_discount_amount ?? '',
    max_uses: props.discountCode?.max_uses ?? '',
    is_active: props.discountCode?.is_active ?? true,
    starts_at: toLocalDatetime(props.discountCode?.starts_at),
    expires_at: toLocalDatetime(props.discountCode?.expires_at),
});

const submit = () => {
    if (props.discountCode) {
        form.put(route('advertiser.store.discount-codes.update', [props.store.id, props.discountCode.id]));
    } else {
        form.post(route('advertiser.store.discount-codes.store', props.store.id));
    }
};
</script>
