<template>
    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatNumber(stats.total_clicks) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Across all variants</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Conversions</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatNumber(stats.total_conversions) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total converted</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.group_cr }}%</p>
                        <p class="text-xs" :class="stats.group_cr >= 2 ? 'text-green-600' : 'text-gray-500'">
                            {{ stats.group_cr >= 2 ? '✓ Healthy' : 'Needs improvement' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.total_revenue) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total earned</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Comparison Chart -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Variant Performance Comparison</h3>
                    <p class="text-sm text-gray-600 mt-1">Compare conversion rates and revenue across all variants</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="chartMetric = 'cr'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                            chartMetric === 'cr' 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        Conversion Rate
                    </button>
                    <button
                        @click="chartMetric = 'revenue'"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                            chartMetric === 'revenue' 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        Revenue
                    </button>
                </div>
            </div>

            <apexchart
                v-if="chartOptions"
                type="bar"
                height="300"
                :options="chartOptions"
                :series="chartSeries"
            />
        </div>

        <!-- Variants Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Variant Details</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Variant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Clicks
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Conversions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CR
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Revenue
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                EPC
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Performance
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr 
                            v-for="(link, index) in rankedLinks" 
                            :key="link.id"
                            :class="[
                                'hover:bg-gray-50',
                                index === 0 ? 'bg-green-50' : ''
                            ]"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div 
                                        class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold"
                                        :style="{ backgroundColor: getVariantColor(index) }"
                                    >
                                        {{ String.fromCharCode(65 + index) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            Variant {{ String.fromCharCode(65 + index) }}
                                            <span v-if="index === 0" class="ml-2 text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                                👑 Winner
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">Link #{{ link.id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatNumber(link.rotation_clicks) }}</div>
                                <div class="text-xs text-gray-500">{{ getPercentage(link.rotation_clicks, stats.total_clicks) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatNumber(link.rotation_conversions) }}</div>
                                <div class="text-xs text-gray-500">{{ getPercentage(link.rotation_conversions, stats.total_conversions) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-semibold" :class="getCRColor(link.rotation_cr)">
                                        {{ link.rotation_cr }}%
                                    </span>
                                    <span v-if="link.rotation_cr > stats.group_cr" class="ml-2 text-xs text-green-600">
                                        ↑ {{ (link.rotation_cr - stats.group_cr).toFixed(2) }}%
                                    </span>
                                    <span v-else-if="link.rotation_cr < stats.group_cr" class="ml-2 text-xs text-red-600">
                                        ↓ {{ (stats.group_cr - link.rotation_cr).toFixed(2) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(link.rotation_revenue) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ formatCurrency(link.rotation_epc) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="link.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    {{ link.is_active ? 'Active' : 'Paused' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div 
                                            class="h-2 rounded-full transition-all"
                                            :class="getPerformanceColor(link.performance_score)"
                                            :style="{ width: link.performance_score + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ link.performance_score }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Test Duration & Recommendation -->
        <div v-if="group.enable_split_test" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">A/B Test Status</h4>
                    
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Test Duration</p>
                            <p class="text-xl font-bold text-gray-900">{{ group.split_test_duration_days }} days</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Time Remaining</p>
                            <p class="text-xl font-bold text-gray-900">{{ calculateDaysRemaining() }} days</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sample Size</p>
                            <p class="text-xl font-bold text-gray-900">{{ formatNumber(stats.total_clicks) }}</p>
                            <p class="text-xs text-gray-600">{{ stats.total_clicks >= 100 ? '✓ Sufficient' : '⚠ Need more data' }}</p>
                        </div>
                    </div>

                    <div v-if="stats.total_clicks >= 100" class="bg-white rounded-lg p-4 border-2 border-blue-200">
                        <h5 class="font-semibold text-gray-900 mb-2">📊 Recommendation</h5>
                        <p class="text-sm text-gray-700 mb-3">{{ getRecommendation() }}</p>
                        <div v-if="winningVariant" class="flex items-center">
                            <span class="text-sm text-gray-700">Winner:</span>
                            <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                Variant {{ String.fromCharCode(65 + rankedLinks.findIndex(l => l.id === winningVariant.id)) }}
                            </span>
                            <span class="ml-2 text-sm text-gray-600">
                                ({{ winningVariant.rotation_cr }}% CR, {{ formatCurrency(winningVariant.rotation_revenue) }} revenue)
                            </span>
                        </div>
                    </div>
                    <div v-else class="bg-yellow-50 rounded-lg p-4 border-2 border-yellow-200">
                        <p class="text-sm text-yellow-800">
                            ⚠️ Need at least 100 clicks for reliable A/B test results. Current: {{ stats.total_clicks }} clicks.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

const props = defineProps({
    group: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
});

const chartMetric = ref('cr');

// Rank links by performance
const rankedLinks = computed(() => {
    return [...props.stats.links].sort((a, b) => b.performance_score - a.performance_score);
});

// Identify winning variant
const winningVariant = computed(() => {
    if (rankedLinks.value.length === 0) return null;
    return rankedLinks.value[0];
});

// Chart configuration
const chartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: {
            show: false,
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded',
            distributed: true,
        },
    },
    dataLabels: {
        enabled: true,
        formatter: (val) => chartMetric.value === 'cr' ? val.toFixed(2) + '%' : formatCurrency(val),
    },
    xaxis: {
        categories: rankedLinks.value.map((_, index) => `Variant ${String.fromCharCode(65 + index)}`),
    },
    yaxis: {
        title: {
            text: chartMetric.value === 'cr' ? 'Conversion Rate (%)' : 'Revenue (₦)',
        },
        labels: {
            formatter: (val) => chartMetric.value === 'cr' ? val.toFixed(2) + '%' : formatCurrency(val),
        },
    },
    colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
    legend: {
        show: false,
    },
    tooltip: {
        y: {
            formatter: (val) => chartMetric.value === 'cr' ? val.toFixed(2) + '%' : formatCurrency(val),
        },
    },
}));

const chartSeries = computed(() => [
    {
        name: chartMetric.value === 'cr' ? 'Conversion Rate' : 'Revenue',
        data: rankedLinks.value.map(link => 
            chartMetric.value === 'cr' ? parseFloat(link.rotation_cr) : parseFloat(link.rotation_revenue)
        ),
    },
]);

const getVariantColor = (index) => {
    const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];
    return colors[index] || '#6b7280';
};

const getPercentage = (value, total) => {
    if (!total || total === 0) return '0.00';
    return ((value / total) * 100).toFixed(2);
};

const getCRColor = (cr) => {
    const rate = parseFloat(cr);
    if (rate >= 3) return 'text-green-600';
    if (rate >= 1.5) return 'text-blue-600';
    if (rate >= 0.5) return 'text-yellow-600';
    return 'text-red-600';
};

const getPerformanceColor = (score) => {
    if (score >= 80) return 'bg-green-500';
    if (score >= 60) return 'bg-blue-500';
    if (score >= 40) return 'bg-yellow-500';
    return 'bg-red-500';
};

const calculateDaysRemaining = () => {
    if (!props.group.split_test_ends_at) return 'N/A';
    const end = new Date(props.group.split_test_ends_at);
    const now = new Date();
    const diff = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
    return Math.max(0, diff);
};

const getRecommendation = () => {
    if (!winningVariant.value) return 'Insufficient data for recommendation';
    
    const winner = winningVariant.value;
    const avgCR = props.stats.group_cr;
    const improvement = ((winner.rotation_cr - avgCR) / avgCR * 100).toFixed(1);
    
    if (improvement > 20) {
        return `Strong winner detected! Variant ${String.fromCharCode(65 + rankedLinks.value.findIndex(l => l.id === winner.id))} outperforms the average by ${improvement}%. Consider making it the primary link.`;
    } else if (improvement > 10) {
        return `Moderate winner. Variant ${String.fromCharCode(65 + rankedLinks.value.findIndex(l => l.id === winner.id))} shows ${improvement}% better performance. Consider giving it more weight.`;
    } else {
        return 'Results are close. Consider running the test longer or trying different variations.';
    }
};

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number || 0);
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount || 0);
};
</script>
