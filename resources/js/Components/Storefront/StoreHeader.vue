<template>
    <!-- ============================================================
         MARKETPLACE THEME - Alibaba/Jumia style header
         ============================================================ -->
    <div v-if="themeSlug === 'marketplace'" class="sticky top-0 z-40">
        <!-- Announcement Bar -->
        <div v-if="headerConfig.announcement_text"
            class="text-center py-2 px-4 text-sm font-medium text-white"
            :style="{ backgroundColor: primaryColor }">
            {{ headerConfig.announcement_text }}
        </div>
        <!-- Main Header Row -->
        <header class="shadow-md" :style="{ backgroundColor: colors.header_bg || '#fff' }">
            <div class="container mx-auto px-4 py-3 flex items-center gap-4">
                <!-- Logo + Store Name -->
                <a :href="homeUrl" class="flex items-center gap-3 flex-shrink-0">
                    <img v-if="store.logo" :src="'/storage/' + store.logo" :alt="store.name"
                        class="h-12 w-12 object-contain rounded-lg shadow-sm" />
                    <div v-else class="h-12 w-12 rounded-lg flex items-center justify-center font-bold text-xl shadow-sm"
                        :style="logoStyles">{{ store.name.charAt(0).toUpperCase() }}</div>
                    <div class="hidden sm:block">
                        <div class="font-bold text-lg leading-tight" :style="{ color: colors.text || '#1F2937' }">{{ store.name }}</div>
                        <div v-if="headerConfig.tagline" class="text-xs opacity-60 max-w-[200px] truncate">{{ headerConfig.tagline }}</div>
                    </div>
                </a>
                <!-- Search Bar -->
                <div class="flex-1 hidden md:flex max-w-lg">
                    <div class="relative w-full">
                        <input type="text" placeholder="Search products..."
                            class="w-full pl-5 pr-12 py-2.5 rounded-full border-2 text-sm bg-gray-50 focus:bg-white focus:outline-none transition"
                            :style="{ borderColor: primaryColor + '60' }" />
                        <button class="absolute right-0 top-0 h-full px-4 rounded-r-full text-white font-medium text-sm flex items-center gap-1"
                            :style="{ backgroundColor: primaryColor }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Actions -->
                <div class="flex items-center gap-2 ml-auto flex-shrink-0">
                    <!-- Cart -->
                    <button @click="$emit('toggleCart')"
                        class="relative p-2.5 rounded-lg hover:bg-gray-100 transition" :style="{ color: primaryColor }">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow">{{ cartCount }}</span>
                    </button>
                    <a v-if="store.phone" :href="'tel:' + store.phone"
                        class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full font-semibold text-sm text-white transition hover:opacity-90 shadow"
                        :style="{ backgroundColor: primaryColor }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Call Us
                    </a>
                    <a v-if="store.whatsapp_number"
                        :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                        class="p-2.5 rounded-lg hover:bg-gray-100 transition text-green-600 hidden md:block">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                    </a>
                    <!-- Mobile hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition" :style="{ color: colors.text || '#1F2937' }">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Desktop Nav Row -->
            <div class="hidden md:block border-t" :style="{ borderColor: primaryColor + '20', backgroundColor: colors.header_bg || '#fff' }">
                <div class="container mx-auto px-4">
                    <nav class="flex items-center gap-1 py-2">
                        <a v-for="link in navLinks" :key="link.href" :href="link.href"
                            class="px-4 py-1.5 text-sm font-medium rounded-full transition hover:opacity-80"
                            :class="isActive(link.href) ? '' : 'hover:bg-gray-100'"
                            :style="isActive(link.href) ? { backgroundColor: primaryColor, color: '#fff' } : { color: colors.text || '#374151' }">
                            {{ link.label }}
                        </a>
                    </nav>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden border-t py-3 px-4 space-y-1" :style="{ backgroundColor: colors.header_bg || '#fff', borderColor: primaryColor + '25' }">
                <a v-for="link in navLinks" :key="link.href" :href="link.href" @click="mobileMenuOpen = false"
                    class="block px-4 py-2.5 rounded-lg text-sm font-medium transition"
                    :style="isActive(link.href) ? { backgroundColor: primaryColor, color: '#fff' } : { color: colors.text || '#374151' }">
                    {{ link.label }}
                </a>
            </div>
        </header>
    </div>

    <!-- ============================================================
         BOUTIQUE THEME - Luxury / fashion centered header
         ============================================================ -->
    <header v-else-if="themeSlug === 'boutique'" class="sticky top-0 z-40"
        :style="{ backgroundColor: colors.header_bg || '#fff', color: colors.text || '#1F2937' }">
        <div class="container mx-auto px-6 py-5">
            <div class="flex flex-col items-center text-center gap-2">
                <div class="flex items-center gap-4">
                    <div class="h-px w-12 opacity-30" :style="{ backgroundColor: primaryColor }"></div>
                    <a :href="homeUrl">
                        <img v-if="store.logo" :src="'/storage/' + store.logo" :alt="store.name"
                            class="h-14 w-14 object-contain rounded-full border-2 shadow-sm" :style="{ borderColor: primaryColor }" />
                        <div v-else class="h-14 w-14 rounded-full flex items-center justify-center font-bold text-2xl border-2 shadow-sm"
                            :style="{ ...logoStyles, borderColor: primaryColor }">{{ store.name.charAt(0).toUpperCase() }}</div>
                    </a>
                    <div class="h-px w-12 opacity-30" :style="{ backgroundColor: primaryColor }"></div>
                </div>
                <a :href="homeUrl" class="text-2xl font-serif font-bold tracking-[0.2em] uppercase hover:opacity-80 transition">{{ store.name }}</a>
                <p v-if="headerConfig.tagline" class="text-xs tracking-widest uppercase opacity-50">{{ headerConfig.tagline }}</p>
            </div>
            <!-- Nav + actions row -->
            <div class="flex items-center justify-center gap-6 mt-5 pt-4 border-t flex-wrap" :style="{ borderColor: primaryColor + '25' }">
                <a v-for="link in navLinks" :key="link.href" :href="link.href"
                    class="text-xs font-semibold tracking-[0.15em] uppercase transition"
                    :style="isActive(link.href) ? { color: primaryColor } : { color: colors.text || '#374151', opacity: 0.65 }">
                    {{ link.label }}
                </a>
                <!-- Cart -->
                <button @click="$emit('toggleCart')" class="relative transition hover:opacity-70" :style="{ color: primaryColor }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ cartCount }}</span>
                </button>
                <a v-if="store.phone" :href="'tel:' + store.phone"
                    class="text-xs font-semibold tracking-[0.15em] uppercase transition hover:opacity-70"
                    :style="{ color: primaryColor }">Contact</a>
                <a v-if="store.whatsapp_number"
                    :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                    class="text-xs font-semibold tracking-[0.15em] uppercase transition hover:opacity-70 text-green-600">WhatsApp</a>
            </div>
        </div>
        <div class="h-px w-full" :style="{ background: `linear-gradient(to right, transparent, ${primaryColor}80, transparent)` }"></div>
    </header>

    <!-- ============================================================
         MINIMAL / DEFAULT THEME - Clean simple header
         ============================================================ -->
    <header v-else class="sticky top-0 z-40 shadow-sm"
        :style="{ backgroundColor: colors.header_bg || '#fff', color: colors.text || '#1F2937', borderBottom: `1px solid ${primaryColor}20` }">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <!-- Logo + Name -->
            <a :href="homeUrl" class="flex items-center gap-3">
                <img v-if="store.logo" :src="'/storage/' + store.logo" :alt="store.name"
                    class="h-11 w-11 object-contain rounded-xl" />
                <div v-else class="h-11 w-11 rounded-xl flex items-center justify-center font-bold text-xl" :style="logoStyles">
                    {{ store.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <div class="text-xl font-bold tracking-tight">{{ store.name }}</div>
                    <div v-if="headerConfig.tagline" class="text-xs opacity-55">{{ headerConfig.tagline }}</div>
                </div>
            </a>
            <!-- Desktop Nav + actions -->
            <div class="hidden md:flex items-center gap-1">
                <a v-for="link in navLinks" :key="link.href" :href="link.href"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition"
                    :style="isActive(link.href) ? { color: primaryColor, fontWeight: '600' } : { color: colors.text || '#374151', opacity: 0.7 }">
                    {{ link.label }}
                </a>
                <!-- Cart -->
                <button @click="$emit('toggleCart')"
                    class="relative ml-2 p-2.5 rounded-full hover:bg-black/5 transition" :style="{ color: primaryColor }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ cartCount }}</span>
                </button>
                <a v-if="store.phone" :href="'tel:' + store.phone"
                    class="ml-1 flex items-center gap-2 px-4 py-2 rounded-xl font-medium text-sm shadow transition hover:opacity-90"
                    :style="{ background: `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`, color: '#fff' }">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Contact
                </a>
            </div>
            <!-- Mobile: cart + hamburger -->
            <div class="flex md:hidden items-center gap-2">
                <button @click="$emit('toggleCart')" class="relative p-2 rounded-full hover:bg-black/5 transition" :style="{ color: primaryColor }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span v-if="cartCount > 0" class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ cartCount }}</span>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg hover:bg-black/5 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Nav Drawer -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t py-2 px-4" :style="{ borderColor: primaryColor + '20' }">
            <a v-for="link in navLinks" :key="link.href" :href="link.href" @click="mobileMenuOpen = false"
                class="flex items-center py-3 text-sm font-medium border-b last:border-b-0 transition"
                :style="{ color: isActive(link.href) ? primaryColor : (colors.text || '#374151'), borderColor: '#f0f0f0' }">
                {{ link.label }}
            </a>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    store: { type: Object, required: true },
    cartCount: { type: Number, default: 0 },
});

