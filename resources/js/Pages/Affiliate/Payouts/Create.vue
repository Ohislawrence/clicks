<template>
    <AppLayout title="Request Payout">
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">Request Payout</h2>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Available Balance</h3>
                                <p class="text-3xl font-bold text-green-600 mt-2">{{ formatCurrency(balance) }}</p>
                            </div>
                            <svg class="w-16 h-16 text-green-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Withdrawal Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    step="1"
                                    min="5000"
                                    :max="balance"
                                    class="pl-8 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="5000"
                                />
                            </div>
                            <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                            <p class="mt-1 text-sm text-gray-500">
                                Minimum: {{ formatCurrency(minimumPayout) }} • Maximum: {{ formatCurrency(balance) }}
                            </p>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 gap-3">
                                <div
                                    @click="form.payment_method = 'paystack'"
                                    :class="[
                                        'border-2 rounded-lg p-4 cursor-pointer transition-all',
                                        form.payment_method === 'paystack'
                                            ? 'border-blue-500 bg-blue-50'
                                            : 'border-gray-200 hover:border-gray-300'
                                    ]"
                                >
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            value="paystack"
                                            v-model="form.payment_method"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                        />
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Paystack</p>
                                            <p class="text-xs text-gray-500">Fast and secure payments</p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    @click="form.payment_method = 'flutterwave'"
                                    :class="[
                                        'border-2 rounded-lg p-4 cursor-pointer transition-all',
                                        form.payment_method === 'flutterwave'
                                            ? 'border-blue-500 bg-blue-50'
                                            : 'border-gray-200 hover:border-gray-300'
                                    ]"
                                >
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            value="flutterwave"
                                            v-model="form.payment_method"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                        />
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Flutterwave</p>
                                            <p class="text-xs text-gray-500">Reliable payment solution</p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    @click="form.payment_method = 'bank_transfer'"
                                    :class="[
                                        'border-2 rounded-lg p-4 cursor-pointer transition-all',
                                        form.payment_method === 'bank_transfer'
                                            ? 'border-blue-500 bg-blue-50'
                                            : 'border-gray-200 hover:border-gray-300'
                                    ]"
                                >
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            value="bank_transfer"
                                            v-model="form.payment_method"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                        />
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Bank Transfer</p>
                                            <p class="text-xs text-gray-500">Direct to your bank account</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-600">{{ form.errors.payment_method }}</p>
                        </div>

                        <!-- Payment Details -->
                        <div v-if="form.payment_method">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Account Details <span class="text-red-500">*</span>
                            </label>

                            <div v-if="paymentDetails && hasCompletePaymentDetails" class="mb-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-900 mb-2">✓ Using saved payment details:</p>
                                        <div class="space-y-1 text-sm text-green-800">
                                            <p><strong>Account Name:</strong> {{ form.payment_details.account_name }}</p>
                                            <p><strong>Account Number:</strong> {{ form.payment_details.account_number }}</p>
                                            <p><strong>Bank:</strong> {{ form.payment_details.bank_name }}</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="showManualEntry = true"
                                        class="text-sm text-blue-600 hover:text-blue-700 underline ml-4"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </div>

                            <div v-if="!hasCompletePaymentDetails || showManualEntry" class="space-y-3">
                                <input
                                    v-model="form.payment_details.account_name"
                                    type="text"
                                    placeholder="Account Name"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                                <input
                                    v-model="form.payment_details.account_number"
                                    type="text"
                                    placeholder="Account Number"
                                    maxlength="10"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                                <input
                                    v-model="form.payment_details.bank_name"
                                    type="text"
                                    placeholder="Bank Name (e.g., Access Bank, GTBank)"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                                <input
                                    v-model="form.payment_details.bank_code"
                                    type="text"
                                    placeholder="Bank Code (Optional - e.g., 044, 058, 011)"
                                    maxlength="10"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    <strong>Tip:</strong> Save your payment details in your
                                    <Link :href="route('profile.show')" class="text-blue-600 hover:underline">profile settings</Link>
                                    for faster withdrawals next time.
                                </p>
                            </div>

                            <p v-if="form.errors.payment_details" class="mt-2 text-sm text-red-600">{{ form.errors.payment_details }}</p>

                            <div v-if="form.payment_method !== 'bank_transfer'" class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-700">
                                    <strong>{{ form.payment_method === 'paystack' ? 'Paystack' : 'Flutterwave' }} Payment:</strong>
                                    Funds will be transferred directly to your bank account using {{ form.payment_method === 'paystack' ? 'Paystack' : 'Flutterwave' }}'s secure payment gateway.
                                </p>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Payouts are processed within 1-3 business days. Make sure your payment details are correct.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <Link
                                :href="route('affiliate.payouts.index')"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || !canSubmit"
                                :class="[
                                    'px-6 py-2 rounded-lg font-medium transition-all',
                                    canSubmit && !form.processing
                                        ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 shadow-lg'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                            >
                                {{ form.processing ? 'Submitting...' : 'Request Payout' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    balance: Number,
    minimumPayout: Number,
    paymentDetails: Object
});

const showManualEntry = ref(false);

const hasCompletePaymentDetails = computed(() => {
    return props.paymentDetails &&
           props.paymentDetails.account_name &&
           props.paymentDetails.account_number &&
           props.paymentDetails.bank_name;
});

const form = useForm({
    amount: '',
    payment_method: '',
    payment_details: props.paymentDetails || {
        account_name: '',
        account_number: '',
        bank_name: '',
        bank_code: '',
    }
});

const canSubmit = computed(() => {
    if (!form.amount || !form.payment_method) return false;
    if (form.amount < props.minimumPayout || form.amount > props.balance) return false;

    // All payment methods now require bank account details
    return form.payment_details.account_name &&
           form.payment_details.account_number &&
           form.payment_details.bank_name;
});

const submit = () => {
    form.post(route('affiliate.payouts.store'));
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>
