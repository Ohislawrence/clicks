<template>
    <Head>
        <title>Shop — {{ store.name }}</title>
        <meta name="description" :content="store.meta_description || `Shop all products from ${store.name}`" />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="`Shop — ${store.name}`" />
        <meta property="og:description" :content="store.meta_description || `Shop all products from ${store.name}`" />
    </Head>

    <StoreLayout :store="store" :cart-count="cartItems.length" @toggle-cart="showCart = !showCart">

        <!-- Preview Banner -->
        <div v-if="previewMode" class="sticky top-0 z-50 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 px-4 shadow-md">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-semibold">PREVIEW MODE — Store not publicly visible.</p>
            </div>
        </div>

        <!-- Page Header -->
        <div class="border-b" :style="{ borderColor: primaryColor + '20', backgroundColor: primaryColor + '05' }">
            <div class="max-w-7xl mx-auto px-4 py-5">
                <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1.5">
                    <a :href="`/store/${store.slug}`" class="hover:underline" :style="{ color: primaryColor }">Home</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-gray-500">Shop</span>
                    <template v-if="activeCategory">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-700 font-medium">{{ activeCategory.name }}</span>
                    </template>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-bold" :style="{ color: primaryColor }">
                    {{ activeCategory ? activeCategory.name : 'All Products' }}
                </h1>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

                <!-- ===== SIDEBAR FILTERS ===== -->
                <aside class="lg:w-60 xl:w-64 shrink-0">
                    <!-- Mobile toggle -->
                    <button @click="filtersOpen = !filtersOpen"
                        class="lg:hidden w-full flex items-center justify-between px-4 py-3 rounded-xl border mb-4 font-medium text-sm"
                        :style="{ borderColor: primaryColor + '40', color: primaryColor }">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Filters
                            <span v-if="activeFiltersCount > 0" class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ activeFiltersCount }}</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': filtersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div :class="{ 'hidden lg:block': !filtersOpen }" class="space-y-4">

                        <!-- Search -->
                        <div class="bg-white border rounded-2xl p-4 shadow-sm" :style="{ borderColor: primaryColor + '20' }">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Search</h3>
                            <div class="relative">
                                <input v-model="searchQuery" type="text" placeholder="Search products…"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 transition"
                                    :style="{ borderColor: primaryColor + '30' }" />
                                <svg class="w-4 h-4 absolute left-2.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div v-if="categories.length > 0" class="bg-white border rounded-2xl p-4 shadow-sm" :style="{ borderColor: primaryColor + '20' }">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Categories</h3>
                            <ul class="space-y-1">
                                <li>
                                    <button @click="selectedCategory = null"
                                        class="w-full text-left flex items-center justify-between px-3 py-2 rounded-xl text-sm transition"
                                        :style="selectedCategory === null ? { backgroundColor: primaryColor + '15', color: primaryColor, fontWeight: 600 } : { color: '#4B5563' }">
                                        <span>All Products</span>
                                        <span class="text-xs text-gray-400">{{ products.length }}</span>
                                    </button>
                                </li>
                                <li v-for="cat in categories" :key="cat.id">
                                    <button @click="selectedCategory = cat.slug"
                                        class="w-full text-left flex items-center justify-between px-3 py-2 rounded-xl text-sm transition"
                                        :style="selectedCategory === cat.slug ? { backgroundColor: primaryColor + '15', color: primaryColor, fontWeight: 600 } : { color: '#4B5563' }">
                                        <span>{{ cat.name }}</span>
                                        <span class="text-xs text-gray-400">{{ cat.products_count }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Price Range -->
                        <div class="bg-white border rounded-2xl p-4 shadow-sm" :style="{ borderColor: primaryColor + '20' }">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Price Range</h3>
                            <div class="flex items-center gap-2 mb-3">
                                <input v-model.number="priceMin" type="number" min="0" placeholder="Min"
                                    class="w-full px-3 py-2 text-sm border rounded-xl focus:outline-none focus:ring-1 transition"
                                    :style="{ borderColor: primaryColor + '30' }" />
                                <span class="text-gray-400">–</span>
                                <input v-model.number="priceMax" type="number" min="0" placeholder="Max"
                                    class="w-full px-3 py-2 text-sm border rounded-xl focus:outline-none focus:ring-1 transition"
                                    :style="{ borderColor: primaryColor + '30' }" />
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <button v-for="range in priceRanges" :key="range.label"
                                    @click="applyPriceRange(range)"
                                    class="text-xs px-2.5 py-1 rounded-full border transition hover:opacity-80"
                                    :style="{ borderColor: primaryColor + '40', color: primaryColor }">
                                    {{ range.label }}
                                </button>
                            </div>
                        </div>

                        <!-- In Stock -->
                        <div class="bg-white border rounded-2xl p-4 shadow-sm" :style="{ borderColor: primaryColor + '20' }">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Availability</h3>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input v-model="inStockOnly" type="checkbox" class="w-4 h-4 rounded" :style="{ accentColor: primaryColor }" />
                                <span class="text-sm text-gray-700">In stock only</span>
                            </label>
                        </div>

                        <button v-if="activeFiltersCount > 0" @click="clearFilters"
                            class="w-full py-2.5 text-sm font-semibold text-red-500 border border-red-200 rounded-2xl hover:bg-red-50 transition">
                            Clear All Filters
                        </button>
                    </div>
                </aside>

                <!-- ===== PRODUCT GRID ===== -->
                <div class="flex-1 min-w-0">
                    <!-- Toolbar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <p class="text-sm text-gray-500">
                            <span class="font-semibold text-gray-800">{{ filteredProducts.length }}</span>
                            {{ filteredProducts.length === 1 ? 'product' : 'products' }}
                            <span v-if="activeCategory"> in <em class="font-medium not-italic">{{ activeCategory.name }}</em></span>
                        </p>
                        <div class="flex items-center gap-2">
                            <!-- View toggle -->
                            <div class="flex items-center border rounded-xl overflow-hidden" :style="{ borderColor: primaryColor + '30' }">
                                <button @click="gridCols = 'grid'" class="p-2 transition"
                                    :style="gridCols === 'grid' ? { backgroundColor: primaryColor, color: '#fff' } : { color: '#9CA3AF' }">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </button>
                                <button @click="gridCols = 'list'" class="p-2 transition"
                                    :style="gridCols === 'list' ? { backgroundColor: primaryColor, color: '#fff' } : { color: '#9CA3AF' }">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                            <!-- Sort -->
                            <select v-model="sortBy" class="text-sm border rounded-xl px-3 py-2 focus:outline-none bg-white"
                                :style="{ borderColor: primaryColor + '30' }">
                                <option value="featured">Featured</option>
                                <option value="price-asc">Price ↑</option>
                                <option value="price-desc">Price ↓</option>
                                <option value="name-asc">Name A–Z</option>
                                <option value="newest">Newest</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active filter chips -->
                    <div v-if="activeFiltersCount > 0" class="flex flex-wrap gap-2 mb-5">
                        <span v-if="selectedCategory" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white" :style="{ backgroundColor: primaryColor }">
                            {{ activeCategory?.name }}
                            <button @click="selectedCategory = null" class="hover:opacity-70 ml-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                        <span v-if="searchQuery" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white" :style="{ backgroundColor: primaryColor }">
                            "{{ searchQuery }}"
                            <button @click="searchQuery = ''" class="hover:opacity-70 ml-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                        <span v-if="priceMin || priceMax" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white" :style="{ backgroundColor: primaryColor }">
                            Price: {{ priceMin || 0 }}–{{ priceMax || '∞' }}
                            <button @click="priceMin = null; priceMax = null" class="hover:opacity-70 ml-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                        <span v-if="inStockOnly" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white" :style="{ backgroundColor: primaryColor }">
                            In Stock
                            <button @click="inStockOnly = false" class="hover:opacity-70 ml-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                    </div>

                    <!-- Grid view -->
                    <div v-if="gridCols === 'grid' && filteredProducts.length > 0"
                        class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-5">
                        <ProductCard v-for="product in filteredProducts" :key="product.id"
                            :product="product" :primary-color="primaryColor"
                            button-text="Add to Cart" @action="addToCart" />
                    </div>

                    <!-- List view -->
                    <div v-else-if="gridCols === 'list' && filteredProducts.length > 0" class="space-y-3">
                        <div v-for="product in filteredProducts" :key="product.id"
                            class="flex items-center gap-4 bg-white border rounded-2xl p-3 sm:p-4 hover:shadow-md transition cursor-pointer group"
                            :style="{ borderColor: primaryColor + '20' }"
                            @click="viewProduct(product)">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 rounded-xl overflow-hidden bg-gray-100">
                                <img v-if="product.images?.[0]" :src="product.images[0]" :alt="product.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition" loading="lazy" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 truncate text-sm sm:text-base group-hover:underline">{{ product.name }}</h3>
                                <p v-if="product.description" class="text-xs sm:text-sm text-gray-500 mt-0.5 line-clamp-2">{{ product.description }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="font-bold" :style="{ color: primaryColor }">{{ currencySymbol }}{{ formatPrice(product.price) }}</span>
                                    <span v-if="product.compare_at_price" class="text-xs text-gray-400 line-through">{{ currencySymbol }}{{ formatPrice(product.compare_at_price) }}</span>
                                    <span v-if="product.has_discount" class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600">-{{ product.discount_percentage }}%</span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 shrink-0 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="filteredProducts.length === 0" class="py-20 text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" :style="{ backgroundColor: primaryColor + '15' }">
                            <svg class="w-8 h-8" :style="{ color: primaryColor }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-500 mb-2">No products found</h3>
                        <p class="text-gray-400 mb-6 text-sm">Try adjusting your filters or search term.</p>
                        <button @click="clearFilters"
                            class="px-6 py-2.5 rounded-full text-white font-semibold text-sm shadow hover:opacity-90 transition"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})` }">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shopping Cart Sidebar -->
        <ShoppingCart :is-open="showCart" :items="cartItems" :primary-color="primaryColor"
            @close="showCart = false" @update-quantity="updateCartQuantity"
            @remove-item="removeFromCart" @checkout="proceedToCheckout" />

        <!-- Checkout Modal -->
        <CheckoutModal v-if="showCheckoutModal"
            :store="store" :items="cartItems" :states="props.states" :primary-color="primaryColor"
            @close="showCheckoutModal = false" @success="handleCheckoutSuccess" />
    </StoreLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { getCurrencySymbol, formatPrice as formatCurrencyPrice } from '@/utils/currency';
import StoreLayout from '@/Components/Storefront/StoreLayout.vue';
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
const filtersOpen       = ref(false);
const gridCols          = ref('grid');
const searchQuery       = ref('');
const selectedCategory  = ref(null);
const priceMin          = ref(null);
const priceMax          = ref(null);
const inStockOnly       = ref(false);
const sortBy            = ref('featured');

const priceRanges = [
    { label: 'Under ₦5k',   min: 0,     max: 5000  },
    { label: '₦5k–₦20k',   min: 5000,  max: 20000 },
    { label: '₦20k–₦50k',  min: 20000, max: 50000 },
    { label: 'Over ₦50k',   min: 50000, max: null  },
];

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const catSlug = params.get('category');
    if (catSlug) selectedCategory.value = catSlug;
});

const primaryColor   = computed(() => props.store.theme_customization?.colors?.primary   || '#3B82F6');
const secondaryColor = computed(() => props.store.theme_customization?.colors?.secondary || primaryColor.value);
const activeCategory = computed(() => props.categories.find(c => c.slug === selectedCategory.value) || null);

const activeFiltersCount = computed(() => {
    let n = 0;
    if (selectedCategory.value) n++;
    if (searchQuery.value)       n++;
    if (priceMin.value || priceMax.value) n++;
    if (inStockOnly.value)       n++;
    return n;
});

const filteredProducts = computed(() => {
    let list = [...props.products];

    if (selectedCategory.value) {
        list = list.filter(p => p.categories?.some(c => c.slug === selectedCategory.value));
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.trim().toLowerCase();
        list = list.filter(p =>
            p.name.toLowerCase().includes(q) ||
            (p.description && p.description.toLowerCase().includes(q))
        );
    }

    if (priceMin.value !== null && priceMin.value !== '') {
        list = list.filter(p => p.price >= priceMin.value);
    }
    if (priceMax.value !== null && priceMax.value !== '') {
        list = list.filter(p => p.price <= priceMax.value);
    }

    if (inStockOnly.value) {
        list = list.filter(p => p.is_in_stock);
    }

    // Sort
    switch (sortBy.value) {
        case 'price-asc':  list.sort((a, b) => a.price - b.price); break;
        case 'price-desc': list.sort((a, b) => b.price - a.price); break;
        case 'name-asc':   list.sort((a, b) => a.name.localeCompare(b.name)); break;
        case 'newest':     break; // already ordered by created_at desc from server
        default:           list.sort((a, b) => (b.is_featured ? 1 : 0) - (a.is_featured ? 1 : 0)); break;
    }

    return list;
});

const applyPriceRange = (range) => {
    priceMin.value = range.min;
    priceMax.value = range.max;
};

const clearFilters = () => {
    searchQuery.value      = '';
    selectedCategory.value = null;
    priceMin.value         = null;
    priceMax.value         = null;
    inStockOnly.value      = false;
};

const formatPrice = (price) => formatCurrencyPrice(price, props.store.currency ?? 'NGN');
const currencySymbol = computed(() => getCurrencySymbol(props.store.currency ?? 'NGN'));

const viewProduct = (product) => {
    router.visit(route('storefront.product', { slug: props.store.slug, productSlug: product.slug }));
};

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
