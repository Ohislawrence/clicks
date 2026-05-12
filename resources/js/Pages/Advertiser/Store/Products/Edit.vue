<template>
    <AppLayout title="Edit Product">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
                    <p class="mt-1 text-sm text-gray-600">Update product information</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                <input type="text" v-model="form.name"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter product name">
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Slug</label>
                                <input type="text" v-model="form.slug"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="auto-generated">
                                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from product name</p>
                                <div v-if="form.errors.slug" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.slug }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                <QuillEditor
                                    v-model:content="form.description"
                                    theme="snow"
                                    toolbar="full"
                                    content-type="html"
                                    :style="{ minHeight: '200px' }"
                                    placeholder="Enter product description..."
                                />
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Pricing</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (₦) *</label>
                                <input type="number" v-model.number="form.price" step="0.01" min="0"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0.00">
                                <div v-if="form.errors.price" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Compare at Price (₦)</label>
                                <input type="number" v-model.number="form.compare_at_price" step="0.01" min="0"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0.00">
                                <p class="mt-1 text-xs text-gray-500">Show original price for discounts</p>
                                <div v-if="form.errors.compare_at_price" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.compare_at_price }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Inventory</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <input type="text" v-model="form.sku"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Product SKU">
                                <div v-if="form.errors.sku" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.sku }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input type="number" v-model.number="form.stock_quantity" min="0"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Leave blank for unlimited">
                                <p class="mt-1 text-xs text-gray-500">Leave blank for unlimited stock</p>
                                <div v-if="form.errors.stock_quantity" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.stock_quantity }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Product Images</h3>

                        <div>
                            <input type="file" ref="imageInput" @change="handleImageUpload" multiple accept="image/*" class="hidden">
                            <button type="button" @click="$refs.imageInput.click()"
                                class="px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Choose Images
                            </button>
                            <p class="mt-2 text-xs text-gray-500">Upload up to 5 images. Max 2MB each.</p>

                            <div v-if="imagePreviews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                                    <img :src="preview" class="w-full h-32 object-cover rounded-lg">
                                    <button type="button" @click="removeImage(index)"
                                        class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div v-if="form.errors.images" class="mt-2 text-sm text-red-500">
                                {{ form.errors.images }}
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Product Type</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <label
                                :class="['flex items-center gap-3 p-4 rounded-lg border cursor-pointer transition-colors', form.product_type === 'tangible' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300']">
                                <input type="radio" v-model="form.product_type" value="tangible" class="hidden">
                                <div :class="['w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0', form.product_type === 'tangible' ? 'border-blue-500' : 'border-gray-400']">
                                    <div v-if="form.product_type === 'tangible'" class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Tangible</div>
                                    <div class="text-xs text-gray-500">Physical product that requires delivery</div>
                                </div>
                            </label>

                            <label
                                :class="['flex items-center gap-3 p-4 rounded-lg border cursor-pointer transition-colors', form.product_type === 'digital' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300']">
                                <input type="radio" v-model="form.product_type" value="digital" class="hidden">
                                <div :class="['w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0', form.product_type === 'digital' ? 'border-blue-500' : 'border-gray-400']">
                                    <div v-if="form.product_type === 'digital'" class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Digital</div>
                                    <div class="text-xs text-gray-500">Downloadable or online product</div>
                                </div>
                            </label>
                        </div>
                        <div v-if="form.errors.product_type" class="mt-2 text-sm text-red-500">{{ form.errors.product_type }}</div>
                    </div>

                    <!-- Delivery Fees -->
                    <div v-if="form.product_type === 'tangible'" class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Shipping Zones & Fees</h3>
                                <p class="text-xs text-gray-500 mt-1">Set delivery fees per state. Customers will see the fee for their state during checkout.</p>
                            </div>
                            <button type="button" @click="addDeliveryFee"
                                class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                + Add State
                            </button>
                        </div>

                        <div v-if="form.delivery_fees.length === 0" class="text-sm text-gray-500 text-center py-6 border border-dashed border-gray-300 rounded-lg">
                            No shipping zones added. Click "Add State" to set delivery fees per state.
                        </div>

                        <div class="space-y-3">
                            <div v-for="(fee, index) in form.delivery_fees" :key="index"
                                class="flex items-center gap-3">
                                <select v-model.number="fee.state_id"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="" disabled>Select state...</option>
                                    <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                                </select>
                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                    <span class="px-3 py-2 text-gray-500 text-sm border-r border-gray-300 bg-gray-50">₦</span>
                                    <input type="number" v-model.number="fee.fee" min="0" step="0.01"
                                        class="w-28 px-3 py-2 text-gray-900 text-sm focus:outline-none"
                                        placeholder="0.00">
                                </div>
                                <button type="button" @click="removeDeliveryFee(index)"
                                    class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div v-if="form.errors.delivery_fees" class="mt-2 text-sm text-red-500">{{ form.errors.delivery_fees }}</div>
                    </div>

                    <!-- Digital Delivery -->
                    <div v-if="form.product_type === 'digital'" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Digital Delivery</h3>
                        <p class="text-sm text-gray-500 mb-4">After payment, customers automatically receive the download link via email.</p>

                        <div class="space-y-4">
                            <!-- Existing file -->
                            <div v-if="product.download_file && !form.remove_download_file" class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="flex-1 text-sm text-gray-700 truncate">{{ product.download_file.split('/').pop() }}</span>
                                <button type="button" @click="form.remove_download_file = true" class="text-sm text-red-500 hover:underline">Remove</button>
                            </div>

                            <div v-if="!product.download_file || form.remove_download_file">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                <input type="file" ref="downloadInput" @change="handleDownloadFile" class="hidden">
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="$refs.downloadInput.click()"
                                        class="px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                        Choose File
                                    </button>
                                    <span class="text-sm text-gray-500">{{ form.download_file ? form.download_file.name : 'No file chosen' }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Max 100 MB. Or use an external URL below.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Or External Download URL</label>
                                <input type="url" v-model="form.download_url"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="https://example.com/file.pdf">
                                <p v-if="form.errors.download_url" class="mt-1 text-sm text-red-500">{{ form.errors.download_url }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Expiry (hours)</label>
                                    <input type="number" v-model.number="form.download_expiry_hours" min="1"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="No expiry">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Downloads</label>
                                    <input type="number" v-model.number="form.max_downloads" min="1"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Unlimited">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categories & Tags -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-5">
                        <h3 class="text-xl font-semibold text-gray-900">Categories & Tags</h3>

                        <div v-if="categories.length > 0">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                            <div class="flex flex-wrap gap-2">
                                <label v-for="cat in categories" :key="cat.id"
                                    :class="['flex items-center gap-2 px-3 py-1.5 rounded-full border cursor-pointer text-sm transition-colors', form.category_ids.includes(cat.id) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-400']">
                                    <input type="checkbox" :value="cat.id" v-model="form.category_ids" class="hidden">
                                    {{ cat.name }}
                                </label>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400">
                            No categories yet. <Link :href="route('advertiser.store.categories.create', store.id)" class="text-blue-600 hover:underline">Create categories</Link> first.
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span v-for="(tag, i) in form.tags" :key="i"
                                    class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                    {{ tag }}
                                    <button type="button" @click="removeTag(i)" class="text-gray-400 hover:text-red-500">×</button>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" v-model="tagInput" @keydown.enter.prevent="addTag" @keydown.comma.prevent="addTag"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Type a tag and press Enter">
                                <button type="button" @click="addTag"
                                    class="px-3 py-2 bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-200">Add</button>
                            </div>
                        </div>
                    </div>

                    <!-- Variants -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Product Variants</h3>
                                <p class="text-xs text-gray-500 mt-1">e.g. Size: S / M / L or Color: Red / Blue. Leave empty if the product has no variants.</p>
                            </div>
                            <button type="button" @click="addVariant"
                                class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                + Add Variant
                            </button>
                        </div>

                        <div v-if="form.variants.length === 0" class="text-sm text-gray-400 text-center py-6 border border-dashed border-gray-200 rounded-lg">
                            No variants added.
                        </div>

                        <div class="space-y-4">
                            <div v-for="(variant, vi) in form.variants" :key="variant.id || vi"
                                class="border border-gray-200 rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Variant {{ vi + 1 }}</span>
                                    <button type="button" @click="removeVariant(vi)" class="text-red-400 hover:text-red-500 text-xs">Remove</button>
                                </div>

                                <!-- Option key-value pairs -->
                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">Options (e.g. Size = Large)</label>
                                    <div class="space-y-2">
                                        <div v-for="(val, key) in variant.options" :key="key" class="flex gap-2 items-center">
                                            <input type="text" :value="key" @change="renameOptionKey(vi, key, $event.target.value)"
                                                class="w-28 border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                                placeholder="e.g. Size">
                                            <span class="text-gray-400">=</span>
                                            <input type="text" v-model="variant.options[key]"
                                                class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                                placeholder="e.g. Large">
                                            <button type="button" @click="removeOptionKey(vi, key)" class="text-red-400 hover:text-red-500 text-xs">×</button>
                                        </div>
                                    </div>
                                    <button type="button" @click="addOptionKey(vi)"
                                        class="mt-2 text-xs text-blue-600 hover:underline">+ Add option</button>
                                </div>

                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">Variant Name</label>
                                    <input type="text" v-model="variant.name"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="e.g. Large / Red">
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Price (₦)</label>
                                        <input type="number" v-model.number="variant.price" step="0.01" min="0"
                                            class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                            placeholder="Same as product">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Compare Price (₦)</label>
                                        <input type="number" v-model.number="variant.compare_at_price" step="0.01" min="0"
                                            class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                            placeholder="Optional">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Stock</label>
                                        <input type="number" v-model.number="variant.stock_quantity" min="0"
                                            class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                            placeholder="Unlimited">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">SKU</label>
                                        <input type="text" v-model="variant.sku"
                                            class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-900 focus:outline-none"
                                            placeholder="Optional">
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" v-model="variant.is_active" class="rounded">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Settings</h3>

                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.is_active" class="mr-3">
                                <span class="text-gray-700">Active (visible in store)</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.is_featured" class="mr-3">
                                <span class="text-gray-700">Featured (highlighted in store)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <Link :href="route('advertiser.store.products.index', store.id)"
                            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Updating...' : 'Update Product' }}
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
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    product: Object,
    store: Object,
    states: Array,
    categories: Array,
});

