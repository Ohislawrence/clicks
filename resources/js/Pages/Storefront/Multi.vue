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

    <StoreLayout :store="store" :cart-count="cartItems.length" @toggle-cart="showCart = !showCart">
        <!-- Preview Mode Banner -->
        <div v-if="previewMode" class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-4 px-4 sticky top-0 z-50 shadow-lg">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-bold text-lg">⚠️ PREVIEW MODE - Store Not Publicly Visible</p>
                        <p class="text-sm opacity-90">
                            <span v-if="subscriptionStatus === 'expired'">Your subscription has expired. Only you can see this store.</span>
                            <span v-else>This store is not active yet. Only you can see this preview.</span>
                        </p>
                    </div>
                </div>
                <Link :href="route('advertiser.store.subscription.index', store.id)" class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition">
                    Activate Store
                </Link>
            </div>
        </div>

        <!-- ===== HERO SECTION ===== -->
        <ThemeHero :store="store" />

        <!-- ===== CATEGORIES SECTION ===== -->
        <section v-if="categories.length > 0" class="py-14 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold mb-2" :style="{ color: primaryColor }">Shop by Category</h2>
                    <p class="text-gray-500">Browse our curated collections</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <a v-for="cat in categories" :key="cat.id"
                        :href="`/store/${store.slug}/shop?category=${cat.slug}`"
                        class="group flex flex-col items-center gap-3 p-5 bg-white rounded-2xl border hover:shadow-lg transition-all duration-200 hover:-translate-y-1"
                        :style="{ borderColor: primaryColor + '25' }">
                        <!-- Category icon / initial -->
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl font-bold text-white shadow-sm"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
                            {{ cat.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-sm font-semibold text-gray-700 text-center group-hover:text-opacity-80 leading-tight">{{ cat.name }}</span>
                        <span class="text-xs text-gray-400">{{ cat.products_count }} items</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== FEATURED PRODUCTS SECTION ===== -->
        <section v-if="featuredProducts.length > 0" class="py-14">
            <div class="container mx-auto px-4">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-3xl font-bold mb-1" :style="{ color: primaryColor }">Featured Products</h2>
                        <p class="text-gray-500">Handpicked items just for you</p>
                    </div>
                    <a :href="`/store/${store.slug}/shop`"
                        class="hidden sm:flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-full border-2 transition hover:opacity-80"
                        :style="{ color: primaryColor, borderColor: primaryColor }">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :product="product"
                        :primary-color="primaryColor"
                        button-text="View Product"
                        @action="viewProduct"
                    />
                </div>
                <div class="text-center mt-10 sm:hidden">
                    <a :href="`/store/${store.slug}/shop`"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-white font-semibold shadow-lg hover:opacity-90 transition"
                        :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
                        Shop All Products
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== NEW ARRIVALS SECTION (non-featured products) ===== -->
        <section v-if="newArrivals.length > 0" class="py-14" :style="{ backgroundColor: primaryColor + '08' }">
            <div class="container mx-auto px-4">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-3xl font-bold mb-1" :style="{ color: primaryColor }">New Arrivals</h2>
                        <p class="text-gray-500">Fresh additions to our catalogue</p>
                    </div>
                    <a :href="`/store/${store.slug}/shop`"
                        class="hidden sm:flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-full border-2 transition hover:opacity-80"
                        :style="{ color: primaryColor, borderColor: primaryColor }">
                        See More
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="product in newArrivals"
                        :key="product.id"
                        :product="product"
                        :primary-color="primaryColor"
                        button-text="View Product"
                        @action="viewProduct"
                    />
                </div>
            </div>
        </section>

        <!-- ===== ALL PRODUCTS FALLBACK (no categories / no featured) ===== -->
        <section v-if="featuredProducts.length === 0 && newArrivals.length === 0 && products.length > 0" class="py-14">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold mb-8" :style="{ color: primaryColor }">Our Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="product in products"
                        :key="product.id"
                        :product="product"
                        :primary-color="primaryColor"
                        button-text="View Product"
                        @action="viewProduct"
                    />
                </div>
            </div>
        </section>

        <!-- ===== EMPTY STATE ===== -->
        <div v-if="products.length === 0" class="py-24 text-center">
            <svg class="w-20 h-20 mx-auto mb-4 opacity-30 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-500 mb-2">No Products Yet</h2>
            <p class="text-gray-400">Check back soon for new products!</p>
        </div>

        <!-- ===== STORE INFO / CTA STRIP ===== -->
        <section class="py-16" :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
            <div class="container mx-auto px-4 text-center text-white">
                <h2 class="text-3xl font-bold mb-3">{{ store.name }}</h2>
                <p v-if="store.description" class="max-w-xl mx-auto opacity-85 mb-8 text-lg" v-html="stripHtml(store.description).substring(0, 180)"></p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a :href="`/store/${store.slug}/shop`"
                        class="bg-white font-bold px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl transition hover:-translate-y-0.5"
                        :style="{ color: primaryColor }">
                        Shop Now
                    </a>
                    <a v-if="store.whatsapp_number"
                        :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                        class="border-2 border-white text-white font-semibold px-8 py-3.5 rounded-full hover:bg-white/10 transition">
                        Chat with Us
                    </a>
                    <a v-else-if="store.phone" :href="'tel:' + store.phone"
                        class="border-2 border-white text-white font-semibold px-8 py-3.5 rounded-full hover:bg-white/10 transition">
                        Call Us
                    </a>
                </div>
            </div>
        </section>

        <!-- Shopping Cart Sidebar -->
        <ShoppingCart
            :is-open="showCart"
            :items="cartItems"
            :primary-color="primaryColor"
            @close="showCart = false"
            @update-quantity="updateCartQuantity"
            @remove-item="removeFromCart"
            @checkout="proceedToCheckout"
        />

        <!-- Checkout Modal -->
        <CheckoutModal
            v-if="showCheckoutModal"
            :store="store"
            :items="cartItems"
            :states="props.states"
            :primary-color="primaryColor"
            @close="showCheckoutModal = false"
            @success="handleCheckoutSuccess"
        />
    </StoreLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StoreLayout from '@/Components/Storefront/StoreLayout.vue';
import ThemeHero from '@/Components/Storefront/ThemeHero.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import ShoppingCart from '@/Components/Storefront/ShoppingCart.vue';
import CheckoutModal from '@/Components/Storefront/CheckoutModal.vue';

const props = defineProps({
    store: { type: Object, required: true },
    products: { type: Array, required: true },
    categories: { type: Array, default: () => [] },
    states: { type: Array, default: () => [] },
    previewMode: { type: Boolean, default: false },
    subscriptionStatus: { type: String, default: 'active' },
});

const showCart = ref(false);
const showCheckoutModal = ref(false);
const cartItems = ref([]);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const addId = params.get('addToCart');
    const qty = parseInt(params.get('quantity')) || 1;
    if (addId) {
        const product = props.products.find(p => p.id == addId);
        if (product) {
            cartItems.value = [{ product, quantity: qty }];
            showCart.value = true;
        }
        // Clean up the URL without reloading
        window.history.replaceState({}, '', window.location.pathname);
    }
});

