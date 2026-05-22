<template>
    <AppLayout title="Advertiser Documentation">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Advertiser Documentation
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page URL Info -->
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-semibold text-green-900">Documentation URL:</div>
                                <code class="text-xs text-green-700 bg-green-100 px-2 py-1 rounded">{{ currentUrl }}</code>
                            </div>
                        </div>
                        <button @click="copyUrl" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            {{ copyButtonText }}
                        </button>
                    </div>
                </div>

                <!-- Table of Contents -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 sm:px-20 bg-gradient-to-r from-blue-600 to-cyan-600 text-white border-b border-gray-200">
                        <div class="text-2xl font-bold">
                            📘 Complete Advertiser Guide
                        </div>
                        <p class="mt-2 text-blue-100">Technical integration, tracking setup, and best practices</p>
                    </div>

                    <div class="p-6 grid md:grid-cols-3 gap-4">
                        <a v-for="section in sections" :key="section.id"
                           :href="`#${section.id}`"
                           class="block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition">
                            <div class="text-3xl mb-2">{{ section.icon }}</div>
                            <div class="font-semibold text-gray-800">{{ section.title }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ section.description }}</div>
                        </a>
                    </div>
                </div>

                <!-- 1. Platform Overview -->
                <div id="overview" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🎯 Platform Overview</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">How the Platform Works</h4>
                        <p class="text-gray-700 mb-6">
                            Our affiliate platform connects your products with thousands of affiliates who will promote them for a commission. You only pay for actual results (sales, leads, etc.)—no upfront advertising costs!
                        </p>

                        <!-- Value Proposition -->
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">💰</div>
                                <div class="font-bold text-lg text-green-700">Pay for Results</div>
                                <p class="text-sm text-gray-600 mt-2">Only pay when affiliates generate actual conversions</p>
                            </div>
                            <div class="bg-blue-50 border-2 border-blue-500 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">📈</div>
                                <div class="font-bold text-lg text-blue-700">Scale Fast</div>
                                <p class="text-sm text-gray-600 mt-2">Leverage thousands of affiliates promoting simultaneously</p>
                            </div>
                            <div class="bg-purple-50 border-2 border-purple-500 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">🎯</div>
                                <div class="font-bold text-lg text-purple-700">Track Everything</div>
                                <p class="text-sm text-gray-600 mt-2">Real-time analytics and conversion tracking</p>
                            </div>
                        </div>

                        <!-- Process Flow -->
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-lg">
                            <h5 class="font-semibold mb-4 text-center text-lg">Campaign Lifecycle</h5>
                            <div class="grid md:grid-cols-5 gap-3">
                                <div class="text-center">
                                    <div class="bg-white rounded-lg p-4 shadow mb-2">
                                        <div class="text-3xl mb-1">📝</div>
                                        <div class="font-semibold text-sm">Create Offer</div>
                                    </div>
                                    <p class="text-xs text-gray-600">Set up your product & commission</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white rounded-lg p-4 shadow mb-2">
                                        <div class="text-3xl mb-1">🔗</div>
                                        <div class="font-semibold text-sm">Integrate</div>
                                    </div>
                                    <p class="text-xs text-gray-600">Add tracking code to your site</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white rounded-lg p-4 shadow mb-2">
                                        <div class="text-3xl mb-1">👥</div>
                                        <div class="font-semibold text-sm">Affiliates Join</div>
                                    </div>
                                    <p class="text-xs text-gray-600">Approve affiliates to promote</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white rounded-lg p-4 shadow mb-2">
                                        <div class="text-3xl mb-1">🚀</div>
                                        <div class="font-semibold text-sm">They Promote</div>
                                    </div>
                                    <p class="text-xs text-gray-600">Affiliates drive traffic</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-white rounded-lg p-4 shadow mb-2">
                                        <div class="text-3xl mb-1">💸</div>
                                        <div class="font-semibold text-sm">Pay Commissions</div>
                                    </div>
                                    <p class="text-xs text-gray-600">Automatic commission deduction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Commission Models -->
                <div id="commission-models" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">💰 Commission Models</h3>
                        <p class="mt-1 text-sm text-gray-500">Understanding how each model works and what your tracking integration must do</p>
                    </div>
                    <div class="p-6 space-y-6">

                        <!-- PPS -->
                        <div class="rounded-xl border-2 border-orange-300 overflow-hidden">
                            <div class="bg-orange-50 px-6 py-4 flex items-center gap-3">
                                <span class="text-3xl">🛒</span>
                                <div>
                                    <h4 class="text-lg font-bold text-orange-900">Pay Per Sale (PPS)</h4>
                                    <p class="text-sm text-orange-700">A fixed amount paid for every confirmed purchase</p>
                                </div>
                            </div>
                            <div class="px-6 py-4 grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">How it works</p>
                                    <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                        <li>You set a <strong>fixed commission amount</strong> (e.g. ₦2,000 per sale)</li>
                                        <li>Affiliate gets paid that amount when a customer completes a purchase</li>
                                        <li><code class="bg-gray-100 px-1 rounded">conversion_value</code> must be <strong>&gt; 0</strong> — the platform rejects zero-value postbacks</li>
                                        <li>Commission does <em>not</em> scale with order size (unlike RevShare)</li>
                                    </ul>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-4">
                                    <p class="text-xs font-semibold text-orange-900 mb-2">🔥 When to use PPS</p>
                                    <ul class="text-sm text-orange-800 space-y-1">
                                        <li>✓ Physical products with a fixed price</li>
                                        <li>✓ E-commerce / WooCommerce / Shopify stores</li>
                                        <li>✓ One-time purchases where order value varies little</li>
                                    </ul>
                                    <div class="mt-3 p-2 bg-white rounded border border-orange-200 text-xs text-gray-600">
                                        <strong>Example:</strong> ShoesNG pays ₦1,500 per sale. Affiliate drives 10 sales = ₦15,000 commission.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PPL -->
                        <div class="rounded-xl border-2 border-green-300 overflow-hidden">
                            <div class="bg-green-50 px-6 py-4 flex items-center gap-3">
                                <span class="text-3xl">📋</span>
                                <div>
                                    <h4 class="text-lg font-bold text-green-900">Pay Per Lead (PPL)</h4>
                                    <p class="text-sm text-green-700">A fixed amount paid for every qualified lead — no purchase required</p>
                                </div>
                            </div>
                            <div class="px-6 py-4 grid md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">How it works</p>
                                    <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                        <li>You set a <strong>fixed commission per lead</strong> (e.g. ₦500 per signup)</li>
                                        <li>Affiliate earns on a form submission, registration, or enquiry — no sale needed</li>
                                        <li><code class="bg-gray-100 px-1 rounded">conversion_value</code> is <strong>optional</strong> — zero is allowed</li>
                                        <li>Fire the tracking pixel / postback on your thank-you / confirmation page</li>
                                    </ul>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4">
                                    <p class="text-xs font-semibold text-green-900 mb-2">🔥 When to use PPL</p>
                                    <ul class="text-sm text-green-800 space-y-1">
                                        <li>✓ Insurance / loan enquiry forms</li>
                                        <li>✓ SaaS free-trial signups</li>
                                        <li>✓ Newsletter / webinar registrations</li>
                                        <li>✓ Real estate / car dealer enquiries</li>
                                    </ul>
                                    <div class="mt-3 p-2 bg-white rounded border border-green-200 text-xs text-gray-600">
                                        <strong>Example:</strong> InsureCo pays ₦800 per completed insurance quote. No payment from the customer is needed.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RevShare -->
                        <div class="rounded-xl border-2 border-purple-300 overflow-hidden">
                            <div class="bg-purple-50 px-6 py-4 flex items-center gap-3">
                                <span class="text-3xl">📊</span>
                                <div>
                                    <h4 class="text-lg font-bold text-purple-900">Revenue Share (RevShare)</h4>
                                    <p class="text-sm text-purple-700">A percentage of every sale — scales with order value</p>
                                </div>
                            </div>
                            <div class="px-6 py-4 space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">How it works</p>
                                        <ul class="space-y-1 text-sm text-gray-700 list-disc list-inside">
                                            <li>You set a <strong>percentage rate</strong> (e.g. 20%)</li>
                                            <li>Commission = <code class="bg-gray-100 px-1 rounded">rate% × conversion_value</code></li>
                                            <li><code class="bg-gray-100 px-1 rounded">conversion_value</code> is <strong>required and must be &gt; 0</strong> — zero-value postbacks are rejected</li>
                                            <li>Higher-value sales earn affiliates more</li>
                                        </ul>
                                    </div>
                                    <div class="bg-purple-50 rounded-lg p-4">
                                        <p class="text-xs font-semibold text-purple-900 mb-2">🔥 When to use RevShare</p>
                                        <ul class="text-sm text-purple-800 space-y-1">
                                            <li>✓ SaaS / subscription businesses</li>
                                            <li>✓ Marketplaces with variable order sizes</li>
                                            <li>✓ Digital products / online courses</li>
                                            <li>✓ Any recurring billing service</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- RevShare Recurring sub-section -->
                                <div class="border-t border-purple-200 pt-4">
                                    <h5 class="text-base font-bold text-purple-900 mb-3">🔄 Recurring RevShare (Subscription Support)</h5>
                                    <p class="text-sm text-gray-700 mb-4">
                                        When your offer is a <strong>subscription or recurring billing service</strong>, you can reward affiliates on
                                        <em>every renewal</em> — not just the first sale. The platform tracks each billing cycle and links it back
                                        to the original affiliate who drove the customer.
                                    </p>

                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-white border-2 border-purple-200 rounded-lg p-4">
                                            <p class="font-semibold text-purple-800 mb-2">1️⃣ One-time RevShare</p>
                                            <p class="text-sm text-gray-700">Commission paid only on the <strong>first sale</strong>. Renewals are not tracked.<br>Use for: lifetime deals, one-off products.</p>
                                        </div>
                                        <div class="bg-white border-2 border-purple-500 rounded-lg p-4">
                                            <p class="font-semibold text-purple-800 mb-2">🔄 Recurring RevShare</p>
                                            <p class="text-sm text-gray-700">Commission paid on the first sale <strong>and every renewal</strong>, up to the duration cap you set (or unlimited lifetime).<br>Use for: monthly/annual subscriptions, SaaS, managed services.</p>
                                        </div>
                                    </div>

                                    <div class="bg-purple-50 rounded-lg p-4 mb-4">
                                        <p class="text-sm font-semibold text-purple-900 mb-3">🔑 How the platform links renewals to affiliates</p>
                                        <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                                            <li><strong>First sale:</strong> customer clicks affiliate link → you fire postback with <code class="bg-purple-100 px-1 rounded">tracking_code</code> + <code class="bg-purple-100 px-1 rounded">customer_id</code> (your subscriber ID).</li>
                                            <li><strong>Each renewal:</strong> your billing system fires a new postback with the same <code class="bg-purple-100 px-1 rounded">customer_id</code>. The platform finds the original affiliate attribution automatically — no tracking_code needed for renewals.</li>
                                            <li><strong>Duration cap:</strong> if you set a cap (e.g. 12 months), postbacks beyond the cap are rejected with a clear error. Unlimited cap = lifetime commissions.</li>
                                        </ol>
                                    </div>

                                    <div class="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-xs overflow-x-auto mb-4">
<span class="text-gray-500">// Initial sale postback (send tracking_code + customer_id)</span>
POST /api/postback
{
  "<span class="text-yellow-400">tracking_code</span>": "<span class="text-green-400">ABC123def456</span>",  <span class="text-gray-500">// From affiliate click URL</span>
  "<span class="text-yellow-400">token</span>": "<span class="text-green-400">your-postback-secret</span>",
  "<span class="text-yellow-400">conversion_value</span>": <span class="text-green-400">5000.00</span>,
  "<span class="text-yellow-400">transaction_id</span>": "<span class="text-green-400">INV-001</span>",
  "<span class="text-yellow-400">customer_id</span>": "<span class="text-green-400">SUB-9981</span>"   <span class="text-gray-500">// Your internal subscriber/customer ID</span>
}

