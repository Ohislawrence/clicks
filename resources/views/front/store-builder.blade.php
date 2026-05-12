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
        background: #111827;
        border: 1px solid #27272a;
        border-radius: 24px;
        transition: transform 0.25s ease, border-color 0.25s ease;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        border-color: #10b981;
    }
    .feature-panel {
        background: #171717;
        border: 1px solid #2c2c2c;
        border-radius: 20px;
    }
    .badge-pill {
        background: #111827;
        border: 1px solid #27272a;
        border-radius: 9999px;
        padding: 0.35rem 1rem;
    }
</style>
@endpush

@section('content')

<!-- Hero -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="badge-pill text-sm uppercase tracking-wider text-emerald-400">
                    Store Builder for Advertisers
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">
                    Launch Your Own Branded Storefront in Minutes
                </h1>
                <p class="text-lg text-neutral-400 max-w-2xl">
                    Create a single-product or multi-product store, publish a public storefront URL, accept payments through Paystack or Flutterwave, and manage orders and subscriptions from your advertiser dashboard.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200">
                        Start Your Store
                    </a>
                    <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                        Talk to Sales
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
                    <div class="feature-panel p-5">
                        <div class="text-sm text-neutral-500 uppercase tracking-wide">Store Type</div>
                        <div class="text-xl font-bold text-white mt-2">Single + Multi Product</div>
                    </div>
                    <div class="feature-panel p-5">
                        <div class="text-sm text-neutral-500 uppercase tracking-wide">Payment</div>
                        <div class="text-xl font-bold text-white mt-2">Paystack / Flutterwave</div>
                    </div>
                    <div class="feature-panel p-5">
                        <div class="text-sm text-neutral-500 uppercase tracking-wide">Pricing</div>
                        <div class="text-xl font-bold text-white mt-2">From ₦5,000 / month</div>
                    </div>
                </div>
            </div>
            <div class="rounded-[2rem] border border-neutral-800 bg-neutral-900 p-8">
                <div class="rounded-3xl bg-neutral-950 border border-neutral-800 p-6 space-y-6">
                    <div class="text-sm uppercase tracking-wider text-emerald-400">Publish a live storefront</div>
                    <div class="text-white text-lg font-semibold">Your store URL</div>
                    <div class="text-neutral-400">{{ url('/store') }}/your-brand</div>
                    <div class="bg-neutral-800 border border-neutral-700 rounded-3xl p-5">
                        <div class="text-sm text-neutral-500 uppercase tracking-wide mb-2">Included with every store</div>
                        <ul class="space-y-3 text-neutral-400 text-sm">
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
<section class="py-20 bg-neutral-900 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 bg-neutral-950 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Why It Works</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white">Designed to help advertisers sell more with less setup.</h2>
            <p class="text-lg text-neutral-400 mt-4">
                Store Builder gives advertisers a complete storefront experience with product catalogs, flexible checkout, and subscription plans built for Nigerian businesses.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-4">Full Customization</div>
                <h3 class="text-2xl font-semibold text-white mb-3">Theme control</h3>
                <p class="text-neutral-400 leading-relaxed">Customize layouts, fonts, colors, spacing and product presentation to keep your storefront on-brand.</p>
            </div>
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-4">Secure Checkout</div>
                <h3 class="text-2xl font-semibold text-white mb-3">Local payment support</h3>
                <p class="text-neutral-400 leading-relaxed">Accept payments directly with Paystack or Flutterwave using your own account keys, or connect payment links.</p>
            </div>
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-4">Order Management</div>
                <h3 class="text-2xl font-semibold text-white mb-3">Simple operations</h3>
                <p class="text-neutral-400 leading-relaxed">Track orders, update fulfillment status, and manage products from the same advertiser dashboard you already use.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-white">Store Plans & Pricing</h2>
            <p class="text-lg text-neutral-400 mt-4">All plans are billed monthly or yearly. Yearly plans include a 17% savings and keep your store live with manual renewal.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-3">Single Product</div>
                <div class="text-4xl font-bold text-white mb-1">₦5,000</div>
                <div class="text-sm text-neutral-400 mb-6">Monthly</div>
                <div class="text-neutral-400 mb-6">Single product store with direct checkout and custom storefront.</div>
                <ul class="space-y-3 text-neutral-400 text-sm mb-6">
                    <li>1 product</li>
                    <li>Theme customization</li>
                    <li>Payment link or API integration</li>
                </ul>
                <div class="text-sm text-emerald-400">Yearly: ₦50,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8 border border-emerald-500/30">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-3">Multi-Product Bronze</div>
                <div class="text-4xl font-bold text-white mb-1">₦15,000</div>
                <div class="text-sm text-neutral-400 mb-6">Monthly</div>
                <div class="text-neutral-400 mb-6">Ideal for small catalogs with up to 10 products.</div>
                <ul class="space-y-3 text-neutral-400 text-sm mb-6">
                    <li>Up to 10 products</li>
                    <li>Order management</li>
                    <li>Store analytics</li>
                </ul>
                <div class="text-sm text-emerald-400">Yearly: ₦150,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-3">Multi-Product Silver</div>
                <div class="text-4xl font-bold text-white mb-1">₦30,000</div>
                <div class="text-sm text-neutral-400 mb-6">Monthly</div>
                <div class="text-neutral-400 mb-6">For growing stores with up to 50 products and advanced catalog control.</div>
                <ul class="space-y-3 text-neutral-400 text-sm mb-6">
                    <li>Up to 50 products</li>
                    <li>Promote featured items</li>
                    <li>Order notifications</li>
                </ul>
                <div class="text-sm text-emerald-400">Yearly: ₦300,000 (save 17%)</div>
            </div>

            <div class="plan-card p-8">
                <div class="text-sm uppercase tracking-wide text-emerald-400 mb-3">Multi-Product Gold</div>
                <div class="text-4xl font-bold text-white mb-1">₦50,000</div>
                <div class="text-sm text-neutral-400 mb-6">Monthly</div>
                <div class="text-neutral-400 mb-6">The premium plan for 200 products, full branding, and advanced store management.</div>
                <ul class="space-y-3 text-neutral-400 text-sm mb-6">
                    <li>Up to 200 products</li>
                    <li>Priority support</li>
                    <li>Full storefront control</li>
                </ul>
                <div class="text-sm text-emerald-400">Yearly: ₦500,000 (save 17%)</div>
            </div>
        </div>
    </div>
