<template>
    <AppLayout title="Store Setup">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Create Your Online Store</h2>
                    <p class="mt-1 text-sm text-gray-600">Set up your store in just a few steps</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

                <!-- Setup Form -->
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Step Indicator -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                                <div class="flex items-center">
                                    <div :class="[
                                        'flex items-center justify-center w-10 h-10 rounded-full border-2',
                                        currentStep >= index + 1 ? 'bg-blue-600 border-blue-600 text-white' : 'bg-gray-100 border-gray-300 text-gray-400'
                                    ]">
                                        {{ index + 1 }}
                                    </div>
                                    <span :class="['ml-3 text-sm font-medium', currentStep >= index + 1 ? 'text-gray-900' : 'text-gray-400']">
                                        {{ step }}
                                    </span>
                                </div>
                                <div v-if="index < steps.length - 1" class="w-24 h-0.5 mx-4 bg-gray-200"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Business Information -->
                    <div v-show="currentStep === 1" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Business Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                                <input type="text" v-model="form.name"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter your business name">
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Store URL Slug</label>
                                <div class="flex items-center">
                                    <span class="text-gray-500 mr-2">/store/</span>
                                    <input type="text" v-model="form.slug"
                                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="auto-generated">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from business name</p>
                                <div v-if="form.errors.slug" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.slug }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                                <textarea v-model="form.description" rows="4"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Tell customers about your business"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                    <input type="email" v-model="form.email"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="hello@yourbusiness.com">
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" v-model="form.phone"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="+234 800 000 0000">
                                    <div v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Number</label>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.083.533 4.044 1.472 5.754L0 24l6.414-1.433A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.652-.49-5.184-1.349l-.371-.22-3.851.86.876-3.751-.242-.387A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                                    </span>
                                    <input type="tel" v-model="form.whatsapp_number"
                                        class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="+234 800 000 0000">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Customers can chat with you directly. A floating WhatsApp button will appear on your store.</p>
                                <div v-if="form.errors.whatsapp_number" class="mt-1 text-sm text-red-500">{{ form.errors.whatsapp_number }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Choose Theme -->
                    <div v-show="currentStep === 2" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Choose Your Theme</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="theme in filteredThemes" :key="theme.id"
                                @click="form.store_theme_id = theme.id"
                                :class="[
                                    'border-2 rounded-xl overflow-hidden cursor-pointer transition-all',
                                    form.store_theme_id === theme.id
                                        ? 'border-blue-500'
                                        : 'border-gray-200 hover:border-blue-300'
                                ]">
                                <div v-if="theme.thumbnail" class="aspect-video bg-gray-100">
                                    <img :src="theme.thumbnail" :alt="theme.name" class="w-full h-full object-cover">
                                </div>
                                <div v-else :style="{
                                    background: `linear-gradient(135deg, ${theme.config?.colors?.primary || '#2563eb'}, ${theme.config?.colors?.secondary || '#1d4ed8'})`
                                }" class="aspect-video"></div>

                                <div class="p-4 bg-gray-50">
                                    <h4 class="font-semibold text-gray-900">{{ theme.name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ theme.description }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.errors.store_theme_id" class="mt-2 text-sm text-red-500">
                            {{ form.errors.store_theme_id }}
                        </div>
                    </div>

                    <!-- Step 3: Payment Configuration -->
                    <div v-show="currentStep === 3" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Payment Configuration</h3>
                        <p class="text-sm text-gray-500 mb-4">Configure how you'll receive payments from customers. You can skip this and add it later from your store settings.</p>

                        <!-- Skip toggle -->
                        <div class="flex items-center justify-between mb-6 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Set up payment now</span>
                            <button type="button" @click="skipPayment = !skipPayment"
                                :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', skipPayment ? 'bg-gray-300' : 'bg-blue-600']">
                                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform', skipPayment ? 'translate-x-1' : 'translate-x-6']"></span>
                            </button>
                        </div>

                        <!-- Skipped notice -->
                        <div v-if="skipPayment" class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 flex items-start gap-3">
                            <svg class="w-5 h-5 text-yellow-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">Payment skipped — your store won't accept orders until you add payment keys.</p>
                                <p class="text-sm text-yellow-700 mt-1">You can add your Paystack or Flutterwave API keys anytime from <strong>Store Dashboard → Edit Store Settings</strong>.</p>
                            </div>
                        </div>

                        <!-- Payment form (shown when not skipped) -->
                        <div v-else class="space-y-5">
                            <!-- Provider -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Payment Provider *</label>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Public Key *</label>
                                <input type="text" v-model="form.payment_public_key"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    :placeholder="form.payment_provider === 'paystack' ? 'pk_test_...' : 'FLWPUBK-...'">
                                <div v-if="form.errors.payment_public_key" class="mt-1 text-sm text-red-500">{{ form.errors.payment_public_key }}</div>
                            </div>

                            <!-- Secret Key -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key *</label>
                                <input type="password" v-model="form.payment_secret_key"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                    :placeholder="form.payment_provider === 'paystack' ? 'sk_test_...' : 'FLWSECK-...'">
                                <p class="mt-1 text-xs text-gray-500">🔒 Your secret key is encrypted and secure</p>
                                <div v-if="form.errors.payment_secret_key" class="mt-1 text-sm text-red-500">{{ form.errors.payment_secret_key }}</div>
                            </div>

                            <!-- Where to find keys -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm">
                                <p class="text-blue-700 font-medium mb-1">Where to find your API keys:</p>
                                <ul class="text-blue-600 space-y-1 list-disc list-inside">
                                    <li v-if="form.payment_provider === 'paystack'">Paystack: Dashboard → Settings → API Keys &amp; Webhooks</li>
                                    <li v-if="form.payment_provider === 'flutterwave'">Flutterwave: Dashboard → Settings → API Keys</li>
                                </ul>
                            </div>

                            <!-- Webhook URLs -->
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                <p class="text-sm font-semibold text-gray-700 mb-3">📌 Webhook URLs — register these in your payment dashboard:</p>
                                <div class="space-y-2">
                                    <div v-if="form.payment_provider === 'paystack'" class="bg-gray-900 rounded-lg px-4 py-2">
                                        <span class="text-xs text-gray-400 block mb-0.5">Paystack webhook URL</span>
                                        <code class="text-green-400 text-xs break-all">{{ origin }}/api/webhooks/paystack/store-order</code>
                                    </div>
                                    <div v-if="form.payment_provider === 'flutterwave'" class="bg-gray-900 rounded-lg px-4 py-2">
                                        <span class="text-xs text-gray-400 block mb-0.5">Flutterwave webhook URL</span>
                                        <code class="text-green-400 text-xs break-all">{{ origin }}/api/webhooks/flutterwave/store-order</code>
                                    </div>
                                    <p v-if="!form.payment_provider" class="text-xs text-gray-500">Select a provider above to see your webhook URL.</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">See the <strong>Webhook Setup</strong> section in the <a :href="route('advertiser.documentation.index')" class="text-blue-600 underline" target="_blank">Documentation</a> for full setup instructions.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Choose Plan -->
                    <div v-show="currentStep === 4" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Choose Your Plan</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div v-for="plan in plans" :key="plan.id"
                                @click="form.store_plan_id = plan.id"
                                :class="[
                                    'border-2 rounded-xl p-6 cursor-pointer transition-all',
                                    form.store_plan_id === plan.id
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-gray-50 hover:border-blue-300'
                                ]">
                                <h4 class="text-lg font-semibold text-gray-900">{{ plan.name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ plan.store_type === 'single' ? 'Single Product' : `Up to ${plan.product_limit || '∞'} Products` }}
                                </p>
                                <div class="mt-4">
                                    <p class="text-2xl font-bold text-blue-600">₦{{ plan.monthly_price.toLocaleString() }}</p>
                                    <p class="text-sm text-gray-500">/month</p>
                                </div>
                                <div class="mt-2">
                                    <p class="text-lg font-semibold text-gray-900">₦{{ plan.yearly_price.toLocaleString() }}</p>
                                    <p class="text-xs text-gray-500">/year (save {{ plan.yearly_discount_percent }}%)</p>
                                </div>
                                <ul class="mt-4 space-y-2">
                                    <li v-for="feature in plan.features.slice(0, 3)" :key="feature" class="text-sm text-gray-700 flex items-start">
                                        <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ feature }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Billing Cycle -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" v-model="form.billing_cycle" value="monthly" class="mr-2">
                                    <span class="text-gray-900">Monthly</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" v-model="form.billing_cycle" value="yearly" class="mr-2">
                                    <span class="text-gray-900">Yearly (Save up to 17%)</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="form.errors.store_plan_id" class="mt-2 text-sm text-red-500">
                            {{ form.errors.store_plan_id }}
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between">
                        <button v-if="currentStep > 1" type="button" @click="currentStep--"
                            class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Previous
                        </button>
                        <div v-else></div>

                        <div class="flex gap-4">
                            <Link :href="route('advertiser.dashboard')"
                                class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </Link>

                            <button v-if="currentStep < 4" type="button" @click="nextStep"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Next
                            </button>

                            <button v-else type="submit" :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Creating...' : 'Create Store' }}
                            </button>
                        </div>
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

