<template>
    <AppLayout title="My Affiliate Links">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">My Affiliate Links</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage and track all your affiliate links</p>
                </div>
                <Link
                    :href="route('affiliate.offers.index')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Browse Offers
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Links Table -->
                <div v-if="links.data.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Offer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tracking Link
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stats
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Earnings
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="link in links.data" :key="link.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img
                                                    :src="link.offer?.thumbnail || '/images/placeholder.png'"
                                                    :alt="link.offer?.name"
                                                    class="h-10 w-10 rounded-lg object-cover"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ link.offer?.name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ link.offer?.commission_model.toUpperCase() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <input
                                                    :value="link.tracking_url"
                                                    readonly
                                                    class="text-sm bg-gray-50 border-gray-200 rounded px-2 py-1 w-64"
                                                />
                                                <button
                                                    @click="copyToClipboard(link.tracking_url)"
                                                    class="px-3 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition-colors text-xs font-medium"
                                                >
                                                    Copy
                                                </button>
                                                <button
                                                    v-if="link.offer?.enable_whatsapp_tracking"
                                                    @click="openWhatsAppModal(link)"
                                                    class="px-3 py-1 bg-green-100 text-green-600 rounded hover:bg-green-200 transition-colors text-xs font-medium flex items-center space-x-1"
                                                    title="Generate WhatsApp Link"
                                                >
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                    </svg>
                                                    <span>WhatsApp</span>
                                                </button>
                                            </div>
                                            <div v-if="link.discount_enabled && link.discount_percentage" class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    💰 {{ link.discount_percentage }}% Discount
                                                </span>
                                                <span class="text-xs text-gray-500">Customers save!</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <div class="text-gray-900 font-medium">{{ link.click_count }} clicks</div>
                                            <div class="text-gray-500">{{ link.conversion_count }} conversions</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(link.total_earnings) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="link.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                        >
                                            {{ link.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button
                                                @click="toggleStatus(link)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                {{ link.is_active ? 'Pause' : 'Activate' }}
                                            </button>
                                            <button
                                                @click="deleteLink(link)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="links.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                        <nav class="flex items-center justify-between">
                            <div class="hidden sm:block">
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ links.from }}</span> to
                                    <span class="font-medium">{{ links.to }}</span> of
                                    <span class="font-medium">{{ links.total }}</span> results
                                </p>
                            </div>
                            <div class="flex-1 flex justify-end">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <Link
                                        v-for="link in links.links"
                                        :key="link.label"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        ]"
                                    />
                                </nav>
                            </div>
                        </nav>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No affiliate links</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by browsing offers and generating your first link</p>
                    <div class="mt-6">
                        <Link
                            :href="route('affiliate.offers.index')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Browse Offers
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Link Modal -->
        <DialogModal :show="showWhatsAppModal" @close="closeWhatsAppModal">
            <template #title>
                WhatsApp Click-to-Chat Link
            </template>

            <template #content>
                <div v-if="whatsappLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                    <p class="mt-2 text-sm text-gray-600">Generating WhatsApp link...</p>
                </div>

                <div v-else-if="whatsappData" class="space-y-4">
                    <!-- Success Banner -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-800 font-medium">
                                WhatsApp link generated successfully!
                            </p>
                        </div>
                    </div>

                    <!-- Tracking ID -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-blue-900 mb-1">Your Tracking ID</label>
                        <div class="flex items-center space-x-2">
                            <code class="flex-1 bg-white px-3 py-2 rounded border border-blue-300 text-blue-900 font-mono text-sm">
                                {{ whatsappData.click_id }}
                            </code>
                            <button
                                @click="copyToClipboard(whatsappData.click_id)"
                                class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium"
                            >
                                Copy
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-blue-700">
                            This ID will be in the WhatsApp message. The advertiser will use it to report conversions.
                        </p>
                    </div>

                    <!-- Message Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message Preview</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ whatsappData.message_preview }}</p>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Customer will see this message in WhatsApp (they can edit before sending)
                        </p>
                    </div>

                    <!-- WhatsApp URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Link</label>
                        <div class="flex items-center space-x-2">
                            <input
                                :value="whatsappData.url"
                                readonly
                                class="flex-1 text-sm bg-gray-50 border-gray-300 rounded px-3 py-2"
                            />
                            <button
                                @click="copyToClipboard(whatsappData.url)"
                                class="px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-xs font-medium"
                            >
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <a
                            :href="whatsappData.url"
                            target="_blank"
                            class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            Open WhatsApp
                        </a>
                        <button
                            @click="shareWhatsApp"
                            class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share Link
                        </button>
                    </div>

                    <!-- How to Use -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                        <h4 class="text-sm font-medium text-yellow-900 mb-2">📱 How to Use:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-xs text-yellow-800">
                            <li>Share this WhatsApp link on social media, your blog, or directly</li>
                            <li>When someone clicks, WhatsApp opens with the pre-filled message</li>
                            <li>They chat with the advertiser and make a purchase</li>
                            <li>Advertiser reports the conversion using your tracking ID: <strong>{{ whatsappData.click_id }}</strong></li>
                            <li>You get credited automatically! 💰</li>
                        </ol>
                    </div>
                </div>
            </template>

            <template #footer>
                <button
                    @click="closeWhatsAppModal"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                >
                    Close
                </button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';

const props = defineProps({
    links: Object
});

const showWhatsAppModal = ref(false);
const whatsappLoading = ref(false);
const whatsappData = ref(null);
const currentLink = ref(null);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0
    }).format(amount);
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
};

const toggleStatus = (link) => {
    if (confirm(`Are you sure you want to ${link.is_active ? 'pause' : 'activate'} this link?`)) {
        router.patch(route('affiliate.links.toggle', link.id));
    }
};

const deleteLink = (link) => {
    if (confirm('Are you sure you want to delete this link? This action cannot be undone.')) {
        router.delete(route('affiliate.links.destroy', link.id));
    }
};

const openWhatsAppModal = async (link) => {
    currentLink.value = link;
    showWhatsAppModal.value = true;
    whatsappLoading.value = true;
    whatsappData.value = null;

    try {
        const response = await fetch(route('affiliate.links.whatsapp', link.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        const data = await response.json();

        if (data.whatsapp_data) {
            whatsappData.value = data.whatsapp_data;
        } else {
            alert('Failed to generate WhatsApp link');
            closeWhatsAppModal();
        }
    } catch (error) {
        console.error('Error generating WhatsApp link:', error);
        alert('An error occurred. Please try again.');
        closeWhatsAppModal();
    } finally {
        whatsappLoading.value = false;
    }
};

const closeWhatsAppModal = () => {
    showWhatsAppModal.value = false;
    whatsappData.value = null;
    currentLink.value = null;
};

const shareWhatsApp = () => {
    if (whatsappData.value && navigator.share) {
        navigator.share({
            title: currentLink.value.offer?.name || 'Check this out!',
            text: 'Click here to chat on WhatsApp:',
            url: whatsappData.value.url
        }).catch((error) => {
            // Fallback to copy
            copyToClipboard(whatsappData.value.url);
        });
    } else {
        copyToClipboard(whatsappData.value.url);
    }
};
</script>
