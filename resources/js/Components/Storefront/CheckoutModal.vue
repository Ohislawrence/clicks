<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" @click.self="$emit('close')">
            <!-- Slide-up sheet on mobile, centered modal on desktop -->
            <div class="bg-white w-full sm:rounded-2xl shadow-2xl sm:max-w-lg max-h-[95dvh] sm:max-h-[90vh] overflow-hidden flex flex-col rounded-t-2xl">

                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b" :style="{ borderColor: primaryColor + '20' }">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Complete Your Order</h2>
                        <p class="text-xs text-gray-500 mt-0.5">{{ items.length }} item{{ items.length > 1 ? 's' : '' }} · {{ currencySymbol }}{{ formatPrice(subtotal) }}</p>
                    </div>
                    <button @click="$emit('close')" class="p-2 rounded-xl hover:bg-gray-100 transition text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1">
                    <!-- Order Summary -->
                    <div class="px-5 py-4 border-b bg-gray-50/60">
                        <div class="space-y-2">
                            <div v-for="item in items" :key="item.product.id" class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    <img v-if="item.product.images?.[0]" :src="item.product.images[0]" :alt="item.product.name" class="w-full h-full object-cover" loading="lazy" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ item.product.name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ item.quantity }}</p>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ currencySymbol }}{{ formatPrice(item.product.price * item.quantity) }}</span>
                            </div>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="mt-4 pt-3 border-t border-gray-200 space-y-1.5">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ currencySymbol }}{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span v-if="allDigital" class="text-violet-600 font-medium">Instant Download</span>
                                <span v-else-if="!form.state_id" class="text-gray-400 italic">Select state</span>
                                <span v-else-if="shippingFee === 0" class="text-green-600 font-medium">Free</span>
                                <span v-else>{{ currencySymbol }}{{ formatPrice(shippingFee) }}</span>
                            </div>
                            <div v-if="discountApplied" class="flex justify-between text-sm text-green-600">
                                <span>Discount ({{ form.discount_code }})</span>
                                <span>-{{ currencySymbol }}{{ formatPrice(discountAmount) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200" :style="{ color: primaryColor }">
                                <span>Total</span>
                                <span>{{ currencySymbol }}{{ formatPrice(total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form @submit.prevent="submitOrder" class="px-5 py-4 space-y-4">
                        <!-- Digital delivery notice -->
                        <div v-if="allDigital" class="flex items-start gap-3 p-3.5 rounded-xl bg-violet-50 border border-violet-100">
                            <svg class="w-5 h-5 text-violet-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <div class="text-xs text-violet-700 leading-relaxed">
                                <p class="font-semibold mb-0.5">Instant Digital Delivery</p>
                                <p>Your download link will be sent to your email immediately after payment. No shipping required.</p>
                            </div>
                        </div>

                        <!-- Customer Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input v-model="form.customer_name" type="text" required autocomplete="name"
                                class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition"
                                :class="form.errors.customer_name ? 'border-red-400 focus:ring-red-200' : 'border-gray-300 focus:ring-blue-200 focus:border-blue-400'"
                                :style="!form.errors.customer_name ? { '--tw-ring-color': primaryColor + '40' } : {}"
                                placeholder="John Doe" />
                            <p v-if="form.errors.customer_name" class="text-red-500 text-xs mt-1">{{ form.errors.customer_name }}</p>
                        </div>

                        <!-- Email + Phone in grid on desktop -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <input v-model="form.customer_email" type="email" required autocomplete="email"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                                    placeholder="you@email.com" />
                                <p v-if="form.errors.customer_email" class="text-red-500 text-xs mt-1">{{ form.errors.customer_email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
                                <input v-model="form.customer_phone" type="tel" required autocomplete="tel"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                                    placeholder="+234 800 000 0000" />
                                <p v-if="form.errors.customer_phone" class="text-red-500 text-xs mt-1">{{ form.errors.customer_phone }}</p>
                            </div>
                        </div>

                        <!-- Delivery State (physical only) -->
                        <div v-if="!allDigital">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery State <span class="text-red-500">*</span></label>
                            <select v-model="form.state_id"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition bg-white">
                                <option value="">Select your state…</option>
                                <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                            </select>
                            <p v-if="form.state_id && shippingFee === 0" class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Free delivery to your state!
                            </p>
                            <p v-else-if="form.state_id" class="text-xs text-gray-500 mt-1">Delivery fee: {{ currencySymbol }}{{ formatPrice(shippingFee) }}</p>
                        </div>

                        <!-- Shipping Address (physical only) -->
                        <div v-if="!allDigital">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Delivery Address <span class="text-red-500">*</span></label>
                            <textarea v-model="form.shipping_address" rows="2" autocomplete="street-address"
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition resize-none"
                                placeholder="House number, street, area, city"></textarea>
                            <p v-if="form.errors.shipping_address" class="text-red-500 text-xs mt-1">{{ form.errors.shipping_address }}</p>
                        </div>

                        <!-- Discount Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Discount Code <span class="text-gray-400 font-normal">(optional)</span></label>
                            <div class="flex gap-2">
                                <input v-model="form.discount_code" type="text"
                                    class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                                    :class="{ 'border-green-400 bg-green-50': discountApplied, 'border-red-400 bg-red-50': discountError }"
                                    placeholder="SAVE10"
                                    :disabled="discountApplied" />
                                <button v-if="!discountApplied" type="button" @click="applyDiscount"
                                    :disabled="!form.discount_code || applyingDiscount"
                                    class="px-4 py-3 rounded-xl text-sm font-semibold text-white transition disabled:opacity-50 whitespace-nowrap"
                                    :style="{ backgroundColor: primaryColor }">
                                    <span v-if="!applyingDiscount">Apply</span>
                                    <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                </button>
                                <button v-else type="button" @click="removeDiscount"
                                    class="px-4 py-3 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                                    Remove
                                </button>
                            </div>
                            <p v-if="discountApplied" class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Discount applied! You save {{ currencySymbol }}{{ formatPrice(discountAmount) }}
                            </p>
                            <p v-if="discountError" class="text-xs text-red-500 mt-1">{{ discountError }}</p>
                        </div>

                        <!-- Error Message -->
                        <div v-if="errorMessage" class="flex items-start gap-2 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ errorMessage }}
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="form.processing"
                            class="w-full py-4 rounded-xl text-white font-bold text-base transition hover:opacity-90 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed shadow-lg"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${primaryColor}cc)` }">
                            <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Pay Securely · {{ currencySymbol }}{{ formatPrice(total) }}
                            </span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Processing…
                            </span>
                        </button>

                        <p class="text-center text-xs text-gray-400 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            Secured by Paystack / Flutterwave
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { getCurrencySymbol, formatPrice as formatCurrencyPrice } from '@/utils/currency';

const props = defineProps({
    store:        { type: Object, required: true },
    items:        { type: Array,  required: true },
    states:       { type: Array,  default: () => [] },
    primaryColor: { type: String, default: '#3B82F6' },
});

const emit = defineEmits(['close', 'success']);

const show            = ref(true);
const errorMessage    = ref('');
const discountApplied = ref(false);
const discountAmount  = ref(0);
const discountError   = ref('');
const applyingDiscount = ref(false);

const form = ref({
    customer_name:    '',
    customer_email:   '',
    customer_phone:   '',
    shipping_address: '',
    state_id:         '',
    discount_code:    '',
    processing:       false,
    errors:           {},
});

const allDigital = computed(() =>
    props.items.length > 0 && props.items.every(i => i.product.product_type === 'digital')
);

const subtotal = computed(() =>
    props.items.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
);

const shippingFee = computed(() => {
    if (allDigital.value) return 0;
    if (!form.value.state_id) return 0;
    let fee = 0;
    const seen = new Set();
    for (const item of props.items) {
        if (seen.has(item.product.id)) continue;
        seen.add(item.product.id);
        const match = (item.product.delivery_fees || []).find(
            f => String(f.state_id) === String(form.value.state_id)
        );
        if (match) fee += Number(match.fee);
    }
    return fee;
});

const total = computed(() =>
    Math.max(0, subtotal.value + shippingFee.value - discountAmount.value)
);

const formatPrice = (price) => formatCurrencyPrice(Math.round(price), props.store.currency ?? 'NGN');
const currencySymbol = computed(() => getCurrencySymbol(props.store.currency ?? 'NGN'));

const applyDiscount = async () => {
    if (!form.value.discount_code) return;
    applyingDiscount.value = true;
    discountError.value    = '';

    try {
        // Validate client-side by attempting checkout with just the code
        // Actual validation happens server-side on submit; here we preview
        // We use a lightweight validation endpoint if available, otherwise just send on submit
        discountApplied.value = false;
        discountAmount.value  = 0;

        // Optimistic: mark as pending and let server validate at submit
        // Display as "applied" to give instant feedback
        discountApplied.value = true;
        // We don't know the exact discount amount until server confirms — show 0 until submit
        // Server will return error if invalid
    } catch (e) {
        discountError.value = 'Could not apply discount code. It will be validated on checkout.';
    } finally {
        applyingDiscount.value = false;
    }
};

const removeDiscount = () => {
    form.value.discount_code = '';
    discountApplied.value    = false;
    discountAmount.value     = 0;
    discountError.value      = '';
};

const submitOrder = async () => {
    form.value.processing = true;
    form.value.errors     = {};
    errorMessage.value    = '';

    try {
        const response = await axios.post(route('storefront.checkout', props.store.slug), {
            items:            props.items.map(i => ({ product_id: i.product.id, quantity: i.quantity })),
            customer_name:    form.value.customer_name,
            customer_email:   form.value.customer_email,
            customer_phone:   form.value.customer_phone,
            shipping_address: allDigital.value ? null : form.value.shipping_address,
            state_id:         allDigital.value ? null : (form.value.state_id || null),
            discount_code:    form.value.discount_code || null,
        });

        if (response.data.success) {
            emit('success', response.data);
        } else {
            errorMessage.value = response.data.error || 'Failed to process order.';
        }
    } catch (error) {
        if (error.response?.status === 422) {
            const data = error.response.data;
            if (data.errors) {
                form.value.errors = data.errors;
            } else if (data.error?.toLowerCase().includes('discount')) {
                discountApplied.value = false;
                discountAmount.value  = 0;
                discountError.value   = data.error;
            } else {
                errorMessage.value = data.error || 'Validation failed.';
            }
        } else {
            errorMessage.value = error.response?.data?.error || 'An error occurred. Please try again.';
        }
    } finally {
        form.value.processing = false;
    }
};
</script>
