<template>
    <!-- Sidebar Overlay -->
    <Transition
        enter-active-class="transition-opacity duration-300"
        leave-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-50"
            @click="$emit('close')"
        ></div>
    </Transition>

    <!-- Cart Sidebar -->
    <Transition
        enter-active-class="transition-transform duration-300"
        leave-active-class="transition-transform duration-300"
        enter-from-class="translate-x-full"
        leave-to-class="translate-x-full"
    >
        <div
            v-if="isOpen"
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col"
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-xl font-bold">Shopping Cart</h2>
                <button
                    @click="$emit('close')"
                    class="p-2 rounded-full hover:bg-neutral-100 transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <div v-if="items.length === 0" class="text-center py-12 text-neutral-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                    <p class="font-medium">Your cart is empty</p>
                    <p class="text-sm mt-1">Add products to get started</p>
                </div>

                <div v-for="item in items" :key="item.product.id" class="flex gap-3 border-b pb-4">
                    <!-- Product Image -->
                    <div class="w-20 h-20 rounded bg-neutral-100 flex-shrink-0 overflow-hidden">
                        <img
                            v-if="item.product.images && item.product.images[0]"
                            :src="'/storage/' + item.product.images[0]"
                            :alt="item.product.name"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-sm line-clamp-2">{{ item.product.name }}</h3>
                        <p class="text-sm font-bold mt-1" :style="{ color: primaryColor }">
                            ₦{{ formatPrice(item.product.price) }}
                        </p>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-2 mt-2">
                            <button
                                @click="$emit('updateQuantity', item.product.id, item.quantity - 1)"
                                class="p-1 rounded border hover:bg-neutral-100 transition"
                                :disabled="item.quantity <= 1"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="text-sm font-medium w-8 text-center">{{ item.quantity }}</span>
                            <button
                                @click="$emit('updateQuantity', item.product.id, item.quantity + 1)"
                                class="p-1 rounded border hover:bg-neutral-100 transition"
                                :disabled="item.product.stock_quantity && item.quantity >= item.product.stock_quantity"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                            <button
                                @click="$emit('removeItem', item.product.id)"
                                class="ml-auto p-1 text-red-500 hover:bg-red-50 rounded transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with Total and Checkout -->
            <div v-if="items.length > 0" class="border-t p-4 space-y-4">
                <!-- Subtotal -->
                <div class="flex justify-between text-lg font-bold">
                    <span>Subtotal:</span>
                    <span :style="{ color: primaryColor }">₦{{ formatPrice(total) }}</span>
                </div>

                <!-- Checkout Button -->
                <button
                    @click="$emit('checkout')"
                    class="w-full py-3 rounded-lg font-semibold text-white transition hover:opacity-90"
                    :style="{ backgroundColor: primaryColor }"
                >
                    Proceed to Checkout
                </button>

                <button
                    @click="$emit('close')"
                    class="w-full py-2 rounded-lg font-medium border transition hover:bg-neutral-50"
                >
                    Continue Shopping
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    primaryColor: {
        type: String,
        default: '#3B82F6',
    },
});

defineEmits(['close', 'updateQuantity', 'removeItem', 'checkout']);

const total = computed(() => {
    return props.items.reduce((sum, item) => {
        return sum + (item.product.price * item.quantity);
    }, 0);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};
</script>
