<template>
    <div class="inline-flex items-center">
        <!-- Badge -->
        <span 
            :class="[
                'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                tierClasses
            ]"
        >
            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            {{ tierLabel }}
        </span>

        <!-- Progress Indicator (Optional) -->
        <div v-if="showProgress && progress !== null" class="ml-3 flex items-center">
            <div class="w-32 bg-gray-200 rounded-full h-2 overflow-hidden">
                <div 
                    :class="[
                        'h-2 rounded-full transition-all duration-500',
                        progressBarClasses
                    ]"
                    :style="{ width: `${Math.min(progress, 100)}%` }"
                ></div>
            </div>
            <span class="ml-2 text-xs text-gray-600 font-medium">
                {{ Math.round(progress) }}%
            </span>
        </div>

        <!-- Bonus Indicator (Optional) -->
        <span v-if="showBonus && bonus > 0" class="ml-2 text-xs text-green-600 font-semibold">
            +{{ bonus }}% commission
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    tier: {
        type: String,
        required: true,
        validator: (value) => ['bronze', 'silver', 'gold', 'platinum'].includes(value.toLowerCase())
    },
    showProgress: {
        type: Boolean,
        default: false
    },
    progress: {
        type: Number,
        default: null // Percentage 0-100
    },
    showBonus: {
        type: Boolean,
        default: false
    },
    bonus: {
        type: Number,
        default: 0
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
});

const tierLabel = computed(() => {
    return props.tier.charAt(0).toUpperCase() + props.tier.slice(1);
});

const tierClasses = computed(() => {
    const baseClasses = {
        bronze: 'bg-gray-100 text-gray-700 border border-gray-300',
        silver: 'bg-slate-100 text-slate-700 border border-slate-300',
        gold: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
        platinum: 'bg-purple-100 text-purple-800 border border-purple-300'
    };

    const sizeClasses = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-3 py-1 text-sm',
        lg: 'px-4 py-1.5 text-base'
    };

    return `${baseClasses[props.tier.toLowerCase()]} ${sizeClasses[props.size]}`;
});

const progressBarClasses = computed(() => {
    const colors = {
        bronze: 'bg-gray-500',
        silver: 'bg-slate-500',
        gold: 'bg-yellow-500',
        platinum: 'bg-purple-500'
    };

    return colors[props.tier.toLowerCase()];
});
</script>

<style scoped>
/* Shine animation for platinum tier */
@keyframes shine {
    0% {
        background-position: -100%;
    }
    100% {
        background-position: 200%;
    }
}

.platinum-shine {
    background: linear-gradient(
        90deg,
        rgba(147, 51, 234, 0.1) 0%,
        rgba(147, 51, 234, 0.3) 50%,
        rgba(147, 51, 234, 0.1) 100%
    );
    background-size: 200% 100%;
    animation: shine 3s infinite;
}
</style>