<span class="text-gray-500">// Renewal postback (send customer_id only — tracking_code not required)</span>
POST /api/postback
{
  "<span class="text-yellow-400">token</span>": "<span class="text-green-400">your-postback-secret</span>",
  "<span class="text-yellow-400">conversion_value</span>": <span class="text-green-400">5000.00</span>,      <span class="text-gray-500">// Monthly renewal amount</span>
  "<span class="text-yellow-400">transaction_id</span>": "<span class="text-green-400">INV-002</span>",
  "<span class="text-yellow-400">customer_id</span>": "<span class="text-green-400">SUB-9981</span>"          <span class="text-gray-500">// Same ID as the original sale</span>
}
                                    </div>

                                    <div class="grid md:grid-cols-3 gap-3 text-xs">
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <p class="font-semibold text-green-800 mb-1">✅ Duration Cap: 12 months</p>
                                            <p class="text-green-700">Platform pays commission on months 1-12. Month 13+ postbacks are rejected automatically.</p>
                                        </div>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <p class="font-semibold text-blue-800 mb-1">♾️ Duration Cap: Unlimited</p>
                                            <p class="text-blue-700">Affiliate earns a commission on every renewal for the lifetime of the customer.</p>
                                        </div>
                                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                            <p class="font-semibold text-orange-800 mb-1">📝 customer_id is required</p>
                                            <p class="text-orange-700">Renewal postbacks without a matching <code>customer_id</code> from an initial sale will be treated as a new first-sale conversion.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comparison table -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Feature</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-600 uppercase">🛒 PPS</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase">📋 PPL</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase">📊 RevShare</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700">What triggers a commission?</td>
                                        <td class="px-4 py-3 text-center text-gray-600">Sale (payment confirmed)</td>
                                        <td class="px-4 py-3 text-center text-gray-600">Lead / signup (no sale needed)</td>
                                        <td class="px-4 py-3 text-center text-gray-600">Sale (payment confirmed)</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-700">Commission type</td>
                                        <td class="px-4 py-3 text-center text-gray-600">Fixed per sale</td>
                                        <td class="px-4 py-3 text-center text-gray-600">Fixed per lead</td>
                                        <td class="px-4 py-3 text-center text-gray-600">% of sale value</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700">conversion_value required?</td>
                                        <td class="px-4 py-3 text-center">✅ Yes (&gt;0)</td>
                                        <td class="px-4 py-3 text-center">❌ Optional (0 OK)</td>
                                        <td class="px-4 py-3 text-center">✅ Yes (&gt;0)</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-700">Recurring / subscription support?</td>
                                        <td class="px-4 py-3 text-center">❌ No</td>
                                        <td class="px-4 py-3 text-center">❌ No</td>
                                        <td class="px-4 py-3 text-center">✅ Yes (with customer_id)</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700">customer_id param needed?</td>
                                        <td class="px-4 py-3 text-center">Optional</td>
                                        <td class="px-4 py-3 text-center">Optional</td>
                                        <td class="px-4 py-3 text-center">Required for renewals</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- 3. Tracking Integration -->
                <div id="tracking-integration" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🔧 Tracking Integration (Simple Guide)</h3>
                    </div>
                    <div class="p-6">
                        <!-- What is Tracking? -->
                        <div class="bg-purple-50 border-2 border-purple-400 rounded-lg p-6 mb-6">
                            <h4 class="text-xl font-bold text-purple-900 mb-3">🤔 What is Tracking? (In Plain English)</h4>
                            <p class="text-gray-700 mb-4">
                                Think of tracking like a "receipt system" for your sales. When someone clicks an affiliate link and buys from you, we need to know:
                            </p>
                            <div class="grid md:grid-cols-3 gap-3 mb-4">
                                <div class="bg-white p-3 rounded border-2 border-purple-200">
                                    <div class="font-bold text-purple-700 mb-1">1. Who sent them?</div>
                                    <div class="text-sm text-gray-600">Which affiliate gets credit</div>
                                </div>
                                <div class="bg-white p-3 rounded border-2 border-purple-200">
                                    <div class="font-bold text-purple-700 mb-1">2. How much did they buy?</div>
                                    <div class="text-sm text-gray-600">Order value for commission</div>
                                </div>
                                <div class="bg-white p-3 rounded border-2 border-purple-200">
                                    <div class="font-bold text-purple-700 mb-1">3. Is it a real sale?</div>
                                    <div class="text-sm text-gray-600">Prevent duplicate payments</div>
                                </div>
                            </div>
                            <div class="bg-purple-100 p-3 rounded">
                                <p class="text-sm text-purple-900">
                                    <strong>📌 The Good News:</strong> You just need to add ONE small piece of code to your website. It's like adding a Google Analytics code—super simple!
                                </p>
                            </div>
                        </div>

                        <!-- Do I Need a Developer? -->
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-orange-400 rounded-lg p-6 mb-8">
                            <h4 class="text-xl font-bold text-orange-900 mb-3">👨‍💻 Do I Need a Developer?</h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-lg p-4 border-2 border-green-300">
                                    <div class="flex items-center mb-2">
                                        <span class="text-2xl mr-2">✅</span>
                                        <span class="font-bold text-green-700">NO Developer Needed:</span>
                                    </div>
                                    <ul class="space-y-2 text-sm text-gray-700">
                                        <li>• WordPress/WooCommerce</li>
                                        <li>• Shopify</li>
                                        <li>• Wix</li>
                                        <li>• Squarespace</li>
                                        <li>• Most website builders</li>
                                    </ul>
                                    <div class="mt-3 text-xs text-green-700 font-semibold">→ You can copy & paste yourself (5 minutes)</div>
                                </div>
                                <div class="bg-white rounded-lg p-4 border-2 border-blue-300">
                                    <div class="flex items-center mb-2">
                                        <span class="text-2xl mr-2">👨‍💻</span>
                                        <span class="font-bold text-blue-700">Developer Helpful:</span>
                                    </div>
                                    <ul class="space-y-2 text-sm text-gray-700">
                                        <li>• Custom-built website</li>
                                        <li>• Mobile app</li>
                                        <li>• Complex checkout system</li>
                                        <li>• Multiple payment gateways</li>
                                    </ul>
                                    <div class="mt-3 text-xs text-blue-700 font-semibold">→ Still simple, just needs code access</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                            <div class="flex">
                                <div class="text-2xl mr-3">ℹ️</div>
                                <div>
                                    <p class="font-semibold text-blue-900">Two Setup Methods (Pick One)</p>
                                    <p class="text-blue-800"><strong>Method 1:</strong> Simple Pixel (recommended for most) — Just copy & paste one line of code.<br><strong>Method 2:</strong> Advanced Integration (for developers) — More accurate, requires programming.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Method Comparison -->
                        <div class="mb-8">
                            <h4 class="text-2xl font-bold mb-4 text-center">Which Method Should I Use?</h4>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="border-4 border-green-500 rounded-lg p-6 bg-green-50">
                                    <div class="flex items-center justify-center mb-4">
                                        <div class="text-5xl mr-3">🍪</div>
                                        <div>
                                            <div class="font-bold text-2xl text-green-700">Method 1: Simple Pixel</div>
                                            <div class="text-sm text-green-600 font-semibold">👍 START HERE (Most Popular)</div>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg p-4 mb-4">
                                        <div class="font-bold text-green-800 mb-2">What is it?</div>
                                        <p class="text-sm text-gray-700">A tiny invisible image you add to your "Order Complete" page. When a customer sees that page, the image loads and tells us about the sale.</p>
                                    </div>

                                    <div class="space-y-2 text-sm mb-4">
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-green-600 mr-2 text-lg">✓</span>
                                            <span><strong>Easy Setup:</strong> 5 minutes, just copy & paste</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-green-600 mr-2 text-lg">✓</span>
                                            <span><strong>No Coding:</strong> You can do it yourself</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-green-600 mr-2 text-lg">✓</span>
                                            <span><strong>Works Everywhere:</strong> WordPress, Shopify, any site</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-orange-600 mr-2 text-lg">!</span>
                                            <span><strong>Small Limitation:</strong> May miss 5-10% of sales (ad blockers)</span>
                                        </div>
                                    </div>

                                    <div class="bg-green-600 text-white p-3 rounded-lg text-center font-bold">
                                        ⭐ Perfect for: Small-Medium businesses
                                    </div>
                                </div>

                                <div class="border-2 border-gray-400 rounded-lg p-6 bg-gray-50 opacity-90">
                                    <div class="flex items-center justify-center mb-4">
                                        <div class="text-5xl mr-3">🔄</div>
                                        <div>
                                            <div class="font-bold text-2xl text-gray-700">Method 2: Advanced</div>
                                            <div class="text-sm text-gray-600 font-semibold">For Developers Only</div>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg p-4 mb-4">
                                        <div class="font-bold text-gray-800 mb-2">What is it?</div>
                                        <p class="text-sm text-gray-700">Your server directly sends sale data to our server using programming code (API). More technical but super accurate.</p>
                                    </div>

                                    <div class="space-y-2 text-sm mb-4">
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-green-600 mr-2 text-lg">✓</span>
                                            <span><strong>99% Accurate:</strong> Never misses a sale</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-green-600 mr-2 text-lg">✓</span>
                                            <span><strong>Ad-blocker Proof:</strong> Always works</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-red-600 mr-2 text-lg">✗</span>
                                            <span><strong>Needs Developer:</strong> Requires programming</span>
                                        </div>
                                        <div class="flex items-start bg-white p-2 rounded">
                                            <span class="text-red-600 mr-2 text-lg">✗</span>
                                            <span><strong>Takes Longer:</strong> 1-2 hours to set up</span>
                                        </div>
                                    </div>

                                    <div class="bg-gray-600 text-white p-3 rounded-lg text-center font-bold">
                                        🏢 Perfect for: Large businesses, apps
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cookie Tracking Setup -->
                        <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-lg p-8">
                            <h4 class="text-2xl font-bold mb-4 flex items-center text-green-800">
                                <span class="text-4xl mr-3">🍪</span>
                                Method 1: Simple Pixel Setup (Start Here!)
                            </h4>

                            <div class="bg-white rounded-lg p-6 mb-6 border-2 border-green-300">
                                <h5 class="font-bold text-lg mb-3 text-gray-800">📋 Step-by-Step Instructions:</h5>

                                <div class="space-y-4">
                                    <!-- Step 1 -->
                                    <div class="flex items-start">
                                        <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">1</div>
                                        <div class="flex-grow">
                                            <div class="font-bold text-gray-800 mb-1">Copy this code:</div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-xs overflow-x-auto mb-2">
                                                &lt;img src="https://dealsintel.com/pixel?value=<span class="text-yellow-400">100.00</span>&txn_id=<span class="text-yellow-400">ORDER123</span>" width="1" height="1" style="display:none" /&gt;
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                ☝️ This is a tiny invisible 1x1 pixel image. When it loads, we know a sale happened!
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2 -->
                                    <div class="flex items-start">
                                        <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">2</div>
                                        <div class="flex-grow">
                                            <div class="font-bold text-gray-800 mb-1">Change the YELLOW parts:</div>
                                            <div class="bg-yellow-50 p-4 rounded-lg border-2 border-yellow-300">
                                                <div class="space-y-3 text-sm">
                                                    <div>
                                                        <code class="bg-yellow-200 px-2 py-1 rounded font-bold">value=100.00</code>
                                                        <span class="ml-2">→ Replace <strong>100.00</strong> with your order total</span>
                                                        <div class="text-xs text-gray-600 ml-2 mt-1">Example: If customer paid ₦50,000 → use <code class="bg-gray-200 px-1">value=50000</code></div>
                                                    </div>
                                                    <div>
                                                        <code class="bg-yellow-200 px-2 py-1 rounded font-bold">txn_id=ORDER123</code>
                                                        <span class="ml-2">→ Replace <strong>ORDER123</strong> with your unique order number</span>
                                                        <div class="text-xs text-gray-600 ml-2 mt-1">Example: If order #4567 → use <code class="bg-gray-200 px-1">txn_id=4567</code></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3 -->
                                    <div class="flex items-start">
                                        <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">3</div>
                                        <div class="flex-grow">
                                            <div class="font-bold text-gray-800 mb-2">Paste it on your "Thank You" page:</div>
                                            <div class="grid md:grid-cols-3 gap-3">
                                                <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-3">
                                                    <div class="font-bold text-purple-700 mb-1">📦 WordPress/WooCommerce</div>
                                                    <p class="text-xs text-gray-600">Dashboard → Appearance → Customize → WooCommerce → Checkout → Add to "Order received" page</p>
                                                </div>
                                                <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-3">
                                                    <div class="font-bold text-blue-700 mb-1">🛍️ Shopify</div>
                                                    <p class="text-xs text-gray-600">Settings → Checkout → Order status page → Additional scripts → Paste code here</p>
                                                </div>
                                                <div class="bg-green-50 border-2 border-green-300 rounded-lg p-3">
                                                    <div class="font-bold text-green-700 mb-1">🌐 Custom Website</div>
                                                    <p class="text-xs text-gray-600">Add to the page that shows after payment is complete (usually "order-complete.html")</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 4 -->
                                    <div class="flex items-start">
                                        <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">4</div>
                                        <div class="flex-grow">
                                            <div class="font-bold text-gray-800 mb-1">Test it:</div>
                                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-300">
                                                <p class="text-sm text-gray-700 mb-2">Make a test purchase on your website. After checkout, check your DealsIntel dashboard. You should see a new conversion! 🎉</p>
                                                <div class="text-xs text-blue-700">💡 Tip: Use a test order or your own purchase to verify it works before going live.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Values Example -->
                            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4">
                                <div class="font-bold text-yellow-900 mb-2 flex items-center">
                                    <span class="text-xl mr-2">⚙️</span>
                                    For Dynamic Orders (Automated)
                                </div>
                                <p class="text-sm text-gray-700 mb-3">Instead of manually typing the order number each time, use your platform's variables so it fills automatically:</p>

                                <div class="space-y-2">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-sm text-purple-700 mb-1">WordPress/WooCommerce:</div>
                                        <code class="text-xs bg-gray-100 p-2 rounded block overflow-x-auto">
                                            &lt;img src="https://dealsintel.com/pixel?value=<span class="text-red-600">&lt;?php echo $order->get_total(); ?&gt;</span>&txn_id=<span class="text-red-600">&lt;?php echo $order->get_order_number(); ?&gt;</span>" width="1" height="1" style="display:none" /&gt;
                                        </code>

                                        <!-- WooCommerce Real Example -->
                                        <div class="mt-3 bg-gradient-to-r from-purple-50 to-pink-50 p-3 rounded border-2 border-purple-300">
                                            <div class="text-xs font-bold text-purple-900 mb-2">📌 Real Example (What Your Customer Sees):</div>
                                            <div class="bg-white p-2 rounded mb-2">
                                                <div class="text-xs text-gray-500 mb-1">If customer bought items worth ₦25,450.00 and order number is #7834:</div>
                                                <code class="text-xs text-green-700 font-mono">
                                                    &lt;img src="https://dealsintel.com/pixel?value=<strong>25450.00</strong>&txn_id=<strong>7834</strong>" width="1" height="1" style="display:none" /&gt;
                                                </code>
                                            </div>
                                            <div class="text-xs text-purple-800">
                                                ✅ The PHP code automatically fills in the order total and order number—no manual work!
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-sm text-blue-700 mb-1">Shopify:</div>
                                        <code class="text-xs bg-gray-100 p-2 rounded block overflow-x-auto" v-pre>
                                            &lt;img src="https://dealsintel.com/pixel?value=<span class="text-red-600">{{ order.total_price }}</span>&txn_id=<span class="text-red-600">{{ order.order_number }}</span>" width="1" height="1" style="display:none" /&gt;
                                        </code>

                                        <!-- Shopify Real Example -->
                                        <div class="mt-3 bg-gradient-to-r from-blue-50 to-cyan-50 p-3 rounded border-2 border-blue-300">
                                            <div class="text-xs font-bold text-blue-900 mb-2">📌 Real Example (What Your Customer Sees):</div>
                                            <div class="bg-white p-2 rounded mb-2">
                                                <div class="text-xs text-gray-500 mb-1">If customer bought items worth ₦149.99 and order number is #1234:</div>
                                                <code class="text-xs text-green-700 font-mono">
                                                    &lt;img src="https://dealsintel.com/pixel?value=<strong>149.99</strong>&txn_id=<strong>1234</strong>" width="1" height="1" style="display:none" /&gt;
                                                </code>
                                            </div>
                                            <div class="text-xs text-blue-800">
                                                ✅ Shopify's Liquid template automatically fills in the values!
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 text-xs text-yellow-800 bg-yellow-100 p-2 rounded">
                                    📝 <strong>What this does:</strong> The red parts get automatically replaced with real order data. No manual work needed!
                                </div>
                            </div>
                        </div>

                        <!-- S2S Tracking Setup -->
                        <div class="mb-8 bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-400 rounded-lg p-8">
                            <h4 class="text-2xl font-bold mb-4 flex items-center text-gray-800">
                                <span class="text-4xl mr-3">🔄</span>
                                Method 2: Advanced Integration (Optional)
                            </h4>

                            <div class="bg-orange-50 border-2 border-orange-400 rounded-lg p-4 mb-6">
                                <div class="flex items-start">
                                    <span class="text-3xl mr-3">⚠️</span>
                                    <div>
                                        <div class="font-bold text-orange-900 mb-1">This Section is for Developers Only</div>
                                        <p class="text-sm text-orange-800">If you successfully set up Method 1 (Simple Pixel) above, you're done! <strong>This section is OPTIONAL</strong> and only needed if you want 99% accuracy or have a developer available.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-6 border-2 border-gray-300 mb-6">
                                <h5 class="font-bold text-lg mb-3 text-gray-800">🤔 When Should You Use This?</h5>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="bg-green-50 border-2 border-green-400 rounded p-3">
                                        <div class="font-bold text-green-700 mb-2">✅ Use Advanced Integration If:</div>
                                        <ul class="text-sm space-y-1 text-gray-700">
                                            <li>• You process 1000+ orders/month</li>
                                            <li>• Your affiliate commissions are high value</li>
                                            <li>• You have a mobile app</li>
                                            <li>• You need 99% tracking accuracy</li>
                                            <li>• You have a developer on your team</li>
                                        </ul>
                                    </div>
                                    <div class="bg-red-50 border-2 border-red-400 rounded p-3">
                                        <div class="font-bold text-red-700 mb-2">❌ Skip This Section If:</div>
                                        <ul class="text-sm space-y-1 text-gray-700">
                                            <li>• You're just starting out</li>
                                            <li>• The Simple Pixel (Method 1) works fine</li>
                                            <li>• You don't have a developer</li>
                                            <li>• Your website is WordPress/Shopify</li>
                                            <li>• You process less than 500 orders/month</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                                <div class="flex items-start">
                                    <div class="text-2xl mr-3">💡</div>
                                    <div>
                                        <p class="font-semibold text-blue-900">What is S2S (Server-to-Server)?</p>
                                        <p class="text-blue-800 text-sm">Instead of your customer's browser telling us about the sale, YOUR SERVER directly talks to OUR SERVER. Think of it like two computers having a private conversation—no middleman, super reliable!</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Developer Instructions -->
                            <details class="bg-white rounded-lg border-2 border-gray-400">
                                <summary class="cursor-pointer p-4 font-bold text-gray-800 hover:bg-gray-50">
                                    👨‍💻 Show Developer Instructions (Click to Expand)
                                </summary>
                                <div class="p-6 border-t-2 border-gray-200">
                                    <h5 class="font-bold text-lg mb-4">📡 API Endpoint (For Your Developer):</h5>

                                    <div class="bg-gray-900 text-gray-100 p-4 rounded-lg mb-6 font-mono text-sm overflow-x-auto">
                                        <div class="text-green-400">POST</div>
                                        <div class="text-yellow-400">https://dealsintel.com/api/postback</div>
                                    </div>

                                    <!-- Secret Token Explanation -->
                                    <div class="mb-6 bg-red-50 border-2 border-red-300 rounded-lg p-4">
                                        <h6 class="font-bold text-red-900 mb-2 flex items-center gap-2">🔐 The Secret Token — What It Is &amp; Why It Matters</h6>
                                        <p class="text-sm text-red-800 mb-3">
                                            Every offer has a unique <strong>Postback Secret Token</strong>. You must send it as the <code class="bg-red-100 px-1 rounded font-mono">token</code> parameter in every postback request. The platform uses it to verify that the request came from your server and not from someone trying to fake conversions.
                                        </p>
                                        <ul class="text-sm text-red-800 space-y-1 list-disc list-inside">
                                            <li><strong>Where to find it:</strong> Go to your offer's detail page → <em>Tracking Integration</em> section → "Your Secret Token". Click <em>Show</em> then <em>Copy</em>.</li>
                                            <li><strong>Keep it secret:</strong> Never expose it in client-side JavaScript, HTML source, or public repos. Treat it like a password.</li>
                                            <li><strong>Each offer has its own token</strong> — if you run multiple offers, each has a different secret.</li>
                                            <li><strong>Regenerating:</strong> If a token is ever compromised, regenerate it from the offer page. The old token stops working immediately.</li>
                                        </ul>
                                    </div>

                                    <!-- Parameters Table -->
                                    <div class="mb-6">
                                        <h6 class="font-bold mb-3">📋 Parameters</h6>
                                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Parameter</th>
                                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Required</th>
                                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    <tr>
                                                        <td class="px-4 py-2 font-mono text-blue-700">tracking_code</td>
                                                        <td class="px-4 py-2 text-green-700 font-medium">Yes</td>
                                                        <td class="px-4 py-2 text-gray-600">The value from <code class="bg-gray-100 px-1 rounded">?tracking_code=</code> appended to your URL when the customer arrived via an affiliate link. Save it in a session on landing.</td>
                                                    </tr>
                                                    <tr class="bg-gray-50">
                                                        <td class="px-4 py-2 font-mono text-blue-700">token</td>
                                                        <td class="px-4 py-2 text-green-700 font-medium">Yes</td>
                                                        <td class="px-4 py-2 text-gray-600">Your offer's <strong>Postback Secret Token</strong> — authenticates your server. Found on your offer's detail page under <em>Tracking Integration</em>.</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 font-mono text-blue-700">conversion_value</td>
                                                        <td class="px-4 py-2 text-gray-600">PPS/RevShare: <span class="text-green-700 font-medium">Yes (&gt;0)</span><br>PPL: Optional</td>
                                                        <td class="px-4 py-2 text-gray-600">Order total or lead value. PPS and RevShare reject zero-value postbacks. PPL allows 0 or omit.</td>
                                                    </tr>
                                                    <tr class="bg-gray-50">
                                                        <td class="px-4 py-2 font-mono text-blue-700">transaction_id</td>
                                                        <td class="px-4 py-2 text-gray-500">Recommended</td>
                                                        <td class="px-4 py-2 text-gray-600">Your unique order or invoice ID. Prevents the same conversion from being counted twice if the postback is retried.</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-4 py-2 font-mono text-blue-700">customer_id</td>
                                                        <td class="px-4 py-2 text-gray-500">RevShare recurring only</td>
                                                        <td class="px-4 py-2 text-gray-600">Your internal subscriber / customer ID. Links renewal postbacks to the same affiliate who drove the original sale.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Two Formats -->
                                    <div class="mb-6">
                                        <h6 class="font-bold mb-3">📤 Two Ways to Send the Request (Both Work)</h6>
                                        <p class="text-sm text-gray-600 mb-4">The endpoint accepts parameters either as a <strong>JSON body</strong> or as <strong>URL query-string</strong>. Use whichever is easier for your stack.</p>

                                        <!-- Format 1: JSON -->
                                        <div class="mb-4">
                                            <div class="bg-blue-100 px-3 py-1 rounded-t text-sm font-semibold text-blue-900">Format 1 — JSON body <span class="font-normal">(recommended for server-side code)</span></div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-blue-400">POST</span> <span class="text-yellow-400">https://dealsintel.com/api/postback</span>
