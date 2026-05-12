<template>
    <!-- Modal Overlay -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
            <!-- Modal Content -->
            <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b sticky top-0 bg-white z-10">
                    <h2 class="text-2xl font-bold">Checkout</h2>
                    <button @click="$emit('close')" class="p-2 rounded-full hover:bg-neutral-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Order Summary -->
                <div class="p-6 border-b bg-neutral-50">
                    <h3 class="font-semibold mb-3">Order Summary</h3>
                    <div class="space-y-2">
                        <div v-for="item in items" :key="item.product.id" class="flex justify-between text-sm">
                            <span>{{ item.product.name }} × {{ item.quantity }}</span>
                            <span class="font-medium">₦{{ formatPrice(item.product.price * item.quantity) }}</span>
                        </div>
                    <!-- Shipping Fee display -->
                        <div class="flex justify-between text-sm pt-2 border-t">
                            <span>Shipping Fee:</span>
                            <span v-if="!form.state_id" class="font-medium text-gray-400">Select a state first</span>
                            <span v-else-if="shippingFee === 0" class="font-medium text-green-600">Free</span>
                            <span v-else class="font-medium">₦{{ formatPrice(shippingFee) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t" :style="{ color: primaryColor }">
                            <span>Total:</span>
                            <span>₦{{ formatPrice(total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form @submit.prevent="submitOrder" class="p-6 space-y-4">
                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name *</label>
                        <input
                            v-model="form.customer_name"
                            type="text"
                            required
                            class="w-full border-neutral-300 rounded-lg focus:ring-2 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.customer_name }"
                            placeholder="John Doe"
                        />
                        <p v-if="form.errors.customer_name" class="text-red-500 text-sm mt-1">{{ form.errors.customer_name }}</p>
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Email Address *</label>
                        <input
                            v-model="form.customer_email"
                            type="email"
                            required
                            class="w-full border-neutral-300 rounded-lg focus:ring-2 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.customer_email }"
                            placeholder="john@example.com"
                        />
                        <p v-if="form.errors.customer_email" class="text-red-500 text-sm mt-1">{{ form.errors.customer_email }}</p>
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number *</label>
                        <input
                            v-model="form.customer_phone"
                            type="tel"
                            required
                            class="w-full border-neutral-300 rounded-lg focus:ring-2 focus:border-transparent"
                            :class="{ 'border-red-500': form.customer_phone }"
                            placeholder="+234 800 000 0000"
                        />
                        <p v-if="form.errors.customer_phone" class="text-red-500 text-sm mt-1">{{ form.errors.customer_phone }}</p>
                    </div>

                    <!-- Shipping Address -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Shipping Address *</label>
                        <textarea
                            v-model="form.shipping_address"
                            required
                            rows="3"
                            class="w-full border-neutral-300 rounded-lg focus:ring-2 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.shipping_address }"
                            placeholder="Enter your full delivery address"
                        ></textarea>
                        <p v-if="form.errors.shipping_address" class="text-red-500 text-sm mt-1">{{ form.errors.shipping_address }}</p>
                    </div>

                    <!-- Delivery State -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Delivery State *</label>
                        <select
                            v-model="form.state_id"
                            required
                            class="w-full border-neutral-300 rounded-lg focus:ring-2 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.state_id }"
                        >
                            <option value="">Select your state...</option>
                            <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                        </select>
                        <p v-if="form.errors.state_id" class="text-red-500 text-sm mt-1">{{ form.errors.state_id }}</p>
                        <p v-if="form.state_id && shippingFee === 0" class="text-xs text-green-600 mt-1">Free delivery to this state</p>
                        <p v-else-if="form.state_id" class="text-xs text-gray-500 mt-1">Shipping fee: ₦{{ formatPrice(shippingFee) }}</p>
                        <p v-else class="text-xs text-gray-500 mt-1">Select your state to see the delivery fee</p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        {{ errorMessage }}
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 rounded-lg text-white font-semibold transition hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                        :style="{ backgroundColor: primaryColor }"
                    >
                        <span v-if="!form.processing">Place Order</span>
                        <span v-else class="flex items-center justify-center space-x-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processing...</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    store: {
        type: Object,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    states: {
        type: Array,
        default: () => [],
    },
    primaryColor: {
        type: String,
        default: '#3B82F6',
    },
});

const emit = defineEmits(['close', 'success']);

const show = ref(true);
const errorMessage = ref('');

const form = ref({
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    shipping_address: '',
    state_id: '',
    items: props.items.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity,
    })),
    processing: false,
    errors: {},
});

const shippingFee = computed(() => {
    if (!form.value.state_id) return 0;
    let total = 0;
    const seen = new Set();
    for (const item of props.items) {
        if (seen.has(item.product.id)) continue;
        seen.add(item.product.id);
        const fees = item.product.delivery_fees || [];
        const match = fees.find(f => String(f.state_id) === String(form.value.state_id));
        if (match) total += Number(match.fee);
    }
    return total;
});

const total = computed(() => {
    const subtotal = props.items.reduce((sum, item) => {
        return sum + (item.product.price * item.quantity);
    }, 0);
    return subtotal + shippingFee.value;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};

const submitOrder = async () => {
    form.value.processing = true;
    form.value.errors = {};
    errorMessage.value = '';

    try {
        const response = await axios.post(route('storefront.checkout', props.store.slug), {
            items: form.value.items,
            customer_name: form.value.customer_name,
            customer_email: form.value.customer_email,
            customer_phone: form.value.customer_phone,
            shipping_address: form.value.shipping_address,
            state_id: form.value.state_id || null,
        });

        if (response.data.success) {
            emit('success', response.data);
        } else {
            errorMessage.value = response.data.error || 'Failed to process order';
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            form.value.errors = error.response.data.errors;
        } else {
            errorMessage.value = error.response?.data?.error || 'An error occurred. Please try again.';
        }
    } finally {
        form.value.processing = false;
    }
};
</script>
