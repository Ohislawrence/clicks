<template>
    <AppLayout title="User Management">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
                <p class="mt-1 text-sm text-gray-600">Manage platform users and their roles</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Search by name or email..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                @input="debouncedSearch"
                            />
                        </div>

                        <select
                            v-model="searchForm.role"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Roles</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                            </option>
                        </select>

                        <select
                            v-model="searchForm.status"
                            @change="applyFilters"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div v-if="users.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Balance
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Joined
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                <div v-if="user.affiliate_code && hasAffiliateRole(user)" class="text-xs text-blue-600 font-mono font-semibold mt-0.5">
                                                    {{ user.affiliate_code }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            v-for="role in (user.roles || [])"
                                            :key="role.id"
                                            class="px-3 py-1 rounded-full text-xs font-semibold mr-1"
                                            :class="{
                                                'bg-purple-100 text-purple-800': role.name === 'admin',
                                                'bg-green-100 text-green-800': role.name === 'affiliate',
                                                'bg-blue-100 text-blue-800': role.name === 'advertiser'
                                            }"
                                        >
                                            {{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}
                                        </span>
                                        <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-gray-400">No role</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatCurrency(user.balance) }}</div>
                                        <div class="text-xs text-gray-500">Lifetime: {{ formatCurrency(user.lifetime_earnings) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-green-100 text-green-800': user.is_active,
                                                'bg-red-100 text-red-800': !user.is_active
                                            }"
                                        >
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <Link
                                                :href="route('admin.users.show', user.id)"
                                                class="text-gray-600 hover:text-gray-900"
                                                title="View Details"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>

                                            <button
                                                @click="openEditModal(user)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Edit User"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="toggleStatus(user)"
                                                class="text-yellow-600 hover:text-yellow-900"
                                                :title="user.is_active ? 'Deactivate User' : 'Activate User'"
                                            >
                                                <svg v-if="user.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="impersonateUser(user)"
                                                class="text-purple-600 hover:text-purple-900"
                                                title="Impersonate User"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="confirmDelete(user)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete User"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total || 0 }} users
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    v-for="(link, index) in users.links"
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url || undefined"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm transition-colors',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : link.url
                                                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <DialogModal :show="showEditModal" @close="closeEditModal">
            <template #title>
                Edit User
            </template>

            <template #content>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="editForm.errors.name" class="text-red-600 text-sm mt-1">{{ editForm.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                            v-model="editForm.email"
                            type="email"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="editForm.errors.email" class="text-red-600 text-sm mt-1">{{ editForm.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input
                            v-model="editForm.phone"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="editForm.errors.phone" class="text-red-600 text-sm mt-1">{{ editForm.errors.phone }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Balance (₦)</label>
                        <input
                            v-model.number="editForm.balance"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <div v-if="editForm.errors.balance" class="text-red-600 text-sm mt-1">{{ editForm.errors.balance }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                        <div class="space-y-2">
                            <label v-for="role in roles" :key="role.id" class="flex items-center">
                                <input
                                    type="checkbox"
                                    :value="role.name"
                                    v-model="editForm.roles"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">{{ role.name.charAt(0).toUpperCase() + role.name.slice(1) }}</span>
                            </label>
                        </div>
                        <div v-if="editForm.errors.roles" class="text-red-600 text-sm mt-1">{{ editForm.errors.roles }}</div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="editForm.is_active"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>

                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                v-model="editForm.is_verified"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">Verified</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeEditModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    @click="updateUser"
                    :disabled="editForm.processing"
                >
                    Update User
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="closeDeleteModal">
            <template #title>
                Delete User
            </template>

            <template #content>
                <p>Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>? This action cannot be undone.</p>
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteUser"
                    :disabled="deleteForm.processing"
                >
                    Delete User
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { debounce } from 'lodash';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const searchForm = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
    status: props.filters.status || '',
});

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const editingUser = ref(null);

const editForm = useForm({
    name: '',
    email: '',
    phone: '',
    balance: 0,
    roles: [],
    is_active: true,
    is_verified: false,
});

const deleteForm = useForm({});

const applyFilters = () => {
    router.get(route('admin.users.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const openEditModal = (user) => {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.phone = user.phone || '';
    editForm.balance = user.balance || 0;
    editForm.roles = (user.roles || []).map(role => role.name);
    editForm.is_active = user.is_active;
    editForm.is_verified = user.is_verified;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingUser.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const updateUser = () => {
    editForm.put(route('admin.users.update', editingUser.value.id), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const toggleStatus = (user) => {
    router.post(route('admin.users.toggle-status', user.id), {}, {
        preserveScroll: true,
    });
};

const impersonateUser = (user) => {
    if (confirm(`Are you sure you want to impersonate ${user.name}? You will be logged in as this user.`)) {
        router.post(route('admin.users.impersonate', user.id));
    }
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    userToDelete.value = null;
};

const deleteUser = () => {
    deleteForm.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};

const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) {
        return '₦0.00';
    }
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount);
};

const formatDate = (date) => {
    if (!date) {
        return 'N/A';
    }
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const hasAffiliateRole = (user) => {
    return user.roles && user.roles.some(role => role.name === 'affiliate');
};
</script>
