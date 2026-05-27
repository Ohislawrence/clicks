<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    lead: Object,
});

const showEmailModal = ref(false);
const showEditModal = ref(false);

const emailForm = useForm({
    subject: '',
    content: '',
});

const editForm = useForm({
    name: props.lead.name,
    email: props.lead.email,
    phone: props.lead.phone,
    company: props.lead.company,
    website: props.lead.website,
    status: props.lead.status,
    source: props.lead.source,
    notes: props.lead.notes,
});

const noteForm = useForm({
    note: '',
});

const submitEmail = () => {
    emailForm.post(route('admin.crm.send-email', props.lead.id), {
        onSuccess: () => {
            showEmailModal.value = false;
            emailForm.reset();
        },
    });
};

const submitEdit = () => {
    editForm.put(route('admin.crm.update', props.lead.id), {
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};

const submitNote = () => {
    noteForm.post(route('admin.crm.add-note', props.lead.id), {
        onSuccess: () => {
            noteForm.reset();
        },
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};
</script>

<template>
    <AppLayout :title="'Lead: ' + lead.name">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link :href="route('admin.crm.index')" class="mr-4 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ lead.name }}</h2>
                        <p class="text-sm text-gray-600">{{ lead.company || 'No Company' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button @click="showEditModal = true" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Edit Lead</button>
                    <button @click="showEmailModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Send Email</button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Lead info -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6 mb-8">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2 text-gray-900">Contact Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Email</label>
                                    <p class="text-gray-900">{{ lead.email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Phone</label>
                                    <p class="text-gray-900">{{ lead.phone || 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Website</label>
                                    <p v-if="lead.website" class="text-blue-600">
                                        <a :href="lead.website" target="_blank">{{ lead.website }}</a>
                                    </p>
                                    <p v-else class="text-gray-900">N/A</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Status</label>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                            'bg-blue-100 text-blue-800': lead.status === 'new',
                                            'bg-yellow-100 text-yellow-800': lead.status === 'contacted',
                                            'bg-green-100 text-green-800': lead.status === 'interested',
                                            'bg-purple-100 text-purple-800': lead.status === 'converted',
                                            'bg-red-100 text-red-800': lead.status === 'rejected',
                                        }">
                                            {{ lead.status.toUpperCase() }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Source</label>
                                    <p class="text-gray-900">{{ lead.source || 'Direct' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold">Added On</label>
                                    <p class="text-gray-900">{{ formatDate(lead.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2 text-gray-900">Quick Note</h3>
                            <form @submit.prevent="submitNote">
                                <textarea v-model="noteForm.note" class="w-full rounded-lg border-gray-300 mb-4" rows="3" placeholder="Add a note..."></textarea>
                                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg text-sm font-medium" :disabled="noteForm.processing">Add Note</button>
                            </form>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm p-6 overflow-hidden">
                            <h3 class="text-lg font-bold mb-6 text-gray-900">Activity History</h3>
                            <div class="flow-root">
                                <ul v-if="lead.activities.length > 0" role="list" class="-mb-8">
                                    <li v-for="(activity, idx) in lead.activities" :key="activity.id">
                                        <div class="relative pb-8">
                                            <span v-if="idx !== lead.activities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white" :class="{
                                                        'bg-gray-400': activity.type === 'note',
                                                        'bg-blue-500': activity.type === 'email',
                                                        'bg-green-500': activity.type === 'status_change',
                                                    }">
                                                        <svg v-if="activity.type === 'email'" class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        <svg v-else-if="activity.type === 'note'" class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <svg v-else class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            {{ activity.description }}
                                                            <span class="font-medium text-gray-900" v-if="activity.user">by {{ activity.user.name }}</span>
                                                        </p>
                                                        <div v-if="activity.metadata && activity.metadata.content" class="mt-2 text-sm text-gray-700 bg-gray-50 p-3 rounded italic">
                                                            "{{ activity.metadata.subject }}"
                                                        </div>
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                        {{ formatDate(activity.created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div v-else class="text-center py-12 text-gray-500">
                                    No activity recorded yet.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4">Edit Lead</h3>
                <form @submit.prevent="submitEdit">
                    <!-- Similar to Create Lead Form with value prefilled from editForm -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input v-model="editForm.name" type="text" class="w-full rounded-lg border-gray-300" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="editForm.email" type="email" class="w-full rounded-lg border-gray-300" required />
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="editForm.status" class="w-full rounded-lg border-gray-300">
                                <option value="new">New</option>
                                <option value="contacted">Contacted</option>
                                <option value="interested">Interested</option>
                                <option value="converted">Converted</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <input v-model="editForm.company" type="text" class="w-full rounded-lg border-gray-300" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false" class="text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg" :disabled="editForm.processing">Save Changes</button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Send Email Modal -->
        <Modal :show="showEmailModal" @close="showEmailModal = false">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4">Send Personalized Email</h3>
                <form @submit.prevent="submitEmail">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input v-model="emailForm.subject" type="text" class="w-full rounded-lg border-gray-300" required />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
                        <textarea v-model="emailForm.content" class="w-full rounded-lg border-gray-300" rows="8" required placeholder="Hi {{ lead.name }}, ..."></textarea>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showEmailModal = false" class="text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg" :disabled="emailForm.processing">Send Email</button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>
