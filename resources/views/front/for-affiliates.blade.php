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

    .vertical-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .vertical-card:hover {
        border-color: #10b981;
        background: #1c1c1c;
        transform: translateY(-2px);
    }

    .step-number {
        background: #10b981;
        color: white;
        font-weight: 700;
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
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">For Affiliates</span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight text-white mb-6 leading-[1.1]">
                Monetize Your Traffic<br />
                <span class="text-emerald-400">with Top CPA Offers</span>
            </h1>
            <p class="text-lg md:text-xl text-neutral-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Join thousands of successful affiliates earning competitive commissions with exclusive offers
                across multiple verticals.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.affiliate') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-200 group">
                    Join Now - It's Free
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-4 rounded-xl font-semibold text-lg border border-neutral-700 transition-all duration-200">
                    Contact Support
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                No credit card required • Get approved in 24 hours
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
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Active Affiliates</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">2,000+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Live Offers</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mb-1">$3M+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Paid Monthly</div>
            </div>
            <div class="stat-card p-5 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">24/7</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Support Available</div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Why Join Us</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Why Affiliates Choose {{ config('app.name') }}
            </h2>
            <p class="text-lg text-neutral-400">
                The platform built by affiliates, for affiliates
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="benefit-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">High Payouts</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Earn competitive commissions with some of the highest payout rates in the industry. Weekly payment options available.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Exclusive Offers</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Access exclusive campaigns not available on other networks, with pre-negotiated higher payouts.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Real-Time Tracking</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Track every click, conversion, and commission in real-time with our advanced dashboard and detailed analytics.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate>
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Dedicated Support</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Get personalized support from experienced affiliate managers who help optimize your campaigns.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="100">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M6 6.5L7.5 8l8.5-8.5M6 6.5l-4 4M6 6.5l3 3M6 6.5l3-3M12 6.5l-1.5 1.5M12 6.5l4 4M12 6.5l-4-4M12 6.5l4-4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Reliable Payments</h3>
                <p class="text-neutral-400 leading-relaxed">
                    On-time payments every time. Multiple payment methods including bank transfer, PayPal, and more.
                </p>
            </div>

            <div class="benefit-card p-6 fade-up" data-animate data-delay="200">
                <div class="feature-icon w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Marketing Tools</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Access banners, landing pages, email creatives, and other promotional materials to maximize conversions.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Verticals Section -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Popular Verticals</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                High-Converting Offers
            </h2>
            <p class="text-lg text-neutral-400">
                Promote offers across multiple industries with proven conversion rates
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="vertical-card p-5 text-center fade-up" data-animate>
                <div class="text-3xl mb-2">🛍️</div>
                <h3 class="font-semibold text-white text-sm">E-commerce</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl mb-2">💰</div>
                <h3 class="font-semibold text-white text-sm">Finance</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl mb-2">❤️</div>
                <h3 class="font-semibold text-white text-sm">Health & Fitness</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl mb-2">🎮</div>
                <h3 class="font-semibold text-white text-sm">Gaming</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="400">
                <div class="text-3xl mb-2">💻</div>
                <h3 class="font-semibold text-white text-sm">Software</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate>
                <div class="text-3xl mb-2">🎓</div>
                <h3 class="font-semibold text-white text-sm">Education</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="100">
                <div class="text-3xl mb-2">✈️</div>
                <h3 class="font-semibold text-white text-sm">Travel</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="200">
                <div class="text-3xl mb-2">📱</div>
                <h3 class="font-semibold text-white text-sm">Mobile Apps</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="300">
                <div class="text-3xl mb-2">🏠</div>
                <h3 class="font-semibold text-white text-sm">Home & Garden</h3>
            </div>
            <div class="vertical-card p-5 text-center fade-up" data-animate data-delay="400">
                <div class="text-3xl mb-2">🎯</div>
                <h3 class="font-semibold text-white text-sm">Lead Gen</h3>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Simple Process</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Start Earning in 4 Steps
            </h2>
            <p class="text-lg text-neutral-400">
                Get up and running quickly with our streamlined process
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center fade-up" data-animate>
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">1</div>
                <h3 class="text-lg font-bold text-white mb-2">Sign Up Free</h3>
                <p class="text-sm text-neutral-400">Create your free affiliate account in minutes</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="100">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">2</div>
                <h3 class="text-lg font-bold text-white mb-2">Browse Offers</h3>
                <p class="text-sm text-neutral-400">Select offers that match your traffic and audience</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="200">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">3</div>
                <h3 class="text-lg font-bold text-white mb-2">Drive Traffic</h3>
                <p class="text-sm text-neutral-400">Use your unique tracking links to promote offers</p>
            </div>
            <div class="text-center fade-up" data-animate data-delay="300">
                <div class="step-number w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">4</div>
                <h3 class="text-lg font-bold text-white mb-2">Get Paid</h3>
                <p class="text-sm text-neutral-400">Earn commissions for every conversion</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Success Stories</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                What Our Affiliates Say
            </h2>
            <p class="text-lg text-neutral-400">
                Hear from affiliates who are earning with us
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="testimonial-card p-6 fade-up" data-animate>
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "Best affiliate network I've worked with. On-time payments, great offers, and responsive support team."
                </p>
                <p class="font-semibold text-white">— Sarah M.</p>
                <p class="text-sm text-neutral-500">Super Affiliate</p>
            </div>
            <div class="testimonial-card p-6 fade-up" data-animate data-delay="100">
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "The tracking is accurate and the dashboard makes it easy to optimize campaigns. Highly recommend!"
                </p>
                <p class="font-semibold text-white">— James K.</p>
                <p class="text-sm text-neutral-500">Media Buyer</p>
            </div>
            <div class="testimonial-card p-6 fade-up" data-animate data-delay="200">
                <div class="flex gap-1 text-emerald-400 mb-4">★★★★★</div>
                <p class="text-neutral-400 mb-4 leading-relaxed">
                    "Finally found a network that actually cares about affiliates. Exclusive offers and top payouts."
                </p>
                <p class="font-semibold text-white">— Michael R.</p>
                <p class="text-sm text-neutral-500">Influencer</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-10 md:p-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Start Earning?
            </h2>
            <p class="text-lg text-neutral-400 mb-8 max-w-xl mx-auto">
                Join {{ config('app.name') }} today and start monetizing your traffic with top-converting offers.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.affiliate') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-xl font-semibold transition-all duration-200 group">
                    Create Free Account
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-3.5 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                    Contact Support
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                No credit card required • Get approved in 24 hours
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