const form = useForm({
    name: props.product.name,
    slug: props.product.slug,
    description: props.product.description,
    price: props.product.price,
    compare_at_price: props.product.compare_at_price,
    stock_quantity: props.product.stock_quantity,
    sku: props.product.sku || '',
    images: [],
    existing_images: props.product.images || [],
    is_active: props.product.is_active,
    is_featured: props.product.is_featured,
    product_type: props.product.product_type || 'tangible',
    delivery_fees: props.product.delivery_fees || [],
    category_ids: (props.product.categories || []).map(c => c.id),
    tags: props.product.tags || [],
    variants: (props.product.variants || []).map(v => ({
        id: v.id,
        name: v.name,
        options: v.options || {},
        price: v.price,
        compare_at_price: v.compare_at_price,
        stock_quantity: v.stock_quantity,
        sku: v.sku || '',
        is_active: v.is_active,
    })),
    download_file: null,
    download_url: props.product.download_url || '',
    remove_download_file: false,
    download_expiry_hours: props.product.download_expiry_hours || null,
    max_downloads: props.product.max_downloads || null,
});

const tagInput = ref('');
const imageInput = ref(null);
const downloadInput = ref(null);
const imagePreviews = ref([...props.product.images || []]);
const imageFiles = ref([]);

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files);

    if (imagePreviews.value.length + files.length > 5) {
        alert('You can only upload up to 5 images');
        return;
    }

    files.forEach(file => {
        if (file.size > 2048 * 1024) {
            alert(`${file.name} is too large. Maximum size is 2MB`);
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push(e.target.result);
            imageFiles.value.push(file);
        };
        reader.readAsDataURL(file);
    });

    form.images = imageFiles.value;
};

