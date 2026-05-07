<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
</script>

<template>
    <Head title="Choose Account Type" />

    <AuthLayout title="Create Your Account">
        <p class="text-center text-neutral-400 mb-8 text-sm">
            Choose the account type that best describes you
        </p>

        <div class="space-y-4">
            <!-- Affiliate Option -->
            <Link
                :href="route('register.affiliate')"
                class="block p-6 bg-neutral-800/30 border border-neutral-700 rounded-xl hover:border-emerald-500 hover:bg-neutral-800/50 transition-all duration-200 group"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-emerald-500 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-white group-hover:text-emerald-400 transition-colors">
                            I'm an Affiliate
                        </h3>
                        <p class="mt-2 text-sm text-neutral-400">
                            Promote offers and earn commissions through your traffic sources like social media, websites, or blogs.
                        </p>
                        <div class="mt-4 flex items-center text-sm text-emerald-500 group-hover:text-emerald-400 font-medium">
                            Get Started
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </Link>

            <!-- Advertiser Option -->
            <Link
                :href="route('register.advertiser')"
                class="block p-6 bg-neutral-800/30 border border-neutral-700 rounded-xl hover:border-blue-500 hover:bg-neutral-800/50 transition-all duration-200 group"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-blue-500 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-white group-hover:text-blue-400 transition-colors">
                            I'm an Advertiser
                        </h3>
                        <p class="mt-2 text-sm text-neutral-400">
                            Create and manage offers, work with affiliates, and grow your business with performance marketing.
                        </p>
                        <div class="mt-4 flex items-center text-sm text-blue-500 group-hover:text-blue-400 font-medium">
                            Get Started
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <template #footer>
            <p class="text-center text-sm text-neutral-400">
                Already have an account?
                <Link :href="route('login')" class="font-semibold text-emerald-500 hover:text-emerald-400 transition-colors">
                    Sign in
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
