<template>
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Geo-Targeting Configuration</h3>
                    <p class="text-sm text-gray-600 mt-1">Control which countries, regions, and cities can access this link</p>
                </div>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.enable_geo_targeting"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm font-medium text-gray-700">Enable Geo-Targeting</span>
                </div>
            </div>

            <div v-if="form.enable_geo_targeting" class="space-y-6">
                <!-- Targeting Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Targeting Mode</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button
                            @click="targetingMode = 'allow'"
                            :class="[
                                'px-4 py-3 rounded-lg border-2 transition-all text-left',
                                targetingMode === 'allow' 
                                    ? 'border-green-500 bg-green-50' 
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" :class="targetingMode === 'allow' ? 'text-green-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">Allow List</div>
                                    <div class="text-xs text-gray-500">Only selected countries allowed</div>
                                </div>
                            </div>
                        </button>

                        <button
                            @click="targetingMode = 'block'"
                            :class="[
                                'px-4 py-3 rounded-lg border-2 transition-all text-left',
                                targetingMode === 'block' 
                                    ? 'border-red-500 bg-red-50' 
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" :class="targetingMode === 'block' ? 'text-red-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">Block List</div>
                                    <div class="text-xs text-gray-500">All except selected countries</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Country Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ targetingMode === 'allow' ? 'Allowed Countries' : 'Blocked Countries' }}
                    </label>
                    
                    <!-- Search Countries -->
                    <div class="relative mb-4">
                        <input
                            v-model="countrySearch"
                            type="text"
                            placeholder="Search countries..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-10"
                        />
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Quick Selections -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button
                            v-for="region in quickRegions"
                            :key="region.name"
                            @click="selectRegion(region.countries)"
                            class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            {{ region.name }}
                        </button>
                        <button
                            @click="clearSelection"
                            class="px-3 py-1 text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                        >
                            Clear All
                        </button>
                    </div>

                    <!-- Selected Countries Display -->
                    <div v-if="selectedCountries.length > 0" class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                Selected: {{ selectedCountries.length }} countries
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div
                                v-for="code in selectedCountries"
                                :key="code"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                :class="targetingMode === 'allow' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                <span class="mr-1">{{ getCountryFlag(code) }}</span>
                                {{ getCountryName(code) }}
                                <button
                                    @click="removeCountry(code)"
                                    class="ml-2 hover:text-gray-900"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Country List -->
                    <div class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                        <div
                            v-for="country in filteredCountries"
                            :key="country.code"
                            @click="toggleCountry(country.code)"
                            class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                        >
                            <input
                                type="checkbox"
                                :checked="selectedCountries.includes(country.code)"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="ml-3 text-2xl">{{ country.flag }}</span>
                            <span class="ml-3 text-sm font-medium text-gray-900">{{ country.name }}</span>
                            <span class="ml-auto text-xs text-gray-500">{{ country.code }}</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Map (Simplified) -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Geographic Coverage</h4>
                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="region in regions" :key="region.name" class="text-center">
                            <div class="text-3xl mb-2">{{ region.icon }}</div>
                            <div class="text-sm font-medium text-gray-900">{{ region.name }}</div>
                            <div class="text-xs text-gray-600 mt-1">
                                {{ getRegionCount(region.countries) }} / {{ region.countries.length }} countries
                            </div>
                            <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div 
                                    class="h-full transition-all"
                                    :class="targetingMode === 'allow' ? 'bg-green-500' : 'bg-red-500'"
                                    :style="{ width: (getRegionCount(region.countries) / region.countries.length * 100) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced: Regions & Cities -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Allowed Regions (Optional)
                        </label>
                        <textarea
                            v-model="form.allowed_regions"
                            rows="3"
                            placeholder="Lagos, Greater Accra, Nairobi..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Comma-separated list of regions/states</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Allowed Cities (Optional)
                        </label>
                        <textarea
                            v-model="form.allowed_cities"
                            rows="3"
                            placeholder="Lagos, Accra, Nairobi..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Comma-separated list of cities</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const targetingMode = ref('allow');
const countrySearch = ref('');

const selectedCountries = computed({
    get() {
        if (targetingMode.value === 'allow') {
            return props.form.allowed_countries || [];
        } else {
            return props.form.blocked_countries || [];
        }
    },
    set(value) {
        if (targetingMode.value === 'allow') {
            props.form.allowed_countries = value;
            props.form.blocked_countries = [];
        } else {
            props.form.blocked_countries = value;
            props.form.allowed_countries = [];
        }
    }
});

