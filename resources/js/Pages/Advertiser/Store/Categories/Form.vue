<template>
    <AppLayout :title="category ? 'Edit Category' : 'New Category'">
        <template #header>
            <h2 class="text-2xl font-bold text-gray-900">{{ category ? 'Edit Category' : 'New Category' }}</h2>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white border border-gray-200 rounded-lg p-6 space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" v-model="form.name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g. Clothing, Electronics">
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Optional description for this category"></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <label class="flex items-center gap-3">
                        <input type="checkbox" v-model="form.is_active" class="rounded">
                        <span class="text-sm text-gray-700">Active (visible in store)</span>
                    </label>

                    <div class="flex justify-between items-center pt-2">
                        <Link :href="route('advertiser.store.categories.index', store.id)"
                            class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : (category ? 'Update Category' : 'Create Category') }}
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
    store: Object,
    category: Object,
});

const form = useForm({
    name: props.category?.name ?? '',
    description: props.category?.description ?? '',
    is_active: props.category?.is_active ?? true,
});

const submit = () => {
    if (props.category) {
        form.put(route('advertiser.store.categories.update', [props.store.id, props.category.id]));
    } else {
        form.post(route('advertiser.store.categories.store', props.store.id));
    }
};
</script>
