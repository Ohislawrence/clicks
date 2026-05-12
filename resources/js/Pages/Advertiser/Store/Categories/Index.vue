<template>
    <AppLayout :title="store.name + ' - Categories'">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Product Categories</h2>
                    <p class="mt-1 text-sm text-gray-600">Organise your products into categories</p>
                </div>
                <Link :href="route('advertiser.store.categories.create', store.id)"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    + New Category
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success"
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="categories.length === 0"
                    class="bg-white border border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-gray-600 font-medium">No categories yet</p>
                    <p class="text-sm text-gray-400 mt-1">Create categories to organise your products</p>
                    <Link :href="route('advertiser.store.categories.create', store.id)"
                        class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        Create First Category
                    </Link>
                </div>

                <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Name</th>
                                <th class="text-left px-5 py-3 font-medium text-gray-600">Slug</th>
                                <th class="text-center px-5 py-3 font-medium text-gray-600">Products</th>
                                <th class="text-center px-5 py-3 font-medium text-gray-600">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ cat.name }}</td>
                                <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ cat.slug }}</td>
                                <td class="px-5 py-3 text-center text-gray-700">{{ cat.products_count }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                        class="px-2 py-0.5 rounded-full text-xs font-medium">
                                        {{ cat.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right space-x-2">
                                    <Link :href="route('advertiser.store.categories.edit', [store.id, cat.id])"
                                        class="text-blue-600 hover:text-blue-700 text-xs font-medium">Edit</Link>
                                    <button @click="deleteCategory(cat)"
                                        class="text-red-500 hover:text-red-600 text-xs font-medium">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <Link :href="route('advertiser.store.products.index', store.id)"
                        class="text-sm text-gray-500 hover:text-gray-700">← Back to Products</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    store: Object,
    categories: Array,
});

const deleteCategory = (cat) => {
    if (!confirm(`Delete category "${cat.name}"? Products in this category will not be deleted.`)) return;
    router.delete(route('advertiser.store.categories.destroy', [props.store.id, cat.id]));
};
</script>
