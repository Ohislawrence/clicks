<script setup>
import { ref, watch } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    leads: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const showCreateModal = ref(false);
const showBulkEmailModal = ref(false);
const selectedLeads = ref([]);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    website: '',
    source: '',
    notes: '',
});

const bulkEmailForm = useForm({
    lead_ids: [],
    subject: '',
    content: '',
});

const debouncedSearch = debounce(() => {
    router.get(route('admin.crm.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch(search, debouncedSearch);
watch(status, () => {
    router.get(route('admin.crm.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
    });
});

const submitCreate = () => {
    form.post(route('admin.crm.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

const openBulkEmailModal = () => {
    if (selectedLeads.value.length === 0) {
        alert('Please select at least one lead.');
        return;
    }
    bulkEmailForm.lead_ids = selectedLeads.value;
    showBulkEmailModal.value = true;
};

const submitBulkEmail = () => {
    bulkEmailForm.post(route('admin.crm.bulk-email'), {
        onSuccess: () => {
            showBulkEmailModal.value = false;
            bulkEmailForm.reset();
            selectedLeads.value = [];
        },
    });
};

const toggleSelection = (leadId) => {
    if (selectedLeads.value.includes(leadId)) {
        selectedLeads.value = selectedLeads.value.filter(id => id !== leadId);
    } else {
        selectedLeads.value.push(leadId);
    }
};

const toggleAll = () => {
    if (selectedLeads.value.length === props.leads.data.length) {
        selectedLeads.value = [];
    } else {
        selectedLeads.value = props.leads.data.map(lead => lead.id);
    }
};

const getStatusBadge = (status) => {
    const classes = {
        'new': 'bg-blue-100 text-blue-800',
        'contacted': 'bg-yellow-100 text-yellow-800',
        'interested': 'bg-green-100 text-green-800',
        'converted': 'bg-purple-100 text-purple-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout title="CRM - Lead Management">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">CRM</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage potential customers and outreach</p>
                </div>
                <div class="flex space-x-3">
                    <button
                        v-if="selectedLeads.length > 0"
                        @click="openBulkEmailModal"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center"
                    >
                        Bulk Email ({{ selectedLeads.length }})
                    </button>
                    <button
                        @click="showCreateModal = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center"
                    >
                        Add Lead
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by name, email or company..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <select
                            v-model="status"
                            class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="interested">Interested</option>
                            <option value="converted">Converted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Leads Table -->
                <div v-if="leads.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input
                                            type="checkbox"
                                            :checked="selectedLeads.length === leads.data.length && leads.data.length > 0"
                                            @change="toggleAll"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lead
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Last Contacted
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="lead in leads.data" :key="lead.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            :checked="selectedLeads.includes(lead.id)"
                                            @change="toggleSelection(lead.id)"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ lead.name }}</div>
                                                <div class="text-sm text-gray-500">{{ lead.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ lead.company || 'N/A' }}</div>
                                        <div v-if="lead.website" class="text-xs text-blue-600 truncate max-w-[150px]">
                                            <a :href="lead.website" target="_blank">{{ lead.website }}</a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="getStatusBadge(lead.status)">
                                            {{ lead.status.charAt(0).toUpperCase() + lead.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ lead.last_contacted_at ? new Date(lead.last_contacted_at).toLocaleDateString() : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('admin.crm.show', lead.id)" class="text-blue-600 hover:text-blue-900 mr-3">View</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ leads.from || 0 }} to {{ leads.to || 0 }} of {{ leads.total || 0 }} leads
                            </div>
                            <div class="flex space-x-2">
                                <component
                                    v-for="(link, index) in leads.links"
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
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-gray-500">No leads found matching your criteria.</p>
                </div>
            </div>
        </div>

        <!-- Create Lead Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4">Add New Lead</h3>
                <form @submit.prevent="submitCreate">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300" required />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300" required />
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-lg border-gray-300" />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <input v-model="form.company" type="text" class="w-full rounded-lg border-gray-300" />
                        </div>
                        <div class="md:col-span-2 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input v-model="form.website" type="url" class="w-full rounded-lg border-gray-300" placeholder="https://" />
                        </div>
                        <div class="md:col-span-2 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Initial Notes</label>
                            <textarea v-model="form.notes" class="w-full rounded-lg border-gray-300" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showCreateModal = false" class="text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg" :disabled="form.processing">Create Lead</button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Email Modal -->
        <Modal :show="showBulkEmailModal" @close="showBulkEmailModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4">Send Bulk Email</h3>
                <p class="text-sm text-gray-600 mb-4">You are about to send an email to {{ bulkEmailForm.lead_ids.length }} selected leads.</p>
                <form @submit.prevent="submitBulkEmail">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input v-model="bulkEmailForm.subject" type="text" class="w-full rounded-lg border-gray-300" required />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea v-model="bulkEmailForm.content" class="w-full rounded-lg border-gray-300" rows="10" required placeholder="Use {name} for lead name..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Template variables: {name}, {company}</p>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showBulkEmailModal = false" class="text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg" :disabled="bulkEmailForm.processing">Send Bulk Emails</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
