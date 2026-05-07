<template>
    <AppLayout title="Affiliate Documentation">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Affiliate Documentation
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page URL Info -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <div>
                                <div class="text-sm font-semibold text-blue-900">Documentation URL:</div>
                                <code class="text-xs text-blue-700 bg-blue-100 px-2 py-1 rounded">{{ currentUrl }}</code>
                            </div>
                        </div>
                        <button @click="copyUrl" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            {{ copyButtonText }}
                        </button>
                    </div>
                </div>

                <!-- Table of Contents -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 sm:px-20 bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-b border-gray-200">
                        <div class="text-2xl font-bold">
                            📚 Complete Affiliate Guide
                        </div>
                        <p class="mt-2 text-purple-100">Everything you need to know to succeed as an affiliate</p>
                    </div>

                    <div class="p-6 grid md:grid-cols-3 gap-4">
                        <a v-for="section in sections" :key="section.id"
                           :href="`#${section.id}`"
                           class="block p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:shadow-md transition">
                            <div class="text-3xl mb-2">{{ section.icon }}</div>
                            <div class="font-semibold text-gray-800">{{ section.title }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ section.description }}</div>
                        </a>
                    </div>
                </div>

                <!-- 1. Getting Started -->
                <div id="getting-started" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🚀 Getting Started</h3>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            <h4 class="text-xl font-semibold mb-3">What is Affiliate Marketing?</h4>
                            <p class="text-gray-700 mb-4">
                                Affiliate marketing is a way to earn money by promoting other companies' products. When someone clicks your special link and makes a purchase, you earn a commission. It's that simple!
                            </p>

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="text-2xl mr-3">💡</div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Quick Example:</p>
                                        <p class="text-blue-800">You share a link to a software product on your blog. Someone clicks it, buys the software for ₦50,000. You earn ₦5,000 (10% commission). The customer gets what they need, the company makes a sale, and you earn money!</p>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-xl font-semibold mb-3">Your Affiliate Journey (5 Simple Steps)</h4>

                            <!-- Journey Diagram -->
                            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-6 rounded-lg mb-6">
                                <div class="grid md:grid-cols-5 gap-4 text-center">
                                    <div class="relative">
                                        <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2 shadow-lg text-2xl">
                                            1️⃣
                                        </div>
                                        <div class="font-semibold text-gray-800">Sign Up</div>
                                        <div class="text-sm text-gray-600">Create account</div>
                                        <div v-if="!isLastStep(0)" class="hidden md:block absolute top-8 left-full w-full h-0.5 bg-purple-300"></div>
                                    </div>
                                    <div class="relative">
                                        <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2 shadow-lg text-2xl">
                                            2️⃣
                                        </div>
                                        <div class="font-semibold text-gray-800">Browse Offers</div>
                                        <div class="text-sm text-gray-600">Find products</div>
                                        <div v-if="!isLastStep(1)" class="hidden md:block absolute top-8 left-full w-full h-0.5 bg-purple-300"></div>
                                    </div>
                                    <div class="relative">
                                        <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2 shadow-lg text-2xl">
                                            3️⃣
                                        </div>
                                        <div class="font-semibold text-gray-800">Get Links</div>
                                        <div class="text-sm text-gray-600">Generate URLs</div>
                                        <div v-if="!isLastStep(2)" class="hidden md:block absolute top-8 left-full w-full h-0.5 bg-purple-300"></div>
                                    </div>
                                    <div class="relative">
                                        <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2 shadow-lg text-2xl">
                                            4️⃣
                                        </div>
                                        <div class="font-semibold text-gray-800">Promote</div>
                                        <div class="text-sm text-gray-600">Share links</div>
                                        <div v-if="!isLastStep(3)" class="hidden md:block absolute top-8 left-full w-full h-0.5 bg-purple-300"></div>
                                    </div>
                                    <div>
                                        <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2 shadow-lg text-2xl">
                                            5️⃣
                                        </div>
                                        <div class="font-semibold text-gray-800">Get Paid</div>
                                        <div class="text-sm text-gray-600">Earn money!</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. How Tracking Works -->
                <div id="tracking" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🔍 How Tracking Works</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">The Magic Behind Your Links</h4>
                        <p class="text-gray-700 mb-6">
                            Every affiliate link you create is unique to you. When someone clicks it, we "remember" that they came from you using a technology called <strong>cookies</strong> (not the food! 🍪). Even if they don't buy immediately, we track them for up to 90 days.
                        </p>

                        <!-- Tracking Flow Diagram -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="font-semibold mb-4 text-center text-lg">Tracking Journey: How You Get Credit</h5>
                            <div class="grid md:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <div class="bg-green-100 border-2 border-green-500 rounded-lg p-4 mb-2">
                                        <div class="text-3xl mb-2">👆</div>
                                        <div class="font-semibold">Step 1: Click</div>
                                    </div>
                                    <p class="text-sm text-gray-600">User clicks your affiliate link</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-blue-100 border-2 border-blue-500 rounded-lg p-4 mb-2">
                                        <div class="text-3xl mb-2">🍪</div>
                                        <div class="font-semibold">Step 2: Cookie</div>
                                    </div>
                                    <p class="text-sm text-gray-600">We save a cookie in their browser (90 days)</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-purple-100 border-2 border-purple-500 rounded-lg p-4 mb-2">
                                        <div class="text-3xl mb-2">🛒</div>
                                        <div class="font-semibold">Step 3: Purchase</div>
                                    </div>
                                    <p class="text-sm text-gray-600">They come back and buy (anytime within 90 days)</p>
                                </div>
                                <div class="text-center">
                                    <div class="bg-yellow-100 border-2 border-yellow-500 rounded-lg p-4 mb-2">
                                        <div class="text-3xl mb-2">💰</div>
                                        <div class="font-semibold">Step 4: Commission</div>
                                    </div>
                                    <p class="text-sm text-gray-600">You earn your commission!</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                            <div class="flex">
                                <div class="text-2xl mr-3">⏱️</div>
                                <div>
                                    <p class="font-semibold text-yellow-900">90-Day Cookie Window</p>
                                    <p class="text-yellow-800">This means if someone clicks your link today and buys 2 weeks later, you still get credit! The cookie "remembers" they came from you.</p>
                                </div>
                            </div>
                        </div>

                        <h4 class="text-xl font-semibold mb-3 mt-8">Your Unique Tracking Link</h4>
                        <p class="text-gray-700 mb-4">Every link you generate looks like this:</p>

                        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm mb-4">
                            https://dealsintel.com/track/<span class="bg-yellow-500 text-gray-900 px-1">ABC123XYZ</span>
                        </div>

                        <p class="text-gray-700 mb-4">That highlighted code (<code class="bg-gray-200 px-2 py-1 rounded">ABC123XYZ</code>) is YOUR unique identifier. It's how we know the click came from you!</p>
                    </div>
                </div>

                <!-- 3. Tier System -->
                <div id="tier-system" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🏆 Tier System & Bonuses</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Earn More as You Grow!</h4>
                        <p class="text-gray-700 mb-6">
                            The more you sell, the higher your tier. Higher tiers get automatic bonus percentages on EVERY sale you make. This bonus is added to your base commission!
                        </p>

                        <!-- Tier Progression -->
                        <div class="grid md:grid-cols-4 gap-4 mb-6">
                            <div class="border-2 border-gray-300 rounded-lg p-4 text-center hover:shadow-lg transition">
                                <div class="text-4xl mb-2">🥉</div>
                                <div class="font-bold text-lg text-gray-700">Bronze</div>
                                <div class="text-2xl font-bold text-gray-600 my-2">+0%</div>
                                <div class="text-sm text-gray-600 mb-2">Starting Tier</div>
                                <div class="text-xs bg-gray-100 p-2 rounded">
                                    <div>0+ conversions</div>
                                    <div>₦0+ earned</div>
                                </div>
                            </div>

                            <div class="border-2 border-gray-400 rounded-lg p-4 text-center hover:shadow-lg transition bg-gradient-to-br from-gray-50 to-gray-100">
                                <div class="text-4xl mb-2">🥈</div>
                                <div class="font-bold text-lg text-gray-700">Silver</div>
                                <div class="text-2xl font-bold text-blue-600 my-2">+5%</div>
                                <div class="text-sm text-gray-600 mb-2">Growing!</div>
                                <div class="text-xs bg-white p-2 rounded border">
                                    <div>50+ conversions</div>
                                    <div>₦5,000+ earned</div>
                                </div>
                            </div>

                            <div class="border-2 border-yellow-400 rounded-lg p-4 text-center hover:shadow-lg transition bg-gradient-to-br from-yellow-50 to-yellow-100">
                                <div class="text-4xl mb-2">🥇</div>
                                <div class="font-bold text-lg text-yellow-700">Gold</div>
                                <div class="text-2xl font-bold text-yellow-600 my-2">+10%</div>
                                <div class="text-sm text-gray-600 mb-2">High Performer!</div>
                                <div class="text-xs bg-white p-2 rounded border border-yellow-300">
                                    <div>200+ conversions</div>
                                    <div>₦25,000+ earned</div>
                                </div>
                            </div>

                            <div class="border-2 border-purple-500 rounded-lg p-4 text-center hover:shadow-lg transition bg-gradient-to-br from-purple-50 to-purple-100">
                                <div class="text-4xl mb-2">💎</div>
                                <div class="font-bold text-lg text-purple-700">Platinum</div>
                                <div class="text-2xl font-bold text-purple-600 my-2">+15%</div>
                                <div class="text-sm text-gray-600 mb-2">Elite Status!</div>
                                <div class="text-xs bg-white p-2 rounded border border-purple-300">
                                    <div>500+ conversions</div>
                                    <div>₦100,000+ earned</div>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Calculation Example -->
                        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-6 mb-6">
                            <h5 class="font-bold text-lg mb-4 text-green-900">💰 Commission Calculation Example</h5>
                            <div class="space-y-3">
                                <div class="bg-white p-4 rounded-lg border border-green-200">
                                    <div class="text-sm text-gray-600 mb-1">Scenario:</div>
                                    <div class="font-semibold">Product sells for ₦50,000 with 10% base commission</div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <div class="text-sm text-gray-600 mb-2">🥉 Bronze Tier (0% bonus)</div>
                                        <div class="space-y-1 text-sm">
                                            <div>Base: ₦5,000 (10%)</div>
                                            <div>Bonus: +₦0 (0%)</div>
                                            <div class="border-t pt-1 font-bold text-lg text-green-600">You earn: ₦5,000</div>
                                        </div>
                                    </div>

                                    <div class="bg-purple-50 p-4 rounded-lg border-2 border-purple-400">
                                        <div class="text-sm text-gray-600 mb-2">💎 Platinum Tier (15% bonus)</div>
                                        <div class="space-y-1 text-sm">
                                            <div>Base: ₦5,000 (10%)</div>
                                            <div>Bonus: +₦750 (15% of ₦5,000)</div>
                                            <div class="border-t pt-1 font-bold text-lg text-purple-600">You earn: ₦5,750</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-yellow-100 p-3 rounded-lg text-center">
                                    <span class="font-bold text-yellow-900">That's ₦750 MORE per sale just for being Platinum! 🎉</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <div class="flex">
                                <div class="text-2xl mr-3">⚡</div>
                                <div>
                                    <p class="font-semibold text-blue-900">Automatic Tier Upgrades</p>
                                    <p class="text-blue-800">Your tier is automatically upgraded when you meet the requirements. No need to apply—we handle it for you!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Referral Program -->
                <div id="referrals" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">👥 Referral Program</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Earn Passive Income Forever!</h4>
                        <p class="text-gray-700 mb-6">
                            Invite other affiliates to join the platform and earn <strong>10% of their commissions</strong> for life! They keep their full earnings, and you get a bonus on top.
                        </p>

                        <!-- Referral Flow -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-lg mb-6">
                            <h5 class="font-semibold mb-4 text-center">How Referral Commissions Work</h5>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-4 shadow">
                                    <div class="text-3xl text-center mb-2">🎯</div>
                                    <div class="font-semibold text-center mb-2">Step 1: Share</div>
                                    <p class="text-sm text-gray-600 text-center">Share your unique referral code with friends</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow">
                                    <div class="text-3xl text-center mb-2">👤</div>
                                    <div class="font-semibold text-center mb-2">Step 2: They Join</div>
                                    <p class="text-sm text-gray-600 text-center">They sign up using your code</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow">
                                    <div class="text-3xl text-center mb-2">💸</div>
                                    <div class="font-semibold text-center mb-2">Step 3: Earn Forever</div>
                                    <p class="text-sm text-gray-600 text-center">You get 10% of whatever they earn</p>
                                </div>
                            </div>
                        </div>

                        <!-- Example Calculation -->
                        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-6">
                            <h5 class="font-bold text-lg mb-4 text-green-900">📊 Real Example</h5>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between bg-white p-3 rounded">
                                    <span class="text-gray-700">Your friend makes a sale and earns:</span>
                                    <span class="font-bold text-lg">₦10,000</span>
                                </div>
                                <div class="flex items-center justify-between bg-white p-3 rounded">
                                    <span class="text-gray-700">Your referral bonus (10%):</span>
                                    <span class="font-bold text-lg text-green-600">+₦1,000</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-100 p-3 rounded border-2 border-green-400">
                                    <span class="font-bold text-gray-800">Your friend keeps:</span>
                                    <span class="font-bold text-xl text-green-700">₦10,000</span>
                                </div>
                                <div class="text-center text-sm text-gray-600 bg-white p-2 rounded">
                                    ✨ They don't lose anything—you both win!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Link Management -->
                <div id="link-management" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">🔗 Link Management & Optimization</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Advanced Features for Power Users</h4>

                        <!-- Link Rotation -->
                        <div class="mb-6">
                            <h5 class="font-semibold text-lg mb-3">🔄 Smart Link Rotation</h5>
                            <p class="text-gray-700 mb-4">
                                Create ONE link that automatically rotates between multiple offers. Perfect for testing which products your audience likes best!
                            </p>

                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="font-semibold mb-2">Rotation Strategies:</div>
                                <div class="grid md:grid-cols-2 gap-3">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-purple-600">⚡ Sequential</div>
                                        <div class="text-sm text-gray-600">Round-robin: Link 1, Link 2, Link 3, repeat</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-blue-600">⚖️ Weighted</div>
                                        <div class="text-sm text-gray-600">Custom split: 50% to Link 1, 30% to Link 2, 20% to Link 3</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-green-600">🎲 Random</div>
                                        <div class="text-sm text-gray-600">Completely random selection for A/B testing</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="font-semibold text-yellow-600">🏆 Performance</div>
                                        <div class="text-sm text-gray-600">70% to best performer, 30% to others</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Geo-Targeting -->
                        <div class="mb-6">
                            <h5 class="font-semibold text-lg mb-3">🌍 Geo-Targeting</h5>
                            <p class="text-gray-700 mb-4">
                                Show different offers based on where your visitor is located. Great for promoting region-specific products!
                            </p>

                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="font-semibold mb-2">Example:</div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <span class="mr-2">🇳🇬</span>
                                        <span>Visitors from Nigeria → See naira-priced offers</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="mr-2">🇺🇸</span>
                                        <span>Visitors from USA → See dollar-priced offers</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="mr-2">🇬🇧</span>
                                        <span>Visitors from UK → See pound-priced offers</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Device Targeting -->
                        <div class="mb-6">
                            <h5 class="font-semibold text-lg mb-3">📱 Device Targeting</h5>
                            <p class="text-gray-700 mb-4">
                                Show mobile-specific offers to mobile users, desktop offers to desktop users.
                            </p>

                            <div class="grid md:grid-cols-3 gap-3">
                                <div class="bg-white border-2 rounded-lg p-3 text-center">
                                    <div class="text-3xl mb-2">📱</div>
                                    <div class="font-semibold">Mobile</div>
                                    <div class="text-xs text-gray-600">iOS/Android apps</div>
                                </div>
                                <div class="bg-white border-2 rounded-lg p-3 text-center">
                                    <div class="text-3xl mb-2">💻</div>
                                    <div class="font-semibold">Desktop</div>
                                    <div class="text-xs text-gray-600">PC software</div>
                                </div>
                                <div class="bg-white border-2 rounded-lg p-3 text-center">
                                    <div class="text-3xl mb-2">📟</div>
                                    <div class="font-semibold">Tablet</div>
                                    <div class="text-xs text-gray-600">iPad apps</div>
                                </div>
                            </div>
                        </div>

                        <!-- Time-Based Scheduling -->
                        <div>
                            <h5 class="font-semibold text-lg mb-3">⏰ Time-Based Scheduling</h5>
                            <p class="text-gray-700 mb-4">
                                Only show offers during specific hours or days. Perfect for limited-time promotions!
                            </p>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="font-semibold mb-2">Example Use Cases:</div>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <span class="mr-2">🌙</span>
                                        <span>Night owl offer: Only active 10 PM - 2 AM</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="mr-2">📅</span>
                                        <span>Weekend special: Only Saturdays and Sundays</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="mr-2">⏰</span>
                                        <span>Business hours: Monday-Friday, 9 AM - 5 PM</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Customer Discounts -->
                <div id="customer-discounts" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                        <h3 class="text-2xl font-bold text-gray-800">💰 Customer Discounts</h3>
                        <p class="mt-2 text-sm text-gray-600">Boost conversions by offering customers instant savings</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Why Use Discounts -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">💡 Why Offer Discounts?</h4>
                            <p class="text-sm text-blue-800">
                                Giving your customers a discount makes them more likely to buy! It's a proven way to increase your
                                conversion rate and earn more commissions. While your commission is calculated on the discounted price,
                                the increased sales often result in higher total earnings.
                            </p>
                        </div>

                        <!-- How It Works -->
                        <div>
                            <h4 class="text-lg font-semibold mb-4">How It Works</h4>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <div class="text-3xl mb-2">1️⃣</div>
                                    <h5 class="font-semibold mb-2 text-purple-900">Set Your Discount</h5>
                                    <p class="text-sm text-gray-700">
                                        When generating your affiliate link, check the "Offer Discount" box and enter a percentage (e.g., 10%)
                                    </p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <div class="text-3xl mb-2">2️⃣</div>
                                    <h5 class="font-semibold mb-2 text-purple-900">Share Your Link</h5>
                                    <p class="text-sm text-gray-700">
                                        Your tracking URL will include the discount parameter automatically (e.g., ?discount=10)
                                    </p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <div class="text-3xl mb-2">3️⃣</div>
                                    <h5 class="font-semibold mb-2 text-purple-900">Customer Saves</h5>
                                    <p class="text-sm text-gray-700">
                                        The advertiser's website reads the discount and applies it at checkout automatically
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Example Calculation -->
                        <div>
                            <h4 class="text-lg font-semibold mb-4">💵 Example: How Commission Works with Discounts</h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <h5 class="font-semibold mb-3 text-red-600">❌ Without Discount</h5>
                                        <ul class="space-y-2 text-sm">
                                            <li>• Product Price: ₦10,000</li>
                                            <li>• Your Commission Rate: 10%</li>
                                            <li>• Conversion Rate: 2%</li>
                                            <li>• 100 clicks = 2 sales</li>
                                            <li class="font-bold pt-2 border-t">Total Earnings: ₦2,000</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold mb-3 text-green-600">✅ With 10% Discount</h5>
                                        <ul class="space-y-2 text-sm">
                                            <li>• Product Price: ₦10,000</li>
                                            <li>• Sale Price: ₦9,000 (10% off)</li>
                                            <li>• Your Commission Rate: 10%</li>
                                            <li>• Conversion Rate: 4% (doubled!)</li>
                                            <li>• 100 clicks = 4 sales</li>
                                            <li class="font-bold pt-2 border-t">Total Earnings: ₦3,600</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-4 bg-green-100 p-3 rounded border-l-4 border-green-500">
                                    <p class="text-sm font-semibold text-green-900">
                                        📈 Result: 80% more earnings by offering a discount!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Best Practices -->
                        <div>
                            <h4 class="text-lg font-semibold mb-4">🎯 Discount Strategies</h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4">
                                    <h5 class="font-semibold mb-2">💡 Sweet Spot: 10-15%</h5>
                                    <p class="text-sm text-gray-700">
                                        This range is attractive to customers without cutting too deeply into your commission
                                    </p>
                                </div>
                                <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
                                    <h5 class="font-semibold mb-2">🧪 Test Different Amounts</h5>
                                    <p class="text-sm text-gray-700">
                                        Try 5%, 10%, and 15% to see which converts best for your audience
                                    </p>
                                </div>
                                <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
                                    <h5 class="font-semibold mb-2">📢 Highlight the Savings</h5>
                                    <p class="text-sm text-gray-700">
                                        In your promotions, emphasize "Get 10% off with my link!" to attract clicks
                                    </p>
                                </div>
                                <div class="border-l-4 border-green-500 bg-green-50 p-4">
                                    <h5 class="font-semibold mb-2">🎁 High-Value Products</h5>
                                    <p class="text-sm text-gray-700">
                                        Discounts work especially well for premium products where percentage savings are significant
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-3 text-orange-900 flex items-center">
                                <span class="text-2xl mr-2">⚠️</span>
                                Important Notes
                            </h4>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span><strong>Commission on Discounted Price:</strong> Your earnings are calculated on the final sale amount after the discount is applied</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span><strong>Advertiser Implementation Required:</strong> The advertiser's website must be set up to read and apply the discount parameter</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span><strong>Each Link Can Have Different Discount:</strong> Create multiple links with different discounts for A/B testing</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 7. Best Practices -->
                <div id="best-practices" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">✨ Best Practices</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- DO's -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-green-600 flex items-center">
                                    <span class="text-2xl mr-2">✅</span>
                                    DO These Things
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Promote products you believe in and have tried</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Disclose that you're using affiliate links (transparency builds trust)</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Create genuine, helpful content around the products</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Test different offers to see what your audience prefers</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Track your performance and optimize based on data</span>
                                    </li>
                                    <li class="flex items-start bg-green-50 p-3 rounded">
                                        <span class="mr-2 text-green-600">✓</span>
                                        <span class="text-sm">Build an email list for long-term success</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- DON'Ts -->
                            <div>
                                <h4 class="text-lg font-semibold mb-4 text-red-600 flex items-center">
                                    <span class="text-2xl mr-2">❌</span>
                                    DON'T Do These Things
                                </h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Click your own links (this is fraud and will get you banned)</span>
                                    </li>
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Use bots or click farms (we detect this)</span>
                                    </li>
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Spam your links everywhere without context</span>
                                    </li>
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Make false claims about products</span>
                                    </li>
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Use misleading headlines or clickbait</span>
                                    </li>
                                    <li class="flex items-start bg-red-50 p-3 rounded">
                                        <span class="mr-2 text-red-600">✗</span>
                                        <span class="text-sm">Violate the advertiser's terms and conditions</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Content Strategy -->
                        <div class="mt-8 bg-purple-50 border-2 border-purple-500 rounded-lg p-6">
                            <h4 class="text-lg font-semibold mb-4 text-purple-900">💡 Content Strategy Tips</h4>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded">
                                    <div class="font-semibold mb-2">📝 Blog Posts</div>
                                    <p class="text-sm text-gray-600">Write reviews, comparisons, tutorials, and "best of" lists</p>
                                </div>
                                <div class="bg-white p-4 rounded">
                                    <div class="font-semibold mb-2">🎥 Video Content</div>
                                    <p class="text-sm text-gray-600">Create YouTube reviews, unboxings, and how-to videos</p>
                                </div>
                                <div class="bg-white p-4 rounded">
                                    <div class="font-semibold mb-2">📱 Social Media</div>
                                    <p class="text-sm text-gray-600">Share honest recommendations with your followers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Payment Info -->
                <div id="payments" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-2xl font-bold text-gray-800">💳 Getting Paid</h3>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-3">Payment Process</h4>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-blue-50 border-2 border-blue-500 rounded-lg p-4">
                                <div class="font-bold text-lg mb-2 text-blue-900">Minimum Payout</div>
                                <div class="text-3xl font-bold text-blue-600 mb-2">₦5,000</div>
                                <p class="text-sm text-blue-800">You must earn at least ₦5,000 before requesting a payout</p>
                            </div>

                            <div class="bg-green-50 border-2 border-green-500 rounded-lg p-4">
                                <div class="font-bold text-lg mb-2 text-green-900">Payment Schedule</div>
                                <div class="text-lg font-bold text-green-600 mb-2">Weekly or Monthly</div>
                                <p class="text-sm text-green-800">Choose your preferred payout frequency in settings</p>
                            </div>
                        </div>

                        <!-- Payment Timeline -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h5 class="font-semibold mb-4">Payment Timeline</h5>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">1</div>
                                    <div>
                                        <div class="font-semibold">Conversion Happens</div>
                                        <div class="text-sm text-gray-600">Customer makes a purchase through your link</div>
                                    </div>
                                </div>
                                <div class="ml-4 border-l-2 border-gray-300 h-6"></div>
                                <div class="flex items-center">
                                    <div class="bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">2</div>
                                    <div>
                                        <div class="font-semibold">Pending Status (0-30 days)</div>
                                        <div class="text-sm text-gray-600">Advertiser reviews the conversion (refund protection period)</div>
                                    </div>
                                </div>
                                <div class="ml-4 border-l-2 border-gray-300 h-6"></div>
                                <div class="flex items-center">
                                    <div class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">3</div>
                                    <div>
                                        <div class="font-semibold">Approved Status</div>
                                        <div class="text-sm text-gray-600">Commission approved and added to your available balance</div>
                                    </div>
                                </div>
                                <div class="ml-4 border-l-2 border-gray-300 h-6"></div>
                                <div class="flex items-center">
                                    <div class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">4</div>
                                    <div>
                                        <div class="font-semibold">Request Payout</div>
                                        <div class="text-sm text-gray-600">You request a payout to your bank account</div>
                                    </div>
                                </div>
                                <div class="ml-4 border-l-2 border-gray-300 h-6"></div>
                                <div class="flex items-center">
                                    <div class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">5</div>
                                    <div>
                                        <div class="font-semibold">Payment Processed (1-3 business days)</div>
                                        <div class="text-sm text-gray-600">Money sent to your account via Paystack/Flutterwave</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                            <div class="flex">
                                <div class="text-2xl mr-3">⚠️</div>
                                <div>
                                    <p class="font-semibold text-yellow-900">Why the Pending Period?</p>
                                    <p class="text-yellow-800">This protects everyone. If a customer refunds their purchase, the commission is cancelled. Once approved, it's guaranteed to be yours!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 9. FAQs -->
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
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg p-8 text-center">
                    <h3 class="text-2xl font-bold mb-3">Need More Help?</h3>
                    <p class="mb-6">Our support team is here to help you succeed!</p>
                    <div class="flex justify-center gap-4">
                        <a href="mailto:support@dealsintel.com" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                            📧 Email Support
                        </a>
                        <a href="#" class="bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-800 transition">
                            💬 Live Chat
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