<span class="text-gray-500">Content-Type: application/json</span>

{
  "<span class="text-yellow-400">tracking_code</span>":    "<span class="text-green-400">ABC123def456</span>",   <span class="text-gray-500">// saved from ?tracking_code= on landing</span>
  "<span class="text-yellow-400">token</span>":            "<span class="text-green-400">YOUR_SECRET_TOKEN</span>", <span class="text-gray-500">// from your offer's Tracking Integration section</span>
  "<span class="text-yellow-400">conversion_value</span>": <span class="text-green-400">1500.00</span>,            <span class="text-gray-500">// order total</span>
  "<span class="text-yellow-400">transaction_id</span>":   "<span class="text-green-400">ORDER-789</span>",        <span class="text-gray-500">// your unique order ID</span>
  "<span class="text-yellow-400">customer_id</span>":      "<span class="text-green-400">SUB-9981</span>"          <span class="text-gray-500">// RevShare recurring only</span>
}
                                            </div>
                                        </div>

                                        <!-- Format 2: Query String -->
                                        <div class="mb-4">
                                            <div class="bg-orange-100 px-3 py-1 rounded-t text-sm font-semibold text-orange-900">Format 2 — Query-string URL <span class="font-normal">(simpler for basic scripts)</span></div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto break-all">
