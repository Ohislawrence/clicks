<template>
    <AppLayout :title="`Wallet — ${advertiser.name}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.wallets.index')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ advertiser.name }}'s Wallet</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ advertiser.email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="showCreditModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        Credit Wallet
                    </button>
                    <button @click="showDebitModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                        Debit Wallet
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Balance Card -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-8 text-white">
                    <p class="text-blue-100 text-sm font-medium">Current Wallet Balance</p>
                    <p class="text-5xl font-bold mt-2">{{ formatCurrency(balance) }}</p>
                </div>

                <!-- Offer Budget Summary -->
                <div v-if="offerBudgetSummary.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Campaign Budget Breakdown</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget Limit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spent</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="offer in offerBudgetSummary" :key="offer.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ offer.name }}</div>
                                        <div v-if="offer.pause_reason" class="text-xs text-red-500 mt-0.5">Paused: {{ offer.pause_reason }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                            :class="offer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ offer.is_active ? 'Active' : 'Paused' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(offer.budget_limit) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">{{ formatCurrency(offer.spent_budget) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">{{ formatCurrency(offer.remaining_budget) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ offer.total_conversions }}
                                        <span v-if="offer.total_conversion_cap"> / {{ offer.total_conversion_cap }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Transaction History</h3>
                    </div>
                    <div v-if="transactions.data.length > 0" class="divide-y divide-gray-100">
                        <div v-for="txn in transactions.data" :key="txn.id" class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-start gap-4">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                                    :class="isCredit(txn.type) ? 'bg-green-100' : 'bg-red-100'">
                                    <svg class="w-4 h-4" :class="isCredit(txn.type) ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isCredit(txn.type) ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3'" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ formatType(txn.type) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ txn.description }}</p>
                                    <p v-if="txn.reference" class="text-xs font-mono text-gray-400 mt-0.5">{{ txn.reference }}</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-bold" :class="isCredit(txn.type) ? 'text-green-600' : 'text-red-600'">
                                    {{ isCredit(txn.type) ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                                </p>
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="statusBadgeClass(txn.status)">{{ txn.status }}</span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(txn.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center text-sm text-gray-500">No transactions yet.</div>

                    <!-- Pagination -->
                    <div v-if="transactions.data.length > 0 && transactions.links" class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                        <component
                            v-for="(link, i) in transactions.links"
                            :key="i"
                            :is="link.url ? Link : 'span'"
                            :href="link.url || undefined"
                            :class="['px-3 py-1.5 rounded-lg text-sm', link.active ? 'bg-blue-600 text-white' : link.url ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed']"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Modal -->
        <DialogModal :show="showCreditModal" @close="showCreditModal = false">
            <template #title>Credit Wallet</template>
            <template #content>
                <div class="space-y-4">
                    <p class="text-sm text-gray-600">Manually add funds to <strong>{{ advertiser.name }}</strong>'s wallet (e.g. bank transfer received).</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                        <input v-model="creditForm.amount" type="number" min="1" step="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 50000" />
                        <p v-if="creditForm.errors.amount" class="text-red-600 text-sm mt-1">{{ creditForm.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <input v-model="creditForm.note" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Bank transfer ref: ABC123" />
                        <p v-if="creditForm.errors.note" class="text-red-600 text-sm mt-1">{{ creditForm.errors.note }}</p>
                    </div>
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="showCreditModal = false">Cancel</SecondaryButton>
                <button @click="submitCredit" :disabled="creditForm.processing" class="ml-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium disabled:opacity-50">
                    Credit Wallet
                </button>
            </template>
        </DialogModal>

        <!-- Debit Modal -->
        <DialogModal :show="showDebitModal" @close="showDebitModal = false">
            <template #title>Debit Wallet</template>
            <template #content>
                <div class="space-y-4">
                    <p class="text-sm text-gray-600">Manually remove funds from <strong>{{ advertiser.name }}</strong>'s wallet. Current balance: <strong>{{ formatCurrency(balance) }}</strong>.</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                        <input v-model="debitForm.amount" type="number" min="1" step="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 10000" />
                        <p v-if="debitForm.errors.amount" class="text-red-600 text-sm mt-1">{{ debitForm.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <input v-model="debitForm.note" type="text" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Chargeback from campaign" />
                        <p v-if="debitForm.errors.note" class="text-red-600 text-sm mt-1">{{ debitForm.errors.note }}</p>
                    </div>
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="showDebitModal = false">Cancel</SecondaryButton>
                <button @click="submitDebit" :disabled="debitForm.processing" class="ml-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium disabled:opacity-50">
                    Debit Wallet
                </button>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    advertiser: Object,
    balance: Number,
    transactions: Object,
    offerBudgetSummary: Array,
});

const showCreditModal = ref(false);
const showDebitModal = ref(false);

const creditForm = useForm({ amount: '', note: '' });
const debitForm = useForm({ amount: '', note: '' });

const submitCredit = () => {
    creditForm.post(route('admin.wallets.credit', props.advertiser.id), {
        onSuccess: () => { showCreditModal.value = false; creditForm.reset(); },
    });
};

const submitDebit = () => {
    debitForm.post(route('admin.wallets.debit', props.advertiser.id), {
        onSuccess: () => { showDebitModal.value = false; debitForm.reset(); },
    });
};

const formatCurrency = (amount) =>
    new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount ?? 0);

const formatDate = (date) =>
    new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const formatType = (type) => ({
    deposit: 'Deposit',
    offer_allocation: 'Offer Allocation',
    offer_topup: 'Offer Top-up',
    offer_refund: 'Refund',
}[type] ?? type);

const isCredit = (type) => ['deposit', 'offer_refund'].includes(type);

const statusBadgeClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-800');
</script>
