<template>
    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Rules</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.total || 0) }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ formatNumber(stats.active || 0) }} active</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Blocks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.total_hits || 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">All-time</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Critical Rules</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.by_severity?.critical || 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">High priority</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Blocks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatNumber(todayBlocks) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Last 24 hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Blacklist by Type -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Blacklist Rules by Type</h3>
                <apexchart
                    v-if="typeChartSeries.length > 0"
                    type="donut"
                    height="300"
                    :options="typeChartOptions"
                    :series="typeChartSeries"
                />
                <div v-else class="flex items-center justify-center h-64 text-gray-400">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="mt-2 text-sm">No data available</p>
                    </div>
                </div>
            </div>

            <!-- Blacklist by Severity -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Severity Distribution</h3>
                <apexchart
                    v-if="severityChartSeries.length > 0"
                    type="bar"
                    height="300"
                    :options="severityChartOptions"
                    :series="severityChartSeries"
                />
                <div v-else class="flex items-center justify-center h-64 text-gray-400">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <p class="mt-2 text-sm">No data available</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Hits Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Blacklist Hits</h3>
                <p class="text-sm text-gray-600 mt-1">Most frequently triggered rules</p>
            </div>

            <div v-if="stats.top_hits && stats.top_hits.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rank
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Severity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hit Count
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Hit
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(entry, index) in stats.top_hits.slice(0, 10)" :key="entry.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div 
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                    :class="getRankColor(index)"
                                >
                                    {{ index + 1 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="getTypeColor(entry.type)"
                                >
                                    {{ formatType(entry.type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 max-w-xs truncate" :title="entry.value">
                                    {{ entry.value }}
                                </div>
                                <div v-if="entry.reason" class="text-xs text-gray-500 max-w-xs truncate" :title="entry.reason">
                                    {{ entry.reason }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span 
                                    class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="getSeverityColor(entry.severity)"
                                >
                                    {{ entry.severity.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ formatAction(entry.action) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-bold text-gray-900">{{ formatNumber(entry.hit_count) }}</div>
                                    <div class="ml-2 w-20 bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="bg-red-500 h-2 rounded-full"
                                            :style="{ width: getHitPercentage(entry.hit_count) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDateTime(entry.last_hit_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-12 text-center text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="mt-2 text-sm">No blacklist hits yet</p>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-blue-900">Most Blocked Type</h4>
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-blue-900">{{ getMostBlockedType() }}</p>
                <p class="text-sm text-blue-700 mt-1">{{ getMostBlockedTypeCount() }} rules</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-yellow-900">Average Hit Rate</h4>
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-yellow-900">{{ getAverageHitRate() }}</p>
                <p class="text-sm text-yellow-700 mt-1">hits per rule</p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-green-900">Protection Rate</h4>
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-900">{{ getProtectionRate() }}%</p>
                <p class="text-sm text-green-700 mt-1">of traffic protected</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

const todayBlocks = computed(() => {
    // Calculate today's blocks from top hits
    if (!props.stats.top_hits || props.stats.top_hits.length === 0) return 0;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    return props.stats.top_hits.reduce((sum, entry) => {
        if (!entry.last_hit_at) return sum;
        const hitDate = new Date(entry.last_hit_at);
        hitDate.setHours(0, 0, 0, 0);
        return hitDate.getTime() === today.getTime() ? sum + 1 : sum;
    }, 0);
});

// Type chart data
const typeChartSeries = computed(() => {
    if (!props.stats.by_type) return [];
    return Object.values(props.stats.by_type);
});

const typeChartOptions = computed(() => ({
    chart: {
        type: 'donut',
    },
    labels: Object.keys(props.stats.by_type || {}).map(type => formatType(type)),
    colors: ['#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#10b981', '#ec4899', '#6366f1'],
    legend: {
        position: 'bottom',
    },
    dataLabels: {
        enabled: true,
        formatter: (val) => val.toFixed(1) + '%',
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Rules',
                        formatter: () => props.stats.total || 0,
                    },
                },
            },
        },
    },
}));

// Severity chart data
const severityChartSeries = computed(() => [
    {
        name: 'Rules',
        data: [
            props.stats.by_severity?.low || 0,
            props.stats.by_severity?.medium || 0,
            props.stats.by_severity?.high || 0,
            props.stats.by_severity?.critical || 0,
        ],
    },
]);

const severityChartOptions = computed(() => ({
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
            distributed: true,
        },
    },
    dataLabels: {
        enabled: true,
    },
    xaxis: {
        categories: ['Low', 'Medium', 'High', 'Critical'],
    },
    colors: ['#3b82f6', '#f59e0b', '#ef4444', '#7f1d1d'],
    legend: {
        show: false,
    },
}));

const getMostBlockedType = () => {
    if (!props.stats.by_type || Object.keys(props.stats.by_type).length === 0) {
        return 'N/A';
    }
    
    const maxType = Object.keys(props.stats.by_type).reduce((a, b) => 
        props.stats.by_type[a] > props.stats.by_type[b] ? a : b
    );
    
    return formatType(maxType);
};

const getMostBlockedTypeCount = () => {
    if (!props.stats.by_type || Object.keys(props.stats.by_type).length === 0) {
        return 0;
    }
    
    return Math.max(...Object.values(props.stats.by_type));
};

const getAverageHitRate = () => {
    if (!props.stats.total || props.stats.total === 0 || !props.stats.total_hits) {
        return '0';
    }
    
    return (props.stats.total_hits / props.stats.total).toFixed(1);
};

const getProtectionRate = () => {
    // This is a mock calculation - in production, you'd calculate based on total clicks vs blocked clicks
    if (!props.stats.total_hits || props.stats.total_hits === 0) {
        return '0.0';
    }
    
    // Assuming 1000 clicks per day average, calculate protection rate
    const estimatedTotalClicks = 1000;
    const protectionRate = Math.min((props.stats.total_hits / estimatedTotalClicks) * 100, 100);
    return protectionRate.toFixed(1);
};

const getHitPercentage = (hitCount) => {
    if (!props.stats.top_hits || props.stats.top_hits.length === 0) return 0;
    const maxHits = Math.max(...props.stats.top_hits.map(h => h.hit_count));
    return (hitCount / maxHits) * 100;
};

const getRankColor = (index) => {
    if (index === 0) return 'bg-yellow-500';
    if (index === 1) return 'bg-gray-400';
    if (index === 2) return 'bg-orange-600';
    return 'bg-blue-500';
};

const formatType = (type) => {
    const types = {
        ip: 'IP',
        ip_range: 'IP Range',
        user_agent: 'User Agent',
        referrer: 'Referrer',
        device_fingerprint: 'Fingerprint',
        country: 'Country',
        asn: 'ASN',
    };
    return types[type] || type;
};

const formatAction = (action) => {
    const actions = {
        block: 'Block',
        reduce_quality: 'Reduce Quality',
        flag: 'Flag',
    };
    return actions[action] || action;
};

const getTypeColor = (type) => {
    const colors = {
        ip: 'bg-blue-100 text-blue-800',
        ip_range: 'bg-indigo-100 text-indigo-800',
        user_agent: 'bg-purple-100 text-purple-800',
        referrer: 'bg-pink-100 text-pink-800',
        device_fingerprint: 'bg-yellow-100 text-yellow-800',
        country: 'bg-green-100 text-green-800',
        asn: 'bg-red-100 text-red-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const getSeverityColor = (severity) => {
    const colors = {
        low: 'bg-blue-100 text-blue-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        critical: 'bg-red-100 text-red-800',
    };
    return colors[severity] || 'bg-gray-100 text-gray-800';
};

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number || 0);
};

const formatDateTime = (dateTime) => {
    if (!dateTime) return 'Never';
    return new Date(dateTime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>
