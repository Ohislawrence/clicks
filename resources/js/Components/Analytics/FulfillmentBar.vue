<template>
    <div class="flex items-center">
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-neutral-100">{{ label }}</span>
                <span class="text-sm text-neutral-400">{{ count }}</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-neutral-800">
                <div
                    :class="barColorClass"
                    :style="{ width: percentage + '%' }"
                    class="h-full rounded-full transition-all duration-300"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: String,
    count: Number,
    color: String,
});

const emit = defineEmits(['click']);

// Calculate percentage based on a max value (you can make this dynamic)
const maxValue = 100; // This could be passed as a prop or calculated from all bars
const percentage = computed(() => {
    if (props.count === 0) return 0;
    return Math.min((props.count / maxValue) * 100, 100);
});

const barColorClass = computed(() => {
    const colors = {
        yellow: 'bg-yellow-500',
        blue: 'bg-blue-500',
        purple: 'bg-purple-500',
        green: 'bg-emerald-500',
        red: 'bg-red-500',
    };
    return colors[props.color] || 'bg-emerald-500';
});
</script>
