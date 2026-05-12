<template>
    <AppLayout title="Theme Customizer">
        <template #header>
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Theme Customizer</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ store.name }} &mdash; Choose a theme and personalize your store</p>
                </div>
                <div class="flex gap-3">
                    <a :href="storeUrl" target="_blank"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition inline-flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview Store
                    </a>
                    <Link :href="route('advertiser.store.dashboard', store.id)"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                        Back to Dashboard
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Success / Error Messages -->
                <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" enctype="multipart/form-data">

                    <!-- ===================== THEME SELECTION ===================== -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Choose Your Theme</h3>
                        <p class="text-sm text-gray-500 mb-5">Each theme has a unique layout, header, hero, and footer style.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <label v-for="theme in themes" :key="theme.id"
                                class="relative cursor-pointer rounded-xl border-2 overflow-hidden transition-all hover:shadow-md"
                                :class="form.store_theme_id == theme.id ? 'border-blue-500 shadow-md' : 'border-gray-200'">
                                <input type="radio" v-model="form.store_theme_id" :value="theme.id" class="sr-only" />

                                <!-- Theme Preview Card -->
                                <div class="h-36 flex items-center justify-center relative overflow-hidden"
                                    :style="themePreviewBg(theme)">
                                    <!-- Theme illustration -->
                                    <div class="text-center px-3">
                                        <div class="font-bold text-white text-lg drop-shadow">{{ theme.name }}</div>
                                        <div class="text-white/80 text-xs mt-1">{{ theme.description }}</div>
                                    </div>
                                    <!-- Selected check -->
                                    <div v-if="form.store_theme_id == theme.id"
                                        class="absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="p-3 bg-white">
                                    <div class="font-medium text-gray-900 text-sm">{{ theme.name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ theme.slug }}</div>
                                </div>
                            </label>
                        </div>
                        <div v-if="form.errors.store_theme_id" class="mt-2 text-sm text-red-500">{{ form.errors.store_theme_id }}</div>
                    </div>

                    <!-- ===================== COLORS ===================== -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Colors</h3>
                        <p class="text-sm text-gray-500 mb-5">These colors are applied across your store's buttons, links, and accents.</p>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                            <div v-for="(label, key) in colorFields" :key="key">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ label }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" v-model="form.colors[key]"
                                        class="h-10 w-14 rounded-lg border border-gray-300 cursor-pointer p-0.5" />
                                    <input type="text" v-model="form.colors[key]"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="#3B82F6" maxlength="7" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== BRANDING ===================== -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Branding & Logo</h3>
                        <p class="text-sm text-gray-500 mb-5">
                            Your logo is managed in
                            <Link :href="route('advertiser.store.edit', store.id)" class="text-blue-600 hover:underline">Store Settings</Link>.
                            Here you can upload a hero banner image.
                        </p>

                        <div class="space-y-5">
                            <!-- Header Tagline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Header Tagline <span class="text-gray-400 text-xs">(optional short slogan under your store name)</span></label>
                                <input type="text" v-model="form.header.tagline"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Best prices guaranteed · Fast delivery" maxlength="80" />
                            </div>

                            <!-- Announcement Bar (marketplace only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Announcement Bar
                                    <span class="text-gray-400 text-xs">(shown at top for Marketplace theme)</span>
                                </label>
                                <input type="text" v-model="form.header.announcement_text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="🎉 Free delivery on orders over ₦50,000!" maxlength="120" />
                            </div>

                            <!-- Hero Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hero Banner Image</label>
                                <!-- Current image preview -->
                                <div v-if="currentHeroImage && !removeHeroImage" class="mb-3 relative inline-block">
                                    <img :src="currentHeroImage" alt="Hero" class="w-full max-w-md h-32 object-cover rounded-lg border border-gray-200" />
                                    <button type="button" @click="removeHeroImage = true; form.hero.image_url = ''"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">Current hero image</p>
                                </div>
                                <input type="file" @change="handleHeroFile" accept="image/*"
                                    class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <p class="text-xs text-gray-500 mt-1">Or enter image URL below:</p>
                                <input type="url" v-model="form.hero.image_url"
                                    class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="https://example.com/hero.jpg" />
                            </div>

                            <!-- Overlay Opacity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Hero Overlay Darkness: {{ form.hero.overlay_opacity }}%
                                </label>
                                <input type="range" v-model.number="form.hero.overlay_opacity" min="0" max="90" step="5"
                                    class="w-full accent-blue-600" />
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Transparent</span><span>Dark</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== HERO CONTENT ===================== -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Hero Section Content</h3>
                        <p class="text-sm text-gray-500 mb-5">The big text and button that appears in your store's hero banner.</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Heading</label>
                                <input type="text" v-model="form.hero.heading"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="store.name" maxlength="80" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subheading</label>
                                <input type="text" v-model="form.hero.subheading"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Discover amazing products at great prices." maxlength="150" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                                <input type="text" v-model="form.hero.button_text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Shop Now" maxlength="40" />
                            </div>
                        </div>
                    </div>

                    <!-- ===================== FOOTER ===================== -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Footer</h3>
                        <p class="text-sm text-gray-500 mb-5">Footer tagline and social media links.</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Footer Tagline</label>
                                <input type="text" v-model="form.footer.tagline"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Your trusted online shopping destination." maxlength="150" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                                        Facebook URL
                                    </label>
                                    <input type="url" v-model="form.footer.facebook"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://facebook.com/yourpage" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                                        </svg>
                                        Instagram URL
                                    </label>
                                    <input type="url" v-model="form.footer.instagram"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://instagram.com/yourpage" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        Twitter / X URL
                                    </label>
                                    <input type="url" v-model="form.footer.twitter"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://x.com/yourhandle" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================== SAVE BUTTON ===================== -->
                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl px-6 py-4">
                        <p class="text-sm text-gray-500">Changes are applied immediately after saving.</p>
                        <div class="flex gap-3">
                            <a :href="storeUrl" target="_blank"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                Preview
                            </a>
                            <button type="submit" :disabled="form.processing"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold disabled:opacity-60 flex items-center gap-2">
                                <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: { type: Object, required: true },
    themes: { type: Array, required: true },
    storeUrl: { type: String, required: true },
});

