<template>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
            <slot name="actions"></slot>
        </div>
        
        <div ref="chartRef" class="min-h-[300px]"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import ApexCharts from 'apexcharts';

const props = defineProps({
    title: String,
    type: {
        type: String,
        default: 'line'
    },
    series: Array,
    categories: Array,
    height: {
        type: Number,
        default: 300
    },
    colors: Array
});

const chartRef = ref(null);
let chart = null;

const defaultColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

onMounted(() => {
    const options = {
        chart: {
            type: props.type,
            height: props.height,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        series: props.series,
        xaxis: {
            categories: props.categories,
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                }
            }
        },
        colors: props.colors || defaultColors,
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            labels: {
                colors: '#6B7280'
            }
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'light',
            x: {
                show: true
            }
        }
    };

    chart = new ApexCharts(chartRef.value, options);
    chart.render();
});

watch(() => [props.series, props.categories], () => {
    if (chart) {
        chart.updateOptions({
            series: props.series,
            xaxis: {
                categories: props.categories
            }
        });
    }
}, { deep: true });
</script>