const primaryColor = computed(() => props.store.theme_customization?.colors?.primary || '#3B82F6');
const secondaryColor = computed(() => props.store.theme_customization?.colors?.secondary || primaryColor.value);

const featuredProducts = computed(() => props.products.filter(p => p.is_featured).slice(0, 8));
const newArrivals = computed(() => props.products.filter(p => !p.is_featured).slice(0, 8));

const stripHtml = (html) => html ? html.replace(/<[^>]*>/g, '') : '';

const viewProduct = (product) => {
    router.visit(route('storefront.product', {
        slug: props.store.slug,
        productSlug: product.slug,
    }));
};

const updateCartQuantity = (productId, quantity) => {
    if (quantity <= 0) { removeFromCart(productId); return; }
    const item = cartItems.value.find(i => i.product.id === productId);
    if (item) item.quantity = quantity;
};

const removeFromCart = (productId) => {
    cartItems.value = cartItems.value.filter(i => i.product.id !== productId);
};

const proceedToCheckout = () => {
    showCart.value = false;
    showCheckoutModal.value = true;
};

const handleCheckoutSuccess = (data) => {
    if (data.payment_url) {
        window.location.href = data.payment_url;
    } else if (data.payment_link) {
        window.open(data.payment_link, '_blank');
        showCheckoutModal.value = false;
        cartItems.value = [];
    }
};
</script>