// Existing customization
const existing = computed(() => props.store.theme_customization || {});

const form = useForm({
    store_theme_id: props.store.store_theme_id,
    hero_image: null, // File upload
    remove_hero_image: false,
    colors: {
        primary:    existing.value.colors?.primary    || '#3B82F6',
        secondary:  existing.value.colors?.secondary  || '#1D4ED8',
        background: existing.value.colors?.background || '#FFFFFF',
        text:       existing.value.colors?.text       || '#1F2937',
        header_bg:  existing.value.colors?.header_bg  || '#FFFFFF',
        footer_bg:  existing.value.colors?.footer_bg  || '#1C1C2E',
    },
    header: {
        tagline:           existing.value.header?.tagline           || '',
        announcement_text: existing.value.header?.announcement_text || '',
    },
    hero: {
        heading:         existing.value.hero?.heading         || '',
        subheading:      existing.value.hero?.subheading      || '',
        button_text:     existing.value.hero?.button_text     || '',
        image_url:       existing.value.hero?.image_url       || '',
        overlay_opacity: existing.value.hero?.overlay_opacity ?? 50,
    },
    footer: {
        tagline:   existing.value.footer?.tagline   || '',
        facebook:  existing.value.footer?.facebook  || '',
        instagram: existing.value.footer?.instagram || '',
        twitter:   existing.value.footer?.twitter   || '',
    },
});

const removeHeroImage = ref(false);
const currentHeroImage = computed(() => existing.value.hero?.image_url || null);

const colorFields = {
    primary:    'Primary Color',
    secondary:  'Secondary Color',
    background: 'Page Background',
    text:       'Text Color',
    header_bg:  'Header Background',
    footer_bg:  'Footer Background',
};

function handleHeroFile(e) {
    const file = e.target.files[0];
    if (file) {
        form.hero_image = file;
        form.hero.image_url = ''; // Clear URL if file is uploaded
    }
}

function themePreviewStyle(theme) {
    const gradients = {
        marketplace: 'from-orange-500 to-red-600',
        boutique: 'from-purple-700 to-pink-600',
        minimal: 'from-blue-500 to-cyan-500',
    };
    const gradient = gradients[theme.slug] || 'from-gray-500 to-gray-700';
    return { backgroundImage: `linear-gradient(135deg, var(--tw-gradient-stops))` };
}

// Get Tailwind-like gradient class based on slug
const themeGradients = {
    marketplace: ['#f97316', '#dc2626'],
    boutique: ['#7c3aed', '#db2777'],
    minimal: ['#3b82f6', '#06b6d4'],
};

function themePreviewBg(theme) {
    const colors = themeGradients[theme.slug] || ['#6b7280', '#4b5563'];
    return { background: `linear-gradient(135deg, ${colors[0]}, ${colors[1]})` };
}

function submit() {
    form.remove_hero_image = removeHeroImage.value;

    form.transform((data) => ({
        ...data,
        _method: 'POST',
    }));

    form.post(route('advertiser.store.theme.update', props.store.id), {
        forceFormData: true,
        onSuccess: () => {
            removeHeroImage.value = false;
        },
    });
}
</script>

<style scoped>
/* Replace themePreviewStyle to use JS-based background */
</style>
