<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
        <!-- Navigation -->
        <nav class="border-b border-neutral-800 bg-neutral-950/95 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link :href="route('front.home')" class="flex items-center gap-2.5">
                        <img src="/logo/full-white.png" alt="ClicksIntel" class="h-8">
                    </Link>

                    <!-- Right Section -->
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('front.home')"
                            class="text-sm font-medium text-neutral-400 hover:text-white transition-colors"
                        >
                            Home
                        </Link>
                        <Link
                            :href="route('front.features')"
                            class="text-sm font-medium text-neutral-400 hover:text-white transition-colors"
                        >
                            Features
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!-- Logo -->
                <Link :href="route('front.home')" class="flex justify-center mb-8">
                    <img src="/logo/full-white.png" alt="ClicksIntel" class="h-10">
                </Link>

                <!-- Title -->
                <h2 v-if="title" class="text-center text-3xl font-bold tracking-tight text-white">
                    {{ title }}
                </h2>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-neutral-900/50 backdrop-blur-sm border border-neutral-800 py-8 px-4 shadow-xl shadow-black/20 sm:rounded-2xl sm:px-10">
                    <slot />
                </div>

                <!-- Footer Links -->
                <div class="mt-6">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </div>
</template>
