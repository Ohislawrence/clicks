@extends('layouts.front')

@section('title', 'Frequently Asked Questions (FAQ) | ' . config('app.name'))
@section('meta_description', 'Find answers to common questions about ' . config('app.name') . '. Learn about payments, offers, tracking, fraud protection, and how to get started as an affiliate or advertiser.')
@section('meta_keywords', 'affiliate marketing FAQ, CPA network questions, affiliate program FAQ, how to get started, payment questions, tracking questions')

@section('og_title', 'FAQ - Your Questions Answered')
@section('og_description', 'Common questions about affiliate marketing, payments, tracking, and getting started on our platform.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "FAQPage",
  "mainEntity": [
    {
      "@@type": "Question",
      "name": "What is {{ config('app.name') }}?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "{{ config('app.name') }} is a performance-based affiliate marketing platform that connects advertisers with affiliates."
      }
    },
    {
      "@@type": "Question",
      "name": "Is there a cost to join?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "No, registration is completely free for both affiliates and advertisers."
      }
    },
    {
      "@@type": "Question",
      "name": "What is the minimum payout?",
      "acceptedAnswer": {
        "@@type": "Answer",
        "text": "The minimum payout threshold is typically $100 USD, but this may vary based on your payment method."
      }
    }
  ]
}
</script>
@endpush