const copyUrl = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        copyButtonText.value = 'Copied!';
        setTimeout(() => {
            copyButtonText.value = 'Copy URL';
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};

const sections = [
    { id: 'getting-started', title: 'Getting Started', description: 'Basics & overview', icon: '🚀' },
    { id: 'tracking', title: 'How Tracking Works', description: 'Understanding cookies & attribution', icon: '🔍' },
    { id: 'tier-system', title: 'Tier System', description: 'Earn bonus commissions', icon: '🏆' },
    { id: 'referrals', title: 'Referral Program', description: 'Passive income opportunities', icon: '👥' },
    { id: 'link-management', title: 'Link Management', description: 'Advanced features', icon: '🔗' },
    { id: 'customer-discounts', title: 'Customer Discounts', description: 'Boost conversions with savings', icon: '💰' },
    { id: 'best-practices', title: 'Best Practices', description: 'Tips for success', icon: '✨' },
    { id: 'payments', title: 'Getting Paid', description: 'Payment info & timeline', icon: '💳' },
    { id: 'faqs', title: 'FAQs', description: 'Common questions', icon: '❓' },
];

const faqs = [
    {
        question: 'How long does the cookie last?',
        answer: '90 days. If someone clicks your link and buys within 90 days, you get credit.'
    },
    {
        question: 'Can I promote multiple offers at once?',
        answer: 'Yes! You can generate links for as many offers as you want.'
    },
    {
        question: 'Do customer discounts reduce my commission?',
        answer: 'Yes, your commission is calculated on the final sale price after the discount. However, discounts typically increase conversion rates enough that you earn more overall. Example: 2 sales at ₦1,000 each = ₦2,000 vs. 4 sales at ₦900 each = ₦3,600.'
    },
    {
        question: 'What happens if someone uses an ad blocker?',
        answer: 'Most ad blockers don\'t block affiliate cookies. However, if tracking fails, the conversion won\'t be attributed to you.'
    },
    {
        question: 'Can I see which specific person clicked my link?',
        answer: 'No. We track clicks anonymously for privacy reasons. You can see aggregate stats (country, device, etc.) but not personal information.'
    },
    {
        question: 'How do I upgrade my tier?',
        answer: 'Tiers are upgraded automatically when you hit the requirements. No application needed!'
    },
    {
        question: 'Can I use paid advertising?',
        answer: 'Yes, but check each offer\'s terms. Some advertisers don\'t allow certain types of paid ads (e.g., bidding on their brand name).'
    },
    {
        question: 'What if my conversion is rejected?',
        answer: 'Advertisers may reject conversions for refunds, fraud, or terms violations. You can contact support if you believe it was rejected in error.'
    },
    {
        question: 'Do I pay any fees?',
        answer: 'No! Joining and using the platform is completely free. You only earn—we never charge you.'
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

const isLastStep = (index) => {
    return index === 4;
};
</script>
