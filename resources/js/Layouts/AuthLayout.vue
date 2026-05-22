<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <!-- Main Content -->
        <div class="flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <!-- Logo -->
                <Link :href="route('front.home')" class="flex justify-center mb-8">
                    <img src="/logo/black.png" alt="ClicksIntel" class="h-10">
                </Link>

                <!-- Title -->
                <h2 v-if="title" class="text-center text-3xl font-bold tracking-tight text-slate-900">
                    {{ title }}
                </h2>

                <div v-if="$page.props.errors?.error" class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mt-8">
                    {{ $page.props.errors.error }}
                </div>

                <div v-if="$page.props.flash?.success" class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 sm:mt-8">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash?.error" class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mt-8">
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white border border-slate-200 py-8 px-4 shadow-sm sm:rounded-2xl sm:px-10">
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
