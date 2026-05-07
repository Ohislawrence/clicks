<template>
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Device Targeting Configuration</h3>
                    <p class="text-sm text-gray-600 mt-1">Control which devices, operating systems, and browsers can access this link</p>
                </div>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.enable_device_targeting"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm font-medium text-gray-700">Enable Device Targeting</span>
                </div>
            </div>

            <div v-if="form.enable_device_targeting" class="space-y-6">
                <!-- Device Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Device Types</label>
                    <div class="grid grid-cols-3 gap-4">
                        <button
                            v-for="device in deviceTypes"
                            :key="device.value"
                            @click="toggleDevice(device.value)"
                            :class="[
                                'p-4 rounded-lg border-2 transition-all',
                                isDeviceSelected(device.value)
                                    ? 'border-blue-500 bg-blue-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-center">
                                <div class="text-3xl mb-2">{{ device.icon }}</div>
                                <div class="text-sm font-semibold text-gray-900">{{ device.label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ device.description }}</div>
                                <div v-if="isDeviceSelected(device.value)" class="mt-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Active
                                    </span>
                                </div>
                            </div>
                        </button>
                    </div>
                    <p v-if="!form.allowed_devices || form.allowed_devices.length === 0" class="text-sm text-gray-500 mt-2">
                        ℹ️ No devices selected = All devices allowed
                    </p>
                </div>

                <!-- Operating System Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Operating Systems</label>
                    <div class="grid grid-cols-4 gap-3">
                        <button
                            v-for="os in operatingSystems"
                            :key="os.value"
                            @click="toggleOS(os.value)"
                            :class="[
                                'p-3 rounded-lg border-2 transition-all text-center',
                                isOSSelected(os.value)
                                    ? 'border-green-500 bg-green-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-2xl mb-1">{{ os.icon }}</div>
                            <div class="text-xs font-semibold text-gray-900">{{ os.label }}</div>
                            <div v-if="isOSSelected(os.value)" class="mt-1">
                                <svg class="w-4 h-4 mx-auto text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>
                    <p v-if="!form.allowed_os || form.allowed_os.length === 0" class="text-sm text-gray-500 mt-2">
                        ℹ️ No OS selected = All operating systems allowed
                    </p>
                </div>

                <!-- Browser Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Browsers</label>
                    <div class="grid grid-cols-4 gap-3">
                        <button
                            v-for="browser in browsers"
                            :key="browser.value"
                            @click="toggleBrowser(browser.value)"
                            :class="[
                                'p-3 rounded-lg border-2 transition-all text-center',
                                isBrowserSelected(browser.value)
                                    ? 'border-purple-500 bg-purple-50'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="text-2xl mb-1">{{ browser.icon }}</div>
                            <div class="text-xs font-semibold text-gray-900">{{ browser.label }}</div>
                            <div v-if="isBrowserSelected(browser.value)" class="mt-1">
                                <svg class="w-4 h-4 mx-auto text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>
                    <p v-if="!form.allowed_browsers || form.allowed_browsers.length === 0" class="text-sm text-gray-500 mt-2">
                        ℹ️ No browsers selected = All browsers allowed
                    </p>
                </div>

                <!-- Quick Presets -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Quick Presets</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            @click="applyPreset('mobile-only')"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left"
                        >
                            <div class="text-lg mb-1">📱</div>
                            <div class="text-sm font-medium text-gray-900">Mobile Only</div>
                            <div class="text-xs text-gray-500">iOS + Android</div>
                        </button>

                        <button
                            @click="applyPreset('desktop-only')"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all text-left"
                        >
                            <div class="text-lg mb-1">🖥️</div>
                            <div class="text-sm font-medium text-gray-900">Desktop Only</div>
                            <div class="text-xs text-gray-500">Windows + macOS + Linux</div>
                        </button>

                        <button
                            @click="clearDeviceTargeting"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all text-left"
                        >
                            <div class="text-lg mb-1">🔄</div>
                            <div class="text-sm font-medium text-gray-900">Clear All</div>
                            <div class="text-xs text-gray-500">Allow all devices</div>
                        </button>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">🎯 Targeting Summary</h4>
                    <div class="space-y-2 text-sm text-blue-800">
                        <div v-if="form.allowed_devices && form.allowed_devices.length > 0">
                            <strong>Devices:</strong> {{ form.allowed_devices.join(', ') }}
                        </div>
                        <div v-else>
                            <strong>Devices:</strong> All devices allowed
                        </div>

                        <div v-if="form.allowed_os && form.allowed_os.length > 0">
                            <strong>Operating Systems:</strong> {{ form.allowed_os.join(', ') }}
                        </div>
                        <div v-else>
                            <strong>Operating Systems:</strong> All OS allowed
                        </div>

                        <div v-if="form.allowed_browsers && form.allowed_browsers.length > 0">
                            <strong>Browsers:</strong> {{ form.allowed_browsers.join(', ') }}
                        </div>
                        <div v-else>
                            <strong>Browsers:</strong> All browsers allowed
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const deviceTypes = [
    { value: 'mobile', label: 'Mobile', icon: '📱', description: 'Smartphones' },
    { value: 'tablet', label: 'Tablet', icon: '📱', description: 'Tablets & iPads' },
    { value: 'desktop', label: 'Desktop', icon: '🖥️', description: 'PCs & Laptops' },
];

