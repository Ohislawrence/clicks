<template>
    <AdminLayout>
        <Head title="Blog Categories" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Blog Categories</h1>
                    <div class="flex space-x-3">
                        <Link :href="route('admin.blog.index')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Back to Posts
                        </Link>
                        <button @click="showCreateModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                            Create Category
                        </button>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Categories Table -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categories.data" :key="category.id">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                    <div v-if="category.description" class="text-sm text-gray-500">{{ category.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ category.slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ category.posts_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="category.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="editCategory(category)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Edit
                                    </button>
                                    <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Create/Edit Modal -->
                <div v-if="showCreateModal || showEditModal" class="fixed z-10 inset-0 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>

                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="submitCategory">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                                        {{ showCreateModal ? 'Create Category' : 'Edit Category' }}
                                    </h3>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                                        <input
                                            v-model="categoryForm.name"
                                            type="text"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug (optional)</label>
                                        <input
                                            v-model="categoryForm.slug"
                                            type="text"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Auto-generated from name"
                                        />
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea
                                            v-model="categoryForm.description"
                                            rows="3"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>

                                    <div class="flex items-center">
                                        <input
                                            v-model="categoryForm.is_active"
                                            type="checkbox"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label class="ml-2 block text-sm text-gray-900">Active</label>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        {{ showCreateModal ? 'Create' : 'Update' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModals"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    categories: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingCategoryId = ref(null);

const categoryForm = reactive({
    name: '',
    slug: '',
    description: '',
    is_active: true,
});

const editCategory = (category) => {
    categoryForm.name = category.name;
    categoryForm.slug = category.slug;
    categoryForm.description = category.description || '';
    categoryForm.is_active = category.is_active;
    editingCategoryId.value = category.id;
    showEditModal.value = true;
};

const closeModals = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    categoryForm.name = '';
    categoryForm.slug = '';
    categoryForm.description = '';
    categoryForm.is_active = true;
    editingCategoryId.value = null;
};

const submitCategory = () => {
    if (showCreateModal.value) {
        router.post(route('admin.blog.categories.store'), categoryForm, {
            onSuccess: () => closeModals(),
        });
    } else if (showEditModal.value) {
        router.put(route('admin.blog.categories.update', editingCategoryId.value), categoryForm, {
            onSuccess: () => closeModals(),
        });
    }
};

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category? This will fail if the category has posts.')) {
        router.delete(route('admin.blog.categories.destroy', id));
    }
};
</script>