defineEmits(['toggleCart']);

const mobileMenuOpen = ref(false);

const themeSlug = computed(() => props.store.theme?.slug || 'minimal');
const customization = computed(() => props.store.theme_customization || {});
const colors = computed(() => customization.value.colors || {});
const primaryColor = computed(() => colors.value.primary || '#3B82F6');
const secondaryColor = computed(() => colors.value.secondary || primaryColor.value);
const headerConfig = computed(() => customization.value.header || {});

const logoStyles = computed(() => ({
    background: `linear-gradient(135deg, ${primaryColor.value}, ${secondaryColor.value})`,
    color: '#fff',
}));

const storeBase = computed(() => `/store/${props.store.slug}`);
const homeUrl = computed(() => storeBase.value);
const isMulti = computed(() => props.store.store_type !== 'single');

const navLinks = computed(() => {
    const links = [{ label: 'Home', href: storeBase.value }];
    if (isMulti.value) links.push({ label: 'Shop', href: `${storeBase.value}/shop` });
    links.push({ label: 'About Us', href: `${storeBase.value}/about` });
    return links;
});

const isActive = (href) => {
    if (typeof window === 'undefined') return false;
    const path = window.location.pathname.replace(/\/$/, '');
    return path === href.replace(/\/$/, '');
};
</script>
