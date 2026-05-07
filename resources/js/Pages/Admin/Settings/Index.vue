<template>
    <AppLayout title="Platform Settings">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Platform Settings</h2>
                <p class="mt-1 text-sm text-gray-600">Configure platform-wide rules and limits</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="saveSettings">
                    <!-- Payout Settings -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                </svg>
                                Payout Settings
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimum Payout Amount (₦)
                                </label>
                                <input
                                    v-model.number="form.minimum_payout"
                                    type="number"
                                    step="100"
                                    min="1000"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Minimum amount affiliates can request for payout</p>
                                <div v-if="form.errors.minimum_payout" class="text-red-600 text-sm mt-1">{{ form.errors.minimum_payout }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Platform Fee (%)
                                </label>
                                <input
                                    v-model.number="form.platform_fee_percentage"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Platform fee deducted from affiliate commissions (0 = no fee)</p>
                                <div v-if="form.errors.platform_fee_percentage" class="text-red-600 text-sm mt-1">{{ form.errors.platform_fee_percentage }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Settings -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
                                </svg>
                                Commission Settings
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Commission Cap (%)
                                </label>
                                <input
                                    v-model.number="form.commission_cap"
                                    type="number"
                                    step="1"
                                    min="0"
                                    max="100"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Maximum commission percentage allowed for offers</p>
                                <div v-if="form.errors.commission_cap" class="text-red-600 text-sm mt-1">{{ form.errors.commission_cap }}</div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Auto-Approve Conversions</label>
                                    <p class="text-sm text-gray-500">Automatically approve all conversions without advertiser review</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        v-model="form.auto_approve_conversions"
                                        type="checkbox"
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Cookie & Tracking Settings -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-600 to-red-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                Cookie & Tracking Settings
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Minimum Cookie Duration (days)
                                    </label>
                                    <input
                                        v-model.number="form.cookie_duration_min"
                                        type="number"
                                        min="1"
                                        max="365"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Minimum allowed cookie duration</p>
                                    <div v-if="form.errors.cookie_duration_min" class="text-red-600 text-sm mt-1">{{ form.errors.cookie_duration_min }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Maximum Cookie Duration (days)
                                    </label>
                                    <input
                                        v-model.number="form.cookie_duration_max"
                                        type="number"
                                        min="1"
                                        max="365"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Maximum allowed cookie duration</p>
                                    <div v-if="form.errors.cookie_duration_max" class="text-red-600 text-sm mt-1">{{ form.errors.cookie_duration_max }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fraud Detection Settings -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-pink-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Fraud Detection & Security
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Enable Fraud Detection</label>
                                    <p class="text-sm text-gray-500">Automatically detect and flag suspicious activities</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        v-model="form.fraud_detection_enabled"
                                        type="checkbox"
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Max Clicks Per IP (per offer/day)
                                </label>
                                <input
                                    v-model.number="form.max_clicks_per_ip"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Maximum clicks allowed from a single IP address per offer per day</p>
                                <div v-if="form.errors.max_clicks_per_ip" class="text-red-600 text-sm mt-1">{{ form.errors.max_clicks_per_ip }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Affiliate & Offer Settings -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                Affiliate & Offer Settings
                            </h3>
                        </div>
                        <div class="px-6 py-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Max Active Links Per Affiliate
                                </label>
                                <input
                                    v-model.number="form.max_active_links_per_affiliate"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p class="mt-1 text-sm text-gray-500">Maximum number of active affiliate links per affiliate</p>
                                <div v-if="form.errors.max_active_links_per_affiliate" class="text-red-600 text-sm mt-1">{{ form.errors.max_active_links_per_affiliate }}</div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Require Offer Approval</label>
                                    <p class="text-sm text-gray-500">New offers must be approved by admin before going live</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        v-model="form.require_offer_approval"
                                        type="checkbox"
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex items-center justify-end space-x-4">
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Reset to Defaults
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors disabled:opacity-50"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Settings</span>
                        </button>
                    </div>

                    <!-- Info Banner -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Important Notes:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Settings are applied platform-wide immediately after saving</li>
                                    <li>Changes to minimum payout will only affect new payout requests</li>
                                    <li>Cookie duration limits apply to new offers only</li>
                                    <li>Fraud detection settings are applied to all new clicks</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    minimum_payout: props.settings.minimum_payout,
    commission_cap: props.settings.commission_cap,
    cookie_duration_max: props.settings.cookie_duration_max,
    cookie_duration_min: props.settings.cookie_duration_min,
    auto_approve_conversions: props.settings.auto_approve_conversions,
    require_offer_approval: props.settings.require_offer_approval,
    max_active_links_per_affiliate: props.settings.max_active_links_per_affiliate,
    fraud_detection_enabled: props.settings.fraud_detection_enabled,
    max_clicks_per_ip: props.settings.max_clicks_per_ip,
    platform_fee_percentage: props.settings.platform_fee_percentage,
});

const saveSettings = () => {
    form.put(route('admin.settings.update'), {
        preserveScroll: true,
    });
};

const resetForm = () => {
    form.reset();
};
</script>
