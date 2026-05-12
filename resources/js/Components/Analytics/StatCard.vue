<template>
    <div class="overflow-hidden bg-neutral-900 shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-neutral-400">{{ title }}</p>
                    <p class="mt-2 text-3xl font-bold text-neutral-100">{{ value }}</p>
                    <p v-if="change !== undefined" :class="changeClass" class="mt-1 text-sm font-medium">
                        <span v-if="change > 0">↑</span>
                        <span v-else-if="change < 0">↓</span>
                        {{ Math.abs(change) }}% vs previous period
                    </p>
                </div>
                <div :class="iconBackgroundClass" class="flex h-12 w-12 items-center justify-center rounded-lg">
                    <svg v-if="icon === 'currency-naira'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="icon === 'shopping-cart'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <svg v-else-if="icon === 'chart-bar'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <svg v-else-if="icon === 'clock'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="icon === 'store'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    value: [String, Number],
    change: Number,
    icon: String,
    variant: {
        type: String,
        default: 'primary',
    },
});

const changeClass = computed(() => {
    if (props.change > 0) return 'text-emerald-400';
    if (props.change < 0) return 'text-red-400';
    return 'text-neutral-400';
});

const iconBackgroundClass = computed(() => {
    if (props.variant === 'warning') return 'bg-yellow-500';
    if (props.variant === 'danger') return 'bg-red-500';
    return 'bg-emerald-500';
});
</script>
