<template>
    <AppLayout :title="isEditing ? 'Edit Offer' : 'Create Offer'">
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('advertiser.offers.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ isEditing ? 'Edit Offer' : 'Create New Offer' }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ isEditing ? 'Update your offer details' : 'Set up a new affiliate offer' }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Product prefill banner -->
                <div v-if="prefill" class="mb-6 flex items-start gap-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
                    <img v-if="prefill.product_image" :src="prefill.product_image" class="w-16 h-16 rounded-lg object-cover flex-shrink-0 border" alt="Product" />
                    <div v-else class="w-16 h-16 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-indigo-800">Creating offer from store product</p>
                        <p class="text-sm text-indigo-700 truncate font-medium">{{ prefill.name }}</p>
                        <p class="text-xs text-indigo-500 mt-0.5">{{ prefill.store_name }} &nbsp;·&nbsp; ₦{{ Number(prefill.product_price).toLocaleString() }}</p>
                        <p class="text-xs text-indigo-500 mt-1">Key fields have been pre-filled from your product. Review and adjust before submitting.</p>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Offer Name *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g., Premium SaaS Tool Pro"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Describe your offer and what makes it valuable for affiliates..."
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                <select
                                    v-model="form.category_id"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview URL *</label>
                                <input
                                    v-model="form.preview_url"
                                    type="url"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="https://yoursite.com/product"
                                />
                                <p class="mt-1 text-sm text-gray-500">Where affiliates can preview your offer</p>
                                <p v-if="form.errors.preview_url" class="mt-1 text-sm text-red-600">{{ form.errors.preview_url }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Settings -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Commission Settings</h3>

                        <div class="space-y-4">
                            <!-- Commission Model Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Commission Model *</label>
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                        :class="form.commission_model === 'pps' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                    >
                                        <input type="radio" v-model="form.commission_model" value="pps" class="sr-only" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Pay Per Sale</p>
                                            <p class="text-xs text-gray-500">Fixed amount per sale</p>
                                        </div>
                                        <span class="text-lg" title="Affiliate earns when a purchase is completed">🛒</span>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                        :class="form.commission_model === 'ppl' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                    >
                                        <input type="radio" v-model="form.commission_model" value="ppl" class="sr-only" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Pay Per Lead</p>
                                            <p class="text-xs text-gray-500">Fixed amount per lead</p>
                                        </div>
                                        <span class="text-lg" title="Affiliate earns when a lead is captured — no purchase required">📋</span>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                        :class="form.commission_model === 'revshare' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                    >
                                        <input type="radio" v-model="form.commission_model" value="revshare" class="sr-only" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Revenue Share</p>
                                            <p class="text-xs text-gray-500">Percentage of revenue</p>
                                        </div>
                                        <span class="text-lg" title="Affiliate earns a % of the sale amount">📊</span>
                                    </label>
                                </div>

                                <!-- Dynamic integration info box -->
                                <div class="mt-3 rounded-lg border-l-4 p-4"
                                    :class="{
                                        'bg-blue-50 border-blue-400': form.commission_model === 'pps',
                                        'bg-green-50 border-green-400': form.commission_model === 'ppl',
                                        'bg-purple-50 border-purple-400': form.commission_model === 'revshare',
                                    }"
                                >
                                    <!-- PPS -->
                                    <template v-if="form.commission_model === 'pps'">
                                        <p class="text-sm font-semibold text-blue-800">🛒 Pay Per Sale — What this means</p>
                                        <ul class="mt-2 space-y-1 text-xs text-blue-700 list-disc list-inside">
                                            <li><strong>Affiliates earn</strong> only when a customer completes a purchase through their link.</li>
                                            <li><strong>You fire</strong> the postback/pixel on your payment confirmation page.</li>
                                            <li><code class="bg-blue-100 px-1 rounded">conversion_value</code> is <strong>required and must be &gt; 0</strong> (the actual sale amount, e.g. 15000).</li>
                                            <li>The platform will reject postbacks with zero or missing <code class="bg-blue-100 px-1 rounded">conversion_value</code>.</li>
                                        </ul>
                                    </template>
                                    <!-- PPL -->
                                    <template v-else-if="form.commission_model === 'ppl'">
                                        <p class="text-sm font-semibold text-green-800">📋 Pay Per Lead — What this means</p>
                                        <ul class="mt-2 space-y-1 text-xs text-green-700 list-disc list-inside">
                                            <li><strong>Affiliates earn</strong> when someone submits a form, signs up, starts a free trial, or completes any qualifying action — <strong>no purchase needed</strong>.</li>
                                            <li><strong>You fire</strong> the postback/pixel on your lead capture confirmation page.</li>
                                            <li><code class="bg-green-100 px-1 rounded">conversion_value</code> is <strong>optional</strong> — you may omit it or pass 0.</li>
                                            <li>Define clearly in your terms what qualifies as a valid lead to avoid disputes.</li>
                                        </ul>
                                    </template>
                                    <!-- RevShare -->
                                    <template v-else-if="form.commission_model === 'revshare'">
                                        <p class="text-sm font-semibold text-purple-800">📊 Revenue Share — What this means</p>
                                        <ul class="mt-2 space-y-1 text-xs text-purple-700 list-disc list-inside">
                                            <li><strong>Affiliates earn</strong> a percentage of every sale they drive. Higher sales = higher commissions.</li>
                                            <li><strong>You fire</strong> the postback/pixel on your payment confirmation page.</li>
                                            <li><code class="bg-purple-100 px-1 rounded">conversion_value</code> is <strong>required and must be &gt; 0</strong> — commission is calculated as <code class="bg-purple-100 px-1 rounded">rate% × conversion_value</code>.</li>
                                            <li>The platform will reject postbacks with zero or missing <code class="bg-purple-100 px-1 rounded">conversion_value</code>.</li>
                                        </ul>

                                        <!-- RevShare recurrence settings -->
                                        <div class="mt-4 pt-3 border-t border-purple-200">
                                            <p class="text-xs font-semibold text-purple-800 mb-2">💳 Commission Recurrence</p>
                                            <div class="grid grid-cols-2 gap-3">
                                                <label
                                                    class="flex items-start gap-2 cursor-pointer rounded-lg border p-3"
                                                    :class="form.revshare_type === 'once' ? 'border-purple-500 bg-purple-50' : 'border-purple-200 bg-white'"
                                                >
                                                    <input type="radio" v-model="form.revshare_type" value="once" class="mt-0.5 accent-purple-600" />
                                                    <div>
                                                        <p class="text-xs font-semibold text-purple-900">One-time</p>
                                                        <p class="text-xs text-purple-600">Commission paid only on the initial sale (e.g. one-off products, lifetime deals)</p>
                                                    </div>
                                                </label>
                                                <label
                                                    class="flex items-start gap-2 cursor-pointer rounded-lg border p-3"
                                                    :class="form.revshare_type === 'recurring' ? 'border-purple-500 bg-purple-50' : 'border-purple-200 bg-white'"
                                                >
                                                    <input type="radio" v-model="form.revshare_type" value="recurring" class="mt-0.5 accent-purple-600" />
                                                    <div>
                                                        <p class="text-xs font-semibold text-purple-900">Recurring</p>
                                                        <p class="text-xs text-purple-600">Commission paid on each renewal billing cycle (subscriptions, SaaS, etc.)</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- Recurring duration picker -->
                                            <div v-if="form.revshare_type === 'recurring'" class="mt-3 p-3 bg-white border border-purple-200 rounded-lg">
                                                <p class="text-xs font-semibold text-purple-800 mb-2">Duration Cap</p>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex-1">
                                                        <select v-model="form.revshare_recurring_duration" class="w-full rounded-lg border-purple-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                                                            <option value="">Unlimited (Lifetime)</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="6">6</option>
                                                            <option value="12">12</option>
                                                            <option value="24">24</option>
                                                        </select>
                                                    </div>
                                                    <div class="flex-1">
                                                        <select v-model="form.revshare_recurring_unit" class="w-full rounded-lg border-purple-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                                                            <option value="month">Month(s)</option>
                                                            <option value="year">Year(s)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p class="mt-2 text-xs text-purple-600">
                                                    <template v-if="form.revshare_recurring_duration">
                                                        Affiliates earn a commission for up to <strong>{{ form.revshare_recurring_duration }} {{ form.revshare_recurring_unit }}(s)</strong> of renewals per customer.
                                                    </template>
                                                    <template v-else>
                                                        Affiliates earn a commission on <strong>every renewal</strong> for the customer's lifetime.
                                                    </template>
                                                </p>
                                                <p class="mt-1 text-xs text-purple-500">
                                                    Pass <code class="bg-purple-50 px-1 rounded">customer_id</code> (your subscriber ID) in each postback so the platform can link renewals to the original attribution and enforce the duration cap.
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Commission Rate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Commission Rate *
                                    <span v-if="form.commission_model === 'revshare'" class="text-gray-500">(Percentage)</span>
                                    <span v-else class="text-gray-500">(NGN)</span>
                                </label>
                                <input
                                    v-model="form.commission_rate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    :placeholder="form.commission_model === 'revshare' ? '25' : '5000'"
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    This is the base commission you offer. Admin will set the platform spread when approving your offer.
                                </p>
                                <p v-if="form.errors.commission_rate" class="mt-1 text-sm text-red-600">{{ form.errors.commission_rate }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cookie Duration (Days) *</label>
                                <input
                                    v-model="form.cookie_duration"
                                    type="number"
                                    min="1"
                                    max="365"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="30"
                                />
                                <p class="mt-1 text-sm text-gray-500">How long to credit conversions after a click (1-365 days)</p>
                                <p v-if="form.errors.cookie_duration" class="mt-1 text-sm text-red-600">{{ form.errors.cookie_duration }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Access & Tracking -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Access & Tracking</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Access Type *</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                        :class="form.access_type === 'open' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                    >
                                        <input type="radio" v-model="form.access_type" value="open" class="sr-only" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Open to All</p>
                                            <p class="text-xs text-gray-500">Any affiliate can promote immediately</p>
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                        :class="form.access_type === 'request' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                    >
                                        <input type="radio" v-model="form.access_type" value="request" class="sr-only" />
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Request Required</p>
                                            <p class="text-xs text-gray-500">Affiliates must request access</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postback URL (Optional)</label>
                                <input
                                    v-model="form.postback_url"
                                    type="url"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="https://yoursite.com/postback"
                                />
                                <p class="mt-1 text-sm text-gray-500">Server-to-server conversion tracking endpoint</p>
                                <p v-if="form.errors.postback_url" class="mt-1 text-sm text-red-600">{{ form.errors.postback_url }}</p>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Make offer active immediately</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Click-to-Chat Tracking -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">WhatsApp Click-to-Chat Tracking</h3>
                            <p class="text-sm text-gray-500 mt-1">Enable tracking for conversions that happen via WhatsApp DMs</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Enable WhatsApp Tracking Toggle -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <label class="flex items-start cursor-pointer">
                                    <input
                                        v-model="form.enable_whatsapp_tracking"
                                        type="checkbox"
                                        class="mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                    />
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">Enable WhatsApp Tracking</span>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Perfect for Nigerian SMEs! Affiliates get WhatsApp links with embedded tracking IDs.
                                            You manually report conversions that happen through WhatsApp chats.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <!-- WhatsApp Settings (shown when enabled) -->
                            <div v-if="form.enable_whatsapp_tracking" class="space-y-4 pl-4 border-l-2 border-green-500">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        WhatsApp Business Number *
                                        <span class="text-gray-500 font-normal">(e.g., 2348012345678)</span>
                                    </label>
                                    <input
                                        v-model="form.whatsapp_number"
                                        type="text"
                                        :required="form.enable_whatsapp_tracking"
                                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                        placeholder="2348012345678"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        Enter your WhatsApp number with country code (no + or spaces)
                                    </p>
                                    <p v-if="form.errors.whatsapp_number" class="mt-1 text-sm text-red-600">{{ form.errors.whatsapp_number }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Message Template (Optional)
                                    </label>
                                    <textarea
                                        v-model="form.whatsapp_message_template"
                                        rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono text-sm"
                                        placeholder="Hi {business_name}, I'm interested in {offer_name}. Ref: {click_id}"
                                    ></textarea>
                                    <div class="mt-2 space-y-1 text-xs text-gray-600">
                                        <p class="font-medium">Available variables:</p>
                                        <p><code class="bg-gray-100 px-1 rounded">{offer_name}</code> - Your offer name</p>
                                        <p><code class="bg-gray-100 px-1 rounded">{business_name}</code> - Your business name</p>
                                        <p><code class="bg-gray-100 px-1 rounded">{click_id}</code> - Unique tracking ID (required for conversion tracking)</p>
                                    </div>
                                </div>

                                <!-- Info Box -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="text-sm text-blue-800">
                                            <p class="font-medium mb-1">How it works:</p>
                                            <ol class="list-decimal list-inside space-y-1 text-xs">
                                                <li>Affiliates generate WhatsApp links with embedded tracking IDs</li>
                                                <li>Customers click and WhatsApp opens with pre-filled message (includes tracking ID)</li>
                                                <li>Customer chats with you and makes a purchase</li>
                                                <li>You report the conversion manually using the tracking ID from the chat</li>
                                                <li>System credits the affiliate automatically!</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Targeting & Product -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Targeting & Product</h3>
                        <p class="text-sm text-gray-500 mb-6">Control who sees this offer. Leave targeting empty to accept all traffic.</p>

                        <div class="space-y-6">
                            <!-- Offer URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Destination / Offer URL
                                    <span class="text-gray-400 font-normal">(where affiliates send traffic)</span>
                                </label>
                                <input
                                    v-model="form.offer_url"
                                    type="url"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="https://yoursite.com/landing-page"
                                />
                                <p class="mt-1 text-xs text-gray-500">If different from the Preview URL above. This is the actual link affiliates promote.</p>
                                <p v-if="form.errors.offer_url" class="mt-1 text-sm text-red-600">{{ form.errors.offer_url }}</p>
                            </div>

                            <!-- Product Image URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Image URL</label>
                                <div class="flex gap-3">
                                    <input
                                        v-model="form.product_image"
                                        type="url"
                                        class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="https://cdn.example.com/product.jpg or CPAlead image URL"
                                    />
                                    <img
                                        v-if="form.product_image"
                                        :src="form.product_image"
                                        class="h-10 w-10 rounded-lg object-cover border border-gray-200 flex-shrink-0"
                                        @error="$event.target.style.display='none'"
                                    />
                                </div>
                                <p v-if="form.errors.product_image" class="mt-1 text-sm text-red-600">{{ form.errors.product_image }}</p>
                            </div>

                            <!-- Target Countries -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Target Countries
                                    <span class="text-gray-400 font-normal">— leave empty for worldwide</span>
                                </label>
                                <div class="relative" v-click-outside="() => countryDropdownOpen = false">
                                    <input
                                        v-model="countrySearch"
                                        type="text"
                                        placeholder="Search countries to add..."
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        @focus="countryDropdownOpen = true"
                                    />
                                    <div v-if="countryDropdownOpen && filteredCountries.length" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-52 overflow-y-auto">
                                        <button
                                            v-for="c in filteredCountries"
                                            :key="c.code"
                                            type="button"
                                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between"
                                            :class="form.target_countries.includes(c.code) ? 'bg-blue-50' : ''"
                                            @click="toggleCountry(c.code)"
                                        >
                                            <span>{{ c.flag }} {{ c.name }}</span>
                                            <svg v-if="form.target_countries.includes(c.code)" class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="form.target_countries.length" class="flex flex-wrap gap-2 mt-2">
                                    <span
                                        v-for="code in form.target_countries"
                                        :key="code"
                                        class="inline-flex items-center gap-1 pl-2 pr-1 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full"
                                    >
                                        {{ countryFlag(code) }} {{ countryName(code) }}
                                        <button type="button" @click="removeCountry(code)" class="ml-0.5 hover:text-blue-600 font-bold leading-none">×</button>
                                    </span>
                                </div>
                                <p v-else class="mt-1 text-xs text-gray-400">🌍 Worldwide (no country restriction)</p>
                            </div>

                            <!-- Target Devices -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Target Devices
                                    <span class="text-gray-400 font-normal">— leave all unchecked to allow all</span>
                                </label>
                                <div class="flex flex-wrap gap-3">
                                    <label v-for="d in deviceOptions" :key="d.value"
                                        class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer transition-colors"
                                        :class="form.target_devices.includes(d.value) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                                    >
                                        <input type="checkbox" :value="d.value" v-model="form.target_devices" class="sr-only" />
                                        <span class="text-lg">{{ d.icon }}</span>
                                        <span class="text-sm font-medium text-gray-700">{{ d.label }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Target OS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Target Operating Systems
                                    <span class="text-gray-400 font-normal">— leave all unchecked to allow all</span>
                                </label>
                                <div class="flex flex-wrap gap-3">
                                    <label v-for="o in osOptions" :key="o.value"
                                        class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer transition-colors"
                                        :class="form.target_os.includes(o.value) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                                    >
                                        <input type="checkbox" :value="o.value" v-model="form.target_os" class="sr-only" />
                                        <span class="text-lg">{{ o.icon }}</span>
                                        <span class="text-sm font-medium text-gray-700">{{ o.label }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Unique IP -->
                            <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input
                                        id="require_unique_ip"
                                        v-model="form.require_unique_ip"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                                    />
                                </div>
                                <div>
                                    <label for="require_unique_ip" class="block text-sm font-medium text-amber-900 cursor-pointer">
                                        Require Unique IP per Conversion
                                    </label>
                                    <p class="text-xs text-amber-700 mt-0.5">
                                        Block visitors whose IP has already converted this offer. Useful for lead-gen offers to prevent duplicate submissions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Terms & Conditions</h3>

                        <div>
                            <textarea
                                v-model="form.terms_and_conditions"
                                rows="6"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Optional: Specify any terms, restrictions, or requirements for affiliates..."
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500">Provide any specific rules or requirements for promoting this offer</p>
                        </div>
                    </div>

                    <!-- Sales Forecast & Wallet -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Sales Forecast & Wallet</h3>
                        <p class="text-sm text-gray-500 mb-6">Help us ensure your offer runs smoothly. We'll use this to show you the minimum wallet balance you should maintain.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expected Sales / Conversions</label>
                                <input
                                    v-model="form.expected_sales"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. 100"
                                />
                                <p class="mt-1 text-xs text-gray-500">How many conversions do you expect?</p>
                                <p v-if="form.errors.expected_sales" class="mt-1 text-sm text-red-600">{{ form.errors.expected_sales }}</p>
                            </div>

                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Daily Conversion Cap</label>
                            <input
                                v-model="form.daily_conversion_cap"
                                type="number"
                                min="0"
                                step="1"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Leave blank or 0 for unlimited"
                            />
                            <p class="mt-1 text-xs text-gray-500">Maximum conversions allowed per day.</p>
                            <p v-if="form.errors.daily_conversion_cap" class="mt-1 text-sm text-red-600">{{ form.errors.daily_conversion_cap }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Conversion Cap</label>
                            <input
                                v-model="form.monthly_conversion_cap"
                                type="number"
                                min="0"
                                step="1"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Leave blank or 0 for unlimited"
                            />
                            <p class="mt-1 text-xs text-gray-500">Maximum conversions allowed per month.</p>
                            <p v-if="form.errors.monthly_conversion_cap" class="mt-1 text-sm text-red-600">{{ form.errors.monthly_conversion_cap }}</p>
                        </div>

                        <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Wallet Balance Required (₦)</label>
                                <input
                                    v-model="form.minimum_wallet_required"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Auto-calculated"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    If your wallet drops below this, your offer pauses and traffic is redirected to another offer.
                                    <span class="text-blue-600">
                                        Auto-calculated:
                                        <template v-if="form.commission_model === 'revshare'">expected sales × product cost × rate %</template>
                                        <template v-else>expected sales × commission rate (₦)</template>
                                    </span>
                                </p>
                                <p v-if="form.errors.minimum_wallet_required" class="mt-1 text-sm text-red-600">{{ form.errors.minimum_wallet_required }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Creatives (Optional) - Only for new offers -->
                    <div v-if="!isEditing" class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Creatives (Optional)</h3>
                                <p class="text-sm text-gray-500 mt-1">Add promotional materials now or later</p>
                            </div>
                            <button
                                type="button"
                                @click="addCreativeSlot"
                                class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors"
                            >
                                + Add Creative
                            </button>
                        </div>

                        <!-- Info Banner -->
                        <div v-if="form.creatives.length === 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-blue-800">
                                    You can add creatives (banners, images, videos, text ads) now or add them later from the offer management page.
                                </p>
                            </div>
                        </div>

                        <!-- Creatives List -->
                        <div v-if="form.creatives.length > 0" class="space-y-4">
                            <div
                                v-for="(creative, index) in form.creatives"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div class="flex items-start justify-between mb-4">
                                    <h4 class="font-medium text-gray-900">Creative {{ index + 1 }}</h4>
                                    <button
                                        type="button"
                                        @click="removeCreative(index)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Type Selection -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                        <div class="grid grid-cols-4 gap-2">
                                            <button
                                                type="button"
                                                v-for="type in creativeTypes"
                                                :key="type.value"
                                                @click="creative.type = type.value"
                                                :class="[
                                                    'p-2 text-xs border rounded-lg transition-colors',
                                                    creative.type === type.value
                                                        ? 'border-purple-600 bg-purple-50 text-purple-900'
                                                        : 'border-gray-200 hover:border-gray-300'
                                                ]"
                                            >
                                                {{ type.label }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                        <input
                                            v-model="creative.name"
                                            type="text"
                                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                            placeholder="e.g., Homepage Banner 728x90"
                                        />
                                    </div>

                                    <!-- File Upload (for non-text types) -->
                                    <div v-if="creative.type !== 'text'" class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                                        <input
                                            type="file"
                                            @change="handleCreativeFileChange($event, index)"
                                            accept="image/*,video/*"
                                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Max 10MB. JPG, PNG, GIF, MP4, MOV</p>
                                    </div>

                                    <!-- Text Content (for text type) -->
                                    <div v-if="creative.type === 'text'" class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ad Copy</label>
                                        <textarea
                                            v-model="creative.content"
                                            rows="3"
                                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                            placeholder="Enter promotional text..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <Link
                            :href="route('advertiser.offers.index')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving...' : (isEditing ? 'Update Offer' : 'Create Offer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    offer: Object,
    categories: Array,
    prefill: Object,
});

const isEditing = computed(() => !!props.offer);

const creativeTypes = [
    { value: 'banner', label: 'Banner' },
    { value: 'image', label: 'Image' },
    { value: 'text', label: 'Text Ad' },
    { value: 'video', label: 'Video' },
];

const deviceOptions = [
    { value: 'desktop', label: 'Desktop', icon: '🖥️' },
    { value: 'mobile',  label: 'Mobile',  icon: '📱' },
    { value: 'tablet',  label: 'Tablet',  icon: '📋' },
];

const osOptions = [
    { value: 'windows', label: 'Windows',  icon: '🪟' },
    { value: 'mac',     label: 'macOS',    icon: '🍎' },
    { value: 'linux',   label: 'Linux',    icon: '🐧' },
    { value: 'android', label: 'Android',  icon: '🤖' },
    { value: 'ios',     label: 'iOS',      icon: '📱' },
];

// Country data (ISO 3166-1 alpha-2)
const countries = [
    { code: 'NG', name: 'Nigeria', flag: '🇳🇬' },
    { code: 'GH', name: 'Ghana', flag: '🇬🇭' },
    { code: 'KE', name: 'Kenya', flag: '🇰🇪' },
    { code: 'ZA', name: 'South Africa', flag: '🇿🇦' },
    { code: 'EG', name: 'Egypt', flag: '🇪🇬' },
    { code: 'TZ', name: 'Tanzania', flag: '🇹🇿' },
    { code: 'UG', name: 'Uganda', flag: '🇺🇬' },
    { code: 'ET', name: 'Ethiopia', flag: '🇪🇹' },
    { code: 'DZ', name: 'Algeria', flag: '🇩🇿' },
    { code: 'MA', name: 'Morocco', flag: '🇲🇦' },
    { code: 'CM', name: 'Cameroon', flag: '🇨🇲' },
    { code: 'CI', name: "Côte d'Ivoire", flag: '🇨🇮' },
    { code: 'SN', name: 'Senegal', flag: '🇸🇳' },
    { code: 'US', name: 'United States', flag: '🇺🇸' },
    { code: 'GB', name: 'United Kingdom', flag: '🇬🇧' },
    { code: 'CA', name: 'Canada', flag: '🇨🇦' },
    { code: 'AU', name: 'Australia', flag: '🇦🇺' },
    { code: 'DE', name: 'Germany', flag: '🇩🇪' },
    { code: 'FR', name: 'France', flag: '🇫🇷' },
    { code: 'IT', name: 'Italy', flag: '🇮🇹' },
    { code: 'ES', name: 'Spain', flag: '🇪🇸' },
    { code: 'NL', name: 'Netherlands', flag: '🇳🇱' },
    { code: 'SE', name: 'Sweden', flag: '🇸🇪' },
    { code: 'NO', name: 'Norway', flag: '🇳🇴' },
    { code: 'DK', name: 'Denmark', flag: '🇩🇰' },
    { code: 'FI', name: 'Finland', flag: '🇫🇮' },
    { code: 'PL', name: 'Poland', flag: '🇵🇱' },
    { code: 'IN', name: 'India', flag: '🇮🇳' },
    { code: 'PK', name: 'Pakistan', flag: '🇵🇰' },
    { code: 'BD', name: 'Bangladesh', flag: '🇧🇩' },
    { code: 'PH', name: 'Philippines', flag: '🇵🇭' },
    { code: 'ID', name: 'Indonesia', flag: '🇮🇩' },
    { code: 'MY', name: 'Malaysia', flag: '🇲🇾' },
    { code: 'SG', name: 'Singapore', flag: '🇸🇬' },
    { code: 'TH', name: 'Thailand', flag: '🇹🇭' },
    { code: 'VN', name: 'Vietnam', flag: '🇻🇳' },
    { code: 'JP', name: 'Japan', flag: '🇯🇵' },
    { code: 'KR', name: 'South Korea', flag: '🇰🇷' },
    { code: 'CN', name: 'China', flag: '🇨🇳' },
    { code: 'BR', name: 'Brazil', flag: '🇧🇷' },
    { code: 'MX', name: 'Mexico', flag: '🇲🇽' },
    { code: 'AR', name: 'Argentina', flag: '🇦🇷' },
    { code: 'CO', name: 'Colombia', flag: '🇨🇴' },
    { code: 'CL', name: 'Chile', flag: '🇨🇱' },
    { code: 'PE', name: 'Peru', flag: '🇵🇪' },
    { code: 'RU', name: 'Russia', flag: '🇷🇺' },
    { code: 'UA', name: 'Ukraine', flag: '🇺🇦' },
    { code: 'TR', name: 'Turkey', flag: '🇹🇷' },
    { code: 'SA', name: 'Saudi Arabia', flag: '🇸🇦' },
    { code: 'AE', name: 'United Arab Emirates', flag: '🇦🇪' },
    { code: 'QA', name: 'Qatar', flag: '🇶🇦' },
    { code: 'KW', name: 'Kuwait', flag: '🇰🇼' },
    { code: 'IQ', name: 'Iraq', flag: '🇮🇶' },
    { code: 'IR', name: 'Iran', flag: '🇮🇷' },
    { code: 'IL', name: 'Israel', flag: '🇮🇱' },
    { code: 'NZ', name: 'New Zealand', flag: '🇳🇿' },
    { code: 'ZW', name: 'Zimbabwe', flag: '🇿🇼' },
    { code: 'ZM', name: 'Zambia', flag: '🇿🇲' },
    { code: 'RW', name: 'Rwanda', flag: '🇷🇼' },
    { code: 'MZ', name: 'Mozambique', flag: '🇲🇿' },
    { code: 'AO', name: 'Angola', flag: '🇦🇴' },
];

const countrySearch = ref('');
const countryDropdownOpen = ref(false);

const filteredCountries = computed(() => {
    const q = countrySearch.value.toLowerCase();
    return countries.filter(c => c.name.toLowerCase().includes(q) || c.code.toLowerCase().includes(q));
});

const countryName = (code) => countries.find(c => c.code === code)?.name ?? code;
const countryFlag = (code) => countries.find(c => c.code === code)?.flag ?? '';

const toggleCountry = (code) => {
    const idx = form.target_countries.indexOf(code);
    if (idx === -1) {
        form.target_countries.push(code);
    } else {
        form.target_countries.splice(idx, 1);
    }
    countrySearch.value = '';
    countryDropdownOpen.value = false;
};

const removeCountry = (code) => {
    const idx = form.target_countries.indexOf(code);
    if (idx !== -1) form.target_countries.splice(idx, 1);
};

// v-click-outside directive
const vClickOutside = {
    mounted(el, binding) {
        el._clickOutsideHandler = (e) => {
            if (!el.contains(e.target)) binding.value(e);
        };
        document.addEventListener('click', el._clickOutsideHandler);
    },
    unmounted(el) {
        document.removeEventListener('click', el._clickOutsideHandler);
    },
};

const form = useForm({
    store_product_id: props.prefill?.store_product_id || props.offer?.store_product_id || null,
    name: props.offer?.name || props.prefill?.name || '',
    description: props.offer?.description || props.prefill?.description || '',
    category_id: props.offer?.category_id || '',
    commission_model: props.offer?.commission_model || 'pps',
    commission_rate: props.offer?.commission_rate || '',
    cookie_duration: props.offer?.cookie_duration || 30,
    revshare_type: props.offer?.revshare_type || 'once',
    revshare_recurring_duration: props.offer?.revshare_recurring_duration || '',
    revshare_recurring_unit: props.offer?.revshare_recurring_unit || 'month',
    access_type: props.offer?.access_type || 'open',
    preview_url: props.offer?.preview_url || props.prefill?.preview_url || '',
    terms_and_conditions: props.offer?.terms_and_conditions || '',
    postback_url: props.offer?.postback_url || '',
    enable_whatsapp_tracking: props.offer?.enable_whatsapp_tracking || false,
    whatsapp_number: props.offer?.whatsapp_number || props.prefill?.whatsapp_number || '',
    whatsapp_message_template: props.offer?.whatsapp_message_template || '',
    is_active: props.offer?.is_active ?? true,
    expected_sales: props.offer?.expected_sales ?? '',
    product_cost: props.offer?.product_cost ?? '',
    minimum_wallet_required: props.offer?.minimum_wallet_required ?? '',
    daily_conversion_cap: props.offer?.daily_conversion_cap ?? '',
    monthly_conversion_cap: props.offer?.monthly_conversion_cap ?? '',
    // Targeting & product
    offer_url: props.offer?.offer_url || props.prefill?.preview_url || '',
    product_image: props.offer?.product_image || props.prefill?.product_image || '',
    target_countries: props.offer?.target_countries || [],
    target_devices: props.offer?.target_devices || [],
    target_os: props.offer?.target_os || [],
    require_unique_ip: props.offer?.require_unique_ip || false,
    creatives: [],
});

// Auto-calculate minimum wallet required when relevant fields change
const recalcMinWallet = () => {
    const sales = parseFloat(form.expected_sales);
    const rate  = parseFloat(form.commission_rate);
    const cost  = parseFloat(form.product_cost);

    if (!(sales > 0 && rate > 0)) return;

    if (form.commission_model === 'revshare') {
        // Rate is a %, cost per conversion = product_cost × rate/100
        if (!(cost > 0)) return;
        form.minimum_wallet_required = (sales * cost * (rate / 100)).toFixed(2);
    } else {
        // pps / ppl: rate is a fixed NGN amount per conversion
        form.minimum_wallet_required = (sales * rate).toFixed(2);
    }
};

watchEffect(recalcMinWallet);

const addCreativeSlot = () => {
    form.creatives.push({
        type: 'image',
        name: '',
        file: null,
        content: '',
    });
};

const removeCreative = (index) => {
    form.creatives.splice(index, 1);
};

const handleCreativeFileChange = (event, index) => {
    form.creatives[index].file = event.target.files[0];
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('advertiser.offers.update', props.offer.id));
    } else {
        form.post(route('advertiser.offers.store'));
    }
};
</script>
