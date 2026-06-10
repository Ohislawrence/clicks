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

    <StoreLayout :store="store" :cart-count="0">

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
                        <span v-else class="font-normal opacity-90">Payment pending.</span>
                    </p>
                </div>
                <Link :href="route('advertiser.store.subscription.index', store.id)"
                    class="shrink-0 bg-white text-orange-600 text-sm font-bold px-4 py-1.5 rounded-full hover:bg-orange-50 transition whitespace-nowrap">
                    Activate →
                </Link>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8 sm:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-start">

                <!-- ===== IMAGE GALLERY ===== -->
                <div class="lg:sticky lg:top-24 space-y-3">
                    <!-- Main Image -->
                    <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border"
                        :style="{ borderColor: primaryColor + '20' }">
                        <img v-if="product.images && product.images[selectedImageIndex]"
                            :src="product.images[selectedImageIndex]"
                            :alt="product.name"
                            class="w-full h-full object-cover transition duration-300"
                            loading="eager" />
                        <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-3">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">No image available</p>
                        </div>
                    </div>

                    <!-- Thumbnail Strip -->
                    <div v-if="product.images && product.images.length > 1" class="flex gap-2 overflow-x-auto pb-1">
                        <button v-for="(image, index) in product.images" :key="index"
                            @click="selectedImageIndex = index"
                            class="shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 transition"
                            :style="{ borderColor: selectedImageIndex === index ? primaryColor : 'transparent', opacity: selectedImageIndex === index ? 1 : 0.55 }">
                            <img :src="image" :alt="`${product.name} ${index + 1}`" class="w-full h-full object-cover" loading="lazy" />
                        </button>
                    </div>

                    <!-- Contact Buttons (desktop only - also shown in product info on mobile) -->
                    <div class="hidden lg:flex gap-3 pt-2">
                        <a v-if="store.whatsapp_number"
                            :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '') + '?text=' + encodeURIComponent('Hi, I am interested in ' + product.name)"
                            target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 font-semibold text-sm transition hover:opacity-80"
                            :style="{ color: '#25D366', borderColor: '#25D366' }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            WhatsApp
                        </a>
                        <a v-if="store.phone" :href="'tel:' + store.phone"
                            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 font-semibold text-sm transition hover:opacity-80"
                            :style="{ color: primaryColor, borderColor: primaryColor }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Call
                        </a>
                    </div>
                </div>

                <!-- ===== PRODUCT INFO ===== -->
                <div class="space-y-5">
                    <!-- Breadcrumb -->
                    <nav class="text-xs text-gray-400">
                        <a :href="`/store/${store.slug}`" class="hover:underline">{{ store.name }}</a>
                        <span class="mx-1">/</span>
                        <span class="text-gray-600">{{ product.name }}</span>
                    </nav>

                    <!-- Digital / Physical Badge -->
                    <div class="flex items-center gap-2">
                        <span v-if="product.product_type === 'digital'"
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full text-white bg-gradient-to-r from-violet-500 to-purple-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Digital Product
                        </span>
                        <span v-else
                            class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full text-white bg-gradient-to-r from-blue-500 to-cyan-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            Physical Product
                        </span>
                    </div>

                    <!-- Product Name -->
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">{{ product.name }}</h1>

                    <!-- SKU -->
                    <p v-if="product.sku" class="text-xs text-gray-400 font-mono">SKU: {{ product.sku }}</p>

                    <!-- Price Block -->
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span class="text-3xl sm:text-4xl font-extrabold" :style="{ color: primaryColor }">
                            {{ currencySymbol }}{{ formatPrice(product.price) }}
                        </span>
                        <span v-if="product.compare_at_price" class="text-xl line-through text-gray-400">
                            {{ currencySymbol }}{{ formatPrice(product.compare_at_price) }}
                        </span>
                        <span v-if="product.has_discount"
                            class="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                            -{{ product.discount_percentage }}% OFF
                        </span>
                    </div>

                    <!-- Stock -->
                    <div class="flex items-center gap-2">
                        <template v-if="product.is_in_stock">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-sm font-medium text-green-600">In Stock</span>
                            <span v-if="product.stock_quantity && product.stock_quantity <= 10"
                                class="text-xs text-orange-600 font-medium bg-orange-50 px-2 py-0.5 rounded-full">
                                Only {{ product.stock_quantity }} left!
                            </span>
                        </template>
                        <template v-else>
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-sm font-medium text-red-600">Out of Stock</span>
                        </template>
                    </div>

                    <!-- Divider -->
                    <hr class="border-gray-100">

                    <!-- Digital delivery notice -->
                    <div v-if="product.product_type === 'digital'"
                        class="flex items-start gap-3 p-3.5 rounded-xl bg-violet-50 border border-violet-100">
                        <svg class="w-5 h-5 text-violet-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <div class="text-xs text-violet-700 leading-relaxed">
                            <p class="font-semibold mb-0.5">Instant Delivery</p>
                            <p>After payment, you'll receive your download link via email immediately. No shipping required.</p>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="space-y-3">
                        <button v-if="previewMode" disabled
                            class="w-full py-4 rounded-2xl bg-gray-200 text-gray-500 font-bold text-base cursor-not-allowed">
                            Store Not Active
                        </button>
                        <button v-else-if="product.is_in_stock"
                            @click="showCheckoutModal = true"
                            class="w-full py-4 rounded-2xl text-white font-bold text-base transition hover:opacity-90 active:scale-[0.98] shadow-lg hover:shadow-xl"
                            :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${primaryColor}cc)` }">
                            <span v-if="product.product_type === 'digital'" class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Get Instant Access — {{ currencySymbol }}{{ formatPrice(product.price) }}
                            </span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Buy Now — {{ currencySymbol }}{{ formatPrice(product.price) }}
                            </span>
                        </button>
                        <button v-else disabled
                            class="w-full py-4 rounded-2xl bg-gray-200 text-gray-500 font-bold text-base cursor-not-allowed">
                            Out of Stock
                        </button>

                        <!-- Mobile contact buttons -->
                        <div class="flex gap-3 lg:hidden">
                            <a v-if="store.whatsapp_number"
                                :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '') + '?text=' + encodeURIComponent('Hi, I am interested in ' + product.name)"
                                target="_blank"
                                class="flex-1 flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border-2 font-semibold text-sm transition hover:opacity-80"
                                :style="{ color: '#25D366', borderColor: '#25D366' }">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                WhatsApp
                            </a>
                            <a v-if="store.phone" :href="'tel:' + store.phone"
                                class="flex-1 flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl border-2 font-semibold text-sm transition hover:opacity-80"
                                :style="{ color: primaryColor, borderColor: primaryColor }">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Call Us
                            </a>
                        </div>
                    </div>

                    <!-- Trust micro-badges -->
                    <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                        <span class="flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            Secure Payment
                        </span>
                        <!-- Physical: show delivery badge -->
                        <span v-if="product.product_type !== 'digital'" class="flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            Fast Delivery
                        </span>
                        <!-- Digital: show instant download badge -->
                        <span v-else class="flex items-center gap-1 bg-violet-50 text-violet-600 px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Instant Download
                        </span>
                        <span class="flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Quality Guaranteed
                        </span>
                    </div>

                    <!-- Description -->
                    <div v-if="product.description" class="border-t border-gray-100 pt-5">
                        <h2 class="text-base font-bold text-gray-800 mb-3">Product Description</h2>
                        <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed" v-html="product.description"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <CheckoutModal
            v-if="showCheckoutModal"
            :store="store"
            :items="[{ product, quantity: 1 }]"
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
import { getCurrencySymbol, formatPrice as formatCurrencyPrice } from '@/utils/currency';

const props = defineProps({
    store:              { type: Object,  required: true },
    product:            { type: Object,  required: true },
    states:             { type: Array,   default: () => [] },
    previewMode:        { type: Boolean, default: false },
    subscriptionStatus: { type: String,  default: 'active' },
});

const selectedImageIndex = ref(0);
const showCheckoutModal  = ref(false);

const primaryColor = computed(() => props.store.theme_customization?.colors?.primary || '#3B82F6');

const formatPrice = (price) => formatCurrencyPrice(price, props.store.currency ?? 'NGN');
const currencySymbol = computed(() => getCurrencySymbol(props.store.currency ?? 'NGN'));

const handleCheckoutSuccess = (data) => {
    if (data.payment_url) {
        window.location.href = data.payment_url;
    } else if (data.payment_link) {
        window.open(data.payment_link, '_blank');
        showCheckoutModal.value = false;
    }
};
</script>
