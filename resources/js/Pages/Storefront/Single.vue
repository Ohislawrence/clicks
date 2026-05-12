<template>
    <Head>
        <title>{{ store.meta_title || store.name }}</title>
        <meta name="description" :content="store.meta_description || store.description" />
        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="store.meta_title || store.name" />
        <meta property="og:description" :content="store.meta_description || store.description" />
        <meta v-if="store.meta_image" property="og:image" :content="store.meta_image" />
        <meta property="og:url" :content="$page.url" />
        <!-- Twitter Card -->
        <meta name="twitter:card" :content="store.meta_image ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="store.meta_title || store.name" />
        <meta name="twitter:description" :content="store.meta_description || store.description" />
        <meta v-if="store.meta_image" name="twitter:image" :content="store.meta_image" />
    </Head>

    <StoreLayout :store="store" :cart-count="0">
        <!-- Preview Mode Banner (Only visible to store owner) -->
        <div v-if="previewMode" class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-4 px-4 sticky top-0 z-50 shadow-lg">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold text-lg">⚠️ PREVIEW MODE - Store Not Publicly Visible</p>
                        <p class="text-sm opacity-90">
                            <span v-if="subscriptionStatus === 'expired'">Your subscription has expired. Only you can see this store. Public visitors will see "Store Unavailable".</span>
                            <span v-else>This store is not active yet (payment pending). Only you can see this store. Public visitors will see "Store Unavailable".</span>
                        </p>
                    </div>
                </div>
                <Link :href="route('advertiser.store.subscription.index', store.id)" class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition">
                    Activate Store
                </Link>
            </div>
        </div>
        
        <div class="container mx-auto px-4 py-8">
            <!-- Product Display -->
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                    <!-- Product Images -->
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div class="aspect-square rounded-lg overflow-hidden bg-neutral-100 border" :style="{ borderColor: primaryColor + '20' }">
                            <img
                                v-if="product.images && product.images[selectedImageIndex]"
                                :src="product.images[selectedImageIndex]"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-neutral-400">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                        </div>

                        <!-- Thumbnail Gallery -->
                        <div v-if="product.images && product.images.length > 1" class="grid grid-cols-5 gap-2">
                            <button
                                v-for="(image, index) in product.images"
                                :key="index"
                                @click="selectedImageIndex = index"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition hover:opacity-75"
                                :class="{
                                    'opacity-100': selectedImageIndex === index,
                                    'opacity-60': selectedImageIndex !== index,
                                }"
                                :style="{ borderColor: selectedImageIndex === index ? primaryColor : 'transparent' }"
                            >
                                <img
                                    :src="image"
                                    :alt="`${product.name} - ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ product.name }}</h1>

                            <!-- SKU -->
                            <p v-if="product.sku" class="text-sm text-neutral-500">SKU: {{ product.sku }}</p>
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                            <div class="flex items-baseline space-x-3">
                                <span class="text-4xl font-bold" :style="{ color: primaryColor }">
                                    ₦{{ formatPrice(product.price) }}
                                </span>
                                <span v-if="product.compare_at_price" class="text-2xl line-through text-neutral-400">
                                    ₦{{ formatPrice(product.compare_at_price) }}
                                </span>
                            </div>
                            <p v-if="product.has_discount" class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                Save {{ product.discount_percentage }}%
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <span
                                v-if="product.is_in_stock"
                                class="inline-flex items-center space-x-2 text-green-600"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="font-medium">In Stock</span>
                            </span>
                            <span v-else class="inline-flex items-center space-x-2 text-red-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="font-medium">Out of Stock</span>
                            </span>
                            <p v-if="product.stock_quantity && product.stock_quantity <= 10" class="text-sm text-orange-600 mt-1">
                                Only {{ product.stock_quantity }} left in stock!
                            </p>
                        </div>

                        <!-- Buy Now Button -->
                        <button
                            v-if="previewMode"
                            disabled
                            class="w-full py-4 rounded-lg bg-neutral-300 text-neutral-600 font-semibold text-lg cursor-not-allowed"
                        >
                            Store Not Active
                        </button>
                        <button
                            v-else-if="product.is_in_stock"
                            @click="showCheckoutModal = true"
                            class="w-full py-4 rounded-lg text-white font-semibold text-lg transition hover:opacity-90"
                            :style="{ backgroundColor: primaryColor }"
                        >
                            Buy Now
                        </button>
                        <button
                            v-else
                            disabled
                            class="w-full py-4 rounded-lg bg-neutral-300 text-neutral-600 font-semibold text-lg cursor-not-allowed"
                        >
                            Out of Stock
                        </button>

                        <!-- Contact Seller -->
                        <div class="flex flex-wrap gap-3">
                            <a
                                v-if="store.whatsapp_number"
                                :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '') + '?text=Hi, I am interested in ' + encodeURIComponent(product.name)"
                                target="_blank"
                                class="flex-1 flex items-center justify-center space-x-2 py-3 px-4 rounded-lg border-2 transition hover:bg-opacity-10"
                                :style="{ borderColor: primaryColor, color: primaryColor }"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                            <a
                                v-if="store.phone"
                                :href="'tel:' + store.phone"
                                class="flex-1 flex items-center justify-center space-x-2 py-3 px-4 rounded-lg border-2 transition hover:bg-opacity-10"
                                :style="{ borderColor: primaryColor, color: primaryColor }"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>Call</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Description -->
                <div class="mt-12 border-t pt-8">
                    <h2 class="text-2xl font-bold mb-4">Product Description</h2>
                    <div class="prose prose-lg max-w-none" v-html="product.description"></div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <CheckoutModal
            v-if="showCheckoutModal"
            :store="store"
            :items="[{ product: product, quantity: 1 }]"
            :states="props.states"
            :primary-color="primaryColor"
            @close="showCheckoutModal = false"
            @success="handleCheckoutSuccess"
        />
    </StoreLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Components/Storefront/StoreLayout.vue';
import CheckoutModal from '@/Components/Storefront/CheckoutModal.vue';

const props = defineProps({
    store: {
        type: Object,
        required: true,
    },
    product: {
        type: Object,
        required: true,
    },
    states: {
        type: Array,
        default: () => [],
    },
    previewMode: {
        type: Boolean,
        default: false,
    },
    subscriptionStatus: {
        type: String,
        default: 'active',
    },
});

const selectedImageIndex = ref(0);
const showCheckoutModal = ref(false);

const primaryColor = computed(() => {
    return props.store.theme_customization?.colors?.primary || '#3B82F6';
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};

const handleCheckoutSuccess = (data) => {
    if (data.payment_url) {
        window.location.href = data.payment_url;
    } else if (data.payment_link) {
        window.open(data.payment_link, '_blank');
        showCheckoutModal.value = false;
    }
};
</script>