// Watch for mode changes to move countries between lists
watch(targetingMode, (newMode, oldMode) => {
    if (oldMode === 'allow' && props.form.allowed_countries?.length > 0) {
        props.form.blocked_countries = [...props.form.allowed_countries];
        props.form.allowed_countries = [];
    } else if (oldMode === 'block' && props.form.blocked_countries?.length > 0) {
        props.form.allowed_countries = [...props.form.blocked_countries];
        props.form.blocked_countries = [];
    }
});

// Countries database (major countries - expand as needed)
const countries = [
    { code: 'NG', name: 'Nigeria', flag: '🇳🇬', region: 'Africa' },
    { code: 'GH', name: 'Ghana', flag: '🇬🇭', region: 'Africa' },
    { code: 'KE', name: 'Kenya', flag: '🇰🇪', region: 'Africa' },
    { code: 'ZA', name: 'South Africa', flag: '🇿🇦', region: 'Africa' },
    { code: 'EG', name: 'Egypt', flag: '🇪🇬', region: 'Africa' },
    { code: 'US', name: 'United States', flag: '🇺🇸', region: 'Americas' },
    { code: 'CA', name: 'Canada', flag: '🇨🇦', region: 'Americas' },
    { code: 'BR', name: 'Brazil', flag: '🇧🇷', region: 'Americas' },
    { code: 'MX', name: 'Mexico', flag: '🇲🇽', region: 'Americas' },
    { code: 'GB', name: 'United Kingdom', flag: '🇬🇧', region: 'Europe' },
    { code: 'DE', name: 'Germany', flag: '🇩🇪', region: 'Europe' },
    { code: 'FR', name: 'France', flag: '🇫🇷', region: 'Europe' },
    { code: 'IT', name: 'Italy', flag: '🇮🇹', region: 'Europe' },
    { code: 'ES', name: 'Spain', flag: '🇪🇸', region: 'Europe' },
    { code: 'CN', name: 'China', flag: '🇨🇳', region: 'Asia' },
    { code: 'IN', name: 'India', flag: '🇮🇳', region: 'Asia' },
    { code: 'JP', name: 'Japan', flag: '🇯🇵', region: 'Asia' },
    { code: 'SG', name: 'Singapore', flag: '🇸🇬', region: 'Asia' },
    { code: 'AU', name: 'Australia', flag: '🇦🇺', region: 'Oceania' },
    { code: 'NZ', name: 'New Zealand', flag: '🇳🇿', region: 'Oceania' },
];

const quickRegions = [
    { name: 'West Africa', countries: ['NG', 'GH', 'SN', 'CI', 'BJ'] },
    { name: 'East Africa', countries: ['KE', 'TZ', 'UG', 'ET'] },
    { name: 'North America', countries: ['US', 'CA', 'MX'] },
    { name: 'Europe (Top 5)', countries: ['GB', 'DE', 'FR', 'IT', 'ES'] },
    { name: 'Asia (Top 5)', countries: ['CN', 'IN', 'JP', 'SG', 'PH'] },
];

const regions = [
    { name: 'Africa', icon: '🌍', countries: ['NG', 'GH', 'KE', 'ZA', 'EG'] },
    { name: 'Americas', icon: '🌎', countries: ['US', 'CA', 'BR', 'MX'] },
    { name: 'Europe', icon: '🌍', countries: ['GB', 'DE', 'FR', 'IT', 'ES'] },
    { name: 'Asia/Pacific', icon: '🌏', countries: ['CN', 'IN', 'JP', 'SG', 'AU'] },
];

const filteredCountries = computed(() => {
    if (!countrySearch.value) {
        return countries;
    }
    const search = countrySearch.value.toLowerCase();
    return countries.filter(c => 
        c.name.toLowerCase().includes(search) || 
        c.code.toLowerCase().includes(search)
    );
});

const toggleCountry = (code) => {
    const current = [...selectedCountries.value];
    const index = current.indexOf(code);
    
    if (index > -1) {
        current.splice(index, 1);
    } else {
        current.push(code);
    }
    
    selectedCountries.value = current;
};

const removeCountry = (code) => {
    selectedCountries.value = selectedCountries.value.filter(c => c !== code);
};

const selectRegion = (countryCodes) => {
    const current = new Set(selectedCountries.value);
    countryCodes.forEach(code => current.add(code));
    selectedCountries.value = Array.from(current);
};

const clearSelection = () => {
    selectedCountries.value = [];
};

const getCountryName = (code) => {
    return countries.find(c => c.code === code)?.name || code;
};

const getCountryFlag = (code) => {
    return countries.find(c => c.code === code)?.flag || '🏳️';
};

const getRegionCount = (regionCountries) => {
    return regionCountries.filter(code => selectedCountries.value.includes(code)).length;
};
</script>
