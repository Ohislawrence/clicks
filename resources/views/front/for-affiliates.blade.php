@extends('layouts.front')

@section('title', 'For Affiliates - Earn with Top CPA Offers | ' . config('app.name'))
@section('meta_description', 'Join 15,000+ successful affiliates earning competitive commissions. Access 2,000+ exclusive offers, weekly payments, and dedicated support. Sign up free today!')
@section('meta_keywords', 'affiliate program, CPA offers, affiliate earnings, make money online, affiliate commissions, weekly payments, affiliate network, performance marketing for affiliates')

@section('og_title', 'Monetize Your Traffic with Premium CPA Offers')
@section('og_description', 'Exclusive campaigns, competitive payouts, and weekly payments. Join thousands of successful affiliates on ' . config('app.name') . '.')

@section('twitter_title', 'For Affiliates - Start Earning Today')
@section('twitter_description', 'Access 2,000+ exclusive offers with weekly payments. Join 15,000+ affiliates earning competitive commissions.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Service",
  "name": "Affiliate Marketing Program",
  "provider": {
    "@@type": "Organization",
    "name": "{{ config('app.name') }}"
  },
  "description": "Join our affiliate program and earn competitive commissions promoting top offers",
  "offers": {
    "@@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  }
}
</script>
@endpush

@push('styles')
<style>
    :root {
        --ink:    #0f172a;
        --ink-2:  #1e293b;
        --ink-3:  #334155;
        --ink-4:  #475569;
        --em:     #0f172a;
        --em-l:   #1e293b;
        --em-dim: #e2e8f0;
        --snow:   #f8fafc;
        --ash:    #64748b;
        --stone:  #94a3b8;
        --wire:   rgba(15,23,42,0.08);
        --wire-s: rgba(15,23,42,0.15);
        --fd: 'Plus Jakarta Sans', sans-serif;
        --fb: 'Plus Jakarta Sans', sans-serif;
    }
    body::before {
        content:''; position:fixed; inset:0;
        background-image: radial-gradient(circle, rgba(15,23,42,0.04) 1px, transparent 1px);
        background-size: 24px 24px; pointer-events:none; z-index:0;
    }
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease, transform .6s ease; }
    .reveal.in-view { opacity:1; transform:translateY(0); }
    .stat-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; }
    .benefit-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:20px;
        transition:transform .25s ease, border-color .25s ease; }
    .benefit-card:hover { border-color:#cbd5e1; transform:translateY(-4px); }
    .vertical-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:16px;
        transition:border-color .2s ease, transform .2s ease; }
    .vertical-card:hover { border-color:#0f172a; transform:translateY(-2px); }
    .step-number { background:#0f172a; color:#ffffff; font-weight:700; }
    .testimonial-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-white border-b border-slate-200 relative overflow-hidden">
    <div class="absolute inset-0 bg-slate-900/5 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-900"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">For Affiliates</span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight text-slate-900 mb-6 leading-[1.1]">
                Monetize Your Traffic<br />
                <span class="text-slate-700">with Top CPA Offers</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
                Join thousands of successful affiliates earning competitive commissions with exclusive offers
                across multiple verticals.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') . '?type=affiliate' }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 group">
                    Join Now - It's Free
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-900 px-8 py-4 rounded-xl font-semibold text-lg border border-slate-200 transition-all duration-200">
                    Contact Support
                </a>
            </div>
            <p class="text-sm text-slate-400 mt-6">
                No credit card required � Get approved in 24 hours
            </p>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-card p-5 text-center reveal">
                <div class="text-3xl md:text-4xl font-bold text-slate-700 mb-1">15,000+</div>
                <div class="text-sm text-slate-400 font-medium uppercase tracking-wide">Active Affiliates</div>
            </div>
            <div class="stat-card p-5 text-center reveal">
                <div class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">2,000+</div>
                <div class="text-sm text-slate-400 font-medium uppercase tracking-wide">Live Offers</div>
            </div>
            <div class="stat-card p-5 text-center reveal">
                <div class="text-3xl md:text-4xl font-bold text-slate-700 mb-1">?2.4B+</div>
                <div class="text-sm text-slate-400 font-medium uppercase tracking-wide">Paid Monthly</div>
            </div>
            <div class="stat-card p-5 text-center reveal">
                <div class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">24/7</div>
                <div class="text-sm text-slate-400 font-medium uppercase tracking-wide">Support Available</div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Why Join Us</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                Why Affiliates Choose {{ config('app.name') }}
            </h2>
            <p class="text-lg text-slate-500">
                The platform built by affiliates, for affiliates
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">High Payouts</h3>
                <p class="text-slate-500 leading-relaxed">
                    Earn competitive commissions with some of the highest payout rates in the industry. Weekly payment options available.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Exclusive Offers</h3>
                <p class="text-slate-500 leading-relaxed">
                    Access exclusive campaigns not available on other networks, with pre-negotiated higher payouts.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Real-Time Tracking</h3>
                <p class="text-slate-500 leading-relaxed">
                    Track every click, conversion, and commission in real-time with our advanced dashboard and detailed analytics.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Dedicated Support</h3>
                <p class="text-slate-500 leading-relaxed">
                    Get personalized support from experienced affiliate managers who help optimize your campaigns.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M6 6.5L7.5 8l8.5-8.5M6 6.5l-4 4M6 6.5l3 3M6 6.5l3-3M12 6.5l-1.5 1.5M12 6.5l4 4M12 6.5l-4-4M12 6.5l4-4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Reliable Payments</h3>
                <p class="text-slate-500 leading-relaxed">
                    On-time payments every time. Multiple payment methods including bank transfer, PayPal, and more.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Marketing Tools</h3>
                <p class="text-slate-500 leading-relaxed">
                    Access banners, landing pages, email creatives, and other promotional materials to maximize conversions.
                </p>
            </div>

            <div class="benefit-card p-6 reveal">
                <div class="feature-icon w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Earn on Advertiser Store Sales</h3>
                <p class="text-slate-500 leading-relaxed">
                    When you share a tracking link to an advertiser&rsquo;s platform-managed store and a customer completes a purchase, you automatically earn your commission &mdash; no manual tracking setup required.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Verticals Section -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-slate-100/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Popular Verticals</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                High-Converting Offers
            </h2>
            <p class="text-lg text-slate-500">
                Promote offers across multiple industries with proven conversion rates
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">🛍️</div>
                <h3 class="font-semibold text-slate-900 text-sm">E-commerce</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">💰</div>
                <h3 class="font-semibold text-slate-900 text-sm">Finance</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">❤️</div>
                <h3 class="font-semibold text-slate-900 text-sm">Health & Fitness</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">🎮</div>
                <h3 class="font-semibold text-slate-900 text-sm">Gaming</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">💻</div>
                <h3 class="font-semibold text-slate-900 text-sm">Software</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">🎓</div>
                <h3 class="font-semibold text-slate-900 text-sm">Education</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">✈️</div>
                <h3 class="font-semibold text-slate-900 text-sm">Travel</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">📱</div>
                <h3 class="font-semibold text-slate-900 text-sm">Mobile Apps</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">🏠</div>
                <h3 class="font-semibold text-slate-900 text-sm">Home & Garden</h3>
            </div>
            <div class="vertical-card p-5 text-center reveal">
                <div class="text-3xl mb-2">🎯</div>
                <h3 class="font-semibold text-slate-900 text-sm">Lead Gen</h3>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Simple Process</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                Start Earning in 4 Steps
            </h2>
            <p class="text-lg text-slate-500">
                Get up and running quickly with our streamlined process
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center reveal">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Sign Up Free</h3>
                <p class="text-sm text-slate-500">Create your free affiliate account in minutes</p>
            </div>
            <div class="text-center reveal">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Browse Offers</h3>
                <p class="text-sm text-slate-500">Select offers that match your traffic and audience</p>
            </div>
            <div class="text-center reveal">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Drive Traffic</h3>
                <p class="text-sm text-slate-500">Use your unique tracking links to promote offers</p>
            </div>
            <div class="text-center reveal">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">4</div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Get Paid</h3>
                <p class="text-sm text-slate-500">Earn commissions for every conversion</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-slate-100/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700">Success Stories</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                What Our Affiliates Say
            </h2>
            <p class="text-lg text-slate-500">
                Hear from affiliates who are earning with us
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="testimonial-card p-6 reveal">
                <div class="flex gap-1 text-slate-700 mb-4">★★★★★</div>
                <p class="text-slate-500 mb-4 leading-relaxed">
                    "Best affiliate network I've worked with. On-time payments, great offers, and responsive support team."
                </p>
                <p class="font-semibold text-slate-900">- Sarah M.</p>
                <p class="text-sm text-slate-400">Super Affiliate</p>
            </div>
            <div class="testimonial-card p-6 reveal">
                <div class="flex gap-1 text-slate-700 mb-4">★★★★★</div>
                <p class="text-slate-500 mb-4 leading-relaxed">
                    "The tracking is accurate and the dashboard makes it easy to optimize campaigns. Highly recommend!"
                </p>
                <p class="font-semibold text-slate-900">- James K.</p>
                <p class="text-sm text-slate-400">Media Buyer</p>
            </div>
            <div class="testimonial-card p-6 reveal">
                <div class="flex gap-1 text-slate-700 mb-4">★★★★★</div>
                <p class="text-slate-500 mb-4 leading-relaxed">
                    "Finally found a network that actually cares about affiliates. Exclusive offers and top payouts."
                </p>
                <p class="font-semibold text-slate-900">- Michael R.</p>
                <p class="text-sm text-slate-400">Influencer</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-10 md:p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                Ready to Start Earning?
            </h2>
            <p class="text-lg text-slate-500 mb-8 max-w-xl mx-auto">
                Join {{ config('app.name') }} today and start monetizing your traffic with top-converting offers.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') . '?type=affiliate' }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-8 py-3.5 rounded-xl font-semibold transition-all duration-200 group">
                    Create Free Account
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-900 px-8 py-3.5 rounded-xl font-semibold border border-slate-200 transition-all duration-200">
                    Contact Support
                </a>
            </div>
            <p class="text-sm text-slate-400 mt-6">
                No credit card required � Get approved in 24 hours
            </p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    var reveals = document.querySelectorAll('.reveal');
    if (!reveals.length) return;
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('in-view'); io.unobserve(e.target); }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(function (r) { io.observe(r); });
})();
</script>
@endpush
