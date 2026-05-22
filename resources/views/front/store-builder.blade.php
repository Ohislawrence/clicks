@extends('layouts.front')

@section('title', 'Store Builder for Advertisers | ' . config('app.name'))
@section('meta_description', 'Create a branded online store with subscription-based storefront plans. Custom themes, direct payment integration, and order management for advertisers.')
@section('meta_keywords', 'store builder, advertiser storefront, ecommerce store builder, paystack integration, flutterwave integration, subscription plans, performance marketing')

@section('og_title', 'Store Builder for Advertisers')
@section('og_description', 'Launch custom online storefronts with product catalogs, checkout, and subscription plans directly from your advertiser dashboard.')

@section('twitter_title', 'Store Builder for Advertisers')
@section('twitter_description', 'Build your branded storefront, manage products and orders, and accept payments with Paystack or Flutterwave.')

@push('styles')
<style>
    .plan-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        transition: transform 0.25s ease, border-color 0.25s ease;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        border-color: #10b981;
    }
    .feature-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
    }
    .badge-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 0.35rem 1rem;
    }
</style>
@endpush

@section('content')

<!-- Hero -->
<section class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="badge-pill text-sm uppercase tracking-wider text-slate-700">
                    Store Builder for Advertisers
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 leading-tight">
                    Launch Your Own Branded Storefront in Minutes
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl">
                    Create a single-product or multi-product store, publish a public storefront URL, accept payments through Paystack or Flutterwave, and manage orders and subscriptions from your advertiser dashboard.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200">
                        Start Your Store
                    </a>
                    <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-900 px-8 py-4 rounded-xl font-semibold border border-slate-200 transition-all duration-200">
                        Talk to Sales
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
                    <div class="feature-panel p-5">
                        <div class="text-sm text-slate-400 uppercase tracking-wide">Store Type</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">Single + Multi Product</div>
                    </div>
                    <div class="feature-panel p-5">
                        <div class="text-sm text-slate-400 uppercase tracking-wide">Payment Mode</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">Direct or Platform-Managed</div>
                    </div>
                    <div class="feature-panel p-5">
                        <div class="text-sm text-slate-400 uppercase tracking-wide">Pricing</div>
                        <div class="text-xl font-bold text-slate-900 mt-2">From ₦5,000 / month</div>
                    </div>
                </div>
            </div>
            <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8">
                <div class="rounded-3xl bg-white border border-slate-200 p-6 space-y-6">
                    <div class="text-sm uppercase tracking-wider text-slate-700">Publish a live storefront</div>
                    <div class="text-slate-900 text-lg font-semibold">Your store URL</div>
                    <div class="text-slate-500">{{ url('/store') }}/your-brand</div>
                    <div class="bg-slate-100 border border-slate-200 rounded-3xl p-5">
                        <div class="text-sm text-slate-400 uppercase tracking-wide mb-2">Included with every store</div>
                        <ul class="space-y-3 text-slate-500 text-sm">
                            <li>Brand theme customization</li>
                            <li>Product & order management</li>
                            <li>Manual subscription renewals</li>
                            <li>Expiry notifications & store status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Store Builder -->
<section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 bg-white border border-slate-200 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Why It Works</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Designed to help advertisers sell more with less setup.</h2>
            <p class="text-lg text-slate-500 mt-4">
                Store Builder gives advertisers a complete storefront experience with product catalogs, flexible checkout, and subscription plans built for Nigerian businesses.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-4">Full Customization</div>
                <h3 class="text-2xl font-semibold text-slate-900 mb-3">Theme control</h3>
                <p class="text-slate-500 leading-relaxed">Customize layouts, fonts, colors, spacing and product presentation to keep your storefront on-brand.</p>
            </div>
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-4">Secure Checkout</div>
                <h3 class="text-2xl font-semibold text-slate-900 mb-3">Two payment modes</h3>
                <p class="text-slate-500 leading-relaxed"><strong class="text-slate-900">Direct mode</strong> — Use your own Paystack or Flutterwave API keys; funds go straight to your payment account.<br><br><strong class="text-slate-900">Platform-Managed mode</strong> — The platform collects payments on your behalf, deducts its fee, and credits your net earnings to your sales wallet automatically.</p>
            </div>
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-4">Order Management</div>
                <h3 class="text-2xl font-semibold text-slate-900 mb-3">Simple operations</h3>
                <p class="text-slate-500 leading-relaxed">Track orders, update fulfillment status, and manage products from the same advertiser dashboard you already use.</p>
            </div>
        </div>
    </div>