const operatingSystems = [
    { value: 'android', label: 'Android', icon: '🤖' },
    { value: 'ios', label: 'iOS', icon: '🍎' },
    { value: 'windows', label: 'Windows', icon: '🪟' },
    { value: 'macos', label: 'macOS', icon: '💻' },
    { value: 'linux', label: 'Linux', icon: '🐧' },
    { value: 'chrome_os', label: 'Chrome OS', icon: '🌐' },
];

const browsers = [
    { value: 'chrome', label: 'Chrome', icon: '🟢' },
    { value: 'firefox', label: 'Firefox', icon: '🦊' },
    { value: 'safari', label: 'Safari', icon: '🧭' },
    { value: 'edge', label: 'Edge', icon: '🌊' },
    { value: 'opera', label: 'Opera', icon: '🎭' },
    { value: 'brave', label: 'Brave', icon: '🦁' },
];

const isDeviceSelected = (device) => {
    return props.form.allowed_devices?.includes(device) || false;
};

const isOSSelected = (os) => {
    return props.form.allowed_os?.includes(os) || false;
};

const isBrowserSelected = (browser) => {
    return props.form.allowed_browsers?.includes(browser) || false;
};

const toggleDevice = (device) => {
    if (!props.form.allowed_devices) {
        props.form.allowed_devices = [];
    }
    
    const index = props.form.allowed_devices.indexOf(device);
    if (index > -1) {
        props.form.allowed_devices.splice(index, 1);
    } else {
        props.form.allowed_devices.push(device);
    }
};

const toggleOS = (os) => {
    if (!props.form.allowed_os) {
        props.form.allowed_os = [];
    }
    
    const index = props.form.allowed_os.indexOf(os);
    if (index > -1) {
        props.form.allowed_os.splice(index, 1);
    } else {
        props.form.allowed_os.push(os);
    }
};

const toggleBrowser = (browser) => {
    if (!props.form.allowed_browsers) {
        props.form.allowed_browsers = [];
    }
    
    const index = props.form.allowed_browsers.indexOf(browser);
    if (index > -1) {
        props.form.allowed_browsers.splice(index, 1);
    } else {
        props.form.allowed_browsers.push(browser);
    }
};

const applyPreset = (preset) => {
    if (preset === 'mobile-only') {
        props.form.allowed_devices = ['mobile'];
        props.form.allowed_os = ['android', 'ios'];
        props.form.allowed_browsers = ['chrome', 'safari'];
    } else if (preset === 'desktop-only') {
        props.form.allowed_devices = ['desktop'];
        props.form.allowed_os = ['windows', 'macos', 'linux'];
        props.form.allowed_browsers = ['chrome', 'firefox', 'edge', 'safari'];
    }
};

const clearDeviceTargeting = () => {
    props.form.allowed_devices = [];
    props.form.allowed_os = [];
    props.form.allowed_browsers = [];
};
</script>
