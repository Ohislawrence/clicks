<template>
    <AppLayout title="My Referrals">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Referral Dashboard</h2>
                    <p class="mt-1 text-sm text-gray-600">Share your affiliate referral link and track your referral earnings.</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Referral Link</h3>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">Referral Code</p>
                            <div class="mt-2 flex items-center gap-3">
                                <code class="truncate rounded bg-white px-3 py-2 text-sm font-semibold text-gray-900">{{ referralStats.referral_code }}</code>
                                <button
                                    @click="copyCode"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">Shareable Signup Link</p>
                            <div class="mt-2 flex items-center gap-3">
                                <input
                                    readonly
                                    :value="referralLink"
                                    class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800"
                                />
                                <button
                                    @click="copyLink"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Share this link with other affiliates who want to join the platform. Their identity is not revealed to you; you only see aggregate referral stats and earned commissions.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-6">
                        <p class="text-sm font-medium text-blue-700">Total Referrals</p>
                        <p class="mt-3 text-3xl font-bold text-blue-900">{{ referralStats.total_referrals }}</p>
                    </div>
                    <div class="rounded-xl border border-green-100 bg-green-50 p-6">
                        <p class="text-sm font-medium text-green-700">Active Referrals</p>
                        <p class="mt-3 text-3xl font-bold text-green-900">{{ referralStats.active_referrals }}</p>
                    </div>
                    <div class="rounded-xl border border-purple-100 bg-purple-50 p-6">
                        <p class="text-sm font-medium text-purple-700">Referral Earnings</p>
                        <p class="mt-3 text-3xl font-bold text-purple-900">₦{{ formatNumber(referralStats.total_earnings) }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">How it works</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex gap-3">
                            <span class="mt-1 text-blue-600">•</span>
                            <span>Your referral link lets other affiliates join the platform under your referral code.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 text-blue-600">•</span>
                            <span>You earn a percentage of their approved affiliate commissions, up to your referral cap.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 text-blue-600">•</span>
                            <span>You do not see personal details about referred affiliates, only aggregate totals.</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1 text-blue-600">•</span>
                            <span>If your referral cap is reached, you will still earn from your own affiliate performance, but no further referral bonuses.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    referralStats: Object,
    referralLink: String,
});

const copied = ref(false);

const formatNumber = (value) => {
    return Number(value || 0).toLocaleString();
};

const copyLink = async () => {
    await navigator.clipboard.writeText(props.referralLink);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 1500);
};

const copyCode = async () => {
    await navigator.clipboard.writeText(props.referralStats.referral_code);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 1500);
};
</script>