</section>

<!-- Workflow -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-white">How Store Builder Works</h2>
            <p class="text-lg text-neutral-400 mt-4">From plan selection to publish, your advertiser store setup is fast, guided, and built for repeatable sales.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">1</div>
                <h3 class="font-semibold text-white mb-2">Choose a plan</h3>
                <p class="text-neutral-400 text-sm">Pick the plan that fits your product catalog and business size.</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">2</div>
                <h3 class="font-semibold text-white mb-2">Customize your store</h3>
                <p class="text-neutral-400 text-sm">Set brand colors, fonts, layout, and product presentation in the dashboard.</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">3</div>
                <h3 class="font-semibold text-white mb-2">Connect payments</h3>
                <p class="text-neutral-400 text-sm">Use your Paystack/Flutterwave keys or payment link for checkout.</p>
            </div>
            <div class="feature-box p-6">
                <div class="text-3xl mb-4">4</div>
                <h3 class="font-semibold text-white mb-2">Publish your store</h3>
                <p class="text-neutral-400 text-sm">Go live, accept orders, and manage fulfilment from one place.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-neutral-900 border border-neutral-800 rounded-3xl p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to launch your advertiser storefront?</h2>
            <p class="text-lg text-neutral-400 mb-8">Start building a branded sales channel that works with your campaigns and turns traffic into customers.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200">
                    Create Your Store
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                    Contact Our Team
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
