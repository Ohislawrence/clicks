<template>
    <AppLayout title="Create Blog Post">
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">Create Blog Post</h2>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.blog.index')" class="text-indigo-600 hover:text-indigo-900">
                        ← Back to Posts
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Create New Post</h1>

                <form @submit.prevent="submit" class="bg-white shadow-md rounded-lg p-6">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug (optional)</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Leave blank to auto-generate from title"
                        />
                        <div v-if="form.errors.slug" class="text-red-600 text-sm mt-1">{{ form.errors.slug }}</div>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select
                            v-model="form.category_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Select a category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.category_id" class="text-red-600 text-sm mt-1">{{ form.errors.category_id }}</div>
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                        <textarea
                            v-model="form.excerpt"
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Brief summary (auto-generated if empty)"
                        ></textarea>
                        <div v-if="form.errors.excerpt" class="text-red-600 text-sm mt-1">{{ form.errors.excerpt }}</div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <div class="mb-16">
                            <QuillEditor
                                v-model:content="form.content"
                                theme="snow"
                                toolbar="full"
                                contentType="html"
                                :style="{ height: '400px' }"
                                class="bg-white"
                            />
                        </div>
                        <div v-if="form.errors.content" class="text-red-600 text-sm mt-1">{{ form.errors.content }}</div>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                        <input
                            type="file"
                            @change="handleImageChange"
                            accept="image/*"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x630px (max 2MB)</p>
                        <div v-if="imagePreview" class="mt-3">
                            <img :src="imagePreview" alt="Preview" class="max-w-sm rounded-lg border border-gray-300" />
                        </div>
                        <div v-if="form.errors.featured_image" class="text-red-600 text-sm mt-1">{{ form.errors.featured_image }}</div>
                    </div>

                    <!-- SEO Section -->
                    <div class="border-t border-gray-200 pt-6 mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input
                                v-model="form.meta_title"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Leave blank to use post title"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea
                                v-model="form.meta_description"
                                rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Leave blank to use excerpt"
                            ></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                            <input
                                v-model="form.meta_keywords"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="keyword1, keyword2, keyword3"
                            />
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing</h3>

                        <div class="flex items-center mb-4">
                            <input
                                v-model="form.is_published"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label class="ml-2 block text-sm text-gray-900">Publish immediately</label>
                        </div>

                        <div v-if="form.is_published" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date (optional)</label>
                            <input
                                v-model="form.published_at"
                                type="datetime-local"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <Link :href="route('admin.blog.index')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Post' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    category_id: '',
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: null,
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    is_published: false,
    published_at: '',
});

const imagePreview = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.featured_image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.blog.store'), {
        forceFormData: true,
    });
};
</script>