<span class="text-blue-400">POST</span> <span class="text-yellow-400">https://dealsintel.com/api/postback</span>?<span class="text-green-400">tracking_code</span>=ABC123def456&amp;<span class="text-green-400">token</span>=YOUR_SECRET_TOKEN&amp;<span class="text-green-400">conversion_value</span>=1500.00&amp;<span class="text-green-400">transaction_id</span>=ORDER-789
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1 px-1">Note: this is still a POST request — not a GET. The parameters are in the URL, but the HTTP method must be POST.</p>
                                        </div>

                                        <div class="bg-yellow-50 p-3 rounded border border-yellow-300 text-sm text-yellow-900">
                                            <strong>💡 How the offer page URL differs:</strong> The example URL shown in your offer's <em>Tracking Integration</em> card (e.g. <code class="bg-yellow-100 px-1 rounded font-mono">…?tracking_code=&#123;TRACKING_CODE&#125;&amp;token=BN7Q…</code>) already has your real secret token filled in — just replace the <code class="bg-yellow-100 px-1 rounded font-mono">&#123;TRACKING_CODE&#125;</code>, <code class="bg-yellow-100 px-1 rounded font-mono">&#123;VALUE&#125;</code>, and <code class="bg-yellow-100 px-1 rounded font-mono">&#123;TXN_ID&#125;</code> placeholders. Both the offer page URL and this JSON example reach the exact same endpoint.
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <h6 class="font-bold mb-3">💻 Code Examples (Copy & Adapt):</h6>

                                        <!-- PHP Example -->
                                        <div class="mb-4">
                                            <div class="bg-purple-100 px-3 py-1 rounded-t font-semibold text-purple-900">PHP (Laravel, WordPress, etc.)</div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-gray-500">// Step 1: On landing page — save the tracking code from the URL</span>
$_SESSION['affiliate_tracking_code'] = $_GET['tracking_code'] ?? null;

<span class="text-gray-500">// Step 2: When order is completed — fire the postback</span>
$trackingCode = $_SESSION['affiliate_tracking_code'];
if ($trackingCode) {
    Http::post('https://dealsintel.com/api/postback', [
        'tracking_code'    => $trackingCode,
        'token'            => 'YOUR_OFFER_POSTBACK_SECRET', <span class="text-gray-500">// From offer settings</span>
        'conversion_value' => $order->total,
        'transaction_id'   => $order->id,
        'customer_id'      => $customer->id,  <span class="text-gray-500">// RevShare recurring: pass your subscriber/customer ID</span>
    ]);
}
                                            </div>
                                        </div>

                                        <!-- Node.js Example -->
                                        <div class="mb-4">
                                            <div class="bg-green-100 px-3 py-1 rounded-t font-semibold text-green-900">Node.js (Express, Next.js, etc.)</div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-gray-500">// Step 1: On landing page — save the tracking code from the URL</span>
req.session.affiliateTrackingCode = req.query.tracking_code || null;

<span class="text-gray-500">// Step 2: When order is completed — fire the postback</span>
const trackingCode = req.session.affiliateTrackingCode;
if (trackingCode) {
  await fetch('https://dealsintel.com/api/postback', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      tracking_code:    trackingCode,
      token:            'YOUR_OFFER_POSTBACK_SECRET', <span class="text-gray-500">// From offer settings</span>
      conversion_value: order.total,
      transaction_id:   order.id,
      customer_id:      customer.id,   <span class="text-gray-500">// RevShare recurring: pass your subscriber/customer ID</span>
    })
  });
}
                                            </div>
                                        </div>

                                        <!-- Python Example -->
                                        <div class="mb-4">
                                            <div class="bg-blue-100 px-3 py-1 rounded-t font-semibold text-blue-900">Python (Django, Flask, etc.)</div>
                                            <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-gray-500"># Step 1: On landing page — save the tracking code from the URL</span>
request.session['affiliate_tracking_code'] = request.GET.get('tracking_code')

<span class="text-gray-500"># Step 2: When order is completed — fire the postback</span>
import requests as http_client
tracking_code = request.session.get('affiliate_tracking_code')
if tracking_code:
    http_client.post('https://dealsintel.com/api/postback', json={
        'tracking_code':    tracking_code,
        'token':            'YOUR_OFFER_POSTBACK_SECRET',  <span class="text-gray-500"># From offer settings</span>
        'conversion_value': order.total,
        'transaction_id':   order.id,
        'customer_id':      customer.id,   <span class="text-gray-500"># RevShare recurring: pass your subscriber/customer ID</span>
    })
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-green-50 border-2 border-green-400 rounded-lg p-4">
                                        <div class="font-bold text-green-900 mb-2 flex items-center">
                                            <span class="text-xl mr-2">✅</span>
                                            Testing Your Integration
                                        </div>
                                        <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                                            <li>Create a test affiliate link in your dashboard</li>
                                            <li>Click it yourself (use incognito mode)</li>
                                            <li>Notice the <code class="bg-gray-200 px-1">?tracking_code=...</code> in your URL — save this value</li>
                                            <li>Make a test purchase on your site, then fire the postback</li>
                                            <li>Check your DealsIntel dashboard—you should see the conversion! 🎉</li>
                                        </ol>
                                    </div>
                                </div>
                            </details>
                        </div>
                        <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-lg p-6">
                            <h4 class="text-2xl font-bold mb-4 flex items-center text-green-800">
                                <span class="text-3xl mr-3">💰</span>
                                Bonus: Affiliate Discount Support
                            </h4>

                            <div class="bg-white rounded-lg p-4 mb-4 border-2 border-green-300">
                                <p class="text-gray-700 mb-4">
                                    <strong>What is this?</strong> Your affiliates can offer customers discounts to increase conversions!
                                    When they enable a discount, their tracking URL includes a <code class="bg-gray-200 px-2 py-1 rounded">?discount=10</code> parameter.
                                </p>
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                    <p class="text-sm text-blue-900">
                                        <strong>💡 Benefits:</strong> Affiliates with discounts typically see 2-3x higher conversion rates, meaning more sales for you!
                                    </p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-5 border border-gray-300">
                                <h5 class="font-bold text-lg mb-3 text-gray-800">📋 How to Read the Discount Parameter</h5>

                                <!-- Example URL -->
                                <div class="mb-4">
                                    <div class="text-sm text-gray-600 mb-2">Example Affiliate Link with Discount:</div>
                                    <div class="bg-gray-100 p-3 rounded border border-gray-300 font-mono text-sm break-all">
                                        https://clicksintel.com/products/123<span class="bg-yellow-200 font-bold">?cid=abc123&discount=10</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        The <span class="font-mono bg-yellow-100 px-1">discount=10</span> means the customer should get 10% off
                                    </div>
                                </div>

                                <!-- Code Examples -->
                                <div class="space-y-4">
                                    <!-- PHP/Laravel Example -->
                                    <div>
                                        <div class="bg-blue-100 px-3 py-1 rounded-t font-semibold text-blue-900">PHP / Laravel Example</div>
                                        <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-gray-500">// On your product/checkout page:</span>

<span class="text-yellow-300">$discountPercentage</span> = <span class="text-blue-400">request</span>()-&gt;<span class="text-green-400">query</span>('discount'); <span class="text-gray-500">// Gets the discount value from URL</span>

<span class="text-purple-400">if</span> (<span class="text-yellow-300">$discountPercentage</span> &amp;&amp; <span class="text-yellow-300">$discountPercentage</span> &gt; 0 &amp;&amp; <span class="text-yellow-300">$discountPercentage</span> &lt;= 100) {
    <span class="text-gray-500">// Calculate discount amount</span>
    <span class="text-yellow-300">$discountAmount</span> = <span class="text-yellow-300">$productPrice</span> * (<span class="text-yellow-300">$discountPercentage</span> / 100);
    <span class="text-yellow-300">$finalPrice</span> = <span class="text-yellow-300">$productPrice</span> - <span class="text-yellow-300">$discountAmount</span>;

    <span class="text-gray-500">// Display to customer</span>
    <span class="text-purple-400">echo</span> <span class="text-green-300">"Original: "</span> . <span class="text-yellow-300">$productPrice</span>;
    <span class="text-purple-400">echo</span> <span class="text-green-300">"Discount: {$discountPercentage}% OFF!"</span>;
    <span class="text-purple-400">echo</span> <span class="text-green-300">"Final Price: "</span> . <span class="text-yellow-300">$finalPrice</span>;
}
                                        </div>
                                    </div>

                                    <!-- JavaScript Example -->
                                    <div>
                                        <div class="bg-yellow-100 px-3 py-1 rounded-t font-semibold text-yellow-900">JavaScript / Frontend Example</div>
                                        <div class="bg-gray-900 text-gray-100 p-4 rounded-b font-mono text-xs overflow-x-auto">
<span class="text-gray-500">// Get discount from URL</span>
<span class="text-purple-400">const</span> <span class="text-blue-400">urlParams</span> = <span class="text-purple-400">new</span> <span class="text-yellow-300">URLSearchParams</span>(<span class="text-blue-400">window</span>.location.search);
<span class="text-purple-400">const</span> <span class="text-blue-400">discount</span> = <span class="text-yellow-300">parseFloat</span>(<span class="text-blue-400">urlParams</span>.<span class="text-green-400">get</span>(<span class="text-green-300">'discount'</span>)) || 0;

