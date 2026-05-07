<template>
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 mb-1">{{ title }}</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ formattedValue }}</h3>
                <p v-if="subtitle" class="text-xs text-gray-500 mt-1">{{ subtitle }}</p>
            </div>
            <div 
                class="w-12 h-12 rounded-lg flex items-center justify-center" 
                :class="iconBgClass"
            >
                <component :is="iconComponent" class="w-6 h-6" :class="iconClass" />
            </div>
        </div>
        
        <div v-if="trend" class="mt-4 flex items-center text-sm">
            <span 
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                :class="trendClass"
            >
                <svg v-if="trend > 0" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <svg v-else class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ Math.abs(trend) }}%
            </span>
            <span class="ml-2 text-gray-600">vs last period</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
    CursorArrowRaysIcon, 
    ChartBarIcon, 
    BanknotesIcon,
    CheckCircleIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: String,
    value: [String, Number],
    subtitle: String,
    trend: Number,
    icon: {
        type: String,
        default: 'chart'
    },
    color: {
        type: String,
        default: 'blue'
    },
    format: {
        type: String,
        default: 'number' // number, currency, percentage
    }
});

const iconComponent = computed(() => {
    const icons = {
        'click': CursorArrowRaysIcon,
        'chart': ChartBarIcon,
        'money': BanknotesIcon,
        'check': CheckCircleIcon,
    };
    return icons[props.icon] || ChartBarIcon;
});

const colorClasses = {
    blue: {
        bg: 'bg-blue-50',
        icon: 'text-blue-600'
    },
    green: {
        bg: 'bg-green-50',
        icon: 'text-green-600'
    },
    purple: {
        bg: 'bg-purple-50',
        icon: 'text-purple-600'
    },
    orange: {
        bg: 'bg-orange-50',
        icon: 'text-orange-600'
    }
};

const iconBgClass = computed(() => colorClasses[props.color]?.bg || colorClasses.blue.bg);
const iconClass = computed(() => colorClasses[props.color]?.icon || colorClasses.blue.icon);

const trendClass = computed(() => {
    return props.trend > 0 
        ? 'bg-green-100 text-green-800' 
        : 'bg-red-100 text-red-800';
});

const formattedValue = computed(() => {
    if (props.format === 'currency') {
        return new Intl.NumberFormat('en-NG', { 
            style: 'currency', 
            currency: 'NGN',
            minimumFractionDigits: 0
        }).format(props.value);
    } else if (props.format === 'percentage') {
        return `${props.value}%`;
    }
    return new Intl.NumberFormat('en-US').format(props.value);
});
</script>
