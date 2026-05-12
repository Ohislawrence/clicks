<template>
    <AppLayout title="Store Themes">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Store Themes</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage theme templates for advertiser stores</p>
                </div>
                <Link :href="route('admin.store-themes.create')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    Create New Theme
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash?.error" class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Themes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="theme in themes"
                        :key="theme.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition"
                    >
                        <!-- Theme Preview -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br"
                            :style="{ background: `linear-gradient(135deg, ${theme.config?.colors?.primary || '#667EEA'} 0%, ${theme.config?.colors?.secondary || '#764BA2'} 100%)` }">
                            <img
                                v-if="theme.thumbnail"
                                :src="`/storage/${theme.thumbnail}`"
                                alt="Theme thumbnail"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                                <div class="text-center text-white p-4">
                                    <h3 class="text-2xl font-bold mb-1">{{ theme.name }}</h3>
                                    <p class="text-sm opacity-90">{{ theme.store_type }}</p>
                                </div>
                            </div>
                            <button
                                @click="toggleActive(theme)"
                                class="absolute top-3 right-3 px-2 py-1 text-xs font-medium rounded-full"
                                :class="theme.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                            >
                                {{ theme.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>

                        <!-- Theme Details -->
                        <div class="p-6">
                            <div class="space-y-3 mb-4">
                                <p v-if="theme.description" class="text-sm text-gray-600">
                                    {{ theme.description }}
                                </p>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                    <span class="capitalize">{{ theme.store_type }} store</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ theme.stores_count }} active stores
                                </div>
                            </div>

                            <!-- Color Palette -->
                            <div v-if="theme.config?.colors" class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Color Palette</p>
                                <div class="flex space-x-2">
                                    <div
                                        v-for="(color, key) in theme.config.colors"
                                        :key="key"
                                        class="w-8 h-8 rounded-full border-2 border-white shadow-sm"
                                        :style="{ backgroundColor: color }"
                                        :title="key"
                                    ></div>
                                </div>
                            </div>

                            <!-- Typography -->
                            <div v-if="theme.config?.typography" class="mb-4 text-xs text-gray-600">
                                <span class="font-semibold">Fonts:</span>
                                {{ theme.config.typography.heading_font }} / {{ theme.config.typography.body_font }}
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('admin.store-themes.edit', theme.id)"
                                    class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteTheme(theme)"
                                    :disabled="theme.stores_count > 0"
                                    class="flex-1 text-center bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    :title="theme.stores_count > 0 ? 'Cannot delete theme in use' : 'Delete theme'"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="themes.length === 0" class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No store themes</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new theme template.</p>
                    <div class="mt-6">
                        <Link :href="route('admin.store-themes.create')" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition">
                            Create New Theme
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    themes: Array,
});

const toggleActive = (theme) => {
    if (confirm(`Are you sure you want to ${theme.is_active ? 'deactivate' : 'activate'} this theme?`)) {
        router.patch(route('admin.store-themes.toggle', theme.id), {}, {
            preserveScroll: true,
        });
    }
};

const deleteTheme = (theme) => {
    if (theme.stores_count > 0) {
        alert('Cannot delete a theme that is currently in use by stores.');
        return;
    }

    if (confirm(`Are you sure you want to delete "${theme.name}"? This action cannot be undone.`)) {
        router.delete(route('admin.store-themes.destroy', theme.id));
    }
};
</script>
