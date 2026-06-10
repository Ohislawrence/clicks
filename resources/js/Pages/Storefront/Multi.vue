<template>
    <Head>
        <title>{{ store.meta_title || store.name }}</title>
        <meta name="description" :content="store.meta_description || store.description" />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="store.meta_title || store.name" />
        <meta property="og:description" :content="store.meta_description || store.description" />
        <meta v-if="store.meta_image" property="og:image" :content="store.meta_image" />
        <meta property="og:url" :content="$page.url" />
        <meta name="twitter:card" :content="store.meta_image ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="store.meta_title || store.name" />
        <meta name="twitter:description" :content="store.meta_description || store.description" />
        <meta v-if="store.meta_image" name="twitter:image" :content="store.meta_image" />
    </Head>

    <StoreLayout :store="store" :cart-count="cartItems.length" @toggle-cart="showCart = !showCart">

        <!-- Preview Banner -->
        <div v-if="previewMode" class="sticky top-0 z-50 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 px-4 shadow-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-5 h-5 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm font-semibold truncate">
                        PREVIEW MODE — Store not publicly visible.
                        <span v-if="subscriptionStatus === 'expired'" class="font-normal opacity-90">Subscription expired.</span>
                    </p>
                </div>
                <Link :href="route('advertiser.store.subscription.index', store.id)"
                    class="shrink-0 bg-white text-orange-600 text-sm font-bold px-4 py-1.5 rounded-full hover:bg-orange-50 transition whitespace-nowrap">
                    Activate →
                </Link>
            </div>
        </div>

        <!-- Hero -->
        <ThemeHero :store="store" />

        <!-- Trust Badges -->
        <section class="border-y" :style="{ borderColor: primaryColor + '15', backgroundColor: primaryColor + '05' }">
            <div class="max-w-5xl mx-auto px-4 py-3">
                <div class="flex items-center justify-center gap-5 sm:gap-10 flex-wrap text-xs font-medium text-gray-600">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Secure Checkout
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Fast Delivery
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Quality Guaranteed
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Customer Support
                    </span>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section v-if="categories.length > 0" class="pt-12 pb-6">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight" :style="{ color: primaryColor }">Shop by Category</h2>
                        <p class="text-gray-500 text-sm mt-1">Browse our collections</p>
                    </div>
                    <a :href="`/store/${store.slug}/shop`" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-70 transition" :style="{ color: primaryColor }">
                        View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <!-- Horizontal scroll on mobile, grid on desktop -->
                <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
                    <a v-for="cat in categories" :key="cat.id"
                        :href="`/store/${store.slug}/shop?category=${cat.slug}`"
                        class="snap-start shrink-0 sm:shrink flex flex-col items-center gap-2.5 p-4 bg-white rounded-2xl border hover:shadow-md transition-all group w-28 sm:w-auto"
                        :style="{ borderColor: primaryColor + '20' }">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-bold text-white transition group-hover:scale-110"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
                            {{ cat.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-semibold text-gray-700 leading-tight line-clamp-2">{{ cat.name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ cat.products_count }} items</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section v-if="featuredProducts.length > 0" class="py-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold text-white mb-2"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">⭐ Featured</span>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight" :style="{ color: primaryColor }">Top Picks</h2>
                        <p class="text-gray-500 text-sm mt-1">Handpicked items just for you</p>
                    </div>
                    <a :href="`/store/${store.slug}/shop`" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-70 transition" :style="{ color: primaryColor }">
                        View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                    <ProductCard v-for="product in featuredProducts" :key="product.id"
                        :product="product" :primary-color="primaryColor"
                        button-text="Add to Cart" @action="addToCart" />
                </div>
                <div class="text-center mt-8 sm:hidden">
                    <a :href="`/store/${store.slug}/shop`"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-white font-semibold shadow hover:opacity-90 transition"
                        :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
                        Shop All Products <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Promo Banner -->
        <section v-if="store.description" class="py-10 mx-4 sm:mx-6 lg:mx-8 rounded-3xl my-4 overflow-hidden relative"
            :style="{ background: `linear-gradient(135deg, ${primaryColor}18, ${secondaryColor}10)`, border: `1px solid ${primaryColor}20` }">
            <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full opacity-10" :style="{ backgroundColor: primaryColor }"></div>
            <div class="absolute -left-6 -bottom-6 w-28 h-28 rounded-full opacity-10" :style="{ backgroundColor: secondaryColor }"></div>
            <div class="relative max-w-3xl mx-auto px-6 text-center">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-3">{{ store.name }}</h3>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed line-clamp-3">{{ stripHtml(store.description) }}</p>
                <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                    <a :href="`/store/${store.slug}/shop`"
                        class="px-7 py-3 rounded-full text-white font-bold shadow-lg hover:shadow-xl transition hover:-translate-y-0.5"
                        :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">Shop Now</a>
                    <a v-if="store.whatsapp_number"
                        :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                        class="px-7 py-3 rounded-full font-semibold border-2 transition hover:opacity-80"
                        :style="{ color: primaryColor, borderColor: primaryColor }">Chat with Us</a>
                </div>
            </div>
        </section>

        <!-- New Arrivals -->
        <section v-if="newArrivals.length > 0" class="py-10" :style="{ backgroundColor: primaryColor + '06' }">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold text-white mb-2 bg-gradient-to-r from-green-500 to-emerald-500">🆕 New</span>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight" :style="{ color: primaryColor }">New Arrivals</h2>
                        <p class="text-gray-500 text-sm mt-1">Fresh additions to our catalogue</p>
                    </div>
                    <a :href="`/store/${store.slug}/shop`" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold hover:opacity-70 transition" :style="{ color: primaryColor }">
                        See more <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                    <ProductCard v-for="product in newArrivals" :key="product.id"
                        :product="product" :primary-color="primaryColor"
                        button-text="Add to Cart" @action="addToCart" />
                </div>
            </div>
        </section>

        <!-- All Products Fallback -->
        <section v-if="featuredProducts.length === 0 && newArrivals.length === 0 && products.length > 0" class="py-12">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6" :style="{ color: primaryColor }">Our Products</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
                    <ProductCard v-for="product in products" :key="product.id"
                        :product="product" :primary-color="primaryColor"
                        button-text="Add to Cart" @action="addToCart" />
                </div>
            </div>
        </section>

        <!-- Empty State -->
        <div v-if="products.length === 0" class="py-28 text-center px-4">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" :style="{ backgroundColor: primaryColor + '15' }">
                <svg class="w-10 h-10" :style="{ color: primaryColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-600 mb-2">No Products Yet</h2>
            <p class="text-gray-400">Check back soon for exciting products!</p>
        </div>

        <!-- Shopping Cart Sidebar -->
        <ShoppingCart
            :is-open="showCart" :items="cartItems" :primary-color="primaryColor"
            @close="showCart = false" @update-quantity="updateCartQuantity"
            @remove-item="removeFromCart" @checkout="proceedToCheckout"
        />

        <!-- Checkout Modal -->
        <CheckoutModal
            v-if="showCheckoutModal"
            :store="store" :items="cartItems" :states="props.states" :primary-color="primaryColor"
            @close="showCheckoutModal = false" @success="handleCheckoutSuccess"
        />
    </StoreLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Components/Storefront/StoreLayout.vue';
import ThemeHero from '@/Components/Storefront/ThemeHero.vue';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import ShoppingCart from '@/Components/Storefront/ShoppingCart.vue';
import CheckoutModal from '@/Components/Storefront/CheckoutModal.vue';

const props = defineProps({
    store:              { type: Object,  required: true },
    products:           { type: Array,   required: true },
    categories:         { type: Array,   default: () => [] },
    states:             { type: Array,   default: () => [] },
    previewMode:        { type: Boolean, default: false },
    subscriptionStatus: { type: String,  default: 'active' },
});

const showCart          = ref(false);
const showCheckoutModal = ref(false);
const cartItems         = ref([]);

const primaryColor   = computed(() => props.store.theme_customization?.colors?.primary   || '#3B82F6');
const secondaryColor = computed(() => props.store.theme_customization?.colors?.secondary || primaryColor.value);

const featuredProducts = computed(() => props.products.filter(p => p.is_featured).slice(0, 8));
const newArrivals      = computed(() => props.products.filter(p => !p.is_featured).slice(0, 8));

const stripHtml = (html) => html ? html.replace(/<[^>]*>/g, '') : '';

const addToCart = (product) => {
    const existing = cartItems.value.find(i => i.product.id === product.id);
    if (existing) { existing.quantity++; } else { cartItems.value.push({ product, quantity: 1 }); }
    showCart.value = true;
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
    showCart.value          = false;
    showCheckoutModal.value = true;
};

const handleCheckoutSuccess = (data) => {
    if (data.payment_url) {
        window.location.href = data.payment_url;
    } else if (data.payment_link) {
        window.open(data.payment_link, '_blank');
        showCheckoutModal.value = false;
        cartItems.value         = [];
    }
};
</script>
