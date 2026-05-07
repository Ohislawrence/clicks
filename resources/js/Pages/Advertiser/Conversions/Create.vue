<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    offers: Array,
    affiliates: Array,
});

const form = useForm({
    offer_id: '',
    affiliate_id: '',
    amount: '',
    transaction_id: '',
    conversion_date: new Date().toISOString().split('T')[0], // Today's date
    notes: '',
});

const selectedOffer = ref(null);

const updateSelectedOffer = () => {
    selectedOffer.value = props.offers.find(o => o.id == form.offer_id);
    calculateEstimatedCommission();
};

const calculateEstimatedCommission = () => {
    if (!selectedOffer.value || !form.amount) return 0;

    if (selectedOffer.value.commission_model === 'revshare') {
        return (parseFloat(form.amount) * parseFloat(selectedOffer.value.commission_rate)) / 100;
    } else {
        return parseFloat(selectedOffer.value.commission_rate);
    }
};

const submit = () => {
    form.post(route('advertiser.conversions.store'));
};
</script>

<template>
    <AppLayout title="Report Manual Conversion">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Report Manual Conversion
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Info Banner -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r">
                    <div class="flex items-start">
                        <div class="text-2xl mr-3">ℹ️</div>
                        <div>
                            <h3 class="font-bold text-blue-900 mb-1">Manual Conversion Entry</h3>
                            <p class="text-sm text-blue-800">
                                Use this form to record offline sales, bank transfers, or any conversion that didn't happen through pixel/API tracking.
                                The affiliate will be notified and their commission will be credited immediately.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Offer Selection -->
                        <div>
                            <InputLabel for="offer_id" value="Select Offer *" />
                            <select
                                id="offer_id"
                                v-model="form.offer_id"
                                @change="updateSelectedOffer"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">-- Choose an offer --</option>
                                <option v-for="offer in offers" :key="offer.id" :value="offer.id">
                                    {{ offer.name }}
                                    ({{ offer.commission_model === 'revshare' ? offer.commission_rate + '%' : '₦' + offer.commission_rate }})
                                </option>
                            </select>
                            <div v-if="form.errors.offer_id" class="text-sm text-red-600 mt-1">
                                {{ form.errors.offer_id }}
                            </div>
                        </div>

                        <!-- Affiliate Selection -->
                        <div>
                            <InputLabel for="affiliate_id" value="Select Affiliate *" />
                            <select
                                id="affiliate_id"
                                v-model="form.affiliate_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">-- Choose an affiliate --</option>
                                <option v-for="affiliate in affiliates" :key="affiliate.id" :value="affiliate.id">
                                    {{ affiliate.name }} ({{ affiliate.email }})
                                </option>
                            </select>
                            <div v-if="form.errors.affiliate_id" class="text-sm text-red-600 mt-1">
                                {{ form.errors.affiliate_id }}
                            </div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <InputLabel for="amount" value="Conversion Amount (₦) *" />
                            <TextInput
                                id="amount"
                                v-model="form.amount"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-1 block w-full"
                                placeholder="e.g., 50000"
                                required
                                @input="calculateEstimatedCommission"
                            />
                            <div v-if="form.errors.amount" class="text-sm text-red-600 mt-1">
                                {{ form.errors.amount }}
                            </div>
                            <div v-if="selectedOffer && form.amount" class="mt-2 text-sm text-green-600 font-semibold">
                                💰 Estimated Commission: ₦{{ calculateEstimatedCommission().toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                            </div>
                        </div>

                        <!-- Transaction ID -->
                        <div>
                            <InputLabel for="transaction_id" value="Transaction ID / Bank Reference *" />
                            <TextInput
                                id="transaction_id"
                                v-model="form.transaction_id"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g., BANK-REF-123456 or ORDER-7834"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-600">
                                Enter the bank transfer reference, order number, or any unique identifier for this sale
                            </p>
                            <div v-if="form.errors.transaction_id" class="text-sm text-red-600 mt-1">
                                {{ form.errors.transaction_id }}
                            </div>
                        </div>

                        <!-- Conversion Date -->
                        <div>
                            <InputLabel for="conversion_date" value="Conversion Date *" />
                            <TextInput
                                id="conversion_date"
                                v-model="form.conversion_date"
                                type="date"
                                :max="new Date().toISOString().split('T')[0]"
                                class="mt-1 block w-full"
                                required
                            />
                            <div v-if="form.errors.conversion_date" class="text-sm text-red-600 mt-1">
                                {{ form.errors.conversion_date }}
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" value="Notes (Optional)" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Add any additional details about this conversion (e.g., customer name, payment method, special circumstances)"
                            ></textarea>
                            <div v-if="form.errors.notes" class="text-sm text-red-600 mt-1">
                                {{ form.errors.notes }}
                            </div>
                        </div>

                        <!-- Warning Box -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r">
                            <div class="flex items-start">
                                <div class="text-xl mr-2">⚠️</div>
                                <div class="text-sm text-yellow-800">
                                    <strong>Important:</strong> Manual conversions are automatically approved and the commission is credited immediately to the affiliate's balance.
                                    Make sure all information is accurate before submitting.
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <SecondaryButton type="button" @click="$inertia.visit(route('advertiser.conversions.index'))">
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>💾 Record Conversion</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="mt-6 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-300 rounded-lg p-6">
                    <h3 class="font-bold text-purple-900 mb-3">📚 When to Use Manual Entry:</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div class="bg-white rounded-lg p-3 border border-purple-200">
                            <div class="font-semibold text-purple-700 mb-1">✓ Bank Transfers</div>
                            <p class="text-xs">Customer transferred money directly to your account</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-purple-200">
                            <div class="font-semibold text-purple-700 mb-1">✓ Phone/WhatsApp Orders</div>
                            <p class="text-xs">Sales made through calls or messaging apps</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-purple-200">
                            <div class="font-semibold text-purple-700 mb-1">✓ Cash Sales</div>
                            <p class="text-xs">In-person transactions with physical payment</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-purple-200">
                            <div class="font-semibold text-purple-700 mb-1">✓ Missed Tracking</div>
                            <p class="text-xs">When pixel/API tracking failed but sale is verified</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