const removeImage = (index) => {
    const removedImage = imagePreviews.value[index];
    imagePreviews.value.splice(index, 1);

    if (form.existing_images.includes(removedImage)) {
        form.existing_images = form.existing_images.filter(img => img !== removedImage);
    } else {
        imageFiles.value.splice(index, 1);
        form.images = imageFiles.value;
    }
};

const handleDownloadFile = (event) => {
    form.download_file = event.target.files[0] || null;
};

const addDeliveryFee = () => {
    form.delivery_fees.push({ state_id: '', fee: 0 });
};

const removeDeliveryFee = (index) => {
    form.delivery_fees.splice(index, 1);
};

// Tags
const addTag = () => {
    const tag = tagInput.value.trim().replace(/,$/, '');
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag);
    }
    tagInput.value = '';
};
const removeTag = (i) => form.tags.splice(i, 1);

// Variants
const addVariant = () => {
    form.variants.push({ id: null, name: '', options: {}, price: null, compare_at_price: null, stock_quantity: null, sku: '', is_active: true });
};
const removeVariant = (i) => form.variants.splice(i, 1);
const addOptionKey = (vi) => {
    form.variants[vi].options = { ...form.variants[vi].options, '': '' };
};
const removeOptionKey = (vi, key) => {
    const opts = { ...form.variants[vi].options };
    delete opts[key];
    form.variants[vi].options = opts;
};
const renameOptionKey = (vi, oldKey, newKey) => {
    if (!newKey || newKey === oldKey) return;
    const opts = {};
    for (const [k, v] of Object.entries(form.variants[vi].options)) {
        opts[k === oldKey ? newKey : k] = v;
    }
    form.variants[vi].options = opts;
};

const submit = () => {
    const data = { ...form.data() };

    if (!data.images || data.images.length === 0) {
        delete data.images;
    }

    data._method = 'PUT';

    form.transform(() => data).post(route('advertiser.store.products.update', [props.store.id, props.product.id]), {
        preserveScroll: true,
        onSuccess: () => {},
    });
};
</script>

<style>
/* Quill Editor Dark Theme */
.ql-snow .ql-stroke {
    stroke: #a3a3a3;
}

.ql-snow .ql-fill {
    fill: #a3a3a3;
}

.ql-snow .ql-picker {
    color: #a3a3a3;
}

.ql-snow .ql-picker-options {
    background-color: #171717;
    border: 1px solid #262626;
}

.ql-toolbar.ql-snow {
    background-color: #171717;
    border: 1px solid #262626;
    border-radius: 0.5rem 0.5rem 0 0;
}

.ql-container.ql-snow {
    background-color: #0a0a0a;
    border: 1px solid #262626;
    border-radius: 0 0 0.5rem 0.5rem;
    color: #f5f5f5;
}

.ql-editor {
    min-height: 200px;
}

.ql-editor.ql-blank::before {
    color: #737373;
}
</style>