<span class="text-purple-400">if</span> (<span class="text-blue-400">discount</span> &gt; 0 &amp;&amp; <span class="text-blue-400">discount</span> &lt;= 100) {
    <span class="text-purple-400">const</span> <span class="text-blue-400">originalPrice</span> = 10000; <span class="text-gray-500">// Your product price</span>
    <span class="text-purple-400">const</span> <span class="text-blue-400">discountAmount</span> = <span class="text-blue-400">originalPrice</span> * (<span class="text-blue-400">discount</span> / 100);
    <span class="text-purple-400">const</span> <span class="text-blue-400">finalPrice</span> = <span class="text-blue-400">originalPrice</span> - <span class="text-blue-400">discountAmount</span>;

    <span class="text-gray-500">// Show discount banner</span>
    <span class="text-blue-400">document</span>.<span class="text-green-400">getElementById</span>(<span class="text-green-300">'discount-banner'</span>).innerHTML =
        <span class="text-green-300">`🎉 Special ${discount}% Discount Applied! You save ₦${discountAmount}!`</span>;
}
                                        </div>
                                    </div>
                                </div>

                                <!-- Important Notes -->
                                <div class="mt-4 bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4">
                                    <div class="font-bold text-yellow-900 mb-2 flex items-center">
                                        <span class="text-xl mr-2">⚠️</span>
                                        Important Implementation Notes
                                    </div>
                                    <ul class="text-sm text-gray-700 space-y-2">
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span><strong>Validate the discount:</strong> Always check that the discount is between 0-100% to prevent abuse</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span><strong>Save to session:</strong> Store the discount in the user's session so it persists through checkout</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span><strong>Show it clearly:</strong> Display the discount prominently to the customer with a banner or badge</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="mr-2">•</span>
                                            <span><strong>Commission on final price:</strong> When reporting conversions, use the discounted price (that's what the affiliate earns commission on)</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Pro Tip -->
                                <div class="mt-4 bg-green-50 border-l-4 border-green-500 p-3 rounded">
                                    <p class="text-sm text-green-900">
                                        <strong>💡 Pro Tip:</strong> Create a visual discount banner that shows when the parameter is present.
                                        Example: "🎉 Affiliate Discount Applied! You're saving {percentage}% on this purchase!"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Offline / Manual Conversion Tracking -->
                <div id="offline-tracking" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
                        <h3 class="text-2xl font-bold text-gray-800">🏦 Offline & Manual Conversion Tracking</h3>
                        <p class="text-sm text-gray-600 mt-1">Perfect for bank transfers, WhatsApp orders, and offline sales</p>
                    </div>
                    <div class="p-6">
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-400 rounded-lg p-6 mb-6">
                            <h4 class="text-xl font-bold text-purple-900 mb-3">💡 What is Manual Conversion Tracking?</h4>
                            <p class="text-gray-700 mb-4">
                                <strong>Don't have a website?</strong> No problem! Many Nigerian businesses operate through WhatsApp, phone calls, and bank transfers.
                                You can still track affiliate sales manually through your dashboard.
                            </p>
                            <div class="bg-white p-4 rounded-lg border-2 border-purple-200">
                                <p class="text-sm text-gray-700">
                                    <strong>How it works:</strong> When a customer contacts you through an affiliate's referral and pays via bank transfer,
                                    you simply log into your dashboard and report the conversion manually. The affiliate gets credited automatically!
                                </p>
                            </div>
                        </div>

                        <!-- Use Cases -->
                        <div class="mb-6">
                            <h4 class="text-xl font-bold mb-4">📱 Perfect For:</h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <span class="text-3xl mr-3">💰</span>
                                        <div>
                                            <div class="font-bold text-blue-900 mb-1">Bank Transfer Sales</div>
                                            <p class="text-sm text-gray-700">Customer transfers money and mentions affiliate code/name in narration</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <span class="text-3xl mr-3">📞</span>
                                        <div>
                                            <div class="font-bold text-green-900 mb-1">Phone/WhatsApp Orders</div>
                                            <p class="text-sm text-gray-700">Customer calls or messages after seeing affiliate's promotion</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <span class="text-3xl mr-3">🏪</span>
                                        <div>
                                            <div class="font-bold text-orange-900 mb-1">In-Person Cash Sales</div>
                                            <p class="text-sm text-gray-700">Customer visits your physical location with affiliate reference</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <span class="text-3xl mr-3">🔧</span>
                                        <div>
                                            <div class="font-bold text-red-900 mb-1">Missed Tracking</div>
                                            <p class="text-sm text-gray-700">When pixel/API tracking failed but you verified the sale happened</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step-by-Step Guide -->
                        <div class="bg-green-50 border-2 border-green-400 rounded-lg p-6 mb-6">
                            <h4 class="text-xl font-bold text-green-900 mb-4">✅ How to Report a Manual Conversion:</h4>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">1</div>
                                    <div class="flex-grow bg-white p-3 rounded-lg border-2 border-green-200">
                                        <div class="font-bold text-gray-800 mb-1">Customer Contacts You</div>
                                        <p class="text-sm text-gray-600">They mention the affiliate's name, code, or you recognize them from a tracking link click</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">2</div>
                                    <div class="flex-grow bg-white p-3 rounded-lg border-2 border-green-200">
                                        <div class="font-bold text-gray-800 mb-1">Customer Completes Payment</div>
                                        <p class="text-sm text-gray-600">Via bank transfer, cash, mobile money, or any payment method</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">3</div>
                                    <div class="flex-grow bg-white p-3 rounded-lg border-2 border-green-200">
                                        <div class="font-bold text-gray-800 mb-1">Go to Your Dashboard</div>
                                        <p class="text-sm text-gray-600">Navigate to <strong>Conversions</strong> → Click <strong>"Report Manual Conversion"</strong> button</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">4</div>
                                    <div class="flex-grow bg-white p-3 rounded-lg border-2 border-green-200">
                                        <div class="font-bold text-gray-800 mb-1">Fill in the Details</div>
                                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                                            <li>• Select the Offer</li>
                                            <li>• Select the Affiliate</li>
                                            <li>• Enter Amount (e.g., ₦25,000)</li>
                                            <li>• Enter Transaction ID (bank reference or your order number)</li>
                                            <li>• Select Date</li>
                                            <li>• Add Notes (optional)</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-green-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3 flex-shrink-0">5</div>
                                    <div class="flex-grow bg-white p-3 rounded-lg border-2 border-green-200">
                                        <div class="font-bold text-gray-800 mb-1">Submit & Done! 🎉</div>
                                        <p class="text-sm text-gray-600">The commission is calculated automatically and credited to the affiliate's balance immediately. They'll receive a notification!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips & Best Practices -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <div class="flex items-start">
                                <span class="text-2xl mr-3">💡</span>
                                <div>
                                    <h5 class="font-bold text-yellow-900 mb-2">Pro Tips:</h5>
                                    <ul class="text-sm text-yellow-800 space-y-1">
                                        <li>✓ Ask customers to mention affiliate code/name during payment</li>
                                        <li>✓ Use bank transfer narration to track affiliate referrals</li>
                                        <li>✓ Create unique codes for each affiliate (e.g., AFF001, JOHN25)</li>
                                        <li>✓ Keep a simple log of which customer came from which affiliate</li>
                                        <li>✓ Report conversions within 24-48 hours of receiving payment</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Conversion Flow -->
                <div id="conversion-flow" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🔄 Conversion Flow Explained</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-4">Complete Tracking Journey</h4>

                        <!-- Visual Flow Diagram -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-lg mb-6">
                            <div class="space-y-4">
                                <!-- Step 1 -->
                                <div class="flex items-start">
                                    <div class="bg-purple-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        1
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">User Clicks Affiliate Link</div>
                                        <div class="text-sm text-gray-600 mb-2">Affiliate shares: <code class="bg-gray-100 px-2 py-1 rounded">https://dealsintel.com/track/ABC123</code></div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-2">⏱️</span>
                                            <span>Instant redirect</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="flex items-start">
                                    <div class="bg-blue-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        2
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">We Track & Redirect</div>
                                        <div class="text-sm text-gray-600 mb-2">We save cookie (90 days) + Click data, then instantly send user to: <code class="bg-gray-100 px-2 py-1 rounded">https://yoursite.com</code></div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-2">🍪</span>
                                            <span>Cookie set, click recorded, user redirected</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="flex items-start">
                                    <div class="bg-green-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        3
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">User Browses & Purchases</div>
                                        <div class="text-sm text-gray-600 mb-2">User explores your site, adds items to cart, completes checkout</div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-2">🛒</span>
                                            <span>Can happen immediately or up to 90 days later</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="flex items-start">
                                    <div class="bg-yellow-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        4
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">Conversion Tracked</div>
                                        <div class="text-sm text-gray-600 mb-2">Your tracking pixel/postback fires on "Thank You" page</div>
                                        <div class="text-xs bg-gray-50 p-2 rounded mt-2">
                                            <div class="font-semibold mb-1">What we capture:</div>
                                            <div class="space-y-1 text-gray-600">
                                                <div>• Order value: ₦50,000</div>
                                                <div>• Transaction ID: ORDER-12345</div>
                                                <div>• Which affiliate gets credit (from cookie)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 5 -->
                                <div class="flex items-start">
                                    <div class="bg-indigo-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        5
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">Commission Calculated</div>
                                        <div class="text-sm text-gray-600 mb-2">System automatically calculates affiliate's commission</div>
                                        <div class="text-xs bg-green-50 p-2 rounded mt-2">
                                            <div class="font-semibold mb-1">Example calculation:</div>
                                            <div class="space-y-1 text-gray-600">
                                                <div>• Sale: ₦50,000</div>
                                                <div>• Commission rate: 10%</div>
                                                <div>• Affiliate tier bonus: +10% (Gold)</div>
                                                <div class="border-t pt-1 font-bold text-green-700">→ Affiliate earns: ₦5,500</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 6 -->
                                <div class="flex items-start">
                                    <div class="bg-pink-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-4 flex-shrink-0 font-bold">
                                        6
                                    </div>
                                    <div class="bg-white rounded-lg p-4 flex-grow shadow">
                                        <div class="font-bold text-lg mb-1">You Review & Approve</div>
                                        <div class="text-sm text-gray-600 mb-2">Conversion appears in your dashboard as "Pending". You can approve or reject within 30 days</div>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-2">✅</span>
                                            <span>Approve = Commission paid. Reject = No payment (e.g., refund)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <div class="flex">
                                <div class="text-2xl mr-3">💡</div>
                                <div>
                                    <p class="font-semibold text-yellow-900">Why Pending Period?</p>
                                    <p class="text-yellow-800">This protects you from paying commissions on refunded orders. Once you approve, the affiliate gets paid. If the customer refunds, reject the conversion.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Budget & Caps -->
                <div id="budget-caps" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">💰 Budget Management & Conversion Caps</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Control Your Spending</h4>
                        <p class="text-gray-700 mb-6">
                            Set limits to prevent overspending. When limits are reached, your offer automatically pauses.
                        </p>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <!-- Conversion Caps -->
                            <div class="border-2 border-purple-500 rounded-lg p-5">
                                <h5 class="font-bold text-lg mb-3 flex items-center">
                                    <span class="text-2xl mr-2">📊</span>
                                    Conversion Caps
                                </h5>
                                <div class="space-y-3">
                                    <div class="bg-purple-50 p-3 rounded">
                                        <div class="font-semibold text-purple-900">Daily Cap</div>
                                        <p class="text-sm text-purple-700">Max conversions per day (e.g., 50/day)</p>
                                        <div class="text-xs text-gray-600 mt-1">Resets at midnight</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded">
                                        <div class="font-semibold text-purple-900">Monthly Cap</div>
                                        <p class="text-sm text-purple-700">Max conversions per month (e.g., 1,000/month)</p>
                                        <div class="text-xs text-gray-600 mt-1">Resets on 1st of month</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded">
                                        <div class="font-semibold text-purple-900">Total Cap</div>
                                        <p class="text-sm text-purple-700">Lifetime limit (e.g., 10,000 total)</p>
                                        <div class="text-xs text-gray-600 mt-1">When reached, offer ends permanently</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Limits -->
                            <div class="border-2 border-green-500 rounded-lg p-5">
                                <h5 class="font-bold text-lg mb-3 flex items-center">
                                    <span class="text-2xl mr-2">💵</span>
                                    Budget Limits
                                </h5>
                                <div class="space-y-3">
                                    <div class="bg-green-50 p-3 rounded">
                                        <div class="font-semibold text-green-900">Daily Budget</div>
                                        <p class="text-sm text-green-700">Max commission payout per day</p>
                                        <div class="text-xs text-gray-600 mt-1">Example: ₦50,000/day</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded">
                                        <div class="font-semibold text-green-900">Monthly Budget</div>
                                        <p class="text-sm text-green-700">Max commission payout per month</p>
                                        <div class="text-xs text-gray-600 mt-1">Example: ₦1,000,000/month</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded">
                                        <div class="font-semibold text-green-900">Total Budget</div>
                                        <p class="text-sm text-green-700">Campaign lifetime budget</p>
                                        <div class="text-xs text-gray-600 mt-1">Example: ₦10,000,000 total</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Example Scenario -->
                        <div class="bg-blue-50 border-2 border-blue-500 rounded-lg p-6">
                            <h5 class="font-bold text-lg mb-4 text-blue-900">📖 Real-World Example</h5>
                            <div class="bg-white p-4 rounded-lg mb-3">
                                <div class="font-semibold mb-2">Your Offer Settings:</div>
                                <div class="grid md:grid-cols-2 gap-2 text-sm">
                                    <div>• Commission: ₦5,000 per sale</div>
                                    <div>• Daily cap: 20 conversions</div>
                                    <div>• Daily budget: ₦100,000</div>
                                    <div>• Monthly budget: ₦2,000,000</div>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <span class="text-green-600 mr-2 mt-1">✓</span>
                                    <div><strong>Day 1:</strong> 15 sales = ₦75,000 spent. ✅ Still active</div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-green-600 mr-2 mt-1">✓</span>
                                    <div><strong>Day 2:</strong> 20 sales = ₦100,000 spent. ⚠️ Daily cap hit—offer pauses until tomorrow</div>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-red-600 mr-2 mt-1">✗</span>
                                    <div><strong>Day 3:</strong> If you had 21 sales, conversion #21 would be rejected (over cap)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Fraud Prevention -->
                <div id="fraud-prevention" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🛡️ Fraud Prevention</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Built-in Protection</h4>
                        <p class="text-gray-700 mb-6">
                            Our system automatically detects and blocks fraudulent traffic. Here's what we monitor:
                        </p>

                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-red-50 border-2 border-red-400 rounded-lg p-4">
                                <div class="text-3xl mb-2">🤖</div>
                                <div class="font-bold text-red-900 mb-2">Bot Detection</div>
                                <p class="text-sm text-gray-600">Automatically blocks bot traffic and click farms</p>
                            </div>
                            <div class="bg-orange-50 border-2 border-orange-400 rounded-lg p-4">
                                <div class="text-3xl mb-2">🔁</div>
                                <div class="font-bold text-orange-900 mb-2">Duplicate Blocking</div>
                                <p class="text-sm text-gray-600">Prevents double-counting same transaction</p>
                            </div>
                            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4">
                                <div class="text-3xl mb-2">⚡</div>
                                <div class="font-bold text-yellow-900 mb-2">Click Velocity</div>
                                <p class="text-sm text-gray-600">Flags suspiciously fast clicking patterns</p>
                            </div>
                        </div>

                        <!-- Quality Score -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-lg mb-6">
                            <h5 class="font-semibold text-lg mb-3">Quality Score System (0-100)</h5>
                            <p class="text-sm text-gray-700 mb-4">Every click gets a quality score. Lower scores indicate potential fraud.</p>

                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="bg-green-100 border-2 border-green-500 rounded-lg p-3 text-center">
                                    <div class="text-2xl mb-1">80-100</div>
                                    <div class="font-bold text-green-700">Excellent</div>
                                    <div class="text-xs text-gray-600">High quality traffic</div>
                                </div>
                                <div class="bg-blue-100 border-2 border-blue-500 rounded-lg p-3 text-center">
                                    <div class="text-2xl mb-1">60-79</div>
                                    <div class="font-bold text-blue-700">Good</div>
                                    <div class="text-xs text-gray-600">Acceptable quality</div>
                                </div>
                                <div class="bg-yellow-100 border-2 border-yellow-500 rounded-lg p-3 text-center">
                                    <div class="text-2xl mb-1">40-59</div>
                                    <div class="font-bold text-yellow-700">Suspicious</div>
                                    <div class="text-xs text-gray-600">Review recommended</div>
                                </div>
                                <div class="bg-red-100 border-2 border-red-500 rounded-lg p-3 text-center">
                                    <div class="text-2xl mb-1">0-39</div>
                                    <div class="font-bold text-red-700">Fraudulent</div>
                                    <div class="text-xs text-gray-600">Auto-blocked</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex">
                                <div class="text-2xl mr-3">💡</div>
                                <div>
                                    <p class="font-semibold text-blue-900">You Have Final Say</p>
                                    <p class="text-blue-800">Even if our system approves a conversion, you can review and reject it if something seems off. Check the quality score in your conversions dashboard.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Best Practices -->
                <div id="best-practices" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">⭐ Best Practices</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Offer Setup -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-purple-600">📝 Creating Winning Offers</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-purple-50 p-3 rounded">
                                        <span class="mr-2 text-purple-600">✓</span>
                                        <span class="text-sm"><strong>Competitive commissions:</strong> Research competitor rates in your niche</span>
                                    </li>
                                    <li class="flex items-start bg-purple-50 p-3 rounded">
                                        <span class="mr-2 text-purple-600">✓</span>
                                        <span class="text-sm"><strong>Clear terms:</strong> Specify what counts as a valid conversion</span>
                                    </li>
                                    <li class="flex items-start bg-purple-50 p-3 rounded">
                                        <span class="mr-2 text-purple-600">✓</span>
                                        <span class="text-sm"><strong>Quality creatives:</strong> Provide banners, images, copy affiliates can use</span>
                                    </li>
                                    <li class="flex items-start bg-purple-50 p-3 rounded">
                                        <span class="mr-2 text-purple-600">✓</span>
                                        <span class="text-sm"><strong>Set realistic caps:</strong> Start conservative, scale as you see ROI</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Managing Affiliates -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-blue-600">👥 Managing Affiliates</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-blue-50 p-3 rounded">
                                        <span class="mr-2 text-blue-600">✓</span>
                                        <span class="text-sm"><strong>Vet applications:</strong> Check affiliate's traffic sources before approval</span>
                                    </li>
                                    <li class="flex items-start bg-blue-50 p-3 rounded">
                                        <span class="mr-2 text-blue-600">✓</span>
                                        <span class="text-sm"><strong>Monitor quality:</strong> Watch quality scores—ban low-quality affiliates</span>
                                    </li>
                                    <li class="flex items-start bg-blue-50 p-3 rounded">
                                        <span class="mr-2 text-blue-600">✓</span>
                                        <span class="text-sm"><strong>Reward top performers:</strong> Consider bonus commissions for your best affiliates</span>
                                    </li>
                                    <li class="flex items-start bg-blue-50 p-3 rounded">
                                        <span class="mr-2 text-blue-600">✓</span>
                                        <span class="text-sm"><strong>Communicate:</strong> Send newsletters with promotions and tips</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Technical -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-green-600">🔧 Technical Tips</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm"><strong>Test tracking:</strong> Make test purchases to verify pixel/postback works</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm"><strong>Use S2S when possible:</strong> More accurate than cookie-only</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm"><strong>Dynamic values:</strong> Pass actual order amount, not hardcoded values</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm"><strong>Deduplication:</strong> Always send unique transaction IDs</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Conversion Management -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-yellow-600">✅ Conversion Management</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-yellow-50 p-3 rounded">
                                        <span class="mr-2 text-yellow-600">✓</span>
                                        <span class="text-sm"><strong>Review regularly:</strong> Check conversions daily to approve/reject quickly</span>
                                    </li>
                                    <li class="flex items-start bg-yellow-50 p-3 rounded">
                                        <span class="mr-2 text-yellow-600">✓</span>
                                        <span class="text-sm"><strong>Be fair:</strong> Only reject legitimate issues (refunds, fraud)</span>
                                    </li>
                                    <li class="flex items-start bg-yellow-50 p-3 rounded">
                                        <span class="mr-2 text-yellow-600">✓</span>
                                        <span class="text-sm"><strong>Fast approval:</strong> Pay affiliates quickly to keep them motivated</span>
                                    </li>
                                    <li class="flex items-start bg-yellow-50 p-3 rounded">
                                        <span class="mr-2 text-yellow-600">✓</span>
                                        <span class="text-sm"><strong>Provide reasons:</strong> If rejecting, explain why (builds trust)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Store Builder Section -->
                <div id="store-builder" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🏪 Store Builder</h3>
                        <p class="text-gray-600 mt-1">Everything you need to know about building and managing your storefront.</p>
                    </div>
                    <div class="p-6 space-y-8">

                        <!-- 1. Creating a Store -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">1</span>
                                Creating Your Store
                            </h4>
                            <p class="text-gray-700 mb-4">Go to <strong>My Store → Setup</strong> to create your store. The setup wizard has four steps:</p>
                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-1">Step 1 — Business Info</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Business name, URL slug, and description</li>
                                        <li>Contact email, phone, and WhatsApp number</li>
                                        <li>About Us content (shown on your about page)</li>
                                    </ul>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-1">Step 2 — Choose Theme</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Select a storefront theme (single or multi-product)</li>
                                        <li>Theme determines your store's layout</li>
                                    </ul>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-1">Step 3 — Payment Setup</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li><strong>Direct mode</strong> — Enter your own Paystack/Flutterwave API keys. Funds go directly into your payment account.</li>
                                        <li><strong>Platform-Managed mode</strong> — The platform collects payments on your behalf using its own Paystack account. Your net earnings (after platform fee) are credited to your sales wallet for withdrawal. Requires a plan with Platform Fee set.</li>
                                        <li>You can switch between modes from <strong>Store Settings</strong> as long as no orders have been placed yet.</li>
                                    </ul>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-1">Step 4 — Choose Plan</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Pick a subscription plan (single or multi-product)</li>
                                        <li>Your plan determines available features</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-r text-sm text-blue-800">
                                <strong>Tip:</strong> For Direct mode stores, configure your payment gateway webhooks so orders are marked paid automatically — see the <a href="#webhook-setup" class="underline font-semibold">Webhook Setup</a> section. For Platform-Managed mode, see the <a href="#platform-payments" class="underline font-semibold">Platform-Managed Payments</a> section — no webhook configuration is needed.
                            </div>
                        </div>

                        <!-- 2. Store Settings -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">2</span>
                                Editing Store Settings
                            </h4>
                            <p class="text-gray-700 mb-4">After creation, go to <strong>My Store → Edit Settings</strong> to update any of the following:</p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left p-3 font-semibold text-gray-700 border-b">Field</th>
                                            <th class="text-left p-3 font-semibold text-gray-700 border-b">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr><td class="p-3 font-medium">Business Name</td><td class="p-3 text-gray-600">Your store's display name</td></tr>
                                        <tr><td class="p-3 font-medium">URL Slug</td><td class="p-3 text-gray-600">The URL path for your store e.g. <code class="bg-gray-100 px-1 rounded">/store/my-shop</code></td></tr>
                                        <tr><td class="p-3 font-medium">Description</td><td class="p-3 text-gray-600">Short store description shown in the hero section</td></tr>
                                        <tr><td class="p-3 font-medium">About Us Content</td><td class="p-3 text-gray-600">Rich-text content for your About Us page. Supports headings, bold, lists, etc.</td></tr>
                                        <tr><td class="p-3 font-medium">Email / Phone</td><td class="p-3 text-gray-600">Contact info shown in your store footer</td></tr>
                                        <tr><td class="p-3 font-medium">WhatsApp Number</td><td class="p-3 text-gray-600">Adds a floating green WhatsApp button to every page of your store. Enter with country code e.g. <code class="bg-gray-100 px-1 rounded">2348012345678</code></td></tr>
                                        <tr><td class="p-3 font-medium">Meta Title</td><td class="p-3 text-gray-600">SEO page title (overrides store name in Google results)</td></tr>
                                        <tr><td class="p-3 font-medium">Meta Description</td><td class="p-3 text-gray-600">SEO description shown in search results and social media previews</td></tr>
                                        <tr><td class="p-3 font-medium">Meta Keywords</td><td class="p-3 text-gray-600">Comma-separated keywords for SEO</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3. Theme Customization -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">3</span>
                                Theme Customization
                            </h4>
                            <p class="text-gray-700 mb-4">Go to <strong>My Store → Customize Theme</strong> to personalize your storefront's look and feel.</p>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <div class="font-semibold text-purple-800 mb-2">🎨 Colors</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Primary &amp; secondary brand colors</li>
                                        <li>Background color</li>
                                        <li>Text color</li>
                                        <li>Header &amp; footer background</li>
                                    </ul>
                                </div>
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                    <div class="font-semibold text-indigo-800 mb-2">📢 Header &amp; Hero</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Tagline and announcement banner text</li>
                                        <li>Hero heading &amp; subheading</li>
                                        <li>Hero button text</li>
                                        <li>Hero background image (upload)</li>
                                        <li>Image overlay opacity</li>
                                    </ul>
                                </div>
                                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                    <div class="font-semibold text-teal-800 mb-2">📋 Footer</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Footer tagline</li>
                                        <li>Facebook, Instagram, Twitter links</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Products -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">4</span>
                                Managing Products
                            </h4>
                            <p class="text-gray-700 mb-4">Go to <strong>My Store → Products</strong> to add, edit, or remove products.</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-2">Product Fields</div>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Name, slug, description (rich text)</li>
                                        <li>Price and compare-at price (discount display)</li>
                                        <li>SKU and stock quantity</li>
                                        <li>Up to 5 product images</li>
                                        <li>Category assignment</li>
                                        <li>Featured flag (appears in featured section)</li>
                                    </ul>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="font-semibold text-gray-800 mb-2">Delivery Fees (per product)</div>
                                    <p class="text-sm text-gray-600 mb-2">Set state-specific delivery fees per product. The fee is calculated automatically at checkout based on the customer's selected state.</p>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>Choose a Nigerian state</li>
                                        <li>Enter the delivery fee for that state</li>
                                        <li>Add as many states as needed</li>
                                        <li>States with no fee set = free delivery</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Checkout Flow -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">5</span>
                                Customer Checkout Flow
                            </h4>
                            <p class="text-gray-700 mb-4">Here's what your customers experience when placing an order:</p>
                            <div class="flex flex-col md:flex-row gap-3 items-stretch">
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">🛍️</div>
                                    <div class="font-semibold text-sm">Browse / Select Product</div>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">🛒</div>
                                    <div class="font-semibold text-sm">Add to Cart / Buy Now</div>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">📍</div>
                                    <div class="font-semibold text-sm">Select Delivery State</div>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">💳</div>
                                    <div class="font-semibold text-sm">Pay (Card / Link)</div>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-green-50 border border-green-300 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">✅</div>
                                    <div class="font-semibold text-sm">Order Confirmed</div>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Orders -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">6</span>
                                Managing Orders
                            </h4>
                            <p class="text-gray-700 mb-3">Go to <strong>My Store → Orders</strong> to view and manage all placed orders.</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                                    <div class="font-bold text-yellow-700">Pending</div>
                                    <p class="text-xs text-gray-600 mt-1">Order placed, payment not yet confirmed</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                    <div class="font-bold text-blue-700">Paid</div>
                                    <p class="text-xs text-gray-600 mt-1">Payment received, ready to fulfil</p>
                                </div>
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                                    <div class="font-bold text-purple-700">Shipped</div>
                                    <p class="text-xs text-gray-600 mt-1">Item dispatched to customer</p>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <div class="font-bold text-green-700">Delivered</div>
                                    <p class="text-xs text-gray-600 mt-1">Order completed successfully</p>
                                </div>
                            </div>
                        </div>

                        <!-- 7. Publishing -->
                        <div class="bg-green-50 border border-green-300 rounded-lg p-5">
                            <h4 class="text-lg font-semibold text-green-800 mb-2">🚀 Publishing Your Store</h4>
                            <p class="text-gray-700 text-sm mb-3">Your store is only visible to the public once it is <strong>active</strong>. To publish:</p>
                            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                <li>Ensure your subscription is active (paid)</li>
                                <li>Add at least one product</li>
                                <li>Click <strong>Publish Store</strong> from the Store Dashboard</li>
                            </ol>
                            <p class="text-sm text-gray-600 mt-3">If your subscription lapses, your store enters preview-only mode — only you can see it. Customers will see a "Store Unavailable" message until you renew.</p>
                        </div>

                    </div>
                </div>

                <!-- Platform-Managed Payments -->
                <div id="platform-payments" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <h3 class="text-2xl font-bold text-gray-800">💳 Platform-Managed Store Payments</h3>
                        <p class="text-gray-600 mt-1">Let the platform collect payments on your behalf, automatically split the revenue, and track affiliate-driven sales.</p>
                    </div>
                    <div class="p-6 space-y-6">

                        <!-- What is it -->
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="border-2 border-gray-300 rounded-xl p-5">
                                <h4 class="font-bold text-gray-800 mb-2">🔑 Direct Mode (Default)</h4>
                                <p class="text-sm text-gray-600 mb-3">You provide your own Paystack or Flutterwave API keys. Customers pay directly into your payment gateway account. You manage your own funds.</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>Full control of your payment account</span></li>
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>Works with Paystack and Flutterwave</span></li>
                                    <li class="flex items-start gap-2"><span class="text-red-500 mt-0.5">✗</span><span>No automatic affiliate commission tracking through store checkout</span></li>
                                    <li class="flex items-start gap-2"><span class="text-red-500 mt-0.5">✗</span><span>Manual refund management via your gateway dashboard</span></li>
                                </ul>
                            </div>
                            <div class="border-2 border-emerald-500 bg-emerald-50 rounded-xl p-5">
                                <h4 class="font-bold text-emerald-800 mb-2">🏦 Platform-Managed Mode</h4>
                                <p class="text-sm text-gray-600 mb-3">The platform uses its own Paystack account to collect payments. After each sale, revenue is automatically split and credited to your sales wallet.</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>Automatic affiliate commission on store purchases</span></li>
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>Revenue credited to your sales wallet</span></li>
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>Refund requests managed through dashboard</span></li>
                                    <li class="flex items-start gap-2"><span class="text-green-600 mt-0.5">✓</span><span>No need to set up your own payment keys</span></li>
                                    <li class="flex items-start gap-2"><span class="text-yellow-600 mt-0.5">!</span><span>Platform fee deducted from each sale (set by your plan)</span></li>
                                </ul>
                            </div>
                        </div>

                        <!-- How the split works -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">How Revenue Is Split</h4>
                            <p class="text-sm text-gray-600 mb-4">When a customer purchases from your platform-managed store, the payment is received by the platform. Here's how the amount is distributed:</p>
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-3">
                                        <span class="font-medium text-gray-700">Gross Sale Amount</span>
                                        <span class="font-bold text-gray-900">₦10,000</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-3">
                                        <span class="font-medium text-red-700">− Platform Fee (e.g. 5%)</span>
                                        <span class="font-bold text-red-700">− ₦500</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <span class="font-medium text-blue-700">− Affiliate Commission (if applicable)</span>
                                        <span class="font-bold text-blue-700">− ₦1,000</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-emerald-50 border border-emerald-300 rounded-lg p-3">
                                        <span class="font-semibold text-emerald-800">= Your Net Earnings (credited to sales wallet)</span>
                                        <span class="font-bold text-emerald-800">₦8,500</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-3">Affiliate commission is only deducted if the customer arrived through a tracked affiliate link with an active offer for that product.</p>
                            </div>
                        </div>

                        <!-- How to enable -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">How to Enable Platform-Managed Mode</h4>
                            <ol class="space-y-3 text-gray-700 text-sm">
                                <li class="flex gap-3"><span class="font-bold text-emerald-600 shrink-0">1.</span><span>Ensure your current plan has a <strong>Platform Fee</strong> configured (shown in your plan details). Contact support or upgrade your plan if not available.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-emerald-600 shrink-0">2.</span><span>Go to <strong>My Store → Edit Settings → Payment Configuration</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-emerald-600 shrink-0">3.</span><span>Under <strong>Payment Collection Mode</strong>, select <strong>Platform-Managed</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-emerald-600 shrink-0">4.</span><span>Save settings. The platform will now use its Paystack account for all new checkout sessions.</span></li>
                            </ol>
                            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                                <strong>⚠️ Important:</strong> Once your store has processed its first order, the payment mode is <strong>locked</strong> and cannot be changed. Choose before going live.
                            </div>
                        </div>

                        <!-- Affiliate link + store offer -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Linking Products to Affiliate Offers</h4>
                            <p class="text-sm text-gray-600 mb-3">For affiliate commissions to be tracked on platform-managed store sales, each product must have an associated <strong>active affiliate offer</strong>.</p>
                            <ol class="space-y-3 text-sm text-gray-700">
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">1.</span><span>Go to <strong>My Store → Products</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">2.</span><span>Find the product and click <strong>Add Offer</strong> in the Offer Status column.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">3.</span><span>The offer creation form is pre-filled with the product's details. Set the commission rate and submit for approval.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">4.</span><span>Once the offer is approved, affiliates can generate tracking links. When a customer arrives via a tracking link and completes a platform-managed checkout, the affiliate automatically receives their commission.</span></li>
                            </ol>
                        </div>

                    </div>
                </div>

                <!-- Sales Payouts -->
                <div id="sales-payouts" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="text-2xl font-bold text-gray-800">💸 Sales Payouts (Withdrawals)</h3>
                        <p class="text-gray-600 mt-1">How to withdraw your platform-managed store earnings to your bank account.</p>
                    </div>
                    <div class="p-6 space-y-5">

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r text-sm text-blue-800">
                            <strong>Only applies to Platform-Managed mode.</strong> If your store uses Direct mode, funds go straight to your Paystack/Flutterwave account — use those dashboards for withdrawals.
                        </div>

                        <!-- Balance & Withdrawal flow -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">How It Works</h4>
                            <div class="flex flex-col md:flex-row gap-3 items-stretch text-sm">
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">🛒</div>
                                    <div class="font-semibold">Customer Pays</div>
                                    <p class="text-xs text-gray-500 mt-1">Order placed &amp; payment verified</p>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">⚡</div>
                                    <div class="font-semibold">Instant Split</div>
                                    <p class="text-xs text-gray-500 mt-1">Platform fee &amp; affiliate commission deducted</p>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">💰</div>
                                    <div class="font-semibold">Credited to Wallet</div>
                                    <p class="text-xs text-gray-500 mt-1">Net amount added to your sales balance</p>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">🏦</div>
                                    <div class="font-semibold">Request Withdrawal</div>
                                    <p class="text-xs text-gray-500 mt-1">Submit bank details &amp; amount</p>
                                </div>
                                <div class="text-gray-400 font-bold self-center hidden md:block">→</div>
                                <div class="flex-1 bg-green-50 border border-green-300 rounded-lg p-4 text-center">
                                    <div class="text-2xl mb-1">✅</div>
                                    <div class="font-semibold">Paid Out</div>
                                    <p class="text-xs text-gray-500 mt-1">Admin approves &amp; transfers to your bank</p>
                                </div>
                            </div>
                        </div>

                        <!-- How to withdraw -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">How to Request a Withdrawal</h4>
                            <ol class="space-y-3 text-sm text-gray-700">
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">1.</span><span>Go to <strong>Sales Payouts</strong> from the main navigation.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">2.</span><span>Your current available balance is shown at the top. The minimum withdrawal amount is <strong>₦500</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">3.</span><span>Fill in the withdrawal form: amount, bank name, account number (10 digits), and account name.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">4.</span><span>Submit. A <strong>pending</strong> payout request is created and your balance is held until the request is processed.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-blue-600 shrink-0">5.</span><span>The admin reviews and approves the transfer. You'll see the status update to <strong>completed</strong> when done.</span></li>
                            </ol>
                        </div>

                        <!-- Payout statuses -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Payout Statuses</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-center">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700">pending</span>
                                    <p class="text-xs text-gray-600 mt-2">Submitted, awaiting admin review</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-blue-100 text-blue-700">processing</span>
                                    <p class="text-xs text-gray-600 mt-2">Admin is processing the bank transfer</p>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-green-100 text-green-700">completed</span>
                                    <p class="text-xs text-gray-600 mt-2">Transfer sent to your bank account</p>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700">rejected</span>
                                    <p class="text-xs text-gray-600 mt-2">Rejected (see notes). Balance refunded.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                            <strong>Note:</strong> You can only have one active pending withdrawal at a time. Cancel a pending request if you need to submit a different amount.
                        </div>
                    </div>
                </div>

                <!-- Store Refunds -->
                <div id="store-refunds" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-rose-50 to-red-50">
                        <h3 class="text-2xl font-bold text-gray-800">↩️ Store Refunds</h3>
                        <p class="text-gray-600 mt-1">How to request a refund for a platform-managed store order.</p>
                    </div>
                    <div class="p-6 space-y-5">

                        <div class="bg-rose-50 border-l-4 border-rose-400 p-4 rounded-r text-sm text-rose-800">
                            <strong>Only applies to Platform-Managed mode.</strong> For Direct mode stores, process refunds directly through your Paystack or Flutterwave dashboard.
                        </div>

                        <!-- When to request -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">When Can I Request a Refund?</h4>
                            <p class="text-sm text-gray-600 mb-3">A refund can be requested for a paid platform-managed order as long as:</p>
                            <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                                <li>The order status is <strong>paid</strong> (not shipped or delivered)</li>
                                <li>No refund has already been requested or processed for this order</li>
                            </ul>
                        </div>

                        <!-- How to request -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">How to Request a Refund</h4>
                            <ol class="space-y-3 text-sm text-gray-700">
                                <li class="flex gap-3"><span class="font-bold text-rose-600 shrink-0">1.</span><span>Go to <strong>My Store → Orders</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-rose-600 shrink-0">2.</span><span>Find the order and click <strong>Request Refund</strong>.</span></li>
                                <li class="flex gap-3"><span class="font-bold text-rose-600 shrink-0">3.</span><span>Enter a note explaining why the refund is needed (required).</span></li>
                                <li class="flex gap-3"><span class="font-bold text-rose-600 shrink-0">4.</span><span>Submit. The request enters <strong>requested</strong> status and is reviewed by the platform admin.</span></li>
                            </ol>
                        </div>

                        <!-- What happens -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">What Happens When a Refund is Approved?</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <span class="text-red-500 font-bold shrink-0">1.</span>
                                    <span>Your <strong>sales wallet balance</strong> is debited by the advertiser net amount (the amount originally credited to your wallet).</span>
                                </div>
                                <div class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <span class="text-red-500 font-bold shrink-0">2.</span>
                                    <span>If an affiliate was credited a commission for this order, their <strong>balance is clawed back</strong> (up to what they have available).</span>
                                </div>
                                <div class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <span class="text-red-500 font-bold shrink-0">3.</span>
                                    <span>The platform attempts a <strong>Paystack refund</strong> to the customer's original payment method for the full order amount.</span>
                                </div>
                            </div>
                            <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
                                <strong>Note:</strong> Ensure your sales wallet has enough balance to cover the refund debit. If your balance is insufficient, the refund request may be rejected.
                            </div>
                        </div>

                        <!-- Refund statuses -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Refund Statuses</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-center">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-yellow-100 text-yellow-700">requested</span>
                                    <p class="text-xs text-gray-600 mt-2">Submitted, awaiting admin review</p>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-green-100 text-green-700">approved</span>
                                    <p class="text-xs text-gray-600 mt-2">Approved — balances reversed &amp; Paystack refund initiated</p>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full font-medium bg-red-100 text-red-700">rejected</span>
                                    <p class="text-xs text-gray-600 mt-2">Rejected — no changes to balances</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Webhook Setup -->
                <div id="webhook-setup" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🔗 Webhook Setup</h3>
                        <p class="text-gray-600 mt-1">Configure your payment gateway to notify us when payments are completed — so orders are marked paid automatically.</p>
                    </div>
                    <div class="p-6 space-y-8">

                        <!-- What are webhooks -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3">What are Webhooks?</h4>
                            <p class="text-gray-700 mb-3">Webhooks are server-to-server notifications. When a customer pays, Paystack or Flutterwave sends an instant HTTP POST to your store's webhook URL — even if the customer closes their browser before being redirected back.</p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                                <strong>Why webhooks are more reliable:</strong> The redirect callback can fail if the customer closes the tab, has a poor connection, or navigates away. Webhooks ensure every successful payment is captured.
                            </div>
                        </div>

                        <!-- Webhook URLs -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3">Your Webhook URLs</h4>
                            <p class="text-gray-700 mb-4">Register these URLs in your payment gateway dashboard:</p>
                            <div class="space-y-3">
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <div class="text-xs text-gray-400 mb-1 font-semibold">PAYSTACK — Store Orders</div>
                                    <code class="text-green-400 text-sm">POST https://clicksintel.com/api/webhooks/paystack/store-order</code>
                                </div>
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <div class="text-xs text-gray-400 mb-1 font-semibold">FLUTTERWAVE — Store Orders</div>
                                    <code class="text-green-400 text-sm">POST https://clicksintel.com/api/webhooks/flutterwave/store-order</code>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-3">Replace <code class="bg-gray-100 px-1 rounded">clicksintel.com</code> with your actual site domain.</p>
                        </div>

                        <!-- Paystack Setup -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">1</span>
                                Setting Up Paystack Webhooks
                            </h4>
                            <ol class="space-y-3 text-gray-700">
                                <li class="flex gap-3">
                                    <span class="font-bold text-blue-600 shrink-0">1.</span>
                                    <span>Log in to your <strong>Paystack Dashboard</strong> at <code class="bg-gray-100 px-1 rounded text-sm">dashboard.paystack.com</code></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-blue-600 shrink-0">2.</span>
                                    <span>Go to <strong>Settings → API Keys &amp; Webhooks</strong></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-blue-600 shrink-0">3.</span>
                                    <span>In the <strong>Webhook URL</strong> field, enter: <code class="bg-gray-100 px-1 rounded text-sm">https://clicksintel.com/api/webhooks/paystack/store-order</code></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-blue-600 shrink-0">4.</span>
                                    <span>Click <strong>Save</strong>. Paystack will now POST to this URL on every successful charge.</span>
                                </li>
                            </ol>
                            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                <strong>Security:</strong> All Paystack webhook requests are verified using an HMAC-SHA512 signature computed from your secret key. Your store automatically validates this — no extra setup needed.
                            </div>
                        </div>

                        <!-- Flutterwave Setup -->
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-orange-100 text-orange-700 rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">2</span>
                                Setting Up Flutterwave Webhooks
                            </h4>
                            <ol class="space-y-3 text-gray-700">
                                <li class="flex gap-3">
                                    <span class="font-bold text-orange-600 shrink-0">1.</span>
                                    <span>Log in to your <strong>Flutterwave Dashboard</strong> at <code class="bg-gray-100 px-1 rounded text-sm">app.flutterwave.com</code></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-orange-600 shrink-0">2.</span>
                                    <span>Go to <strong>Settings → Webhooks</strong></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-orange-600 shrink-0">3.</span>
                                    <span>Enter your webhook URL: <code class="bg-gray-100 px-1 rounded text-sm">https://clicksintel.com/api/webhooks/flutterwave/store-order</code></span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-orange-600 shrink-0">4.</span>
                                    <span>Set a <strong>Secret Hash</strong> — a custom string you choose (e.g. <code class="bg-gray-100 px-1 rounded text-sm">my-secret-hash-123</code>). You will enter this same value as your <strong>Webhook Secret</strong> in your store's payment settings.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="font-bold text-orange-600 shrink-0">5.</span>
                                    <span>Click <strong>Save</strong>.</span>
                                </li>
                            </ol>
                            <div class="mt-4 bg-orange-50 border border-orange-200 rounded-lg p-4 text-sm text-orange-800">
                                <strong>Flutterwave Note:</strong> Flutterwave uses a custom <em>verif-hash</em> header for webhook verification. Make sure the Secret Hash you set in Flutterwave matches exactly what you enter in your store's <strong>Webhook Secret</strong> field.
                            </div>
                        </div>

                        <!-- Security note -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">🔒 Security &amp; Best Practices</h4>
                            <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                                <li>Always use your <strong>live</strong> keys and webhook URL for production stores.</li>
                                <li>Webhooks are verified server-side — forged requests are rejected automatically.</li>
                                <li>Webhook endpoints always respond with HTTP 200 quickly, even if processing is deferred.</li>
                                <li>Duplicate webhook calls (same order paid twice) are handled safely — orders won't be double-credited.</li>
                                <li>For Paystack: your store's secret key is used for webhook signature verification — no extra configuration needed.</li>
                                <li>For Flutterwave: you must enter a separate webhook secret in your store payment settings.</li>
                            </ul>
                        </div>

                    </div>
                </div>

                <!-- FAQs -->
                <div id="faqs" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">❓ Frequently Asked Questions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="(faq, index) in faqs" :key="index" class="border-b pb-4">
                                <button @click="toggleFaq(index)" class="w-full text-left flex justify-between items-center">
                                    <span class="font-semibold text-gray-800">{{ faq.question }}</span>
                                    <span class="text-2xl">{{ openFaqs.includes(index) ? '−' : '+' }}</span>
                                </button>
                                <div v-if="openFaqs.includes(index)" class="mt-2 text-gray-600 pl-4">
                                    {{ faq.answer }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support -->
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg p-8 text-center">
                    <h3 class="text-2xl font-bold mb-3">Need Integration Help?</h3>
                    <p class="mb-6">Our technical support team can help you get set up!</p>
                    <div class="flex justify-center gap-4">
                        <a href="mailto:integration@dealsintel.com" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                            📧 Integration Support
                        </a>
                        <a href="#" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                            📚 API Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const currentUrl = computed(() => window.location.href);
const copyButtonText = ref('Copy URL');

const copyUrl = () => {
    const text = window.location.href;
    const onSuccess = () => {
        copyButtonText.value = 'Copied!';
        setTimeout(() => { copyButtonText.value = 'Copy URL'; }, 2000);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onSuccess).catch(() => fallbackCopy(text, onSuccess));
    } else {
        fallbackCopy(text, onSuccess);
    }
};

const fallbackCopy = (text, onSuccess = null) => {
    const el = document.createElement('textarea');
    el.value = text;
    el.setAttribute('readonly', '');
    el.style.cssText = 'position:fixed;top:-9999px;left:-9999px';
    document.body.appendChild(el);
    el.select();
    try { document.execCommand('copy'); if (onSuccess) onSuccess(); } catch { prompt('Copy this URL:', text); }
    document.body.removeChild(el);
};

const sections = [
    { id: 'overview', title: 'Platform Overview', description: 'How it works', icon: '🎯' },
    { id: 'commission-models', title: 'Commission Models', description: 'PPS, PPL & RevShare explained', icon: '💰' },
    { id: 'tracking-integration', title: 'Tracking Integration', description: 'Technical setup guide', icon: '🔧' },
    { id: 'offline-tracking', title: 'Offline/Manual Tracking', description: 'For bank transfers & offline sales', icon: '🏦' },
    { id: 'conversion-flow', title: 'Conversion Flow', description: 'Step-by-step process', icon: '🔄' },
    { id: 'budget-caps', title: 'Budget & Caps', description: 'Control spending', icon: '💰' },
    { id: 'fraud-prevention', title: 'Fraud Prevention', description: 'Built-in protection', icon: '🛡️' },
    { id: 'best-practices', title: 'Best Practices', description: 'Tips for success', icon: '⭐' },
    { id: 'store-builder', title: 'Store Builder', description: 'Build & manage your storefront', icon: '🏪' },
    { id: 'platform-payments', title: 'Platform-Managed Payments', description: 'Let the platform collect & split sales', icon: '💳' },
    { id: 'sales-payouts', title: 'Sales Payouts', description: 'Withdraw your store earnings', icon: '💸' },
    { id: 'store-refunds', title: 'Store Refunds', description: 'Requesting & managing refunds', icon: '↩️' },
    { id: 'webhook-setup', title: 'Webhook Setup', description: 'Configure payment notifications', icon: '🔗' },
    { id: 'faqs', title: 'FAQs', description: 'Common questions', icon: '❓' },
];

const faqs = [
    {
        question: 'Do I need both cookie tracking AND S2S?',
        answer: 'No — choose one method. The tracking pixel (cookie-based) is the simplest to set up. S2S postback is more reliable and unaffected by ad-blockers. If you use both, always pass a unique transaction_id in every request so the same conversion is not counted twice. Important: RevShare offers only support S2S postback — the pixel is not available for RevShare.'
    },
    {
        question: 'What commission rate should I offer?',
        answer: 'Typical rates: SaaS products 20-30%, digital products 30-50%, physical products 5-15%. Check competitors and ensure you still profit after commissions.'
    },
    {
        question: 'Can I change commission rates later?',
        answer: 'Yes. Edit your offer and submit it for re-approval. Once the admin approves the updated offer, the new rate applies to all affiliates on that offer immediately — there is no per-affiliate rate memory. Plan rate changes carefully so active affiliates are not surprised.'
    },
    {
        question: 'How quickly should I approve conversions?',
        answer: 'We recommend reviewing pending conversions promptly — ideally within a few days of them appearing in your dashboard. Approved conversions credit the affiliate; rejected ones are not paid. The admin can also enable auto-approval globally, in which case valid postbacks are approved immediately without manual review.'
    },
    {
        question: 'What if an affiliate sends fraudulent traffic?',
        answer: 'Reject suspicious conversions from your Conversions page — rejected conversions are never paid. The platform also prevents duplicate conversions by rejecting any postback that reuses the same transaction_id within 30 days. If you need a specific affiliate removed from your offer entirely, contact the admin to revoke their access.'
    },
    {
        question: 'Can I have different commission rates per affiliate?',
        answer: 'Not currently — all affiliates on an offer earn the same base rate. Top performers are rewarded automatically through the platform\'s tier system, which adds up to 15% bonus commission as affiliates reach higher tiers (Bronze → Silver → Gold → Platinum) based on their conversion volume and earnings.'
    },
    {
        question: 'What happens when my budget cap is reached?',
        answer: 'If auto-pause is enabled on your offer, it will be automatically paused when the cap is hit and you will receive a notification. New conversions beyond the cap are rejected. You can reactivate the offer by topping up your wallet or increasing the cap. If auto-pause is not enabled, the cap acts as a warning threshold only.'
    },
    {
        question: 'Do I pay platform fees on top of commissions?',
        answer: 'Yes — the admin sets a platform spread percentage for your offer during approval. This is added on top of your commission rate, so your total cost per conversion is slightly higher than what the affiliate receives. For example: if the commission rate is ₦10,000 and the platform spread is 10%, you are charged ₦11,000 per conversion while the affiliate earns ₦10,000. The exact spread for your offer is visible on your offer\'s detail page.'
    },
    {
        question: 'Can I test the tracking before going live?',
        answer: 'Yes. Set your offer to "Request Access" mode so only approved affiliates can promote it. Create a test affiliate account, approve it, generate a tracking link, and use that link to make a test purchase. Check your Conversions page to confirm the postback or pixel was received correctly before opening the offer to all affiliates.'
    },
    {
        question: 'What if the tracking pixel doesn\'t fire?',
        answer: 'Double-check: 1) Pixel is on thank-you page, 2) Page actually loads after purchase, 3) Transaction ID is unique, 4) Amount is passed correctly. Contact support if still having issues.'
    },
    {
        question: 'What is Platform-Managed payment mode?',
        answer: 'In Platform-Managed mode the platform collects store payments using its own Paystack account, automatically deducts the platform fee and any affiliate commission, and credits your net earnings to your sales wallet. This enables automatic affiliate commission tracking on store sales.'
    },
    {
        question: 'Can I switch between Direct and Platform-Managed payment modes?',
        answer: 'Yes, but only before your store processes its first order. Once any order has been placed the payment mode is locked to protect existing order records. Plan ahead before going live.'
    },
    {
        question: 'How long does a sales payout take?',
        answer: 'Payout requests are reviewed manually by the admin team, typically within 1-3 business days. You will see the status update to \'processing\' and then \'completed\' when the bank transfer is sent.'
    },
    {
        question: 'What if my refund request is rejected?',
        answer: 'If an admin rejects your refund request, no balances are changed — your sales wallet and the affiliate\'s balance stay the same. You can review the rejection note provided and contact support if you believe it was rejected in error.'
    },
    {
        question: 'Do affiliates earn commission on platform-managed store orders?',
        answer: 'Yes, automatically! When a customer arrives through an affiliate tracking link, makes a purchase in your platform-managed store, and the product has an active affiliated offer, the commission is calculated and credited to the affiliate\'s balance instantly after payment verification.'
    }
];

const openFaqs = ref([]);

const toggleFaq = (index) => {
    if (openFaqs.value.includes(index)) {
        openFaqs.value = openFaqs.value.filter(i => i !== index);
    } else {
        openFaqs.value.push(index);
    }
};
</script>
