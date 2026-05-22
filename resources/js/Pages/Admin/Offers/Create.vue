<template>
    <AppLayout title="Create Offer">
        <template #header>
            <div class="flex items-center justify-between">
                <Link :href="route('admin.offers.index')" class="text-sm text-blue-600 hover:text-blue-700">
                    ← Back to Offer Management
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">Create Offer</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6 bg-white rounded-xl shadow-sm p-8">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Offer Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Advertiser</label>
                            <select v-model="form.advertiser_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select advertiser</option>
                                <option v-for="adv in advertisers" :key="adv.id" :value="adv.id">{{ adv.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Offer Channel</label>
                        <select v-model="form.offer_channel" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option v-for="channel in offerChannels" :key="channel.value" :value="channel.value">{{ channel.label }}</option>
                        </select>
                    </div>

                    <div v-if="form.offer_channel !== 'platform'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Network Name</label>
                            <input v-model="form.network_name" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. CPAlead" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Network Offer ID</label>
                            <input v-model="form.network_offer_id" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="External network offer id" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="form.description" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" rows="3" required></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Commission Model</label>
                            <select v-model="form.commission_model" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="pps">Pay Per Sale (PPS)</option>
                                <option value="ppl">Pay Per Lead (PPL)</option>
                                <option value="revshare">Revenue Share</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Commission Rate</label>
                            <input v-model="form.commission_rate" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Daily Conversion Cap</label>
                            <input v-model="form.daily_conversion_cap" type="number" min="0" step="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Leave blank or 0 for unlimited" />
                            <p class="mt-1 text-xs text-gray-500">Maximum conversions per day. Leave blank or enter 0 for no daily cap.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Monthly Conversion Cap</label>
                            <input v-model="form.monthly_conversion_cap" type="number" min="0" step="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Leave blank or 0 for unlimited" />
                            <p class="mt-1 text-xs text-gray-500">Maximum conversions per month. Leave blank or enter 0 for no monthly cap.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Offer URL</label>
                            <input v-model="form.offer_url" type="url" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Product Image URL</label>
                            <input v-model="form.product_image" type="url" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div v-if="form.offer_channel === 'cpalead'">
                        <label class="block text-xs font-medium text-gray-700 mb-1">CPAlead Offer ID</label>
                        <input v-model="form.cpalead_offer_id" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Target Countries (comma separated ISO codes)</label>
                        <input v-model="form.target_countries" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="NG,GH,KE" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Target Devices</label>
                            <input v-model="form.target_devices" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="desktop,mobile,tablet" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Target OS</label>
                            <input v-model="form.target_os" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="windows,mac,linux,android,ios" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="unique_ip" v-model="form.require_unique_ip" type="checkbox" class="rounded border-gray-300" />
                        <label for="unique_ip" class="text-xs text-gray-700">Require Unique IP per Conversion</label>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Terms & Conditions</label>
                        <textarea v-model="form.terms_and_conditions" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" rows="3"></textarea>
                    </div>

                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                            Create Offer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    categories: Array,
    advertisers: Array,
    offerChannels: Array,
});

const form = useForm({
    name: '',
    category_id: '',
    advertiser_id: '',
    offer_channel: 'platform',
    network_name: '',
    network_offer_id: '',
    description: '',
    commission_model: 'pps',
    commission_rate: 0,
    offer_url: '',
    product_image: '',
    is_cpalead: false,
    cpalead_offer_id: '',
    target_countries: '',
    target_devices: '',
    target_os: '',
    daily_conversion_cap: '',
    monthly_conversion_cap: '',
    require_unique_ip: false,
    terms_and_conditions: '',
});

const submit = () => {
    form.post(route('admin.offers.store'), {
        preserveScroll: true,
    });
};
</script>
