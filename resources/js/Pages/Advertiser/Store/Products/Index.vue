<template>
    <AppLayout title="Products">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage your store products</p>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ products.filter(p => !p.deleted_at).length }} of {{ productLimit || '∞' }} products used
                    </p>
                </div>
                <Link v-if="canAddMore" :href="route('advertiser.store.products.create', store.id)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Product
                    </Link>
                    <div v-else class="text-yellow-500 text-sm">
                        Product limit reached. Upgrade your plan to add more.
                    </div>
                </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Products Table -->
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div v-if="products.filter(p => !p.deleted_at).length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Product</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Price</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Stock</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Featured</th>
                                    <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products.filter(p => !p.deleted_at)" :key="product.id"
                                    class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div v-if="product.images && product.images.length > 0" class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                                <img :src="product.images[0]" :alt="product.name" class="w-full h-full object-cover">
                                            </div>
                                            <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-gray-900 font-medium">{{ product.name }}</p>
                                                <p class="text-sm text-gray-500">SKU: {{ product.sku || 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div>
                                            <p class="text-gray-900 font-medium">₦{{ product.price.toLocaleString() }}</p>
                                            <p v-if="product.compare_at_price" class="text-sm text-gray-400 line-through">
                                                ₦{{ product.compare_at_price.toLocaleString() }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span v-if="product.stock_quantity === null" class="text-gray-400">Unlimited</span>
                                        <span v-else :class="[
                                            'font-medium',
                                            product.stock_quantity > 10 ? 'text-green-600' :
                                            product.stock_quantity > 0 ? 'text-yellow-600' :
                                            'text-red-600'
                                        ]">
                                            {{ product.stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <button @click="toggleStatus(product)" :class="[
                                            'px-2 py-1 text-xs rounded-full transition-colors',
                                            product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                                        ]">
                                            {{ product.is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="py-3 px-4">
                                        <button @click="toggleFeatured(product)" :class="[
                                            'transition-colors',
                                            product.is_featured ? 'text-yellow-500' : 'text-gray-300 hover:text-gray-500'
                                        ]">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('advertiser.store.products.edit', [store.id, product.id])"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </Link>
                                            <button @click="deleteProduct(product)"
                                                class="p-2 text-red-500 hover:bg-red-500/10 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-16 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-lg mb-2">No products yet</p>
                        <p class="text-sm mb-4">Add your first product to start selling</p>
                        <Link v-if="canAddMore" :href="route('advertiser.store.products.create', store.id)"
                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add Product
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    products: Array,
    store: Object,
    productLimit: Number,
    canAddMore: Boolean,
});

const toggleStatus = (product) => {
    router.patch(route('advertiser.store.products.toggle', [props.store.id, product.id]), {}, {
        preserveScroll: true,
    });
};

const toggleFeatured = (product) => {
    router.patch(route('advertiser.store.products.featured', [props.store.id, product.id]), {}, {
        preserveScroll: true,
    });
};

const deleteProduct = (product) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('advertiser.store.products.destroy', [props.store.id, product.id]), {
            preserveScroll: true,
        });
    }
};
</script>