@push('styles')
<style>
    .faq-category {
        scroll-margin-top: 80px;
    }

    .faq-item {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .faq-item:hover {
        border-color: #333333;
        background: #1c1c1c;
    }

    .faq-question {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .faq-item.open .faq-answer {
        max-height: 500px;
    }
    .faq-item.open .chevron-icon {
        transform: rotate(180deg);
    }

    .chevron-icon {
        transition: transform 0.2s ease;
    }

    .category-tab {
        background: #171717;
        border: 1px solid #262626;
        transition: all 0.2s ease;
    }
    .category-tab:hover {
        border-color: #404040;
        background: #1f1f1f;
    }
    .category-tab.active {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    .category-tab.active .tab-icon {
        color: white;
    }

    .info-box {
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
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">FAQ</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Frequently Asked<br />
                <span class="text-emerald-400">Questions</span>
            </h1>
            <p class="text-lg text-neutral-400 leading-relaxed">
                Find answers to common questions about our platform, payments, tracking, and how to get started.
            </p>
        </div>
    </div>
</section>

<!-- Category Navigation -->
<section class="sticky top-16 z-40 bg-neutral-900/95 backdrop-blur-sm border-b border-neutral-800 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-2">
            <a href="#general" class="category-tab inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                General
            </a>
            <a href="#affiliates" class="category-tab inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Affiliates
            </a>
            <a href="#advertisers" class="category-tab inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                Advertisers
            </a>
            <a href="#technical" class="category-tab inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Technical
            </a>
        </div>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-16 bg-neutral-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- General Questions -->
        <div id="general" class="faq-category mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">General Questions</h2>
            </div>

            <div class="space-y-4">
                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What is {{ config('app.name') }}?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            {{ config('app.name') }} is a performance-based affiliate marketing platform that connects advertisers with affiliates.
                            Advertisers can promote their offers, and affiliates can earn commissions by driving conversions through their marketing channels.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How does the platform work?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Advertisers create offers on our platform with specific commission structures. Affiliates browse available offers,
                            request access, and receive unique tracking links. When affiliates drive conversions through their links, they
                            earn commissions based on the offer's payout model.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">Is there a cost to join?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            No, registration is completely free for both affiliates and advertisers. We only earn when you succeed through
                            our commission structure.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Affiliates -->
        <div id="affiliates" class="faq-category mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">For Affiliates</h2>
            </div>

            <div class="space-y-4">
                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How do I become an affiliate?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Simply register for a free affiliate account, complete your profile, and provide any required verification documents.
                            Once approved, you can start browsing and promoting offers immediately.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What types of offers are available?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            We offer a wide variety of campaigns across multiple verticals including e-commerce, finance, health, gaming,
                            software, and more. Offers include various payout models: CPA (Cost Per Action), CPL (Cost Per Lead),
                            CPS (Cost Per Sale), and RevShare (Revenue Share).
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How do I get paid?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed mb-3">
                            We offer multiple payment methods including:
                        </p>
                        <ul class="list-disc list-inside text-neutral-400 space-y-1 ml-2">
                            <li>Bank Transfer / Wire</li>
                            <li>PayPal</li>
                            <li>Other payment processors (varies by region)</li>
                        </ul>
                        <p class="text-neutral-400 leading-relaxed mt-3">
                            Payment schedules vary by offer but typically range from weekly to monthly payouts once you reach the minimum threshold.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What is the minimum payout?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            The minimum payout threshold is typically $100 USD, but this may vary based on your payment method and account settings.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What traffic sources are allowed?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed mb-3">
                            Acceptable traffic sources vary by offer but commonly include:
                        </p>
                        <ul class="list-disc list-inside text-neutral-400 space-y-1 ml-2">
                            <li>Social media advertising</li>
                            <li>Search engine marketing (Google Ads, Bing Ads)</li>
                            <li>Email marketing (opt-in lists only)</li>
                            <li>Content websites and blogs</li>
                            <li>Native advertising</li>
                            <li>Influencer marketing</li>
                        </ul>
                        <p class="text-neutral-400 leading-relaxed mt-3">
                            Always check each offer's specific terms for traffic restrictions.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How quickly do conversions appear?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Most conversions appear in your dashboard within minutes to hours. Some offers may have delayed tracking based
                            on the advertiser's conversion confirmation process.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Advertisers -->
        <div id="advertisers" class="faq-category mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">For Advertisers</h2>
            </div>

            <div class="space-y-4">
                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How do I create a campaign?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            After registering as an advertiser, you can create offers through your dashboard. Define your target action,
                            commission structure, geographic targeting, and any traffic restrictions. Our team will review and approve
                            your offer before it goes live.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What payout models do you support?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed mb-3">
                            We support multiple commission models:
                        </p>
                        <ul class="list-disc list-inside text-neutral-400 space-y-1 ml-2">
                            <li><strong class="text-white">CPA (Cost Per Action):</strong> Pay for completed actions (purchases, sign-ups)</li>
                            <li><strong class="text-white">CPL (Cost Per Lead):</strong> Pay for qualified leads</li>
                            <li><strong class="text-white">CPS (Cost Per Sale):</strong> Pay a percentage of sale value</li>
                            <li><strong class="text-white">CPM (Cost Per Mille):</strong> Pay per thousand impressions</li>
                            <li><strong class="text-white">RevShare:</strong> Share revenue percentage with affiliates</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How do I track conversions?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            We provide multiple tracking integration options including server-to-server postbacks, pixel tracking, and API integration.
                            Our team will help you implement the best solution for your technical setup.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">Can I approve affiliates individually?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Yes, you have full control over who promotes your offers. You can set offers to require approval, allowing you to
                            review each affiliate's application before granting access.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">How do I prevent fraud?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Our platform includes built-in fraud detection including IP analysis, duplicate detection, device fingerprinting,
                            and behavioral pattern recognition. You can also set custom validation rules and review conversions before approval.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Questions -->
        <div id="technical" class="faq-category mb-16">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Technical Questions</h2>
            </div>

            <div class="space-y-4">
                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">Do you provide an API?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Yes, we offer a comprehensive RESTful API for tracking, reporting, and account management. API documentation is
                            available in your dashboard once logged in.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What tracking parameters can I use?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Our tracking links support custom parameters for sub-ID tracking, allowing you to segment your campaigns by source,
                            creative, keyword, or any custom dimension.
                        </p>
                    </div>
                </div>

                <div class="faq-item" data-faq>
                    <div class="faq-question p-5 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white pr-4">What is your cookie duration?</h3>
                        <svg class="chevron-icon w-5 h-5 text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                    <div class="faq-answer px-5 pb-5">
                        <p class="text-neutral-400 leading-relaxed">
                            Cookie duration varies by offer and is set by the advertiser. Common durations range from 24 hours to 90 days.
                            Check each offer's details for specific cookie lifetime information.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support CTA -->
        <div class="info-box p-8 text-center fade-up" data-animate>
            <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-3">Still Have Questions?</h2>
            <p class="text-neutral-400 mb-6 max-w-md mx-auto">
                Our support team is here to help. Contact us for personalized assistance with your account or campaigns.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('front.contact') }}" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                    Contact Support
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="#" class="inline-flex items-center justify-center bg-neutral-800 hover:bg-neutral-700 text-white px-6 py-3 rounded-xl font-semibold border border-neutral-700 transition-all duration-200">
                    View Documentation
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // FAQ Accordion functionality
    (function() {
        const faqItems = document.querySelectorAll('[data-faq]');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');

            question.addEventListener('click', () => {
                // Close other open items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('open')) {
                        otherItem.classList.remove('open');
                    }
                });

                // Toggle current item
                item.classList.toggle('open');
            });
        });

        // Open first FAQ item by default
        if (faqItems.length > 0) {
            faqItems[0].classList.add('open');
        }
    })();

    // Category tabs - highlight active on scroll
    (function() {
        const sections = document.querySelectorAll('.faq-category');
        const navLinks = document.querySelectorAll('.category-tab');

        function updateActiveTab() {
            let current = '';
            const scrollPosition = window.scrollY + 100;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionBottom = sectionTop + section.offsetHeight;

                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    current = '#' + section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === current) {
                    link.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', updateActiveTab);
        updateActiveTab();
    })();

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
