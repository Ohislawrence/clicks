<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
</script>

<template>
    <Head title="Choose Account Type" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Create Your Account</h2>
            <p class="mt-2 text-sm text-gray-600">Choose the account type that best describes you</p>
        </div>

        <div class="space-y-4">
            <!-- Affiliate Option -->
            <Link
                :href="route('register.affiliate')"
                class="block p-6 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-500 transition-all duration-200 hover:shadow-lg group"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-indigo-600 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600">
                            I'm an Affiliate
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Promote offers and earn commissions through your traffic sources like social media, websites, or blogs.
                        </p>
                        <div class="mt-4 flex items-center text-sm text-indigo-600 group-hover:text-indigo-700 font-medium">
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
                class="block p-6 bg-white border-2 border-gray-200 rounded-lg hover:border-green-500 transition-all duration-200 hover:shadow-lg group"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600">
                            I'm an Advertiser
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Create and manage offers, work with affiliates, and grow your business with performance marketing.
                        </p>
                        <div class="mt-4 flex items-center text-sm text-green-600 group-hover:text-green-700 font-medium">
                            Get Started
                            <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <Link :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
