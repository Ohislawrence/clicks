<template>
    <Head>
        <title>Order Confirmation - {{ store.name }}</title>
    </Head>

    <div class="min-h-screen bg-neutral-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-8">
            <!-- Success Icon -->
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-neutral-900 mb-2">Thank You for Your Order!</h1>
                <p class="text-neutral-600">Your order has been received and is being processed.</p>
            </div>

            <!-- Order Details -->
            <div class="border-t border-b py-6 mb-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-neutral-600">Order Number:</span>
                    <span class="font-bold">{{ order.order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Date:</span>
                    <span class="font-medium">{{ order.created_at }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-neutral-600">Payment Status:</span>
                    <span
                        class="px-3 py-1 rounded-full text-sm font-semibold"
                        :class="{
                            'bg-green-100 text-green-700': order.payment_status === 'paid',
                            'bg-yellow-100 text-yellow-700': order.payment_status === 'pending',
                            'bg-red-100 text-red-700': order.payment_status === 'failed',
                        }"
                    >
                        {{ order.payment_status }}
                    </span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h2 class="font-bold text-lg mb-3">Order Items</h2>
                <div class="space-y-3">
                    <div
                        v-for="(item, index) in order.items"
                        :key="index"
                        class="flex justify-between items-center py-2 border-b"
                    >
                        <div>
                            <p class="font-medium">{{ item.name }}</p>
                            <p class="text-sm text-neutral-600">Quantity: {{ item.quantity }}</p>
                        </div>
                        <p class="font-semibold">₦{{ formatPrice(item.total) }}</p>
                    </div>
                </div>

                <!-- Totals -->
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-neutral-600">
                        <span>Subtotal:</span>
                        <span>₦{{ formatPrice(order.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-neutral-600">
                        <span>Shipping:</span>
                        <span>₦{{ formatPrice(order.shipping_fee) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-2 border-t">
                        <span>Total:</span>
                        <span>₦{{ formatPrice(order.total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="mb-6 p-4 bg-neutral-50 rounded-lg">
                <h3 class="font-bold mb-2">Delivery Information</h3>
                <p class="text-sm"><strong>Name:</strong> {{ order.customer_name }}</p>
                <p class="text-sm"><strong>Email:</strong> {{ order.customer_email }}</p>
            </div>

            <!-- Store Contact -->
            <div class="border-t pt-6 space-y-4">
                <p class="text-sm text-neutral-600 text-center">
                    A confirmation email has been sent to <strong>{{ order.customer_email }}</strong>
                </p>
                <p class="text-sm text-neutral-600 text-center">
                    If you have any questions about your order, please contact us:
                </p>
                <div class="flex justify-center space-x-4">
                    <a
                        v-if="store.email"
                        :href="'mailto:' + store.email"
                        class="flex items-center space-x-2 text-sm text-blue-600 hover:underline"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                        <span>Email Us</span>
                    </a>
                    <a
                        v-if="store.phone"
                        :href="'tel:' + store.phone"
                        class="flex items-center space-x-2 text-sm text-blue-600 hover:underline"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                            />
                        </svg>
                        <span>Call Us</span>
                    </a>
                    <a
                        v-if="store.whatsapp_number"
                        :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')"
                        target="_blank"
                        class="flex items-center space-x-2 text-sm text-blue-600 hover:underline"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Continue Shopping Button -->
            <div class="mt-8 text-center">
                <a
                    :href="route('storefront.show', store.slug)"
                    class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                >
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    store: {
        type: Object,
        required: true,
    },
    order: {
        type: Object,
        required: true,
    },
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};
</script>
