<template>
    <AdminLayout>
        <Head title="Edit Blog Post" />

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.blog.index')" class="text-indigo-600 hover:text-indigo-900">
                        ← Back to Posts
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Post</h1>

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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        ></textarea>
                        <div v-if="form.errors.excerpt" class="text-red-600 text-sm mt-1">{{ form.errors.excerpt }}</div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea
                            v-model="form.content"
                            rows="15"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                            required
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Use HTML tags for formatting</p>
                        <div v-if="form.errors.content" class="text-red-600 text-sm mt-1">{{ form.errors.content }}</div>
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
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea
                                v-model="form.meta_description"
                                rows="2"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                            <input
                                v-model="form.meta_keywords"
                                type="text"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="border-t border-gray-200 pt-6 mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Views:</span>
                                <span class="ml-2 font-medium">{{ post.views }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-2 font-medium">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                            </div>
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
                            <label class="ml-2 block text-sm text-gray-900">Published</label>
                        </div>

                        <div v-if="form.is_published" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
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
                            {{ form.processing ? 'Updating...' : 'Update Post' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    post: Object,
    categories: Array,
});

const form = useForm({
    category_id: props.post.category_id,
    title: props.post.title,
    slug: props.post.slug,
    excerpt: props.post.excerpt || '',
    content: props.post.content,
    meta_title: props.post.meta_title || '',
    meta_description: props.post.meta_description || '',
    meta_keywords: props.post.meta_keywords || '',
    is_published: props.post.is_published,
    published_at: props.post.published_at ? new Date(props.post.published_at).toISOString().slice(0, 16) : '',
});

const submit = () => {
    form.put(route('admin.blog.update', props.post.id));
};
</script>
