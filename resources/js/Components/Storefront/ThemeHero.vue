<template>
    <!-- ===== MARKETPLACE HERO ===== -->
    <section v-if="themeSlug === 'marketplace'"
        class="relative w-full overflow-hidden"
        :style="marketplaceHeroStyle"
    >
        <!-- Background Image with overlay -->
        <div v-if="heroConfig.image_url" class="absolute inset-0">
            <img :src="heroConfig.image_url" alt="" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent"
                :style="{ opacity: (heroConfig.overlay_opacity || 55) / 100 }"></div>
        </div>
        <!-- No image: gradient bg -->
        <div v-else class="absolute inset-0"
            :style="{ background: `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)` }"></div>

        <!-- Content -->
        <div class="relative container mx-auto px-4 py-20 md:py-28 lg:py-36">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-widest rounded-full mb-4 bg-white/20 text-white backdrop-blur-sm">
                    Official Store
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4 drop-shadow-lg">
                    {{ heroConfig.heading || store.name }}
                </h1>
                <p class="text-lg md:text-xl text-white/85 mb-8 leading-relaxed max-w-xl drop-shadow">
                    {{ heroConfig.subheading || 'Discover amazing products at unbeatable prices.' }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#products"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-full font-bold text-lg transition-all hover:scale-105 hover:shadow-xl shadow-lg"
                        :style="{ backgroundColor: '#fff', color: primaryColor }"
                    >
                        {{ heroConfig.button_text || 'Shop Now' }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a v-if="store.whatsapp_number"
                        :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-full font-bold text-lg border-2 border-white text-white transition-all hover:bg-white/10"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Decorative wave -->
        <div class="absolute bottom-0 left-0 right-0 overflow-hidden leading-none">
            <svg viewBox="0 0 1440 40" class="w-full" fill="white" preserveAspectRatio="none">
                <path d="M0,20 C480,40 960,0 1440,20 L1440,40 L0,40 Z"/>
            </svg>
        </div>
    </section>

    <!-- ===== BOUTIQUE HERO ===== -->
    <section v-else-if="themeSlug === 'boutique'" class="relative overflow-hidden" :style="boutiqueHeroStyle">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center min-h-[420px] lg:min-h-[520px] gap-8">
                <!-- Text Side -->
                <div class="flex-1 text-center lg:text-left py-16 lg:py-24">
                    <div class="w-16 h-0.5 mx-auto lg:mx-0 mb-6" :style="{ backgroundColor: primaryColor }"></div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold leading-tight mb-6"
                        :style="{ color: heroConfig.image_url ? '#fff' : (colors.text || '#1F2937') }">
                        {{ heroConfig.heading || store.name }}
                    </h1>
                    <p class="text-lg mb-8 leading-relaxed max-w-md mx-auto lg:mx-0 opacity-80"
                        :style="{ color: heroConfig.image_url ? '#fff' : (colors.text || '#4B5563') }">
                        {{ heroConfig.subheading || 'Curated selections for the discerning buyer.' }}
                    </p>
                    <a href="#products"
                        class="inline-flex items-center gap-2 px-8 py-3.5 font-semibold tracking-wider uppercase text-sm transition-all hover:scale-105"
                        :style="{ backgroundColor: primaryColor, color: '#fff', borderRadius: '2px' }"
                    >
                        {{ heroConfig.button_text || 'Explore Collection' }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <!-- Image Side -->
                <div class="flex-1 hidden lg:block">
                    <div v-if="heroConfig.image_url" class="relative">
                        <div class="absolute -top-4 -right-4 w-full h-full border-2 rounded-sm" :style="{ borderColor: primaryColor + '60' }"></div>
                        <img :src="heroConfig.image_url" alt="" class="w-full h-80 object-cover rounded-sm shadow-2xl relative z-10" />
                    </div>
                    <div v-else class="h-80 rounded-sm flex items-center justify-center text-6xl font-serif font-bold opacity-10"
                        :style="{ background: primaryColor + '15', color: primaryColor }">
                        {{ store.name.charAt(0) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Background image if set -->
        <div v-if="heroConfig.image_url" class="absolute inset-0 -z-10">
            <img :src="heroConfig.image_url" alt="" class="w-full h-full object-cover lg:hidden" />
            <div class="absolute inset-0 lg:hidden" :style="{ backgroundColor: 'rgba(0,0,0,0.5)' }"></div>
        </div>
    </section>

    <!-- ===== MINIMAL HERO ===== -->
    <section v-else class="relative overflow-hidden" :style="minimalHeroStyle">
        <!-- Background image if set -->
        <div v-if="heroConfig.image_url" class="absolute inset-0">
            <img :src="heroConfig.image_url" alt="" class="w-full h-full object-cover" />
            <div class="absolute inset-0" :style="{ backgroundColor: `rgba(0,0,0,${(heroConfig.overlay_opacity || 40) / 100})` }"></div>
        </div>

        <div class="relative container mx-auto px-4 py-20 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight"
                :style="{ color: heroConfig.image_url ? '#fff' : (colors.text || '#1F2937') }">
                {{ heroConfig.heading || store.name }}
            </h1>
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto opacity-80"
                :style="{ color: heroConfig.image_url ? '#e5e7eb' : (colors.text || '#4B5563') }">
                {{ heroConfig.subheading || 'Find exactly what you\'re looking for.' }}
            </p>
            <a href="#products"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-lg font-semibold text-lg transition-all hover:opacity-90 hover:scale-105 shadow-lg"
                :style="{ backgroundColor: primaryColor, color: '#fff' }"
            >
                {{ heroConfig.button_text || 'Browse Products' }}
            </a>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    store: { type: Object, required: true },
});

const themeSlug = computed(() => props.store.theme?.slug || 'minimal');
const customization = computed(() => props.store.theme_customization || {});
const colors = computed(() => customization.value.colors || {});
const primaryColor = computed(() => colors.value.primary || '#3B82F6');
const secondaryColor = computed(() => colors.value.secondary || primaryColor.value);
const heroConfig = computed(() => customization.value.hero || {});

const marketplaceHeroStyle = computed(() => ({
    minHeight: '420px',
    backgroundColor: primaryColor.value,
}));

const boutiqueHeroStyle = computed(() => ({
    backgroundColor: heroConfig.value.image_url ? 'transparent' : (colors.value.background || '#fafafa'),
    position: 'relative',
}));

const minimalHeroStyle = computed(() => ({
    background: heroConfig.value.image_url
        ? 'transparent'
        : `linear-gradient(135deg, ${primaryColor.value}15 0%, ${secondaryColor.value}25 100%)`,
    minHeight: '320px',
    display: 'flex',
    alignItems: 'center',
}));
</script>
