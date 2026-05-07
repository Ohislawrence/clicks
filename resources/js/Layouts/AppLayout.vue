<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const page = usePage();
const showingNavigationDropdown = ref(false);
const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

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

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    document.body.style.overflow = isMobileMenuOpen.value ? 'hidden' : '';
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
    document.body.style.overflow = '';
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.body.style.overflow = '';
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <!-- Modern Navigation -->
            <nav
                class="sticky top-0 z-50 transition-all duration-300"
                :class="[
                    isScrolled
                        ? 'bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-200/50'
                        : 'bg-white border-b border-gray-100'
                ]"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 lg:h-20">
                        <!-- Logo Section -->
                        <div class="flex items-center">
                            <Link
                                :href="route('dashboard')"
                                class="flex items-center space-x-3 group"
                            >
                                <ApplicationMark class="block h-8 w-auto transition-transform group-hover:scale-105" />
                                <img src="/logo/black.png" alt="CPA Network" class="hidden sm:inline h-8 w-auto" />
                            </Link>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden lg:flex lg:items-center lg:space-x-1">
                            <!-- Affiliate Menu -->
                            <template v-if="isAffiliate">
                                <NavLink :href="route('affiliate.dashboard')" :active="route().current('affiliate.dashboard')" class="px-4 py-2">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('affiliate.offers.index')" :active="route().current('affiliate.offers.*')" class="px-4 py-2">
                                    Offers
                                </NavLink>
                                <NavLink :href="route('affiliate.links.index')" :active="route().current('affiliate.links.*')" class="px-4 py-2">
                                    My Links
                                </NavLink>
                                <NavLink :href="route('affiliate.reports.index')" :active="route().current('affiliate.reports.*')" class="px-4 py-2">
                                    Reports
                                </NavLink>
                                <NavLink :href="route('affiliate.payouts.index')" :active="route().current('affiliate.payouts.*')" class="px-4 py-2">
                                    Payouts
                                </NavLink>
                                <NavLink :href="route('affiliate.documentation.index')" :active="route().current('affiliate.documentation.*')" class="px-4 py-2">
                                    Documentation
                                </NavLink>
                            </template>

                            <!-- Advertiser Menu -->
                            <template v-if="isAdvertiser">
                                <NavLink :href="route('advertiser.dashboard')" :active="route().current('advertiser.dashboard')" class="px-4 py-2">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('advertiser.offers.index')" :active="route().current('advertiser.offers.*')" class="px-4 py-2">
                                    My Offers
                                </NavLink>
                                <NavLink :href="route('advertiser.conversions.index')" :active="route().current('advertiser.conversions.*')" class="px-4 py-2">
                                    Conversions
                                </NavLink>
                                <NavLink :href="route('advertiser.reports.index')" :active="route().current('advertiser.reports.*')" class="px-4 py-2">
                                    Reports
                                </NavLink>
                                <NavLink :href="route('advertiser.access-requests.index')" :active="route().current('advertiser.access-requests.*')" class="px-4 py-2">
                                    Access Requests
                                </NavLink>
                                <NavLink :href="route('advertiser.documentation.index')" :active="route().current('advertiser.documentation.*')" class="px-4 py-2">
                                    Documentation
                                </NavLink>
                            </template>

                            <!-- Admin Menu -->
                            <template v-if="isAdmin">
                                <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')" class="px-4 py-2">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')" class="px-4 py-2">
                                    Reports
                                </NavLink>
                                <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')" class="px-4 py-2">
                                    Users
                                </NavLink>
                                <NavLink :href="route('admin.offers.index')" :active="route().current('admin.offers.*')" class="px-4 py-2">
                                    Offers
                                </NavLink>
                                <NavLink :href="route('admin.conversions.index')" :active="route().current('admin.conversions.*')" class="px-4 py-2">
                                    Conversions
                                </NavLink>
                                <NavLink :href="route('admin.blacklists.index')" :active="route().current('admin.blacklists.*')" class="px-4 py-2">
                                    Blacklists
                                </NavLink>
                                <NavLink :href="route('admin.payouts.index')" :active="route().current('admin.payouts.*')" class="px-4 py-2">
                                    Payouts
                                </NavLink>
                                <NavLink :href="route('admin.settings.index')" :active="route().current('admin.settings.*')" class="px-4 py-2">
                                    Settings
                                </NavLink>
                            </template>
                        </div>

                        <!-- Right Section -->
                        <div class="flex items-center space-x-4">
                            <!-- Teams Dropdown -->
                            <div v-if="$page.props.jetstream.hasTeamFeatures" class="hidden sm:block">
                                <Dropdown align="right" width="60">
                                    <template #trigger>
                                        <button class="flex items-center space-x-2 px-3 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ $page.props.auth.user.current_team.name }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="w-64">
                                            <div class="px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
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
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- User Dropdown -->
                            <div class="hidden sm:block">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center space-x-3 focus:outline-none group">
                                            <div class="relative">
                                                <img
                                                    v-if="$page.props.jetstream.managesProfilePhotos"
                                                    class="h-10 w-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-blue-500 transition-all duration-200"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name"
                                                >
                                                <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-white"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 hidden md:block">{{ $page.props.auth.user.name }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="px-4 py-3">
                                            <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                                        </div>
                                        <div class="border-t border-gray-100"></div>
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

                            <!-- Mobile Menu Button -->
                            <button
                                @click="toggleMobileMenu"
                                class="lg:hidden relative w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all duration-200 flex items-center justify-center"
                            >
                                <div class="w-5 h-5 flex flex-col justify-between">
                                    <span
                                        class="w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"
                                        :class="{'rotate-45 translate-y-2': isMobileMenuOpen}"
                                    ></span>
                                    <span
                                        class="w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"
                                        :class="{'opacity-0': isMobileMenuOpen}"
                                    ></span>
                                    <span
                                        class="w-full h-0.5 bg-gray-600 rounded-full transition-all duration-300"
                                        :class="{'-rotate-45 -translate-y-2': isMobileMenuOpen}"
                                    ></span>
                                </div>
                            </button>
                        </div>
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
                                    <img src="/logo/black.png" alt="CPA Network" class="h-8 w-auto" />
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
                                    class="h-12 w-12 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url"
                                    :alt="$page.props.auth.user.name"
                                >
                                <div v-else class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ $page.props.auth.user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Navigation Links -->
                        <div class="py-4">
                            <!-- Affiliate Menu -->
                            <template v-if="isAffiliate">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Main Menu
                                </div>
                                <ResponsiveNavLink :href="route('affiliate.dashboard')" :active="route().current('affiliate.dashboard')" @click="closeMobileMenu">
                                    Dashboard
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('affiliate.offers.index')" :active="route().current('affiliate.offers.*')" @click="closeMobileMenu">
                                    Offers
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('affiliate.links.index')" :active="route().current('affiliate.links.*')" @click="closeMobileMenu">
                                    My Links
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('affiliate.reports.index')" :active="route().current('affiliate.reports.*')" @click="closeMobileMenu">
                                    Reports
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('affiliate.payouts.index')" :active="route().current('affiliate.payouts.*')" @click="closeMobileMenu">
                                    Payouts
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('affiliate.documentation.index')" :active="route().current('affiliate.documentation.*')" @click="closeMobileMenu">
                                    Documentation
                                </ResponsiveNavLink>
                            </template>

                            <!-- Advertiser Menu -->
                            <template v-if="isAdvertiser">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Main Menu
                                </div>
                                <ResponsiveNavLink :href="route('advertiser.dashboard')" :active="route().current('advertiser.dashboard')" @click="closeMobileMenu">
                                    Dashboard
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('advertiser.offers.index')" :active="route().current('advertiser.offers.*')" @click="closeMobileMenu">
                                    My Offers
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('advertiser.conversions.index')" :active="route().current('advertiser.conversions.*')" @click="closeMobileMenu">
                                    Conversions
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('advertiser.reports.index')" :active="route().current('advertiser.reports.*')" @click="closeMobileMenu">
                                    Reports
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('advertiser.access-requests.index')" :active="route().current('advertiser.access-requests.*')" @click="closeMobileMenu">
                                    Access Requests
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('advertiser.documentation.index')" :active="route().current('advertiser.documentation.*')" @click="closeMobileMenu">
                                    Documentation
                                </ResponsiveNavLink>
                            </template>

                            <!-- Admin Menu -->
                            <template v-if="isAdmin">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Main Menu
                                </div>
                                <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')" @click="closeMobileMenu">
                                    Dashboard
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')" @click="closeMobileMenu">
                                    Reports
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')" @click="closeMobileMenu">
                                    Users
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.offers.index')" :active="route().current('admin.offers.*')" @click="closeMobileMenu">
                                    Offers
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.conversions.index')" :active="route().current('admin.conversions.*')" @click="closeMobileMenu">
                                    Conversions
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.blacklists.index')" :active="route().current('admin.blacklists.*')" @click="closeMobileMenu">
                                    Blacklists
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.payouts.index')" :active="route().current('admin.payouts.*')" @click="closeMobileMenu">
                                    Payouts
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('admin.settings.index')" :active="route().current('admin.settings.*')" @click="closeMobileMenu">
                                    Settings
                                </ResponsiveNavLink>
                            </template>

                            <!-- Team Management in Mobile -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-100 my-4"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Team Management
                                </div>
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')" @click="closeMobileMenu">
                                    Team Settings
                                </ResponsiveNavLink>
                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')" @click="closeMobileMenu">
                                    Create New Team
                                </ResponsiveNavLink>
                            </template>

                            <!-- Mobile Footer Links -->
                            <div class="border-t border-gray-100 my-4"></div>
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')" @click="closeMobileMenu">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" @click="closeMobileMenu">
                                API Tokens
                            </ResponsiveNavLink>
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button" @click="closeMobileMenu">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </transition>
            </nav>

            <!-- Page Header -->
            <header v-if="$slots.header" class="bg-white border-b border-gray-100 sticky top-16 lg:top-20 z-30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Smooth scrolling for mobile menu */
.lg\:hidden {
    -webkit-overflow-scrolling: touch;
}

/* Custom scrollbar for mobile menu */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
</style>