</section>

<!-- Platform-Managed Payments -->
<section class="py-20 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">New Feature</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Platform-Managed Payments</h2>
            <p class="text-lg text-slate-500 mt-4">
                Don't want to manage your own payment gateway? Let the platform handle it. Choose Platform-Managed mode and we'll collect payments, split the revenue, and credit your wallet automatically.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Direct vs Platform comparison -->
            <div class="space-y-4">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                    <div class="text-sm font-semibold uppercase tracking-wider text-slate-500 mb-3">&#128273; Direct Mode (Default)</div>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Use your own Paystack or Flutterwave API keys</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Funds go directly into your payment account</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Works with Paystack and Flutterwave</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-400 mt-0.5">&#8212;</span><span class="text-slate-400">Manual affiliate commission management</span></li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                    <div class="text-sm font-semibold uppercase tracking-wider text-slate-700 mb-3">&#127974; Platform-Managed Mode</div>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>No payment gateway setup required</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Revenue split automatically after each sale</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Affiliate commissions tracked &amp; credited automatically</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Net earnings credited to your sales wallet</span></li>
                        <li class="flex items-start gap-2"><span class="text-slate-700 mt-0.5">&#10003;</span><span>Withdraw earnings to your bank any time</span></li>
                        <li class="flex items-start gap-2"><span class="text-yellow-500 mt-0.5">!</span><span class="text-slate-500">Platform fee deducted (set per plan by admin)</span></li>
                    </ul>
                </div>
            </div>

            <!-- Revenue split example -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                <div class="text-sm uppercase tracking-wider text-slate-700 font-semibold mb-4">Revenue Split Example</div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center bg-slate-100 rounded-xl p-3">
                        <span class="text-slate-600">Customer pays</span>
                        <span class="font-bold text-slate-900">&#8358;10,000</span>
                    </div>
                    <div class="flex justify-between items-center bg-red-50 border border-red-200 rounded-xl p-3">
                        <span class="text-red-600">&#8722; Platform fee (5%)</span>
                        <span class="font-bold text-red-600">&#8722; &#8358;500</span>
                    </div>
                    <div class="flex justify-between items-center bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <span class="text-blue-600">&#8722; Affiliate commission (if applicable)</span>
                        <span class="font-bold text-blue-600">&#8722; &#8358;1,000</span>
                    </div>
                    <div class="flex justify-between items-center bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                        <span class="text-emerald-700 font-semibold">= Your earnings (added to wallet)</span>
                        <span class="font-bold text-slate-700 text-base">&#8358;8,500</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-4">All splits happen automatically when payment is verified. Affiliate commission only applies if the customer came via a tracked affiliate link.</p>
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <div class="text-sm font-semibold text-slate-900 mb-2">Your Earnings Wallet</div>
                    <p class="text-xs text-slate-500">Earnings accumulate in your <strong class="text-slate-600">Sales Wallet</strong>. Request a withdrawal any time &mdash; minimum &#8358;500 &mdash; to any Nigerian bank account from your advertiser portal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Store Plans & Pricing</h2>
            <p class="text-lg text-slate-500 mt-4">All plans are billed monthly or yearly. Yearly plans include a 17% savings and keep your store live with manual renewal.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-3">Single Product</div>
                <div class="text-4xl font-bold text-slate-900 mb-1">₦5,000</div>
                <div class="text-sm text-slate-500 mb-6">Monthly</div>
                <div class="text-slate-500 mb-6">Single product store with direct checkout and custom storefront.</div>
                <ul class="space-y-3 text-slate-500 text-sm mb-6">
                    <li>1 product</li>
                    <li>Theme customization</li>
                    <li>Payment link or API integration</li>
                </ul>
                <div class="text-sm text-slate-700">Yearly: ₦50,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8 border border-slate-300/30">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-3">Multi-Product Bronze</div>
                <div class="text-4xl font-bold text-slate-900 mb-1">₦15,000</div>
                <div class="text-sm text-slate-500 mb-6">Monthly</div>
                <div class="text-slate-500 mb-6">Ideal for small catalogs with up to 10 products.</div>
                <ul class="space-y-3 text-slate-500 text-sm mb-6">
                    <li>Up to 10 products</li>
                    <li>Order management</li>
                    <li>Store analytics</li>
                </ul>
                <div class="text-sm text-slate-700">Yearly: ₦150,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-3">Multi-Product Silver</div>
                <div class="text-4xl font-bold text-slate-900 mb-1">₦30,000</div>
                <div class="text-sm text-slate-500 mb-6">Monthly</div>
                <div class="text-slate-500 mb-6">For growing stores with up to 50 products and advanced catalog control.</div>
                <ul class="space-y-3 text-slate-500 text-sm mb-6">
                    <li>Up to 50 products</li>
                    <li>Promote featured items</li>
                    <li>Order notifications</li>
                </ul>
                <div class="text-sm text-slate-700">Yearly: ₦300,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-slate-700 mb-3">Multi-Product Gold</div>
                <div class="text-4xl font-bold text-slate-900 mb-1">₦50,000</div>
                <div class="text-sm text-slate-500 mb-6">Monthly</div>
                <div class="text-slate-500 mb-6">The premium plan for 200 products, full branding, and advanced store management.</div>
                <ul class="space-y-3 text-slate-500 text-sm mb-6">
                    <li>Up to 200 products</li>
                    <li>Priority support</li>
                    <li>Full storefront control</li>
                </ul>
                <div class="text-sm text-slate-700">Yearly: ₦500,000 (save 17%)</div>
            </div>
        </div>
    </div>
