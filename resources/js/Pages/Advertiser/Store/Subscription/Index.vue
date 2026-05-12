<template>
    <AppLayout title="Subscription">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Store Subscription</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage your store subscription and billing</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Subscription Status -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Current Plan: {{ store.plan.name }}</h3>
                            <p class="text-gray-500 mt-1">
                                {{ store.plan.store_type === 'single' ? 'Single Product Store' : `Up to ${store.plan.product_limit || '∞'} Products` }}
                            </p>
                        </div>
                        <div :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium',
                            subscriptionDetails.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                        ]">
                            {{ subscriptionDetails.is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <p class="text-sm text-gray-500">Billing Cycle</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1 capitalize">{{ subscriptionDetails.billing_cycle }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Next Payment</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">₦{{ subscriptionDetails.next_payment_amount.toLocaleString() }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Expires</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">
                                {{ subscriptionDetails.expires_at ? new Date(subscriptionDetails.expires_at).toLocaleDateString() : 'N/A' }}
                            </p>
                            <p v-if="subscriptionDetails.days_until_expiry !== null" class="text-sm text-gray-500 mt-1">
                                {{ subscriptionDetails.days_until_expiry }} days remaining
                            </p>
                        </div>
                    </div>

                    <!-- Renewal Alert -->
                    <div v-if="!subscriptionDetails.is_active" class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-red-700 font-semibold">Subscription Expired</h4>
                                <p class="text-red-600 text-sm mt-1">Your store is currently inactive. Renew your subscription to reactivate.</p>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="subscriptionDetails.is_expiring_soon" class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-yellow-700 font-semibold">Subscription Expiring Soon</h4>
                                <p class="text-yellow-600 text-sm mt-1">Your subscription will expire in {{ subscriptionDetails.days_until_expiry }} day(s). Renew now to avoid interruption.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Renewal Button -->
                    <div class="mt-6">
                        <button @click="showRenewalModal = true"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Renew Subscription
                        </button>
                    </div>
                </div>

                <!-- Available Plans -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Available Plans</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="plan in availablePlans" :key="plan.id"
                            :class="[
                                'border-2 rounded-xl p-4',
                                plan.id === store.store_plan_id ? 'border-blue-500 bg-blue-50' : 'border-gray-200'
                            ]">
                            <div class="flex items-start justify-between">
                                <h4 class="text-lg font-semibold text-gray-900">{{ plan.name }}</h4>
                                <span v-if="plan.id === store.store_plan_id" class="text-xs bg-blue-600 text-white px-2 py-1 rounded">
                                    Current
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ plan.store_type === 'single' ? 'Single Product' : `${plan.product_limit || '∞'} Products` }}
                            </p>
                            <div class="mt-3">
                                <p class="text-xl font-bold text-blue-600">₦{{ plan.monthly_price.toLocaleString() }}</p>
                                <p class="text-xs text-gray-500">/month</p>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm font-semibold text-gray-900">₦{{ plan.yearly_price.toLocaleString() }}</p>
                                <p class="text-xs text-gray-500">/year (save {{ plan.yearly_discount_percent }}%)</p>
                            </div>
                            <button v-if="plan.id !== store.store_plan_id" @click="changePlan(plan.id)"
                                class="mt-4 w-full px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                Switch to {{ plan.name }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Payment History</h3>

                    <div v-if="paymentHistory.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Date</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Reference</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Amount</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Period</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in paymentHistory" :key="payment.id" class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ new Date(payment.created_at).toLocaleDateString() }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-500 font-mono">{{ payment.payment_reference }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">₦{{ payment.amount.toLocaleString() }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">
                                        {{ new Date(payment.period_start).toLocaleDateString() }} - {{ new Date(payment.period_end).toLocaleDateString() }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            payment.status === 'paid' ? 'bg-green-100 text-green-700' :
                                            payment.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-red-100 text-red-700'
                                        ]">
                                            {{ payment.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-12 text-gray-500">
                        <p>No payment history yet</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Renewal Modal -->
        <div v-if="showRenewalModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" @click.self="showRenewalModal = false">
            <div class="bg-white border border-gray-200 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Renew Subscription</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-400">
                                <input type="radio" v-model="renewalForm.billing_cycle" value="monthly" class="mr-3">
                                <div class="flex-1">
                                    <span class="text-gray-900">Monthly</span>
                                    <span class="text-blue-600 font-semibold ml-2">₦{{ store.plan.monthly_price.toLocaleString() }}</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-400">
                                <input type="radio" v-model="renewalForm.billing_cycle" value="yearly" class="mr-3">
                                <div class="flex-1">
                                    <span class="text-gray-900">Yearly</span>
                                    <span class="text-blue-600 font-semibold ml-2">₦{{ store.plan.yearly_price.toLocaleString() }}</span>
                                    <span class="text-xs text-gray-500 ml-2">(save {{ store.plan.yearly_discount_percent }}%)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button @click="showRenewalModal = false"
                        class="flex-1 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="initiateRenewal"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    subscriptionDetails: Object,
    availablePlans: Array,
    paymentHistory: Array,
});

const showRenewalModal = ref(false);
const renewalForm = ref({
    billing_cycle: props.store.billing_cycle,
});

const initiateRenewal = () => {
    router.post(route('advertiser.store.subscription.renew', props.store.id), renewalForm.value);
};

const changePlan = (planId) => {
    if (confirm('Are you sure you want to change your plan? The new rate will apply on your next renewal.')) {
        router.post(route('advertiser.store.subscription.change-plan', props.store.id), {
            store_plan_id: planId,
        }, {
            preserveScroll: true,
        });
    }
};
</script>
