<template>
    <AppLayout title="Offer Creatives">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('advertiser.offers.show', offer.id)" class="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-block">
                        ← Back to Offer
                    </Link>
                    <h2 class="text-2xl font-bold text-gray-900">Creatives - {{ offer.name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Upload and manage promotional materials for affiliates</p>
                </div>
                <button
                    @click="showAddModal = true"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors"
                >
                    + Add Creative
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Info Banner -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Creatives help affiliates promote your offers effectively:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li><strong>Banners</strong> - Display ads for websites (JPG, PNG, GIF)</li>
                                <li><strong>Images</strong> - Social media graphics and product photos</li>
                                <li><strong>Text Ads</strong> - Pre-written promotional copy</li>
                                <li><strong>Videos</strong> - Short promotional videos (MP4, MOV)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Creatives Grid -->
                <div v-if="creatives.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="creative in creatives"
                        :key="creative.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow"
                    >
                        <!-- Preview -->
                        <div class="relative bg-gray-100 aspect-video flex items-center justify-center">
                            <img
                                v-if="creative.type === 'banner' || creative.type === 'image'"
                                :src="creative.file_url"
                                :alt="creative.name"
                                class="w-full h-full object-contain"
                            />
                            <div v-else-if="creative.type === 'video'" class="relative w-full h-full">
                                <video
                                    :src="creative.file_url"
                                    class="w-full h-full object-contain"
                                    controls
                                />
                            </div>
                            <div v-else class="p-6 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Text Ad</p>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-2 right-2">
                                <span :class="[
                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                    creative.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ creative.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Type Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                    {{ creative.type }}
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ creative.name }}</h3>
                            
                            <!-- Info Grid -->
                            <div class="space-y-2 text-sm text-gray-600">
                                <div v-if="creative.dimensions" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ creative.dimensions }}
                                </div>
                                <div v-if="creative.size" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    {{ creative.size }}
                                </div>
                                <div v-if="creative.format" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ creative.format }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    {{ creative.clicks_count }} clicks
                                </div>
                            </div>

                            <!-- Content Preview (for text ads) -->
                            <div v-if="creative.type === 'text' && creative.content" class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700 line-clamp-3">{{ creative.content }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <button
                                    @click="viewCreative(creative)"
                                    class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    View
                                </button>
                                <button
                                    @click="editCreative(creative)"
                                    class="px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="toggleCreativeStatus(creative)"
                                    :class="[
                                        'px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                                        creative.is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'
                                    ]"
                                >
                                    {{ creative.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button
                                    @click="confirmDelete(creative)"
                                    class="px-3 py-2 bg-red-100 text-red-800 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No creatives yet</h3>
                    <p class="text-gray-600 mb-4">Add your first creative to help affiliates promote your offer</p>
                    <button
                        @click="showAddModal = true"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-colors"
                    >
                        Add Creative
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Creative Modal -->
        <DialogModal :show="showAddModal" @close="closeAddModal">
            <template #title>
                Add New Creative
            </template>

            <template #content>
                <form @submit.prevent="submitCreative" class="space-y-4">
                    <!-- Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Creative Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                v-for="type in creativeTypes"
                                :key="type.value"
                                @click="creativeForm.type = type.value"
                                :class="[
                                    'p-4 border-2 rounded-lg text-left transition-colors',
                                    creativeForm.type === type.value
                                        ? 'border-blue-600 bg-blue-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <div class="font-medium text-gray-900">{{ type.label }}</div>
                                <div class="text-sm text-gray-500">{{ type.description }}</div>
                            </button>
                        </div>
                        <div v-if="creativeForm.errors.type" class="text-red-600 text-sm mt-1">{{ creativeForm.errors.type }}</div>
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Creative Name</label>
                        <input
                            v-model="creativeForm.name"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g., Homepage Banner 728x90"
                        />
                        <div v-if="creativeForm.errors.name" class="text-red-600 text-sm mt-1">{{ creativeForm.errors.name }}</div>
                    </div>

                    <!-- File Upload (for banner, image, video) -->
                    <div v-if="creativeForm.type !== 'text'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept="image/*,video/*"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            Max 10MB. Supported: JPG, PNG, GIF, MP4, MOV
                        </p>
                        <div v-if="creativeForm.errors.file" class="text-red-600 text-sm mt-1">{{ creativeForm.errors.file }}</div>
                    </div>

                    <!-- Text Content (for text ads) -->
                    <div v-if="creativeForm.type === 'text'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ad Copy</label>
                        <textarea
                            v-model="creativeForm.content"
                            rows="6"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Enter promotional text that affiliates can use..."
                        ></textarea>
                        <div v-if="creativeForm.errors.content" class="text-red-600 text-sm mt-1">{{ creativeForm.errors.content }}</div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="closeAddModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    @click="submitCreative"
                    :disabled="creativeForm.processing"
                >
                    <span v-if="creativeForm.processing">Uploading...</span>
                    <span v-else>Add Creative</span>
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- View Creative Modal -->
        <DialogModal :show="showViewModal" @close="showViewModal = false" max-width="4xl">
            <template #title>
                {{ selectedCreative?.name }}
            </template>

            <template #content>
                <div v-if="selectedCreative" class="space-y-4">
                    <!-- Preview -->
                    <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center min-h-[300px]">
                        <img
                            v-if="selectedCreative.type === 'banner' || selectedCreative.type === 'image'"
                            :src="selectedCreative.file_url"
                            :alt="selectedCreative.name"
                            class="max-w-full max-h-[500px] object-contain"
                        />
                        <video
                            v-else-if="selectedCreative.type === 'video'"
                            :src="selectedCreative.file_url"
                            class="max-w-full max-h-[500px]"
                            controls
                        />
                        <div v-else class="text-center p-8">
                            <p class="text-lg font-medium text-gray-900 mb-4">Text Ad Copy:</p>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ selectedCreative.content }}</p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Type:</span>
                            <span class="ml-2 text-gray-900 uppercase">{{ selectedCreative.type }}</span>
                        </div>
                        <div v-if="selectedCreative.dimensions">
                            <span class="font-medium text-gray-700">Dimensions:</span>
                            <span class="ml-2 text-gray-900">{{ selectedCreative.dimensions }}</span>
                        </div>
                        <div v-if="selectedCreative.size">
                            <span class="font-medium text-gray-700">File Size:</span>
                            <span class="ml-2 text-gray-900">{{ selectedCreative.size }}</span>
                        </div>
                        <div v-if="selectedCreative.format">
                            <span class="font-medium text-gray-700">Format:</span>
                            <span class="ml-2 text-gray-900">{{ selectedCreative.format }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Clicks:</span>
                            <span class="ml-2 text-gray-900">{{ selectedCreative.clicks_count }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Status:</span>
                            <span :class="[
                                'ml-2 px-2 py-1 text-xs font-semibold rounded-full',
                                selectedCreative.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            ]">
                                {{ selectedCreative.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Copy URL -->
                    <div v-if="selectedCreative.file_url">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File URL</label>
                        <div class="flex">
                            <input
                                :value="selectedCreative.file_url"
                                readonly
                                class="flex-1 rounded-l-lg border-gray-300 bg-gray-50 text-sm"
                            />
                            <button
                                @click="copyUrl(selectedCreative.file_url)"
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors"
                            >
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showViewModal = false">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>

        <!-- Edit Creative Modal -->
        <DialogModal :show="showEditModal" @close="closeEditModal">
            <template #title>
                Edit Creative
            </template>

            <template #content>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Creative Name</label>
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                            placeholder="e.g., Homepage Banner 728x90"
                        />
                        <div v-if="editForm.errors.name" class="text-red-600 text-sm mt-1">{{ editForm.errors.name }}</div>
                    </div>

                    <!-- Text Content (for text ads) -->
                    <div v-if="selectedCreative?.type === 'text'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ad Copy</label>
                        <textarea
                            v-model="editForm.content"
                            rows="6"
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                            placeholder="Enter promotional text..."
                        ></textarea>
                        <div v-if="editForm.errors.content" class="text-red-600 text-sm mt-1">{{ editForm.errors.content }}</div>
                    </div>

                    <!-- Replace File (for non-text types) -->
                    <div v-if="selectedCreative?.type !== 'text'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Replace File (Optional)</label>
                        <input
                            type="file"
                            @change="handleEditFileChange"
                            accept="image/*,video/*"
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            Leave empty to keep current file. Max 10MB.
                        </p>
                        <div v-if="editForm.errors.file" class="text-red-600 text-sm mt-1">{{ editForm.errors.file }}</div>
                    </div>

                    <!-- Status Toggle -->
                    <div>
                        <label class="flex items-center">
                            <input
                                v-model="editForm.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            />
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="closeEditModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    @click="submitEdit"
                    :disabled="editForm.processing"
                >
                    <span v-if="editForm.processing">Updating...</span>
                    <span v-else>Update Creative</span>
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                Delete Creative
            </template>

            <template #content>
                Are you sure you want to delete this creative? This action cannot be undone.
            </template>

            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    @click="deleteCreative"
                    :disabled="deleteForm.processing"
                >
                    <span v-if="deleteForm.processing">Deleting...</span>
                    <span v-else>Delete</span>
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    offer: Object,
    creatives: Array,
});

const showAddModal = ref(false);
const showViewModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedCreative = ref(null);

const creativeTypes = [
    { value: 'banner', label: 'Banner', description: 'Display ads for websites' },
    { value: 'image', label: 'Image', description: 'Social media graphics' },
    { value: 'text', label: 'Text Ad', description: 'Pre-written copy' },
    { value: 'video', label: 'Video', description: 'Promotional videos' },
];

const creativeForm = useForm({
    type: 'image',
    name: '',
    file: null,
    content: '',
});

const deleteForm = useForm({});

const editForm = useForm({
    name: '',
    content: '',
    file: null,
    is_active: true,
});

const handleFileChange = (event) => {
    creativeForm.file = event.target.files[0];
};

const submitCreative = () => {
    creativeForm.post(route('advertiser.creatives.store', props.offer.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddModal();
        },
    });
};

const closeAddModal = () => {
    showAddModal.value = false;
    creativeForm.reset();
};

const viewCreative = (creative) => {
    selectedCreative.value = creative;
    showViewModal.value = true;
};

const toggleCreativeStatus = (creative) => {
    router.patch(route('advertiser.creatives.toggle', [props.offer.id, creative.id]), {}, {
        preserveScroll: true,
    });
};

const editCreative = (creative) => {
    selectedCreative.value = creative;
    editForm.name = creative.name;
    editForm.content = creative.content || '';
    editForm.is_active = creative.is_active;
    editForm.file = null;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.reset();
    selectedCreative.value = null;
};

const handleEditFileChange = (event) => {
    editForm.file = event.target.files[0];
};

const submitEdit = () => {
    editForm.post(route('advertiser.creatives.update', [props.offer.id, selectedCreative.value.id]), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const confirmDelete = (creative) => {
    selectedCreative.value = creative;
    showDeleteModal.value = true;
};

const deleteCreative = () => {
    deleteForm.delete(route('advertiser.creatives.destroy', [props.offer.id, selectedCreative.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedCreative.value = null;
        },
    });
};

const copyUrl = (url) => {
    navigator.clipboard.writeText(url);
    alert('URL copied to clipboard!');
};
</script>
