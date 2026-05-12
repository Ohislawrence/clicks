<template>
    <AppLayout title="Edit Store Theme">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Store Theme</h2>
                    <p class="mt-1 text-sm text-gray-600">Update theme template configuration</p>
                </div>
                <Link :href="route('admin.store-themes.index')" class="text-gray-600 hover:text-gray-900">
                    ← Back to Themes
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Theme Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="e.g., Modern Minimalist"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug (optional)
                                </label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="auto-generated if empty"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Brief description of the theme style and features"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Store Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.store_type"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">Select type...</option>
                                    <option value="single">Single Product Only</option>
                                    <option value="multi">Multi Product Only</option>
                                    <option value="both">Both Types</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Thumbnail Image
                                </label>
                                <input
                                    @change="handleThumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Layout Configuration -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Layout</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Header Style</label>
                                <select v-model="form.config.layout.header_style" class="w-full rounded-lg border-gray-300">
                                    <option value="centered">Centered</option>
                                    <option value="left-aligned">Left Aligned</option>
                                    <option value="minimal">Minimal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Grid</label>
                                <select v-model="form.config.layout.product_grid" class="w-full rounded-lg border-gray-300">
                                    <option value="1-column">1 Column</option>
                                    <option value="2-column">2 Columns</option>
                                    <option value="3-column">3 Columns</option>
                                    <option value="4-column">4 Columns</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sidebar Position</label>
                                <select v-model="form.config.layout.sidebar_position" class="w-full rounded-lg border-gray-300">
                                    <option value="none">None</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Color Configuration -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Colors</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary</label>
                                <input v-model="form.config.colors.primary" type="color" class="w-full h-10 rounded-lg border-gray-300" />
                                <input v-model="form.config.colors.primary" type="text" class="w-full mt-1 text-xs rounded border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary</label>
                                <input v-model="form.config.colors.secondary" type="color" class="w-full h-10 rounded-lg border-gray-300" />
                                <input v-model="form.config.colors.secondary" type="text" class="w-full mt-1 text-xs rounded border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Accent</label>
                                <input v-model="form.config.colors.accent" type="color" class="w-full h-10 rounded-lg border-gray-300" />
                                <input v-model="form.config.colors.accent" type="text" class="w-full mt-1 text-xs rounded border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Text</label>
                                <input v-model="form.config.colors.text" type="color" class="w-full h-10 rounded-lg border-gray-300" />
                                <input v-model="form.config.colors.text" type="text" class="w-full mt-1 text-xs rounded border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Background</label>
                                <input v-model="form.config.colors.background" type="color" class="w-full h-10 rounded-lg border-gray-300" />
                                <input v-model="form.config.colors.background" type="text" class="w-full mt-1 text-xs rounded border-gray-300" />
                            </div>
                        </div>
                    </div>

                    <!-- Typography -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Typography</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Heading Font</label>
                                <select v-model="form.config.typography.heading_font" class="w-full rounded-lg border-gray-300">
                                    <option value="Inter">Inter</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Playfair Display">Playfair Display</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Space Grotesk">Space Grotesk</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Body Font</label>
                                <select v-model="form.config.typography.body_font" class="w-full rounded-lg border-gray-300">
                                    <option value="Inter">Inter</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Lato">Lato</option>
                                    <option value="Roboto">Roboto</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Heading Size</label>
                                <select v-model="form.config.typography.heading_size" class="w-full rounded-lg border-gray-300">
                                    <option value="large">Large</option>
                                    <option value="extra-large">Extra Large</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Components & Features -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Components & Features</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input v-model="form.config.components.show_breadcrumbs" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Show Breadcrumbs</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.config.components.show_social_share" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Show Social Share</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.config.components.show_related_products" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Show Related Products</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.config.components.show_reviews" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Show Reviews</span>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input v-model="form.config.features.sticky_header" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Sticky Header</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.config.features.quick_view" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Quick View</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.config.features.product_zoom" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm text-gray-700">Product Zoom</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center">
                                    <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-emerald-600" />
                                    <span class="ml-2 text-sm font-medium text-gray-700">Active Theme</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input v-model.number="form.sort_order" type="number" min="0" class="w-full rounded-lg border-gray-300" />
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <Link :href="route('admin.store-themes.index')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition disabled:opacity-50">
                            {{ form.processing ? 'Updating...' : 'Update Theme' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    theme: Object,
});

const form = useForm({
    name: props.theme.name,
    slug: props.theme.slug,
    description: props.theme.description,
    thumbnail: null,
    store_type: props.theme.store_type,
    is_active: props.theme.is_active,
    sort_order: props.theme.sort_order,
    config: {
        layout: props.theme.config?.layout || {
            header_style: 'centered',
            product_grid: '3-column',
            sidebar_position: 'none',
        },
        colors: props.theme.config?.colors || {
            primary: '#000000',
            secondary: '#ffffff',
            accent: '#f0f0f0',
            text: '#333333',
            background: '#ffffff',
        },
        typography: props.theme.config?.typography || {
            heading_font: 'Inter',
            body_font: 'Inter',
            heading_size: 'large',
        },
        components: props.theme.config?.components || {
            show_breadcrumbs: true,
            show_social_share: true,
            show_related_products: true,
            show_reviews: false,
        },
        features: props.theme.config?.features || {
            sticky_header: true,
            quick_view: true,
            product_zoom: true,
        },
    },
});

const handleThumbnail = (e) => {
    form.thumbnail = e.target.files[0];
};

const submit = () => {
    form.post(route('admin.store-themes.update', props.theme.id), {
        _method: 'put',
    });
};
</script>
