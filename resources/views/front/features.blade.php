@extends('layouts.front')

@section('title')
Platform Features - Advanced Tracking & Analytics | {{ config('app.name') }}
@endsection

@section('meta_description')
Explore powerful features: real-time tracking, fraud protection, smart link rotation, multiple payment models, API integration, and 24/7 support for affiliates and advertisers.
@endsection

@section('meta_keywords')
affiliate tracking, real-time analytics, fraud detection, link rotation, API integration, performance tracking, conversion tracking, affiliate dashboard
@endsection

@section('og_title')
Powerful Features for Performance Marketing Success
@endsection

@section('og_description')
Real-time tracking, advanced analytics, fraud protection, and smart automation tools for affiliates and advertisers.
@endsection

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "SoftwareApplication",
  "name": "{{ config('app.name') }} Platform",
  "applicationCategory": "BusinessApplication",
  "offers": {
    "@@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  },
  "featureList": "Real-time tracking, Fraud protection, Analytics dashboard, API access, Multiple payment models"
}
</script>
@endpush

@push('styles')
<style>
    .feature-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
        transition: all 0.25s ease;
    }
    .feature-card:hover {
        border-color: #404040;
        background: #1c1c1c;
        transform: translateY(-4px);
    }

    .feature-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .badge-feature {
        background: #1f1f1f;
        border: 1px solid #2c2c2c;
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.7rem;
        font-weight: 500;
        color: #a3a3a3;
    }

    .stat-bubble {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
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
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.048.58.024 1.193-.14 1.743m-3.763 2.244l-2.5 2.5m3.763-2.244a3.003 3.003 0 00-2.122-2.122m-1.641 1.641L5.5 16.5" />
                </svg>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Platform Capabilities</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Everything you need<br />
                <span class="text-emerald-400">for performance marketing</span>
            </h1>
            <p class="text-lg text-neutral-400 leading-relaxed">
                Real-time tracking, advanced analytics, fraud protection, and smart automation tools
                designed to help affiliates and advertisers maximize their results.
            </p>
        </div>
    </div>
</section>

<!-- Stats Summary -->
<section class="py-12 bg-neutral-900 border-y border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-bubble p-4 text-center">
                <div class="text-2xl font-bold text-emerald-400 mb-1">99.9%</div>
                <div class="text-xs text-neutral-500 uppercase tracking-wide">Tracking Accuracy</div>
            </div>
            <div class="stat-bubble p-4 text-center">
                <div class="text-2xl font-bold text-white mb-1">&lt;100ms</div>
                <div class="text-xs text-neutral-500 uppercase tracking-wide">Click Response Time</div>
            </div>
            <div class="stat-bubble p-4 text-center">
                <div class="text-2xl font-bold text-white mb-1">24/7</div>
                <div class="text-xs text-neutral-500 uppercase tracking-wide">Support Available</div>
            </div>
            <div class="stat-bubble p-4 text-center">
                <div class="text-2xl font-bold text-white mb-1">50B+</div>
                <div class="text-xs text-neutral-500 uppercase tracking-wide">Monthly Requests</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Core Features</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Powerful tools for serious marketers
            </h2>
            <p class="text-lg text-neutral-400">
                Everything you need to track, optimize, and scale your performance marketing campaigns.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Feature 1 - Real-Time Analytics -->
            <div class="feature-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xl font-bold text-white">Real-Time Analytics</h3>
                    <span class="badge-feature">Live</span>
                </div>
                <p class="text-neutral-400 leading-relaxed">
                    Monitor every click, conversion, and commission as they happen. Real-time dashboards with instant updates and detailed breakdowns by source, device, and geography.
                </p>
            </div>

            <!-- Feature 2 - Smart Link Rotation -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12h9" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Smart Link Rotation</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Optimize performance with intelligent link rotation. Automatically distribute traffic across multiple offers based on rules, weights, or performance metrics.
                </p>
            </div>

            <!-- Feature 3 - Fraud Protection -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Advanced Fraud Protection</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Multi-layered fraud detection including IP analysis, device fingerprinting, behavioral patterns, and AI-powered anomaly detection to protect your campaigns.
                </p>
            </div>

            <!-- Feature 4 - Multiple Payout Models -->
            <div class="feature-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Multiple Payout Models</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Support for CPA, CPL, CPS, CPM, and RevShare. Flexible commission structures to match your business model and campaign goals.
                </p>
            </div>

            <!-- Feature 5 - Detailed Reporting -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Detailed Reporting</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Comprehensive reports with customizable date ranges, filters, and export options (CSV, Excel, PDF). Make data-driven decisions with actionable insights.
                </p>
            </div>

            <!-- Feature 6 - API Integration -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">RESTful API</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Robust API for seamless integration with your existing tools and workflows. Full documentation, webhooks, and real-time data synchronization available.
                </p>
            </div>

            <!-- Feature 7 - 24/7 Support -->
            <div class="feature-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">24/7 Expert Support</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Dedicated support team available around the clock via live chat, email, and phone. Get fast, knowledgeable assistance whenever you need it.
                </p>
            </div>

            <!-- Feature 8 - Cloud-Based -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Cloud-Native Platform</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Access your dashboard from anywhere. Our cloud-based platform ensures 99.9% uptime, automatic scaling, and lightning-fast performance worldwide.
                </p>
            </div>

            <!-- Feature 9 - Custom Tracking -->
            <div class="feature-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Custom Tracking Parameters</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Add unlimited custom tracking parameters and subIDs. Track performance across different traffic sources, campaigns, creatives, and keywords with granular precision.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Advanced Features Section -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Enterprise Ready</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Built for scale
            </h2>
            <p class="text-lg text-neutral-400">
                Advanced capabilities for high-volume campaigns and enterprise clients.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 text-center">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Server-Side Tracking</h3>
                <p class="text-sm text-neutral-400">
                    Direct postback integration for maximum accuracy and speed.
                </p>
            </div>
            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 text-center">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Sub-Affiliate Management</h3>
                <p class="text-sm text-neutral-400">
                    Create and manage sub-affiliate networks with multi-tier tracking.
                </p>
            </div>
            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 text-center">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">White-Label Solution</h3>
                <p class="text-sm text-neutral-400">
                    Custom branding and personalized dashboard for enterprise clients.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-10 md:p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Experience These Features?
            </h2>
            <p class="text-lg text-neutral-400 mb-8 max-w-xl mx-auto">
                Join {{ config('app.name') }} today and take your affiliate marketing to the next level.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.affiliate') }}" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-xl font-semibold transition-all duration-200">
                    Sign Up as Affiliate
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('register.advertiser') }}" class="inline-flex items-center justify-center bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-3.5 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                    Sign Up as Advertiser
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                No credit card required • Get started in minutes • Cancel anytime
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
