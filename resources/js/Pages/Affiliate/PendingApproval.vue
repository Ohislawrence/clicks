<template>
    <AppLayout title="Account Pending Approval">
        <div class="min-h-screen bg-gray-50 flex items-center justify-center py-16 px-4">
            <div class="max-w-lg w-full">
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Step 1: Email not verified yet -->
                <div v-if="!emailVerified" class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-8 text-center text-white">
                        <div class="text-6xl mb-4">📧</div>
                        <h1 class="text-2xl font-bold mb-2">Verify Your Email First</h1>
                        <p class="text-yellow-100">Step 1 of 2</p>
                    </div>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-6">
                            We sent a verification link to your email address. Please check your inbox and click the link to verify your email before your account can be reviewed.
                        </p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                            <p class="text-sm text-yellow-800 font-medium mb-1">📌 Not seeing the email?</p>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Check your spam / junk folder</li>
                                <li>• Allow a few minutes for delivery</li>
                                <li>• Make sure the email address is correct</li>
                            </ul>
                        </div>
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition mb-3"
                        >
                            Resend Verification Email
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="text-sm text-gray-500 hover:text-gray-700">
                            Sign out
                        </Link>
                    </div>
                </div>

                <!-- Step 2: Email verified, waiting for admin approval -->
                <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-center text-white">
                        <div class="text-6xl mb-4">⏳</div>
                        <h1 class="text-2xl font-bold mb-2">Account Under Review</h1>
                        <p class="text-indigo-200">Step 2 of 2 — Email Verified ✅</p>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center justify-center mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white text-sm font-bold">✓</div>
                                <div class="h-1 w-16 bg-green-400 rounded"></div>
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500 text-white text-sm font-bold animate-pulse">2</div>
                                <div class="h-1 w-16 bg-gray-200 rounded"></div>
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 text-sm font-bold">3</div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-500 mb-6 flex justify-between px-4">
                            <span class="text-green-600 font-medium">Email verified</span>
                            <span class="text-purple-600 font-medium">Admin review</span>
                            <span>Access granted</span>
                        </div>

                        <p class="text-gray-600 text-center mb-6">
                            Your email is verified. Our team is reviewing your application and will notify you by email once a decision has been made.
                        </p>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                                <span class="text-blue-500 mr-3 mt-0.5">📋</span>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">What happens next?</p>
                                    <p class="text-xs text-blue-700">Our admin team reviews your profile and traffic sources. Approval typically takes 1-2 business days.</p>
                                </div>
                            </div>
                            <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                                <span class="text-purple-500 mr-3 mt-0.5">🔔</span>
                                <div>
                                    <p class="text-sm font-medium text-purple-900">You'll be notified</p>
                                    <p class="text-xs text-purple-700">Once approved, you'll receive an email and can immediately start browsing offers and promoting.</p>
                                </div>
                            </div>
                            <div class="flex items-start p-3 bg-green-50 rounded-lg">
                                <span class="text-green-500 mr-3 mt-0.5">📚</span>
                                <div>
                                    <p class="text-sm font-medium text-green-900">Use this time wisely</p>
                                    <p class="text-xs text-green-700">Read the relevant documentation to prepare while you wait for account approval.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <Link
                                :href="userRole === 'advertiser' ? route('advertiser.documentation.index') : route('affiliate.documentation.index')"
                                class="flex-1 text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition text-sm"
                            >
                                📖 Read Documentation
                            </Link>
                            <Link :href="route('logout')" method="post" as="button"
                                class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-3 px-4 rounded-lg transition text-sm"
                            >
                                Sign Out
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    emailVerified: Boolean,
    userRole: String,
});
</script>