const props = defineProps({
    plans: Array,
    themes: Array,
});

const currentStep = ref(1);
const steps = ['Business Info', 'Choose Theme', 'Payment Setup', 'Choose Plan'];
const skipPayment = ref(false);
const origin = window.location.origin;

const form = useForm({
    store_plan_id: null,
    billing_cycle: 'monthly',
    name: '',
    slug: '',
    description: '',
    email: '',
    phone: '',
    whatsapp_number: '',
    store_theme_id: null,
    theme_customization: {},
    payment_method: 'api',
    payment_provider: 'paystack',
    payment_public_key: '',
    payment_secret_key: '',
});

const selectedPlan = computed(() => {
    return props.plans.find(p => p.id === form.store_plan_id);
});

const filteredThemes = computed(() => {
    if (!selectedPlan.value) return props.themes;

    return props.themes.filter(theme => {
        return theme.store_type === 'both' || theme.store_type === selectedPlan.value.store_type;
    });
});

const nextStep = () => {
    if (currentStep.value === 1 && !form.name) {
        form.errors.name = 'Business name is required';
        return;
    }
    if (currentStep.value === 2 && !form.store_theme_id) {
        form.errors.store_theme_id = 'Please select a theme';
        return;
    }
    if (currentStep.value === 4 && !form.store_plan_id) {
        form.errors.store_plan_id = 'Please select a plan';
        return;
    }
    currentStep.value++;
};

const submit = () => {
    if (skipPayment.value) {
        form.payment_provider = null;
        form.payment_public_key = '';
        form.payment_secret_key = '';
    }
    form.post(route('advertiser.store.create'), {
        onSuccess: () => {
            // Redirect handled by controller
        },
    });
};
</script>
