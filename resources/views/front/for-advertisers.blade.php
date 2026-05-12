@extends('layouts.front')

@section('title', 'For Advertisers - Scale Your Business | ' . config('app.name'))
@section('meta_description', 'Connect with 15,000+ vetted affiliates. Performance-based pricing, advanced fraud protection, and real-time analytics. Only pay for results. Start your campaign today!')
@section('meta_keywords', 'performance marketing, advertiser network, CPA advertising, affiliate network for advertisers, lead generation, traffic generation, conversion tracking, fraud protection')

@section('og_title', 'Scale Your Business with Quality Affiliate Traffic')
@section('og_description', 'Performance-based advertising with 15,000+ affiliates. Advanced fraud protection, real-time tracking, and dedicated account management.')

@section('twitter_title', 'For Advertisers - Drive Quality Conversions')
@section('twitter_description', 'Connect with vetted affiliates and scale your business. Performance-based pricing - only pay for results.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Service",
  "name": "Performance Marketing for Advertisers",
  "provider": {
    "@@type": "Organization",
    "name": "{{ config('app.name') }}"
  },
  "description": "Connect with quality affiliates and scale your business with performance-based advertising",
  "areaServed": "Worldwide"
}
</script>
@endpush

@push('styles')
<style>
    .benefit-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 20px;
        transition: all 0.25s ease;
    }
    .benefit-card:hover {
        border-color: #3a3a3a;
        background: #1e1e1e;
        transform: translateY(-4px);
    }

    .feature-box {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .feature-box:hover {
        border-color: #3a3a3a;
        background: #1c1c1c;
    }

    .step-number {
        background: #10b981;
        color: white;
        font-weight: 700;
    }

    .industry-tag {
        background: #171717;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .industry-tag:hover {
        border-color: #10b981;
        background: #1a1a1a;
        transform: translateY(-2px);
    }

    .testimonial-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
    }

    .stat-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
    }

    .logo-badge {
        background: #1f1f1f;
        border: 1px solid #2c2c2c;
        border-radius: 9999px;
        padding: 0.25rem 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #a3a3a3;
    }

    .fade-up {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .fade-up.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .delay-100 { transition-delay: 0.05s; }
    .delay-200 { transition-delay: 0.1s; }
    .delay-300 { transition-delay: 0.15s; }
    .delay-400 { transition-delay: 0.2s; }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-emerald-500/5 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">For Advertisers</span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight text-white mb-6 leading-[1.1]">
                Scale Your Business with<br />
                <span class="text-emerald-400">Performance Marketing</span>
            </h1>
            <p class="text-lg md:text-xl text-neutral-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Connect with high-performing affiliates and only pay for results. Drive quality leads, sales, and conversions at scale.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 group">
                    Start Advertising
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.store-builder') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold text-lg border border-neutral-700 transition-all duration-200">
                    Explore Store Builder
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold text-lg border border-neutral-700 transition-all duration-200">
                    Contact Sales
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                Now build your own advertiser storefront with product catalogs, checkout, and subscription plans.
            </p>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-neutral-900 border-y border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-card p-5 text-center fade-up" data-animate>
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mb-1">15,000+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Affiliate Partners</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">500+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Active Advertisers</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">10M+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Conversions Monthly</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mb-1">99.9%</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Tracking Accuracy</div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Why Choose Us</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Why Advertisers Trust {{ config('app.name') }}
            </h2>
            <p class="text-lg text-neutral-400">
                Join hundreds of brands scaling profitably with our performance marketing platform
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="benefit-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Performance-Based</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Pay only for actual results. Set your own CPA, CPL, or revenue share rates and scale profitably.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Quality Traffic</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Access vetted affiliates with proven track records. We pre-screen all partners for quality and compliance.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Advanced Fraud Protection</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Multi-layered fraud detection including IP analysis, device fingerprinting, and AI-powered anomaly detection.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Real-Time Analytics</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Monitor campaign performance with detailed real-time reports. Track clicks, conversions, ROI, and more instantly.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Full Control</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Approve affiliates individually, set traffic restrictions, and maintain complete control over your brand.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Dedicated Support</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Work with experienced account managers who help optimize your campaigns for maximum ROI.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Store Builder Highlight -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Store Builder</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-white">
                    Build a Branded Storefront for Your Offers
                </h2>
                <p class="text-lg text-neutral-400 max-w-xl">
                    Launch a customised single-product or multi-product storefront in minutes. Connect direct payments, publish your own brand store URL, and manage products, orders, and subscriptions from the advertiser portal.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="feature-box p-5">
                        <h3 class="font-semibold text-white mb-2">Customizable store themes</h3>
                        <p class="text-sm text-neutral-400">Choose themes, set colors, fonts, headings, spacing, and create a look that matches your brand.</p>
                    </div>
                    <div class="feature-box p-5">
                        <h3 class="font-semibold text-white mb-2">Direct payment integration</h3>
                        <p class="text-sm text-neutral-400">Accept payments through Paystack or Flutterwave using your own keys, or use payment links for faster setup.</p>
                    </div>
                    <div class="feature-box p-5">
                        <h3 class="font-semibold text-white mb-2">Subscription-based pricing</h3>
                        <p class="text-sm text-neutral-400">Start with affordable store plans from ₦5,000 monthly. Renew manually and keep your shop live while you grow.</p>
                    </div>
                    <div class="feature-box p-5">
                        <h3 class="font-semibold text-white mb-2">Order & product management</h3>
                        <p class="text-sm text-neutral-400">Manage products, order status, store visibility and subscription expiry without leaving the advertiser dashboard.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mt-6">
                    <a href="{{ route('front.store-builder') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200">
                        View Store Builder Plans
                    </a>
                    <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                        Learn More
                    </a>
                </div>
            </div>

            <div class="bg-neutral-950 border border-neutral-800 rounded-3xl p-8">
                <div class="text-sm uppercase tracking-wider text-emerald-400 font-semibold mb-4">Starting plans</div>
                <div class="space-y-4">
                    <div class="p-5 bg-neutral-900 border border-neutral-800 rounded-2xl">
                        <div class="text-sm text-neutral-500">Single Product Store</div>
                        <div class="text-2xl font-bold text-white">₦5,000</div>
                        <div class="text-sm text-neutral-400">Monthly / ₦50,000 yearly (save 17%)</div>
                    </div>
                    <div class="p-5 bg-neutral-900 border border-neutral-800 rounded-2xl">
                        <div class="text-sm text-neutral-500">Multi-Product Bronze</div>
                        <div class="text-2xl font-bold text-white">₦15,000</div>
                        <div class="text-sm text-neutral-400">Monthly / ₦150,000 yearly - up to 10 products</div>
                    </div>
                    <div class="p-5 bg-neutral-900 border border-neutral-800 rounded-2xl">
                        <div class="text-sm text-neutral-500">Multi-Product Silver</div>
                        <div class="text-2xl font-bold text-white">₦30,000</div>
                        <div class="text-sm text-neutral-400">Monthly / ₦300,000 yearly - up to 50 products</div>
                    </div>
                    <div class="p-5 bg-neutral-900 border border-neutral-800 rounded-2xl">
                        <div class="text-sm text-neutral-500">Multi-Product Gold</div>
                        <div class="text-2xl font-bold text-white">₦50,000</div>
                        <div class="text-sm text-neutral-400">Monthly / ₦500,000 yearly - up to 200 products</div>
                    </div>
                </div>
                <p class="text-sm text-neutral-500 mt-6">
                    All prices are in NGN. Manual subscription renewal is required, and stores expire if not renewed on time.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Simple Process</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Launch Your Campaign in 4 Steps
            </h2>
            <p class="text-lg text-neutral-400">
                Get started quickly and start seeing results
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center fade-up" data-animate>
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-white mb-2">Create Account</h3>
                <p class="text-sm text-neutral-400">Sign up and set up your advertiser profile</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="100">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-white mb-2">Create Offer</h3>
                <p class="text-sm text-neutral-400">Define your offer, payout, and targeting</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="200">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-white mb-2">Get Traffic</h3>
                <p class="text-sm text-neutral-400">Approved affiliates start promoting</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="300">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">4</div>
                <h3 class="text-lg font-bold text-white mb-2">Track & Scale</h3>
                <p class="text-sm text-neutral-400">Monitor results and scale winners</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Powerful Features for Advertisers
            </h2>
            <p class="text-lg text-neutral-400">
                Everything you need to run successful performance campaigns
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate>
                <div class="text-2xl">🎯</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Smart Targeting</h3>
                    <p class="text-sm text-neutral-400">Target by geography, device, OS, browser, and custom parameters.</p>
                </div>
            </div>
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate data-delay="100">
                <div class="text-2xl">📊</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Advanced Reporting</h3>
                    <p class="text-sm text-neutral-400">Detailed reports with customizable metrics. Export for your own analysis.</p>
                </div>
            </div>
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate data-delay="200">
                <div class="text-2xl">🔗</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Flexible Tracking</h3>
                    <p class="text-sm text-neutral-400">Postback URLs, pixel tracking, API integration, and SDK support.</p>
                </div>
            </div>
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate data-delay="300">
                <div class="text-2xl">✅</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Conversion Validation</h3>
                    <p class="text-sm text-neutral-400">Review conversions before payout with automatic fraud filtering.</p>
                </div>
            </div>
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate>
                <div class="text-2xl">🔐</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Brand Safety</h3>
                    <p class="text-sm text-neutral-400">Control where your brand appears. Block specific traffic sources.</p>
                </div>
            </div>
            <div class="feature-box p-5 flex items-start gap-4 fade-up" data-animate data-delay="100">
                <div class="text-2xl">🚀</div>
                <div>
                    <h3 class="font-semibold text-white mb-1">Full API Access</h3>
                    <p class="text-sm text-neutral-400">Automated campaign management, reporting, and integration with your tools.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Industries -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Verticals</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Industries We Serve
            </h2>
            <p class="text-lg text-neutral-400">
                Proven success across multiple verticals
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="industry-tag p-4 text-center fade-up" data-animate>
                <div class="text-3xl mb-2">🛍️</div>
                <p class="font-medium text-white">E-commerce</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl mb-2">💰</div>
                <p class="font-medium text-white">Financial Services</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl mb-2">📱</div>
                <p class="font-medium text-white">Mobile Apps</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl mb-2">🎮</div>
                <p class="font-medium text-white">Gaming</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate>
                <div class="text-3xl mb-2">✈️</div>
                <p class="font-medium text-white">Travel & Tourism</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl mb-2">❤️</div>
                <p class="font-medium text-white">Health & Wellness</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl mb-2">🎓</div>
                <p class="font-medium text-white">Education</p>
            </div>
            <div class="industry-tag p-4 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl mb-2">💻</div>
                <p class="font-medium text-white">SaaS & Software</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Testimonials</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Trusted by Leading Brands
            </h2>
            <p class="text-lg text-neutral-400">
                See what our advertising partners have to say
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="testimonial-card p-6 fade-up" data-animate>
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "{{ config('app.name') }} helped us scale our customer acquisition by 300% while maintaining excellent ROI. The quality of traffic is outstanding."
                </p>
                <p class="font-semibold text-white">— Lisa T.</p>
                <p class="text-sm text-neutral-500">E-commerce Director</p>
            </div>
            <div class="testimonial-card p-6 fade-up" data-animate data-delay="100">
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "Best CPA network we've worked with. The fraud protection is top-notch and the reporting tools are incredibly detailed."
                </p>
                <p class="font-semibold text-white">— David C.</p>
                <p class="text-sm text-neutral-500">Performance Marketing Manager</p>
            </div>
            <div class="testimonial-card p-6 fade-up" data-animate data-delay="200">
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "The account management team is exceptional. They've helped us optimize our campaigns and connect with the right affiliates."
                </p>
                <p class="font-semibold text-white">— Amanda R.</p>
                <p class="text-sm text-neutral-500">Head of Growth</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-10 md:p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Scale Your Business?
            </h2>
            <p class="text-lg text-neutral-400 mb-8 max-w-xl mx-auto">
                Start driving quality conversions with {{ config('app.name') }}'s performance marketing platform.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-xl font-semibold transition-all duration-200 group">
                    Start Advertising
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-3.5 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                    Contact Sales
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                Schedule a demo and see how we can help grow your business
            </p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Intersection Observer for fade-up animations
    (function() {
        const observerOptions = { threshold: 0.15, rootMargin: '0px 0px -20px 0px' };

        const elements = document.querySelectorAll('[data-animate]');
        elements.forEach((el) => {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            obs.observe(el);
        });
    })();
</script>
@endpush
