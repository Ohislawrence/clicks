<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    title: String,
});

const page = usePage();
const isSidebarCollapsed = ref(false);
const isMobileMenuOpen = ref(false);

const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value.roles || []);

const isAffiliate = computed(() => userRoles.value.some(role => role.name === 'affiliate'));
const isAdvertiser = computed(() => userRoles.value.some(role => role.name === 'advertiser'));
const isAdmin = computed(() => userRoles.value.some(role => role.name === 'admin'));

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    document.body.style.overflow = isMobileMenuOpen.value ? 'hidden' : '';
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
    document.body.style.overflow = '';
};

// Navigation items for each role
const affiliateNavItems = [
    { name: 'Dashboard', route: 'affiliate.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Offers', route: 'affiliate.offers.index', match: 'affiliate.offers.*', icon: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' },
    { name: 'My Links', route: 'affiliate.links.index', match: 'affiliate.links.*', icon: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1' },
    { name: 'Reports', route: 'affiliate.reports.index', match: 'affiliate.reports.*', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Payouts', route: 'affiliate.payouts.index', match: 'affiliate.payouts.*', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Documentation', route: 'affiliate.documentation.index', match: 'affiliate.documentation.*', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' }
];

const advertiserNavItems = [
    { name: 'Dashboard', route: 'advertiser.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'My Offers', route: 'advertiser.offers.index', match: 'advertiser.offers.*', icon: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' },
    { name: 'Conversions', route: 'advertiser.conversions.index', match: 'advertiser.conversions.*', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Reports', route: 'advertiser.reports.index', match: 'advertiser.reports.*', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Access Requests', route: 'advertiser.access-requests.index', match: 'advertiser.access-requests.*', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'My Stores', route: 'advertiser.store.index', match: 'advertiser.store.*', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
    { name: 'Documentation', route: 'advertiser.documentation.index', match: 'advertiser.documentation.*', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' }
];

const adminNavItems = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Reports', route: 'admin.reports.index', match: 'admin.reports.*', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'Users', route: 'admin.users.index', match: 'admin.users.*', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Stores', route: 'admin.stores.index', match: 'admin.stores.*', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Offers', route: 'admin.offers.index', match: 'admin.offers.*', icon: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' },
    { name: 'Conversions', route: 'admin.conversions.index', match: 'admin.conversions.*', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Blacklists', route: 'admin.blacklists.index', match: 'admin.blacklists.*', icon: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' },
    { name: 'Payouts', route: 'admin.payouts.index', match: 'admin.payouts.*', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Blog', route: 'admin.blog.index', match: 'admin.blog.*', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z' },
    { name: 'Store Plans', route: 'admin.store-plans.index', match: 'admin.store-plans.*', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Store Themes', route: 'admin.store-themes.index', match: 'admin.store-themes.*', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' },
    { name: 'Settings', route: 'admin.settings.index', match: 'admin.settings.*', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }
];

const navItems = computed(() => {
    if (isAdmin.value) return adminNavItems;
    if (isAdvertiser.value) return advertiserNavItems;
    if (isAffiliate.value) return affiliateNavItems;
    return [];
});

const accountBalance = computed(() => {
    if (isAffiliate.value) return parseFloat(user.value.balance || 0).toFixed(2);
    if (isAdvertiser.value) return parseFloat(user.value.advertiser_balance || 0).toFixed(2);
    return null;
});

const balanceLabel = computed(() => {
    if (isAffiliate.value) return 'Earnings Balance';
    if (isAdvertiser.value) return 'Ad Balance';
    return '';
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <!-- Desktop Sidebar -->
            <aside
                class="hidden lg:fixed lg:inset-y-0 lg:flex lg:flex-col lg:transition-all lg:duration-300 lg:z-50 bg-white border-r border-gray-200 shadow-sm"
                :class="isSidebarCollapsed ? 'lg:w-20' : 'lg:w-64'"
            >
                <!-- Logo -->
                <div class="flex h-16 items-center justify-between px-4 border-b border-gray-100">
                    <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <ApplicationMark class="block h-8 w-auto" />
                        <img v-if="!isSidebarCollapsed" src="/logo/black.png" alt="CPA Network" class="h-6 w-auto" />
                    </Link>
                    <button
                        v-if="!isSidebarCollapsed"
                        @click="toggleSidebar"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button
                        v-if="isSidebarCollapsed"
                        @click="toggleSidebar"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <Link
                        v-for="item in navItems"
                        :key="item.route"
                        :href="route(item.route)"
                        :class="[
                            route().current(item.match || item.route)
                                ? 'bg-blue-50 text-blue-700 border-blue-500'
                                : 'text-gray-700 hover:bg-gray-50 border-transparent hover:border-gray-300',
                            'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 border-l-4'
                        ]"
                    >
                        <svg class="flex-shrink-0 w-5 h-5" :class="isSidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                        </svg>
                        <span v-if="!isSidebarCollapsed">{{ item.name }}</span>
                    </Link>
                </nav>

                <!-- Balance Card (Affiliate & Advertiser) -->
                <div v-if="accountBalance !== null && !isSidebarCollapsed" class="mx-3 mb-3 px-4 py-3 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow">
                    <p class="text-xs font-medium text-blue-200 uppercase tracking-wider">{{ balanceLabel }}</p>
                    <p class="text-xl font-bold mt-0.5">₦{{ accountBalance }}</p>
                </div>
                <div v-if="accountBalance !== null && isSidebarCollapsed" class="mx-2 mb-3 flex items-center justify-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center" :title="`${balanceLabel}: ₦${accountBalance}`">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- User Section -->
                <div class="border-t border-gray-100 p-4">
                    <Dropdown align="top" width="48">
                        <template #trigger>
                            <button class="flex items-center w-full space-x-3 hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                <img
                                    v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="h-8 w-8 rounded-lg object-cover"
                                    :src="$page.props.auth.user.profile_photo_url"
                                    :alt="$page.props.auth.user.name"
                                >
                                <div v-else class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div v-if="!isSidebarCollapsed" class="flex-1 text-left">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
                                </div>
                                <svg v-if="!isSidebarCollapsed" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <!-- Teams Dropdown -->
                            <div v-if="$page.props.jetstream.hasTeamFeatures" class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Manage Team
                                </div>
                                <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                    Team Settings
                                </DropdownLink>
                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                    Create New Team
                                </DropdownLink>
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-100 my-2"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Switch Teams
                                    </div>
                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <DropdownLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span>{{ team.name }}</span>
                                                </div>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </template>
                            </div>

                            <DropdownLink :href="route('profile.show')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </div>
                            </DropdownLink>
                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    API Tokens
                                </div>
                            </DropdownLink>
                            <div class="border-t border-gray-100"></div>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button">
                                    <div class="flex items-center text-red-600">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Log Out
                                    </div>
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </aside>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4">
                    <Link :href="route('dashboard')" class="flex items-center space-x-2">
                        <ApplicationMark class="block h-8 w-auto" />
                        <img src="/logo/black.png" alt="CPA Network" class="h-6 w-auto" />
                    </Link>
                    <!-- Balance Badge (mobile) -->
                    <div v-if="accountBalance !== null" class="flex-1 flex justify-center px-3">
                        <div class="flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-sm">
                            <svg class="w-3.5 h-3.5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-bold">₦{{ accountBalance }}</span>
                        </div>
                    </div>
                    <button
                        @click="toggleMobileMenu"
                        class="p-2 rounded-lg hover:bg-gray-100 text-gray-500"
                    >
                        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Overlay -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isMobileMenuOpen"
                    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                    @click="closeMobileMenu"
                ></div>
            </transition>

            <!-- Mobile Menu Sidebar -->
            <transition
                enter-active-class="transition duration-200 ease-out transform"
                enter-from-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition duration-150 ease-in transform"
                leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <div
                    v-if="isMobileMenuOpen"
                    class="fixed top-0 left-0 bottom-0 w-80 bg-white shadow-2xl z-50 lg:hidden overflow-y-auto"
                >
                    <!-- Mobile Menu Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <Link :href="route('dashboard')" class="flex items-center space-x-2" @click="closeMobileMenu">
                                <ApplicationMark class="block h-8 w-auto" />
                                <img src="/logo/black.png" alt="CPA Network" class="h-6 w-auto" />
                            </Link>
                            <button @click="closeMobileMenu" class="p-2 rounded-lg hover:bg-gray-100">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- User Info -->
                        <div class="flex items-center space-x-3">
                            <img
                                v-if="$page.props.jetstream.managesProfilePhotos"
                                class="h-12 w-12 rounded-lg object-cover"
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                            >
                            <div v-else class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                <p class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</p>
                            </div>
                        </div>

                        <!-- Balance (mobile drawer) -->
                        <div v-if="accountBalance !== null" class="mt-4 flex items-center space-x-2 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow">
                            <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-blue-200 font-medium">{{ balanceLabel }}</p>
                                <p class="text-lg font-bold leading-none">₦{{ accountBalance }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navigation Links -->
                    <nav class="px-3 py-4 space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.route"
                            :href="route(item.route)"
                            @click="closeMobileMenu"
                            :class="[
                                route().current(item.match || item.route)
                                    ? 'bg-blue-50 text-blue-700 border-blue-500'
                                    : 'text-gray-700 hover:bg-gray-50 border-transparent hover:border-gray-300',
                                'flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 border-l-4'
                            ]"
                        >
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>

                    <!-- Mobile Menu Footer -->
                    <div class="border-t border-gray-100 px-3 py-4 space-y-1">
                        <!-- Team Management -->
                        <template v-if="$page.props.jetstream.hasTeamFeatures">
                            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Team Management
                            </div>
                            <Link
                                :href="route('teams.show', $page.props.auth.user.current_team)"
                                @click="closeMobileMenu"
                                class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                Team Settings
                            </Link>
                            <Link
                                v-if="$page.props.jetstream.canCreateTeams"
                                :href="route('teams.create')"
                                @click="closeMobileMenu"
                                class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                            >
                                Create New Team
                            </Link>
                        </template>

                        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Account
                        </div>
                        <Link
                            :href="route('profile.show')"
                            @click="closeMobileMenu"
                            class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </Link>
                        <Link
                            v-if="$page.props.jetstream.hasApiFeatures"
                            :href="route('api-tokens.index')"
                            @click="closeMobileMenu"
                            class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            API Tokens
                        </Link>
                        <form @submit.prevent="logout">
                            <button
                                type="submit"
                                class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </transition>

            <!-- Main Content Area -->
            <div class="lg:pl-64 transition-all duration-300" :class="{'lg:pl-20': isSidebarCollapsed}">
                <!-- Top Bar (Mobile spacer / Desktop optional header) -->
                <div class="lg:hidden h-16"></div>

                <!-- Page Header -->
                <header v-if="$slots.header" class="bg-white border-b border-gray-100 sticky top-0 lg:top-0 z-30">
                    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="py-8">
                    <div class="mx-auto px-4 sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar */
aside::-webkit-scrollbar,
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

aside::-webkit-scrollbar-track,
.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

aside::-webkit-scrollbar-thumb,
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

aside::-webkit-scrollbar-thumb:hover,
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
