<template>
    <AppLayout title="Edit Store">
        <!-- Success Toast -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div v-if="showSuccess" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">Store settings saved successfully!</span>
            </div>
        </Transition>

        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Store Settings</h2>
                    <p class="mt-1 text-sm text-gray-600">Update your store information and appearance</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Edit Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Business Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Business Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                                <input type="text" v-model="form.name"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Store URL Slug *</label>
                                <div class="flex items-center">
                                    <span class="text-gray-500 mr-2">/store/</span>
                                    <input type="text" v-model="form.slug"
                                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div v-if="form.errors.slug" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.slug }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                                <textarea v-model="form.description" rows="4"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">About Us Content</label>
                                <p class="text-xs text-gray-500 mb-2">This content appears on your store's About Us page.</p>
                                <div class="mb-16">
                                    <QuillEditor
                                        v-model:content="form.about_content"
                                        theme="snow"
                                        toolbar="full"
                                        contentType="html"
                                        :style="{ height: '300px' }"
                                        class="bg-white"
                                    />
                                </div>
                                <div v-if="form.errors.about_content" class="mt-1 text-sm text-red-500">{{ form.errors.about_content }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                <input type="email" v-model="form.contact_email"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="contact@yourbusiness.com">
                                <div v-if="form.errors.contact_email" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.contact_email }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                <input type="tel" v-model="form.contact_phone"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="+234 800 000 0000">
                                <div v-if="form.errors.contact_phone" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.contact_phone }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                                <input type="tel" v-model="form.whatsapp_number"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="+234 800 000 0000">
                                <div v-if="form.errors.whatsapp_number" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.whatsapp_number }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">SEO Settings</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" v-model="form.meta_title"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Leave blank to use business name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea v-model="form.meta_description" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Brief description of your store"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                                <input type="text" v-model="form.meta_keywords"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="keyword1, keyword2, keyword3">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Configuration -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">Payment Configuration</h3>
                        <p class="text-sm text-gray-500 mb-5">Update your Paystack or Flutterwave API keys. Leave fields blank to keep existing values.</p>

                        <!-- Current status indicator -->
                        <div v-if="!store.payment_provider" class="mb-4 flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Payment not configured — your store cannot accept orders until you add API keys.
                        </div>
                        <div v-else class="mb-4 flex items-center gap-2 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Currently using <strong>{{ store.payment_provider === 'paystack' ? 'Paystack' : 'Flutterwave' }}</strong>. Update keys below to change.
                        </div>

                        <div class="space-y-4">
                            <!-- Provider -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Payment Provider</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="form.payment_provider === 'paystack' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <input type="radio" v-model="form.payment_provider" value="paystack" class="mr-3">
                                        <span class="text-gray-900 font-medium">Paystack</span>
                                    </label>
                                    <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                        :class="form.payment_provider === 'flutterwave' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <input type="radio" v-model="form.payment_provider" value="flutterwave" class="mr-3">
                                        <span class="text-gray-900 font-medium">Flutterwave</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Public Key -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Public Key</label>
                                <input type="text" v-model="form.payment_public_key"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    :placeholder="form.payment_provider === 'paystack' ? 'pk_live_...' : 'FLWPUBK-...'">
                                <p class="mt-1 text-xs text-gray-500">Leave blank to keep existing key</p>
                                <div v-if="form.errors.payment_public_key" class="mt-1 text-sm text-red-500">{{ form.errors.payment_public_key }}</div>
                            </div>

                            <!-- Secret Key -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                                <input type="password" v-model="form.payment_secret_key"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    :placeholder="form.payment_provider === 'paystack' ? 'sk_live_...' : 'FLWSECK-...'">
                                <p class="mt-1 text-xs text-gray-500">🔒 Encrypted at rest. Leave blank to keep existing key.</p>
                                <div v-if="form.errors.payment_secret_key" class="mt-1 text-sm text-red-500">{{ form.errors.payment_secret_key }}</div>
                            </div>

                            <!-- Webhook Secret (Flutterwave only) -->
                            <div v-if="form.payment_provider === 'flutterwave'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Webhook Secret Hash <span class="text-gray-400 font-normal">(Flutterwave only)</span></label>
                                <input type="password" v-model="form.payment_webhook_secret"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    placeholder="Your custom secret hash from Flutterwave">
                                <p class="mt-1 text-xs text-gray-500">Must match the Secret Hash you set in Flutterwave Dashboard → Webhooks. Leave blank to keep existing.</p>
                            </div>

                            <!-- Webhook URLs -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                <p class="text-sm font-semibold text-gray-700 mb-3">📌 Webhook URLs — register in your payment dashboard:</p>
                                <div class="space-y-2">
                                    <div v-if="form.payment_provider === 'paystack'" class="bg-gray-900 rounded-lg px-4 py-2">
                                        <span class="text-xs text-gray-400 block mb-0.5">Paystack webhook URL</span>
                                        <code class="text-green-400 text-xs break-all select-all">{{ origin }}/api/webhooks/paystack/store-order</code>
                                    </div>
                                    <div v-if="form.payment_provider === 'flutterwave'" class="bg-gray-900 rounded-lg px-4 py-2">
                                        <span class="text-xs text-gray-400 block mb-0.5">Flutterwave webhook URL</span>
                                        <code class="text-green-400 text-xs break-all select-all">{{ origin }}/api/webhooks/flutterwave/store-order</code>
                                    </div>
                                    <p v-if="!form.payment_provider" class="text-xs text-gray-500">Select a provider above to see your webhook URL.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <Link :href="route('advertiser.store.dashboard', store.id)"
                            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const showSuccess = ref(false);

const props = defineProps({
    store: Object,
});

const origin = window.location.origin;

const form = useForm({
    name: props.store.name,
    slug: props.store.slug,
    description: props.store.description || '',
    about_content: props.store.about_content || '',
    theme_customization: props.store.theme_customization || {},
    email: props.store.email || '',
    phone: props.store.phone || '',
    whatsapp_number: props.store.whatsapp_number || '',
    meta_title: props.store.meta_title || '',
    meta_description: props.store.meta_description || '',
    meta_keywords: props.store.meta_keywords || '',
    payment_provider: props.store.payment_provider || 'paystack',
    payment_public_key: '',
    payment_secret_key: '',
    payment_webhook_secret: '',
});

const submit = () => {
    form.put(route('advertiser.store.update', props.store.id), {
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => { showSuccess.value = false; }, 3000);
        },
    });
};
</script>
