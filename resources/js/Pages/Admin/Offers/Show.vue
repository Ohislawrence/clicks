<template>
    <AppLayout title="Offer Details">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('admin.offers.index')" class="text-sm text-blue-600 hover:text-blue-700">
                        ← Back to Offers
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ offer.name }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Offer ID #{{ offer.id }} · {{ offer.advertiser?.name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                                        <Link
                                            :href="route('admin.offers.edit', offer.id)"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                                        >
                                            Edit
                                        </Link>
                    <button
                        @click="toggleFeatured"
                        class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                        :class="offer.is_featured
                            ? 'bg-yellow-100 border-yellow-300 text-yellow-800 hover:bg-yellow-200'
                            : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        {{ offer.is_featured ? '★ Featured' : '☆ Feature' }}
                    </button>
                    <button
                        @click="toggleStatus"
                        class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                        :class="offer.is_active
                            ? 'bg-red-50 border-red-300 text-red-700 hover:bg-red-100'
                            : 'bg-green-50 border-green-300 text-green-700 hover:bg-green-100'"
                    >
                        {{ offer.is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button
                        @click="showDeleteModal = true"
                        class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ stats.total_clicks.toLocaleString() }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ stats.total_conversions.toLocaleString() }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_revenue) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Commissions</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_commissions) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Conv. Rate</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ stats.conversion_rate }}%</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Affiliates</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ stats.active_affiliates }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Offer Details -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Basic Info -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl flex-shrink-0 overflow-hidden">
                                        <img v-if="offer.thumbnail" :src="offer.thumbnail" :alt="offer.name" class="w-full h-full object-cover" />
                                        <span v-else>{{ offer.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ offer.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ offer.category?.name }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-yellow-100 text-yellow-800': offer.approval_status === 'pending',
                                                    'bg-green-100 text-green-800': offer.approval_status === 'approved',
                                                    'bg-red-100 text-red-800': offer.approval_status === 'rejected',
                                                }"
                                            >
                                                {{ offer.approval_status === 'pending' ? 'Pending Review' :
                                                   offer.approval_status === 'approved' ? 'Approved' : 'Rejected' }}
                                            </span>
                                            <span v-if="offer.approval_status === 'approved'"
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                                :class="offer.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                            >
                                                {{ offer.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span v-if="offer.is_featured" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Featured
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ offer.description }}</p>
                            <div v-if="offer.terms_and_conditions" class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Terms &amp; Conditions</p>
                                <p class="text-sm text-gray-600">{{ offer.terms_and_conditions }}</p>
                            </div>
                        </div>

                        <!-- Offer Configuration -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Offer Configuration</h4>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Model</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 uppercase">{{ offer.commission_model }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Rate</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        <span v-if="offer.commission_model === 'revshare'">{{ offer.commission_rate }}%</span>
                                        <span v-else>{{ formatCurrency(offer.commission_rate) }}</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Platform Spread</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ offer.platform_spread_percentage != null ? offer.platform_spread_percentage + '%' : '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Advertiser Payout</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        <template v-if="offer.advertiser_payout != null">
                                            <span v-if="offer.commission_model === 'revshare'">
                                                {{ offer.advertiser_payout }}% <span class="text-xs font-normal text-gray-500">of sale</span>
                                            </span>
                                            <span v-else>{{ formatCurrency(offer.advertiser_payout) }}</span>
                                        </template>
                                        <template v-else>—</template>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliate Payout</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        <template v-if="offer.affiliate_payout != null">
                                            <span v-if="offer.commission_model === 'revshare'">
                                                {{ offer.affiliate_payout }}% <span class="text-xs font-normal text-gray-500">of sale</span>
                                            </span>
                                            <span v-else>{{ formatCurrency(offer.affiliate_payout) }}</span>
                                        </template>
                                        <template v-else>—</template>
                                    </dd>
                                </div>
                                <div v-if="offer.commission_model === 'revshare'">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">RevShare Type</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ offer.revshare_type === 'recurring' ? '🔄 Recurring / Subscription' : '1️⃣ One-time' }}
                                    </dd>
                                </div>
                                <div v-if="offer.commission_model === 'revshare' && offer.revshare_type === 'recurring'">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration Cap</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ offer.revshare_recurring_duration
                                            ? offer.revshare_recurring_duration + ' ' + offer.revshare_recurring_unit + '(s)'
                                            : 'Unlimited (Lifetime)' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cookie Duration</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ offer.cookie_duration }} days</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Access Type</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 capitalize">{{ offer.access_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Preview URL</dt>
                                    <dd class="mt-1 text-sm font-semibold text-blue-600 truncate">
                                        <a :href="offer.preview_url" target="_blank" rel="noopener noreferrer">{{ offer.preview_url }}</a>
                                    </dd>
                                </div>
                                <div v-if="offer.budget_limit">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(offer.spent_budget) }} / {{ formatCurrency(offer.budget_limit) }}
                                    </dd>
                                </div>
                                <div v-if="offer.postback_url">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Postback URL</dt>
                                    <dd class="mt-1 text-sm font-mono text-gray-600 truncate">{{ offer.postback_url }}</dd>
                                </div>
                                <div v-if="offer.enable_whatsapp_tracking">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp Tracking</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ offer.whatsapp_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(offer.created_at) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Rejection Reason (if rejected) -->
                        <div v-if="offer.approval_status === 'rejected' && offer.rejection_reason" class="bg-red-50 border border-red-200 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-red-800 mb-2">Rejection Reason</h4>
                            <p class="text-sm text-red-700">{{ offer.rejection_reason }}</p>
                            <p v-if="offer.reviewer" class="mt-2 text-xs text-red-500">
                                Reviewed by {{ offer.reviewer.name }} on {{ formatDate(offer.reviewed_at) }}
                            </p>
                        </div>

                        <!-- Active Affiliates -->
                        <div v-if="offer.affiliateLinks?.length" class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">
                                Active Affiliate Links
                                <span class="ml-2 text-sm font-normal text-gray-500">({{ offer.affiliateLinks.length }} total)</span>
                            </h4>
                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="link in offer.affiliateLinks.slice(0, 10)"
                                    :key="link.id"
                                    class="py-3 flex items-center justify-between"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ link.affiliate?.name || 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500 font-mono">{{ link.tracking_code }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                            :class="link.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'"
                                        >
                                            {{ link.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Advertiser & Actions -->
                    <div class="space-y-6">

                        <!-- Advertiser Info -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Advertiser</h4>
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold flex-shrink-0">
                                    {{ offer.advertiser?.name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ offer.advertiser?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ offer.advertiser?.email }}</p>
                                </div>
                            </div>
                            <Link
                                :href="route('admin.users.show', offer.advertiser_id)"
                                class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                View Advertiser Profile
                            </Link>
                        </div>

                        <!-- Approval Actions -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Approval</h4>

                            <!-- Already reviewed banner -->
                            <div v-if="offer.approval_status !== 'pending' && offer.reviewer" class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500">
                                    Reviewed by <span class="font-medium text-gray-700">{{ offer.reviewer.name }}</span>
                                    on {{ formatDate(offer.reviewed_at) }}
                                </p>
                            </div>

                            <!-- Pending: show approve / reject -->
                            <div v-if="offer.approval_status === 'pending'" class="space-y-3">
                                <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    This offer is awaiting your review. Set the platform spread before approving.
                                </p>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Platform Spread % <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="approveForm.platform_spread_percentage"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="e.g. 10"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">The platform's cut on top of the advertiser's commission rate.</p>
                                    <p v-if="approveForm.errors.platform_spread_percentage" class="mt-1 text-xs text-red-600">{{ approveForm.errors.platform_spread_percentage }}</p>
                                </div>

                                <!-- RevShare recurring fields (only for revshare offers) -->
                                <div v-if="offer.commission_model === 'revshare'" class="pt-2 border-t border-gray-100 space-y-3">
                                    <p class="text-xs font-semibold text-purple-800">📊 RevShare Settings</p>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Commission Recurrence</label>
                                        <div class="flex gap-2">
                                            <label class="flex-1 flex items-center gap-2 cursor-pointer rounded-lg border p-2 text-xs"
                                                :class="approveForm.revshare_type === 'once' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                                <input type="radio" v-model="approveForm.revshare_type" value="once" class="accent-purple-600" />
                                                <span>One-time</span>
                                            </label>
                                            <label class="flex-1 flex items-center gap-2 cursor-pointer rounded-lg border p-2 text-xs"
                                                :class="approveForm.revshare_type === 'recurring' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                                <input type="radio" v-model="approveForm.revshare_type" value="recurring" class="accent-purple-600" />
                                                <span>Recurring</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="approveForm.revshare_type === 'recurring'" class="flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Duration Cap</label>
                                            <select v-model="approveForm.revshare_recurring_duration" class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                                <option value="">Unlimited</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="6">6</option>
                                                <option value="12">12</option>
                                                <option value="24">24</option>
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                                            <select v-model="approveForm.revshare_recurring_unit" class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                                <option value="month">Month(s)</option>
                                                <option value="year">Year(s)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    @click="approveOffer"
                                    :disabled="approveForm.processing || !approveForm.platform_spread_percentage"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 disabled:opacity-50 transition-colors"
                                >
                                    {{ approveForm.processing ? 'Approving…' : 'Approve Offer' }}
                                </button>
                                <button
                                    @click="showRejectModal = true"
                                    class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-50 transition-colors"
                                >
                                    Reject Offer
                                </button>
                            </div>

                            <!-- Approved: allow re-review -->
                            <div v-else-if="offer.approval_status === 'approved'" class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-green-800">Approved</span>
                                </div>
                                <button
                                    @click="showRejectModal = true"
                                    class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-50 transition-colors"
                                >
                                    Revoke &amp; Reject
                                </button>
                            </div>

                            <!-- Rejected: allow re-approve -->
                            <div v-else class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-red-800">Rejected</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Platform Spread % <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="approveForm.platform_spread_percentage"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        placeholder="e.g. 10"
                                    />
                                </div>

                                <!-- RevShare recurring fields (only for revshare offers) -->
                                <div v-if="offer.commission_model === 'revshare'" class="pt-2 border-t border-gray-100 space-y-3">
                                    <p class="text-xs font-semibold text-purple-800">📊 RevShare Settings</p>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Commission Recurrence</label>
                                        <div class="flex gap-2">
                                            <label class="flex-1 flex items-center gap-2 cursor-pointer rounded-lg border p-2 text-xs"
                                                :class="approveForm.revshare_type === 'once' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                                <input type="radio" v-model="approveForm.revshare_type" value="once" class="accent-purple-600" />
                                                <span>One-time</span>
                                            </label>
                                            <label class="flex-1 flex items-center gap-2 cursor-pointer rounded-lg border p-2 text-xs"
                                                :class="approveForm.revshare_type === 'recurring' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                                <input type="radio" v-model="approveForm.revshare_type" value="recurring" class="accent-purple-600" />
                                                <span>Recurring</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="approveForm.revshare_type === 'recurring'" class="flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Duration Cap</label>
                                            <select v-model="approveForm.revshare_recurring_duration" class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                                <option value="">Unlimited</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="6">6</option>
                                                <option value="12">12</option>
                                                <option value="24">24</option>
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                                            <select v-model="approveForm.revshare_recurring_unit" class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                                <option value="month">Month(s)</option>
                                                <option value="year">Year(s)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    @click="approveOffer"
                                    :disabled="approveForm.processing || !approveForm.platform_spread_percentage"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 disabled:opacity-50 transition-colors"
                                >
                                    {{ approveForm.processing ? 'Approving…' : 'Approve Offer' }}
                                </button>
                            </div>
                        </div>

                        <!-- Integration Verification -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-1">Integration Check</h4>
                            <p class="text-xs text-gray-500 mb-4">Verify the advertiser has properly set up S2S postback or pixel tracking before approving.</p>

                            <!-- Integration method badge -->
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-medium text-gray-600">Method:</span>
                                <span v-if="offer.postback_url" class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">S2S Postback</span>
                                <span v-else class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Pixel / Manual</span>
                            </div>

                            <!-- Result panel -->
                            <div v-if="integrationResult" class="mb-4 space-y-2 text-sm">
                                <!-- Overall status -->
                                <div class="flex items-center gap-2 p-2.5 rounded-lg"
                                    :class="integrationResult.integration_verified ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                                    <svg v-if="integrationResult.integration_verified" class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span :class="integrationResult.integration_verified ? 'text-green-800 font-semibold' : 'text-red-800 font-semibold'">
                                        {{ integrationResult.integration_verified ? 'Integration verified' : 'Integration not verified' }}
                                    </span>
                                </div>

                                <!-- Postback result -->
                                <div v-if="integrationResult.postback_configured" class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Postback endpoint reachable</span>
                                        <span :class="integrationResult.postback_reachable ? 'text-green-700 font-medium' : 'text-red-700 font-medium'" class="text-xs">
                                            {{ integrationResult.postback_reachable ? 'Yes' : 'No' }}
                                            <span v-if="integrationResult.postback_status_code"> ({{ integrationResult.postback_status_code }})</span>
                                        </span>
                                    </div>
                                    <div v-if="integrationResult.postback_response_snippet" class="bg-gray-50 border border-gray-200 rounded p-2">
                                        <p class="text-xs font-mono text-gray-600 break-all">{{ integrationResult.postback_response_snippet }}</p>
                                    </div>
                                </div>
                                <div v-else class="text-xs text-gray-500 italic">No postback URL configured — advertiser is using pixel tracking.</div>

                                <!-- Conversion history -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Conversions received</span>
                                    <span class="text-xs font-medium text-gray-900">{{ integrationResult.total_conversions.toLocaleString() }}</span>
                                </div>
                                <div v-if="integrationResult.last_conversion_at" class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">Last conversion</span>
                                    <span class="text-xs font-medium text-gray-900">{{ formatDate(integrationResult.last_conversion_at) }}</span>
                                </div>

                                <!-- Breakdown by method -->
                                <div v-if="integrationResult.total_conversions > 0" class="mt-1 pt-2 border-t border-gray-100 space-y-1">
                                    <p class="text-xs font-medium text-gray-500">By tracking method:</p>
                                    <div v-for="(count, method) in integrationResult.conversions_by_method" :key="method" class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 capitalize">{{ method === 'cookie' ? 'Pixel (cookie)' : method }}</span>
                                        <span class="text-xs font-medium text-gray-800">{{ count }}</span>
                                    </div>
                                </div>

                                <!-- Pixel-specific note (when no postback URL) -->
                                <div v-if="!integrationResult.postback_configured" class="mt-2 p-2.5 bg-amber-50 border border-amber-200 rounded-lg">
                                    <p class="text-xs text-amber-800 font-medium mb-1">Pixel tracking cannot be tested server-side</p>
                                    <p class="text-xs text-amber-700">To verify the pixel: open an affiliate link in a browser, complete a test purchase on the advertiser's site, then re-run this test — a "Pixel (cookie)" conversion should appear above.</p>
                                </div>
                            </div>

                            <!-- Error -->
                            <p v-if="integrationError" class="mb-3 text-xs text-red-600 bg-red-50 border border-red-200 rounded p-2">{{ integrationError }}</p>

                            <button
                                @click="runIntegrationTest"
                                :disabled="integrationLoading"
                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50 transition-colors flex items-center justify-center gap-2"
                            >
                                <svg v-if="integrationLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                {{ integrationLoading ? 'Testing…' : (integrationResult ? 'Re-run Test' : 'Run Integration Test') }}
                            </button>
                        </div>

                        <!-- Quick Links -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h4 class="text-base font-semibold text-gray-900 mb-4">Quick Links</h4>
                            <div class="space-y-2">
                                <a :href="offer.preview_url" target="_blank" rel="noopener noreferrer"
                                    class="flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>Preview URL</span>
                                </a>
                                <Link
                                    :href="route('admin.conversions.index')"
                                    class="flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-800"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>View Conversions</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creatives (full-width) -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-base font-semibold text-gray-900">
                            Creatives
                            <span class="ml-2 text-sm font-normal text-gray-500">({{ offer.creatives?.length ?? 0 }} total)</span>
                        </h4>
                    </div>

                    <div v-if="!offer.creatives?.length" class="text-center py-12 text-gray-400 text-sm">
                        No creatives uploaded for this offer yet.
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div
                            v-for="creative in offer.creatives"
                            :key="creative.id"
                            class="border border-gray-200 rounded-xl overflow-hidden flex flex-col"
                            :class="creative.is_active ? '' : 'opacity-60'"
                        >
                            <!-- Preview -->
                            <div class="bg-gray-100 aspect-video flex items-center justify-center relative overflow-hidden">
                                <img
                                    v-if="creative.file_path && ['banner', 'image'].includes(creative.type)"
                                    :src="'/storage/' + creative.file_path"
                                    :alt="creative.name"
                                    class="w-full h-full object-contain"
                                />
                                <video
                                    v-else-if="creative.file_path && creative.type === 'video'"
                                    :src="'/storage/' + creative.file_path"
                                    class="w-full h-full object-contain"
                                    muted
                                />
                                <div v-else class="flex flex-col items-center text-gray-400 px-4 text-center">
                                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-xs">{{ creative.content?.substring(0, 80) }}</span>
                                </div>
                                <!-- Status badge -->
                                <span
                                    class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-xs font-semibold"
                                    :class="creative.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                                >
                                    {{ creative.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Info -->
                            <div class="p-3 flex flex-col flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ creative.name }}</p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-medium uppercase">{{ creative.type }}</span>
                                    <span v-if="creative.format" class="text-xs text-gray-500">{{ creative.format }}</span>
                                    <span v-if="creative.width && creative.height" class="text-xs text-gray-500">{{ creative.width }}×{{ creative.height }}</span>
                                    <span v-if="creative.size" class="text-xs text-gray-400">{{ creative.size }}</span>
                                </div>
                                <p v-if="creative.clicks_count" class="mt-1 text-xs text-gray-500">{{ creative.clicks_count }} clicks</p>

                                <!-- Actions -->
                                <div class="mt-3 flex items-center gap-2">
                                    <button
                                        @click="toggleCreative(creative)"
                                        class="flex-1 px-2 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                                        :class="creative.is_active
                                            ? 'border-orange-300 text-orange-700 hover:bg-orange-50'
                                            : 'border-green-300 text-green-700 hover:bg-green-50'"
                                    >
                                        {{ creative.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        @click="confirmDeleteCreative(creative)"
                                        class="px-2 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition-colors"
                                        title="Delete creative"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <DialogModal :show="showRejectModal" @close="showRejectModal = false">
            <template #title>Reject Offer</template>
            <template #content>
                <p class="text-sm text-gray-600 mb-4">Provide a reason for rejection. The advertiser will be notified.</p>
                <textarea
                    v-model="rejectForm.rejection_reason"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm"
                    placeholder="Explain why this offer is being rejected..."
                ></textarea>
                <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-xs text-red-600">{{ rejectForm.errors.rejection_reason }}</p>
            </template>
            <template #footer>
                <SecondaryButton @click="showRejectModal = false">Cancel</SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :disabled="rejectForm.processing || !rejectForm.rejection_reason"
                    @click="rejectOffer"
                >
                    {{ rejectForm.processing ? 'Rejecting…' : 'Reject Offer' }}
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Delete Creative Confirmation Modal -->
        <DialogModal :show="showDeleteCreativeModal" @close="showDeleteCreativeModal = false">
            <template #title>Delete Creative</template>
            <template #content>
                <p class="text-sm text-gray-600">
                    Are you sure you want to permanently delete the creative
                    <strong>{{ creativeToDelete?.name }}</strong>?
                    The file will be removed from storage.
                </p>
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteCreativeModal = false">Cancel</SecondaryButton>
                <DangerButton class="ml-3" @click="deleteCreative">Delete</DangerButton>
            </template>
        </DialogModal>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Delete Offer</template>
            <template #content>
                <p class="text-sm text-gray-600">
                    Are you sure you want to permanently delete <strong>{{ offer.name }}</strong>?
                    This action cannot be undone. All associated clicks, conversions and affiliate links will also be removed.
                </p>
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">Cancel</SecondaryButton>
                <DangerButton class="ml-3" @click="deleteOffer">Delete Offer</DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    offer: Object,
    stats: Object,
});

const showRejectModal = ref(false);
const showDeleteModal = ref(false);
const showDeleteCreativeModal = ref(false);
const creativeToDelete = ref(null);

// Integration verification state
const integrationLoading = ref(false);
const integrationResult  = ref(null);
const integrationError   = ref(null);

const approveForm = useForm({
    platform_spread_percentage: props.offer.platform_spread_percentage ?? '',
    revshare_type: props.offer.revshare_type || 'once',
    revshare_recurring_duration: props.offer.revshare_recurring_duration || '',
    revshare_recurring_unit: props.offer.revshare_recurring_unit || 'month',
});

const rejectForm = useForm({
    rejection_reason: props.offer.rejection_reason ?? '',
});

const formatCurrency = (value) => {
    return '₦' + Number(value || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' });
};

const toggleStatus = () => {
    router.post(route('admin.offers.toggle', props.offer.id));
};

const toggleFeatured = () => {
    router.post(route('admin.offers.featured', props.offer.id));
};

const approveOffer = () => {
    approveForm.post(route('admin.offers.approve', props.offer.id));
};

const rejectOffer = () => {
    rejectForm.post(route('admin.offers.reject', props.offer.id), {
        onSuccess: () => { showRejectModal.value = false; },
    });
};

const deleteOffer = () => {
    router.delete(route('admin.offers.destroy', props.offer.id), {
        onSuccess: () => { showDeleteModal.value = false; },
    });
};

const toggleCreative = (creative) => {
    router.patch(route('admin.offers.creatives.toggle', { offer: props.offer.id, creative: creative.id }));
};

const confirmDeleteCreative = (creative) => {
    creativeToDelete.value = creative;
    showDeleteCreativeModal.value = true;
};

const deleteCreative = () => {
    router.delete(route('admin.offers.creatives.destroy', { offer: props.offer.id, creative: creativeToDelete.value.id }), {
        onSuccess: () => {
            showDeleteCreativeModal.value = false;
            creativeToDelete.value = null;
        },
    });
};

const runIntegrationTest = async () => {
    integrationLoading.value = true;
    integrationError.value   = null;
    integrationResult.value  = null;

    try {
        const response = await axios.post(
            route('admin.offers.verify-integration', props.offer.id),
            {},
            { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }
        );
        integrationResult.value = response.data;
    } catch (err) {
        integrationError.value = err.response?.data?.message ?? 'Failed to run test. Please try again.';
    } finally {
        integrationLoading.value = false;
    }
};
</script>
