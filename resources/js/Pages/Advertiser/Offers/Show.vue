<template>
    <AppLayout title="Offer Details">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ offer.name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage offer details and track performance</p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('advertiser.creatives.index', offer.id)"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        Manage Creatives
                    </Link>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Performance Stats -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.totalClicks.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Conversions</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.totalConversions.toLocaleString() }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatCurrency(stats.totalRevenue) }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.conversionRate }}%</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Avg. Commission</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatCurrency(stats.averageCommission) }}</p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Status Alert -->
                <div v-if="offer.approval_status === 'pending'" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Pending Admin Approval</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                This offer is currently pending admin review. Once approved, it will be visible to affiliates. You will receive an email notification when reviewed.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else-if="offer.approval_status === 'rejected'" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">Offer Rejected</h3>
                            <p class="mt-1 text-sm text-red-700">
                                This offer was rejected by {{ offer.reviewer ? offer.reviewer.name : 'admin' }} on {{ new Date(offer.reviewed_at).toLocaleDateString() }}.
                            </p>
                            <div v-if="offer.rejection_reason" class="mt-2 p-3 bg-white rounded-lg border border-red-200">
                                <p class="text-sm font-medium text-red-800 mb-1">Rejection Reason:</p>
                                <p class="text-sm text-red-700 whitespace-pre-wrap">{{ offer.rejection_reason }}</p>
                            </div>
                            <div class="mt-3">
                                <div class="rounded-md border border-red-300 bg-white p-4">
                                    <p class="text-sm text-red-700">
                                        Offer editing is disabled from the advertiser dashboard. Please contact admin for any changes or resubmission.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="offer.approval_status === 'approved'" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Offer Approved</h3>
                            <p class="mt-1 text-sm text-green-700">
                                This offer has been approved{{ offer.reviewer ? ' by ' + offer.reviewer.name : '' }}{{ offer.reviewed_at ? ' on ' + new Date(offer.reviewed_at).toLocaleDateString() : '' }}. It is {{ offer.is_active ? 'currently visible to affiliates' : 'currently inactive' }}.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Offer Details -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Offer Details
                        </h3>
                        <span :class="[
                            'px-3 py-1 rounded-full text-sm font-medium',
                            offer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        ]">
                            {{ offer.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Offer Name</label>
                                <p class="text-gray-900">{{ offer.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ offer.category.name }}
                                </span>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ offer.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Commission Model</label>
                                <p class="text-gray-900 uppercase">{{ offer.commission_model }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Commission Rate</label>
                                <p class="text-gray-900 font-semibold text-lg">
                                    <span v-if="offer.commission_model === 'revshare'">{{ offer.commission_rate }}%</span>
                                    <span v-else>{{ formatCurrency(offer.commission_rate) }}</span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cookie Duration</label>
                                <p class="text-gray-900">{{ offer.cookie_duration }} days</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Access Type</label>
                                <span :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                    offer.access_type === 'open' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ offer.access_type === 'open' ? 'Open Access' : 'Request Required' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview URL</label>
                                <a :href="offer.preview_url" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                                    {{ offer.preview_url }}
                                </a>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Destination / Offer URL</label>
                                <p v-if="offer.offer_url" class="text-gray-900 break-all">
                                    <a :href="offer.offer_url" target="_blank" class="text-blue-600 hover:text-blue-800">{{ offer.offer_url }}</a>
                                </p>
                                <p v-else class="text-gray-500">Not set — customers will be redirected to the preview URL.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Wallet Required</label>
                                <p class="text-gray-900">{{ offer.minimum_wallet_required ? formatCurrency(offer.minimum_wallet_required) : 'Not configured' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Allocated Budget</label>
                                <p class="text-gray-900">{{ offer.budget_limit ? formatCurrency(offer.budget_limit) : 'None' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spent Budget</label>
                                <p class="text-gray-900">{{ offer.spent_budget ? formatCurrency(offer.spent_budget) : formatCurrency(0) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remaining Budget</label>
                                <p class="text-gray-900">
                                    {{ offer.budget_limit ? formatCurrency(offer.budget_limit - (offer.spent_budget || 0)) : 'Unlimited' }}
                                </p>
                            </div>

                            <div v-if="offer.terms_and_conditions" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ offer.terms_and_conditions }}</p>
                            </div>

                            <div v-if="offer.terms_and_conditions" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ offer.terms_and_conditions }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Created At</label>
                                <p class="text-gray-900">{{ formatDate(offer.created_at) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Updated</label>
                                <p class="text-gray-900">{{ formatDate(offer.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-cyan-600 to-sky-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm8 3a1 1 0 110 2 1 1 0 010-2zm-4 0a1 1 0 110 2 1 1 0 010-2zm8 8H6v-2h10v2z"/></svg>
                            Targeting Details
                        </h3>
                    </div>
                    <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destination / Offer URL</label>
                            <p v-if="offer.offer_url" class="text-gray-900 break-all">
                                <a :href="offer.offer_url" target="_blank" class="text-blue-600 hover:text-blue-800">{{ offer.offer_url }}</a>
                            </p>
                            <p v-else class="text-gray-500">Not set. The link will fall back to the preview URL.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Countries</label>
                            <p v-if="offer.target_countries && offer.target_countries.length" class="text-gray-900">
                                {{ offer.target_countries.join(', ') }}
                            </p>
                            <p v-else class="text-gray-500">All countries</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Devices</label>
                            <p v-if="offer.target_devices && offer.target_devices.length" class="text-gray-900">
                                {{ offer.target_devices.map(d => capitalize(d)).join(', ') }}
                            </p>
                            <p v-else class="text-gray-500">All devices</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Target Operating Systems</label>
                            <p v-if="offer.target_os && offer.target_os.length" class="text-gray-900">
                                {{ offer.target_os.map(o => capitalize(o)).join(', ') }}
                            </p>
                            <p v-else class="text-gray-500">All operating systems</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unique IP Enforcement</label>
                            <p class="text-gray-900">{{ offer.require_unique_ip ? 'Enabled' : 'Disabled' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Daily Conversion Cap</label>
                            <p class="text-gray-900">{{ offer.daily_conversion_cap ?? 'No daily cap' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Conversion Cap</label>
                            <p class="text-gray-900">{{ offer.monthly_conversion_cap ?? 'No monthly cap' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Access Requests (if applicable) -->
                <!-- Tracking Integration Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-900 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            Tracking Integration
                        </h3>
                        <Link :href="route('advertiser.documentation.index')" class="text-sm text-blue-300 hover:text-white flex items-center gap-1">
                            📘 Full Documentation
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </Link>
                    </div>

                    <div class="px-6 py-6 space-y-6">

                        <!-- RevShare: S2S only notice -->
                        <div v-if="offer.commission_model === 'revshare'" class="flex gap-3 bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <span class="text-2xl flex-shrink-0">📊</span>
                            <div class="text-sm text-purple-800">
                                <p class="font-semibold mb-1">Revenue Share — Server-to-Server (S2S) only</p>
                                <p>The pixel tracking method is not supported for RevShare offers because it cannot guarantee accurate sale amounts. You <strong>must</strong> fire the postback from your server on every payment confirmation.</p>
                                <p v-if="offer.revshare_type === 'recurring'" class="mt-2">
                                    This is a <strong>recurring</strong> offer. Pass <code class="bg-purple-100 px-1 rounded">customer_id</code> (your subscriber ID) in every postback so the platform can link renewal payments to the original affiliate attribution.
                                </p>
                            </div>
                        </div>

                        <!-- Pixel tracking (PPS / PPL only) -->
                        <div v-if="offer.commission_model !== 'revshare'" class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🖼️</span>
                                <h4 class="font-semibold text-gray-800">Method 1 — Tracking Pixel <span class="ml-2 text-xs font-normal bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Recommended for most</span></h4>
                            </div>
                            <p class="text-sm text-gray-600">Place this 1×1 pixel image on your <strong>order confirmation / thank-you page</strong>. It fires automatically when the customer's browser loads the page.</p>

                            <div v-if="offer.commission_model === 'ppl'" class="text-xs text-green-700 bg-green-50 border border-green-200 rounded px-3 py-2">
                                📋 <strong>PPL:</strong> <code>value</code> is optional — a lead commission fires even without a sale amount.
                            </div>
                            <div v-else class="text-xs text-orange-700 bg-orange-50 border border-orange-200 rounded px-3 py-2">
                                🛒 <strong>PPS:</strong> <code>value</code> must be set to the order total — zero-value pixels are rejected.
                            </div>

                            <!-- WooCommerce -->
                            <div>
                                <div class="bg-purple-100 px-3 py-1 rounded-t text-xs font-semibold text-purple-900">WooCommerce (PHP)</div>
                                <div class="relative">
                                    <code class="block bg-gray-900 text-green-300 px-4 py-3 rounded-b text-xs font-mono overflow-x-auto whitespace-pre">&lt;img src="{{ pixelBase }}?value=&lt;?php echo $order-&gt;get_total(); ?&gt;&amp;txn_id=&lt;?php echo $order-&gt;get_order_number(); ?&gt;" width="1" height="1" style="display:none" /&gt;</code>
                                    <button @click="copyToClipboard(pixelWoocommerce, 'pixel-woo')" class="absolute top-2 right-2 px-2 py-1 text-xs bg-gray-700 text-gray-200 rounded hover:bg-gray-600">
                                        {{ copied === 'pixel-woo' ? '✓' : 'Copy' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Shopify -->
                            <div>
                                <div class="bg-green-100 px-3 py-1 rounded-t text-xs font-semibold text-green-900">Shopify (Liquid)</div>
                                <div class="relative">
                                    <code class="block bg-gray-900 text-green-300 px-4 py-3 rounded-b text-xs font-mono overflow-x-auto whitespace-pre">&lt;img src="{{ pixelBase }}?value=&#123;&#123; order.total_price &#125;&#125;&amp;txn_id=&#123;&#123; order.order_number &#125;&#125;" width="1" height="1" style="display:none" /&gt;</code>
                                    <button @click="copyToClipboard(pixelShopify, 'pixel-shopify')" class="absolute top-2 right-2 px-2 py-1 text-xs bg-gray-700 text-gray-200 rounded hover:bg-gray-600">
                                        {{ copied === 'pixel-shopify' ? '✓' : 'Copy' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Generic HTML -->
                            <div>
                                <div class="bg-blue-100 px-3 py-1 rounded-t text-xs font-semibold text-blue-900">Generic HTML</div>
                                <div class="relative">
                                    <code class="block bg-gray-900 text-green-300 px-4 py-3 rounded-b text-xs font-mono overflow-x-auto whitespace-pre">&lt;img src="{{ pixelBase }}?value=ORDER_TOTAL&amp;txn_id=ORDER_ID" width="1" height="1" style="display:none" /&gt;</code>
                                    <button @click="copyToClipboard(pixelGeneric, 'pixel-html')" class="absolute top-2 right-2 px-2 py-1 text-xs bg-gray-700 text-gray-200 rounded hover:bg-gray-600">
                                        {{ copied === 'pixel-html' ? '✓' : 'Copy' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Divider between pixel and S2S -->
                        <div v-if="offer.commission_model !== 'revshare'" class="border-t border-gray-200"></div>

                        <!-- S2S Postback -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🔄</span>
                                <h4 class="font-semibold text-gray-800">
                                    {{ offer.commission_model === 'revshare' ? 'Server-to-Server (S2S) Postback' : 'Method 2 — Server-to-Server (S2S) Postback' }}
                                    <span v-if="offer.commission_model !== 'revshare'" class="ml-2 text-xs font-normal bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Advanced / most accurate</span>
                                    <span v-else class="ml-2 text-xs font-normal bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Required</span>
                                </h4>
                            </div>
                            <p class="text-sm text-gray-600">Fire this request from your server when a payment is confirmed. Unlike the pixel, it is not affected by ad-blockers or browser privacy settings.</p>

                            <!-- Endpoint -->
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Endpoint</p>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 bg-gray-900 text-green-300 px-3 py-2 rounded text-xs font-mono break-all">POST {{ postbackBase }}</code>
                                    <button @click="copyToClipboard(postbackBase, 'postback-url')" class="flex-shrink-0 px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                        {{ copied === 'postback-url' ? '✓' : 'Copy' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Parameters table -->
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Parameter</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Required</th>
                                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-blue-700">tracking_code</td>
                                            <td class="px-3 py-2 text-green-600 font-medium">Yes</td>
                                            <td class="px-3 py-2 text-gray-600">From <code>?tracking_code=</code> in the affiliate URL when customer arrived</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="px-3 py-2 font-mono text-blue-700">token</td>
                                            <td class="px-3 py-2 text-green-600 font-medium">Yes</td>
                                            <td class="px-3 py-2 text-gray-600">Your secret token (see below)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-blue-700">conversion_value</td>
                                            <td class="px-3 py-2" :class="offer.commission_model === 'ppl' ? 'text-gray-500' : 'text-green-600 font-medium'">{{ offer.commission_model === 'ppl' ? 'Optional' : 'Yes (> 0)' }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ offer.commission_model === 'ppl' ? 'Lead value — can be 0 or omitted' : 'Order/sale amount — must be greater than zero' }}</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="px-3 py-2 font-mono text-blue-700">transaction_id</td>
                                            <td class="px-3 py-2 text-gray-500">Recommended</td>
                                            <td class="px-3 py-2 text-gray-600">Your unique order/invoice ID — prevents duplicate conversions</td>
                                        </tr>
                                        <tr v-if="offer.commission_model === 'revshare' && offer.revshare_type === 'recurring'">
                                            <td class="px-3 py-2 font-mono text-purple-700">customer_id</td>
                                            <td class="px-3 py-2 text-green-600 font-medium">Yes (renewals)</td>
                                            <td class="px-3 py-2 text-gray-600">Your subscriber/customer ID — links renewal payments to the original affiliate attribution</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Full example URL -->
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Example Postback URL</p>
                                <div class="flex items-start gap-2">
                                    <code class="flex-1 block bg-gray-900 text-green-300 px-3 py-2 rounded text-xs font-mono break-all leading-relaxed">{{ postbackExampleUrl }}</code>
                                    <button @click="copyToClipboard(postbackExampleUrl, 'url')" class="flex-shrink-0 px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                        {{ copied === 'url' ? '✓' : 'Copy' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Secret token -->
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Your Secret Token</p>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 block bg-gray-900 text-yellow-300 px-3 py-2 rounded text-xs font-mono break-all">
                                        {{ showSecret ? offer.postback_secret : '•'.repeat(24) }}
                                    </code>
                                    <button @click="showSecret = !showSecret" class="flex-shrink-0 px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">{{ showSecret ? 'Hide' : 'Show' }}</button>
                                    <button @click="copyToClipboard(offer.postback_secret, 'secret')" class="flex-shrink-0 px-2 py-1.5 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">{{ copied === 'secret' ? '✓' : 'Copy' }}</button>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <button @click="showRegenerateConfirm = true" class="text-xs text-red-600 hover:text-red-700 font-medium">Regenerate token</button>
                                    <span class="text-xs text-gray-400">— invalidates the old token immediately</span>
                                </div>
                            </div>
                        </div>

                        <!-- Link to docs -->
                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Need help integrating? See the full guide with PHP, Node.js and Python examples.</p>
                            <Link :href="route('advertiser.documentation.index')" class="text-xs text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap ml-4">
                                Open Documentation →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Access Requests (if applicable) -->
                <div v-if="offer.access_type === 'request' && offer.access_requests && offer.access_requests.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            Recent Access Requests
                        </h3>
                    </div>
                    <div class="px-6 py-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliate</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="request in offer.access_requests.slice(0, 5)" :key="request.id">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ request.affiliate.name }}</div>
                                            <div class="text-sm text-gray-500">{{ request.affiliate.email }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900">{{ request.message || 'No message provided' }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                {
                                                    'bg-yellow-100 text-yellow-800': request.status === 'pending',
                                                    'bg-green-100 text-green-800': request.status === 'approved',
                                                    'bg-red-100 text-red-800': request.status === 'rejected'
                                                }
                                            ]">
                                                {{ request.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(request.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="offer.access_requests.length > 5" class="mt-4 text-center">
                            <Link
                                :href="route('advertiser.access-requests.index', { offer: offer.id })"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                View all {{ offer.access_requests.length }} access requests →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Delete Offer</template>
            <template #content>
                Are you sure you want to delete this offer? This action cannot be undone and will remove all associated data.
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">Cancel</SecondaryButton>
                <DangerButton class="ml-3" @click="deleteOffer" :disabled="deleteForm.processing">
                    <span v-if="deleteForm.processing">Deleting...</span>
                    <span v-else>Delete Offer</span>
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Regenerate Postback Secret Confirmation -->
        <DialogModal :show="showRegenerateConfirm" @close="showRegenerateConfirm = false">
            <template #title>Regenerate Postback Secret</template>
            <template #content>
                <p class="text-sm text-gray-600">
                    This will immediately invalidate your current secret token. Any postback URLs on your advertiser server that use the old token will stop working until you update them.
                </p>
                <p class="mt-2 text-sm font-semibold text-red-600">Are you sure you want to continue?</p>
            </template>
            <template #footer>
                <SecondaryButton @click="showRegenerateConfirm = false">Cancel</SecondaryButton>
                <DangerButton class="ml-3" @click="regenerateSecret">Regenerate</DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    offer: Object,
    stats: Object,
});

const showDeleteModal = ref(false);
const deleteForm = useForm({});

// Postback secret UI state
const showSecret = ref(false);
const showRegenerateConfirm = ref(false);
const copied = ref(null);

const postbackExampleUrl = computed(() => {
    const base = window.location.origin + '/api/postback';
    return `${base}?tracking_code={TRACKING_CODE}&conversion_value={VALUE}&transaction_id={TXN_ID}&token=${props.offer.postback_secret}`;
});

const postbackBase = computed(() => window.location.origin + '/api/postback');

const pixelBase = computed(() => window.location.origin + '/pixel');

const pixelWoocommerce = computed(() => {
    const base = pixelBase.value;
    return `<img src="${base}?value=<?php echo $order->get_total(); ?>&txn_id=<?php echo $order->get_order_number(); ?>" width="1" height="1" style="display:none" />`;
});

const pixelShopify = computed(() => {
    const base = pixelBase.value;
    return `<img src="${base}?value={{ order.total_price }}&txn_id={{ order.order_number }}" width="1" height="1" style="display:none" />`;
});

const pixelGeneric = computed(() => {
    const base = pixelBase.value;
    return `<img src="${base}?value=ORDER_TOTAL&txn_id=ORDER_ID" width="1" height="1" style="display:none" />`;
});

const copyToClipboard = (text, key = 'url') => {
    const doIt = () => {
        copied.value = key;
        setTimeout(() => { copied.value = null; }, 2000);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(doIt).catch(() => fallbackCopy(text, doIt));
    } else {
        fallbackCopy(text, doIt);
    }
};

const fallbackCopy = (text, onSuccess = null) => {
    const el = document.createElement('textarea');
    el.value = text;
    el.setAttribute('readonly', '');
    el.style.cssText = 'position:fixed;top:-9999px;left:-9999px';
    document.body.appendChild(el);
    el.select();
    try { document.execCommand('copy'); if (onSuccess) onSuccess(); } catch { prompt('Copy this:', text); }
    document.body.removeChild(el);
};

const regenerateSecret = () => {
    router.post(route('advertiser.offers.regenerate-postback-secret', props.offer.id), {}, {
        onSuccess: () => { showRegenerateConfirm.value = false; },
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0,
    }).format(amount || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-NG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const capitalize = (value) => {
    if (!value) return '';
    return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteOffer = () => {
    deleteForm.delete(route('advertiser.offers.destroy', props.offer.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};
</script>
