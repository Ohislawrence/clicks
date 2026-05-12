<template>
    <Head>
        <title>{{ storeName }} - Store Unavailable</title>
    </Head>

    <div class="min-h-screen bg-neutral-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-8 text-center">
            <!-- Unavailable Icon -->
            <div class="mx-auto w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    />
                </svg>
            </div>

            <!-- Message -->
            <h1 class="text-2xl font-bold text-neutral-900 mb-3">Store Unavailable</h1>
            <p class="text-neutral-600 mb-2">{{ storeName }}</p>
            <p class="text-neutral-500 text-sm">{{ message }}</p>

            <!-- Back to Home -->
            <div class="mt-8">
                <a
                    href="/"
                    class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                >
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    storeName: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        default: 'This store is currently unavailable.',
    },
});
</script>
