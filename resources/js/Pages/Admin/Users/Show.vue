<template>
    <AppLayout title="User Details">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">User Details</h2>
                    <p class="mt-1 text-sm text-gray-600">View and manage user information</p>
                </div>
                <Link :href="route('admin.users.index')" class="text-sm text-blue-600 hover:text-blue-700">
                    ← Back to Users
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- User Profile Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center space-x-4">
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ user.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ user.email }}</p>
                                        <div v-if="user.affiliate_code && hasRole('affiliate')" class="mt-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-mono font-bold bg-blue-100 text-blue-800">
                                                {{ user.affiliate_code }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-purple-100 text-purple-800': role.name === 'admin',
                                                    'bg-green-100 text-green-800': role.name === 'affiliate',
                                                    'bg-blue-100 text-blue-800': role.name === 'advertiser'
                                                }"
                                            >
                                                {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-medium"
                                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    >
                                        {{ user.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="user.affiliate_code && hasRole('affiliate')" class="p-3 bg-blue-50 rounded-lg border-2 border-blue-200">
                                    <p class="text-xs text-blue-600 font-semibold mb-1">Affiliate Code</p>
                                    <p class="text-lg font-bold text-blue-900 font-mono">{{ user.affiliate_code }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">Phone</p>
                                    <p class="text-sm font-medium text-gray-900">{{ user.phone || 'Not provided' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">Joined</p>
                                    <p class="text-sm font-medium text-gray-900">{{ new Date(user.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Referral Cap Management (Affiliates Only) -->
                        <div v-if="hasRole('affiliate') && referralCapData" class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Referral Commission Cap Settings</h3>

                            <!-- Current Status -->
                            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Current Status</p>
                                        <p
                                            class="text-xl font-bold"
                                            :class="{
                                                'text-green-600': !referralCapData.has_reached_cap && referralCapData.progress.overall_percentage < 80,
                                                'text-yellow-600': !referralCapData.has_reached_cap && referralCapData.progress.overall_percentage >= 80,
                                                'text-red-600': referralCapData.has_reached_cap
                                            }"
                                        >
                                            {{ referralCapData.has_reached_cap ? 'Cap Reached' : referralCapData.is_active ? 'Active' : 'Inactive' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Referral Earnings</p>
                                        <p class="text-xl font-bold text-gray-900">{{ formatCurrency(referralCapData.current_earnings) }}</p>
                                    </div>
                                </div>

                                <div v-if="referralCapData.cap_type !== 'unlimited'" class="grid grid-cols-2 gap-4">
                                    <div v-if="referralCapData.cap_type === 'amount' || referralCapData.cap_type === 'both'" class="p-3 bg-white rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Amount Progress</p>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                            <div
                                                class="h-2 rounded-full"
                                                :class="{
                                                    'bg-green-500': referralCapData.progress.amount_percentage < 80,
                                                    'bg-yellow-500': referralCapData.progress.amount_percentage >= 80 && referralCapData.progress.amount_percentage < 100,
                                                    'bg-red-500': referralCapData.progress.amount_percentage >= 100
                                                }"
                                                :style="{ width: Math.min(referralCapData.progress.amount_percentage, 100) + '%' }"
                                            ></div>
                                        </div>
                                        <p class="text-xs text-gray-600">{{ Math.round(referralCapData.progress.amount_percentage) }}% of {{ formatCurrency(referralCapData.cap_amount) }}</p>
                                    </div>

                                    <div v-if="referralCapData.cap_type === 'time' || referralCapData.cap_type === 'both'" class="p-3 bg-white rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Time Progress</p>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                            <div
                                                class="h-2 rounded-full"
                                                :class="{
                                                    'bg-green-500': referralCapData.progress.time_percentage < 80,
                                                    'bg-yellow-500': referralCapData.progress.time_percentage >= 80 && referralCapData.progress.time_percentage < 100,
                                                    'bg-red-500': referralCapData.progress.time_percentage >= 100
                                                }"
                                                :style="{ width: Math.min(referralCapData.progress.time_percentage, 100) + '%' }"
                                            ></div>
                                        </div>
                                        <p class="text-xs text-gray-600">{{ Math.round(referralCapData.progress.time_percentage) }}% of {{ referralCapData.cap_months }} months</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <form @submit.prevent="updateReferralCap" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cap Type</label>
                                    <select
                                        v-model="capForm.referral_cap_type"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="unlimited">Unlimited (No Cap)</option>
                                        <option value="amount">Amount Cap</option>
                                        <option value="time">Time Cap</option>
                                        <option value="both">Both Amount & Time Cap</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Choose how to limit referral commissions</p>
                                </div>

                                <div v-if="capForm.referral_cap_type === 'amount' || capForm.referral_cap_type === 'both'" class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Referral Amount (₦)</label>
                                        <input
                                            v-model.number="capForm.referral_cap_amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Enter maximum amount"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Total amount affiliate can earn from referrals</p>
                                    </div>
                                </div>

                                <div v-if="capForm.referral_cap_type === 'time' || capForm.referral_cap_type === 'both'" class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Months)</label>
                                        <input
                                            v-model.number="capForm.referral_cap_months"
                                            type="number"
                                            min="1"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Enter number of months"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">How long affiliate can earn from referrals</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                                    <button
                                        type="button"
                                        @click="resetCapForm"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="capForm.processing"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {{ capForm.processing ? 'Saving...' : 'Update Cap Settings' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Stats Sidebar -->
                    <div class="space-y-6">
                        <!-- Stats Cards -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                            <div class="space-y-4">
                                <div class="p-3 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Balance</p>
                                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.balance) }}</p>
                                </div>
                                <div class="p-3 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Pending</p>
                                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.pending_balance) }}</p>
                                </div>
                                <div class="p-3 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Lifetime Earnings</p>
                                    <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.total_earnings) }}</p>
                                </div>
                                <div class="p-3 bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Conversions</p>
                                    <p class="text-lg font-bold text-gray-900">{{ stats.total_conversions }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
    stats: Object,
    referralCapData: Object
});

const hasRole = (roleName) => {
    return props.user.roles.some(role => role.name === roleName);
};

const capForm = useForm({
    referral_cap_type: props.referralCapData?.cap_type || 'unlimited',
    referral_cap_amount: props.referralCapData?.cap_amount || null,
    referral_cap_months: props.referralCapData?.cap_months || null,
});

const updateReferralCap = () => {
    capForm.put(route('admin.users.update-referral-cap', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Form will be reset with new values from server
        }
    });
};

const resetCapForm = () => {
    capForm.referral_cap_type = props.referralCapData?.cap_type || 'unlimited';
    capForm.referral_cap_amount = props.referralCapData?.cap_amount || null;
    capForm.referral_cap_months = props.referralCapData?.cap_months || null;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>
