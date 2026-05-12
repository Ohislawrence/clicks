<template>
    <Head>
        <title>{{ product.name }} - {{ store.name }}</title>
        <meta name="description" :content="stripHtml(product.description).substring(0, 160)" />
        <!-- Open Graph -->
        <meta property="og:type" content="product" />
        <meta property="og:title" :content="`${product.name} - ${store.name}`" />
        <meta property="og:description" :content="stripHtml(product.description).substring(0, 160)" />
        <meta v-if="productImage" property="og:image" :content="productImage" />
        <meta v-else-if="store.meta_image" property="og:image" :content="store.meta_image" />
        <meta property="og:url" :content="$page.url" />
        <!-- Twitter Card -->
        <meta name="twitter:card" :content="(productImage || store.meta_image) ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="`${product.name} - ${store.name}`" />
        <meta name="twitter:description" :content="stripHtml(product.description).substring(0, 160)" />
        <meta v-if="productImage" name="twitter:image" :content="productImage" />
        <meta v-else-if="store.meta_image" name="twitter:image" :content="store.meta_image" />
    </Head>

    <StoreLayout :store="store" :cart-count="0">
        <template #nav-links>
            <Link
                :href="route('storefront.show', store.slug)"
                class="flex items-center space-x-1 px-3 py-2 rounded-lg hover:bg-opacity-10 hover:bg-black transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Store</span>
            </Link>
        </template>

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
                                :src="'/storage/' + product.images[selectedImageIndex]"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-neutral-400">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
                                <img :src="'/storage/' + image" :alt="`${product.name} - ${index + 1}`" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ product.name }}</h1>
                            <p v-if="product.sku" class="text-sm text-neutral-500">SKU: {{ product.sku }}</p>
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                            <div class="flex items-baseline space-x-3">
                                <span class="text-4xl font-bold" :style="{ color: primaryColor }">₦{{ formatPrice(product.price) }}</span>
                                <span v-if="product.compare_at_price" class="text-2xl line-through text-neutral-400">₦{{ formatPrice(product.compare_at_price) }}</span>
                            </div>
                            <p v-if="product.has_discount" class="inline-block px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                Save {{ product.discount_percentage }}%
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <span v-if="product.is_in_stock" class="inline-flex items-center space-x-2 text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">In Stock</span>
                            </span>
                            <span v-else class="inline-flex items-center space-x-2 text-red-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Out of Stock</span>
                            </span>
                            <p v-if="product.stock_quantity && product.stock_quantity <= 10" class="text-sm text-orange-600 mt-1">
                                Only {{ product.stock_quantity }} left in stock!
                            </p>
                        </div>

                        <!-- Quantity Selector -->
                        <div v-if="product.is_in_stock" class="flex items-center space-x-4">
                            <label class="font-medium">Quantity:</label>
                            <div class="flex items-center border rounded-lg">
                                <button
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="px-4 py-2 hover:bg-neutral-100 transition"
                                    :disabled="quantity <= 1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    :max="product.stock_quantity || 999"
                                    class="w-16 text-center border-0 focus:ring-0"
                                />
                                <button
                                    @click="quantity++"
                                    class="px-4 py-2 hover:bg-neutral-100 transition"
                                    :disabled="product.stock_quantity && quantity >= product.stock_quantity"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart / Buy Now -->
                        <div v-if="previewMode" class="space-y-3">
                            <button
                                disabled
                                class="w-full py-4 rounded-lg bg-neutral-300 text-neutral-600 font-semibold text-lg cursor-not-allowed"
                            >
                                Store Not Active
                            </button>
                        </div>
                        <div v-else-if="product.is_in_stock" class="space-y-3">
                            <button
                                @click="buyNow"
                                class="w-full py-4 rounded-lg text-white font-semibold text-lg transition hover:opacity-90"
                                :style="{ backgroundColor: primaryColor }"
                            >
                                Buy Now
                            </button>
                            <button
                                @click="addToCart"
                                class="w-full py-3 rounded-lg font-semibold border-2 transition hover:bg-opacity-10"
                                :style="{ borderColor: primaryColor, color: primaryColor }"
                            >
                                Add to Cart
                            </button>
                        </div>
                        <button
                            v-else
                            disabled
                            class="w-full py-4 rounded-lg bg-neutral-300 text-neutral-600 font-semibold text-lg cursor-not-allowed"
                        >
                            Out of Stock
                        </button>
                    </div>
                </div>

                <!-- Product Description -->
                <div class="mt-12 border-t pt-8">
                    <h2 class="text-2xl font-bold mb-4">Product Description</h2>
                    <div class="prose prose-lg max-w-none" v-html="product.description"></div>
                </div>

                <!-- Related Products -->
                <div v-if="relatedProducts.length > 0" class="mt-12 border-t pt-8">
                    <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <ProductCard
                            v-for="relatedProduct in relatedProducts"
                            :key="relatedProduct.id"
                            :product="relatedProduct"
                            :primary-color="primaryColor"
                            button-text="View"
                            @action="viewRelatedProduct"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <CheckoutModal
            v-if="showCheckoutModal"
            :store="store"
            :items="[{ product: product, quantity: quantity }]"
            :states="props.states"
            :primary-color="primaryColor"
            @close="showCheckoutModal = false"
            @success="handleCheckoutSuccess"
        />
    </StoreLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StoreLayout from '@/Components/Storefront/StoreLayout.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
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
    relatedProducts: {
        type: Array,
        default: () => [],
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
const quantity = ref(1);
const showCheckoutModal = ref(false);

const primaryColor = computed(() => {
    return props.store.theme_customization?.colors?.primary || '#3B82F6';
});

// First product image as absolute URL for OG tags
const productImage = computed(() => {
    const img = props.product.images?.[0];
    if (!img) return null;
    return img.startsWith('http') ? img : window.location.origin + '/storage/' + img;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-NG').format(price);
};

const stripHtml = (html) => {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const buyNow = () => {
    showCheckoutModal.value = true;
};

const addToCart = () => {
    // Navigate back to multi store with product in cart
    router.visit(route('storefront.show', props.store.slug), {
        data: { addToCart: props.product.id, quantity: quantity.value },
    });
};

const viewRelatedProduct = (product) => {
    router.visit(route('storefront.product', {
        slug: props.store.slug,
        productSlug: product.slug,
    }));
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
