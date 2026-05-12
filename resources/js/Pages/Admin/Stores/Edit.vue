<template>
    <AppLayout :title="`Edit Store: ${store.name}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <Link :href="route('admin.stores.show', store.id)" class="text-neutral-400 hover:text-neutral-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h2 class="text-2xl font-bold text-neutral-100">Edit Store</h2>
                    </div>
                    <p class="mt-1 text-sm text-neutral-400">{{ store.name }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="updateStore" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-neutral-100 mb-4">Basic Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Store Name *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Slug *</label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                />
                                <p class="mt-1 text-sm text-neutral-400">URL: /store/{{ form.slug }}</p>
                                <p v-if="form.errors.slug" class="mt-1 text-sm text-red-500">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Plan & Theme -->
                    <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-neutral-100 mb-4">Plan & Theme</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Plan *</label>
                                <select
                                    v-model="form.store_plan_id"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                                        {{ plan.name }} - ₦{{ formatNumber(plan.monthly_price) }}/mo
                                    </option>
                                </select>
                                <p v-if="form.errors.store_plan_id" class="mt-1 text-sm text-red-500">{{ form.errors.store_plan_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Billing Cycle *</label>
                                <select
                                    v-model="form.billing_cycle"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <p v-if="form.errors.billing_cycle" class="mt-1 text-sm text-red-500">{{ form.errors.billing_cycle }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Theme *</label>
                                <select
                                    v-model="form.store_theme_id"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option v-for="theme in themes" :key="theme.id" :value="theme.id">
                                        {{ theme.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.store_theme_id" class="mt-1 text-sm text-red-500">{{ form.errors.store_theme_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Subscription Status *</label>
                                <select
                                    v-model="form.subscription_status"
                                    required
                                    class="w-full rounded-lg bg-neutral-950 border-neutral-800 text-neutral-100 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <p v-if="form.errors.subscription_status" class="mt-1 text-sm text-red-500">{{ form.errors.subscription_status }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-neutral-300 mb-2">Subscription Payment Gateway *</label>
                                <p class="text-xs text-neutral-400 mb-3">The payment gateway used when this store renews its subscription. The advertiser cannot change this.</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
                                        :class="form.subscription_payment_gateway === 'paystack' ? 'border-emerald-500 bg-emerald-500/10' : 'border-neutral-700 bg-neutral-950'">
                                        <input type="radio" v-model="form.subscription_payment_gateway" value="paystack" class="mr-3 accent-emerald-500">
                                        <span class="text-neutral-100 font-medium">Paystack</span>
                                    </label>
                                    <label class="flex items-center p-3 rounded-lg border cursor-pointer transition-colors"
                                        :class="form.subscription_payment_gateway === 'flutterwave' ? 'border-emerald-500 bg-emerald-500/10' : 'border-neutral-700 bg-neutral-950'">
                                        <input type="radio" v-model="form.subscription_payment_gateway" value="flutterwave" class="mr-3 accent-emerald-500">
                                        <span class="text-neutral-100 font-medium">Flutterwave</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.subscription_payment_gateway" class="mt-1 text-sm text-red-500">{{ form.errors.subscription_payment_gateway }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-neutral-100 mb-4">Status</h3>
                        <div class="flex items-center space-x-2">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                id="is_active"
                                class="rounded bg-neutral-950 border-neutral-800 text-emerald-500 focus:ring-emerald-500"
                            />
                            <label for="is_active" class="text-sm text-neutral-300">Store is active</label>
                        </div>
                        <p class="mt-2 text-sm text-neutral-400">When inactive, the store will not be accessible to the public.</p>
                        <p v-if="form.errors.is_active" class="mt-1 text-sm text-red-500">{{ form.errors.is_active }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6">
                        <button
                            type="button"
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-500/20 text-red-500 rounded-lg hover:bg-red-500/30"
                        >
                            Delete Store
                        </button>
                        <div class="flex space-x-3">
                            <Link
                                :href="route('admin.stores.show', store.id)"
                                class="px-4 py-2 bg-neutral-800 text-neutral-300 rounded-lg hover:bg-neutral-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    plans: Array,
    themes: Array,
});

const form = useForm({
    name: props.store.name,
    slug: props.store.slug,
    description: props.store.description,
    store_plan_id: props.store.store_plan_id,
    store_theme_id: props.store.store_theme_id,
    billing_cycle: props.store.billing_cycle,
    subscription_status: props.store.subscription_status,
    subscription_payment_gateway: props.store.subscription_payment_gateway || 'paystack',
    is_active: props.store.is_active,
});

const updateStore = () => {
    form.put(route('admin.stores.update', props.store.id));
};

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this store? This action cannot be undone and will delete all products and orders.')) {
        router.delete(route('admin.stores.destroy', props.store.id));
    }
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-NG').format(num || 0);
};
</script>
