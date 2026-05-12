<template>
    <div class="rounded-xl overflow-hidden border transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1 group" :style="cardStyles">
        <!-- Product Image -->
        <div class="aspect-square bg-neutral-100 relative overflow-hidden">
            <img
                v-if="product.images && product.images[0]"
                :src="product.images[0]"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-110 transition duration-500 ease-out"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-neutral-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
            </div>

            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-col gap-2">
                <span
                    v-if="product.has_discount"
                    class="px-3 py-1.5 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full shadow-lg animate-pulse"
                >
                    -{{ product.discount_percentage }}% OFF
                </span>
                <span
                    v-if="product.is_featured"
                    class="px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1"
                >
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Featured
                </span>
            </div>

            <!-- Out of Stock Overlay -->
            <div
                v-if="!product.is_in_stock"
                class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center backdrop-blur-sm"
            >
                <span class="text-white font-bold text-lg px-6 py-3 bg-red-500 rounded-lg shadow-lg">Out of Stock</span>
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-5 space-y-3">
            <h3 class="font-bold text-lg line-clamp-2 group-hover:text-opacity-80 transition-colors">{{ product.name }}</h3>

            <p v-if="product.description" class="text-sm opacity-60 line-clamp-2 leading-relaxed">
                {{ stripHtml(product.description) }}
            </p>

            <!-- Price -->
            <div class="flex items-baseline space-x-2">
                <span class="text-2xl font-bold" :style="{ color: primaryColor }">
                    ₦{{ formatPrice(product.price) }}
                </span>
                <span v-if="product.compare_at_price" class="text-sm line-through opacity-40">
                    ₦{{ formatPrice(product.compare_at_price) }}
                </span>
            </div>

            <!-- Action Button -->
            <button
                v-if="showButton && product.is_in_stock"
                @click="$emit('action', product)"
                class="w-full mt-4 px-6 py-3 rounded-lg font-semibold transition-all transform hover:scale-105 hover:shadow-lg"
                :style="buttonStyles"
            >
                {{ buttonText }}
            </button>

            <button
                v-else-if="!product.is_in_stock"
                disabled
                class="w-full mt-4 px-6 py-3 rounded-lg font-semibold bg-neutral-300 text-neutral-600 cursor-not-allowed"
            >
                Out of Stock
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    primaryColor: {
        type: String,
        default: '#3B82F6',
    },
    showButton: {
        type: Boolean,
        default: true,
    },
    buttonText: {
        type: String,
        default: 'Add to Cart',
    },
});

defineEmits(['action']);

const cardStyles = computed(() => ({
    borderColor: props.primaryColor + '10',
}));

const buttonStyles = computed(() => ({
    background: `linear-gradient(135deg, ${props.primaryColor}, ${props.primaryColor}dd)`,
    color: '#FFFFFF',
}));

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};

const stripHtml = (html) => {
    if (!html) return '';
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};
</script>