</section>

<!-- Workflow -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">How Store Builder Works</h2>
            <p class="text-lg text-slate-500 mt-4">From plan selection to publish, your advertiser store setup is fast, guided, and built for repeatable sales.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">1</div>
                <h3 class="font-semibold text-slate-900 mb-2">Choose a plan</h3>
                <p class="text-slate-500 text-sm">Pick the plan that fits your product catalog and business size.</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">2</div>
                <h3 class="font-semibold text-slate-900 mb-2">Customize your store</h3>
                <p class="text-slate-500 text-sm">Set brand colors, fonts, layout, and product presentation in the dashboard.</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">3</div>
                <h3 class="font-semibold text-slate-900 mb-2">Connect payments</h3>
                <p class="text-slate-500 text-sm">Choose Direct mode (your own Paystack/Flutterwave keys) or Platform-Managed mode (platform collects &amp; splits revenue).</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">4</div>
                <h3 class="font-semibold text-slate-900 mb-2">Publish your store</h3>
                <p class="text-slate-500 text-sm">Go live, accept orders, and manage fulfilment from one place.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-slate-50 border border-slate-200 rounded-3xl p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Ready to launch your advertiser storefront?</h2>
            <p class="text-lg text-slate-500 mb-8">Start building a branded sales channel that works with your campaigns and turns traffic into customers.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200">
                    Create Your Store
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-900 px-8 py-4 rounded-xl font-semibold border border-slate-200 transition-all duration-200">
                    Contact Our Team
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
