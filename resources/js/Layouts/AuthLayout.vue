<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-neutral-950 text-neutral-100">
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
