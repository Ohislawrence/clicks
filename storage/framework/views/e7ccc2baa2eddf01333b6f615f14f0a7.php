<?php $__env->startSection('title', config('app.name') . ' — Performance Marketing Platform | CPA Network'); ?>
<?php $__env->startSection('meta_description', 'Join the leading performance marketing platform trusted by 15,000+ affiliates and 500+ advertisers. Real-time tracking, fraud protection, weekly payments. Start earning today!'); ?>
<?php $__env->startSection('meta_keywords', 'affiliate marketing platform, CPA network, performance marketing, affiliate network, CPA offers, affiliate program, advertiser network, commission tracking, affiliate dashboard'); ?>

<?php $__env->startSection('og_title', config('app.name') . ' — Performance Marketing That Achieves Results'); ?>
<?php $__env->startSection('og_description', 'Connect with top-performing affiliates and advertisers. Advanced tracking, real-time analytics, and dedicated support to grow your business.'); ?>

<?php $__env->startSection('twitter_title', config('app.name') . ' — Performance Marketing Platform'); ?>
<?php $__env->startSection('twitter_description', 'Join 15,000+ marketers earning with exclusive offers. Real-time tracking, weekly payments, and 24/7 support.'); ?>

<?php $__env->startPush('structured_data'); ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php echo e(config('app.name')); ?>",
  "url": "<?php echo e(url('/')); ?>",
  "logo": "<?php echo e(asset('images/logo.png')); ?>",
  "description": "Leading performance marketing platform connecting affiliates and advertisers worldwide",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "1250"
  },
  "sameAs": [
    "https://www.facebook.com/dealsintel",
    "https://twitter.com/dealsintel",
    "https://www.linkedin.com/company/dealsintel"
  ]
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(32px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-32px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(32px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.96); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes marquee {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .animate-slide-left {
        animation: slideInLeft 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }
    .animate-slide-right {
        animation: slideInRight 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }
    .animate-scale-in {
        animation: scaleIn 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }
    .delay-100 { animation-delay: 0.05s; }
    .delay-200 { animation-delay: 0.1s; }
    .delay-300 { animation-delay: 0.15s; }
    .delay-400 { animation-delay: 0.2s; }
    .delay-500 { animation-delay: 0.25s; }
    .delay-600 { animation-delay: 0.3s; }

    .stat-card {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .stat-card.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .feature-card {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .feature-card.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .feature-card:hover {
        border-color: #404040;
        transform: translateY(-4px);
        transition: all 0.25s ease;
    }

    .check-item {
        opacity: 0;
        transform: translateX(-12px);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
    .check-item.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .marquee-track {
        display: flex;
        animation: marquee 28s linear infinite;
    }
    .marquee-track:hover {
        animation-play-state: paused;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #1f1f1f;
        border: 1px solid #2c2c2c;
        border-radius: 9999px;
        padding: 0.25rem 1rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #a3a3a3;
    }

    .btn-primary {
        background-color: #10b981;
        color: white;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .btn-primary:hover {
        background-color: #059669;
        transform: translateY(-1px);
    }
    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background-color: #262626;
        border: 1px solid #404040;
        color: #e5e5e5;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        background-color: #2e2e2e;
        border-color: #525252;
        transform: translateY(-1px);
    }
    .btn-secondary:active {
        transform: translateY(0);
    }

    .dashboard-card {
        background: #171717;
        border: 1px solid #262626;
        transition: all 0.2s ease;
    }
    .dashboard-card:hover {
        border-color: #333333;
        background: #1c1c1c;
    }

    .badge-success {
        background-color: rgba(16, 185, 129, 0.12);
        color: #34d399;
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.2rem 0.5rem;
        border-radius: 9999px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero Section -->
<section class="relative overflow-hidden bg-neutral-950 border-b border-neutral-800">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 md:py-36">
        <div class="text-center max-w-4xl mx-auto">

            <h1 class="animate-fade-in-up delay-200 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight text-white mb-6 leading-[1.1]">
                Performance Marketing<br class="hidden sm:block" />
                <span class="text-emerald-400">That Achieves Results</span>
            </h1>

            <p class="animate-fade-in-up delay-300 text-lg md:text-xl text-neutral-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Connect with top-performing affiliates and advertisers. Advanced tracking, real-time analytics, and dedicated support to grow your business.
            </p>

            <div class="animate-fade-in-up delay-400 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('front.for-affiliates')); ?>" class="group btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-lg font-semibold text-base">
                    I'm an Affiliate
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="<?php echo e(route('front.for-advertisers')); ?>" class="group btn-secondary inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-lg font-semibold text-base">
                    I'm an Advertiser
                </a>
            </div>

            <div class="animate-fade-in delay-500 mt-10 flex flex-wrap items-center justify-center gap-4 text-sm text-neutral-500">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    No credit card required
                </span>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Get started in minutes
                </span>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                    24/7 Support
                </span>
            </div>
        </div>
    </div>
</section>

<!-- Trust Badge Row -->
<section class="py-6 border-b border-neutral-800 bg-neutral-900/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-8 text-xs text-neutral-500">
            <span class="uppercase tracking-wider">Trusted by industry leaders</span>
            <div class="flex flex-wrap items-center justify-center gap-6">
                <span class="text-neutral-600 font-medium">OliLearn</span>
                <span class="text-neutral-600 font-medium">BoomOdd</span>
                <span class="text-neutral-600 font-medium">TaxMaster NG</span>
                <span class="text-neutral-600 font-medium">DealsIntel</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-sm font-semibold uppercase tracking-wider text-emerald-400/80 mb-3 block">Core Capabilities</span>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-4">
                Everything You Need to Succeed
            </h2>
            <p class="text-lg text-neutral-400 max-w-2xl mx-auto">
                Powerful features designed for performance marketers at every scale
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="feature-card bg-neutral-900 border border-neutral-800 rounded-2xl p-8" data-animate>
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Real-Time Tracking</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Track every click, conversion, and commission in real-time with military-grade accuracy. Never miss a conversion.
                </p>
            </div>

            <div class="feature-card bg-neutral-900 border border-neutral-800 rounded-2xl p-8" data-animate>
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Advanced Fraud Protection</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Enterprise-level fraud detection powered by AI. Protect your campaigns from invalid traffic and fake conversions.
                </p>
            </div>

            <div class="feature-card bg-neutral-900 border border-neutral-800 rounded-2xl p-8" data-animate>
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Multiple Payment Models</h3>
                <p class="text-neutral-400 leading-relaxed">
                    Support for CPA, CPL, CPS, CPM, and RevShare. Flexible commission structures to match your business model.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-neutral-900 border-y border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center">
            <div class="stat-card" data-stat>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">15,000+</div>
                <div class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Active Affiliates</div>
            </div>
            <div class="stat-card" data-stat>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">2,000+</div>
                <div class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Live Campaigns</div>
            </div>
            <div class="stat-card" data-stat>
                <div class="text-4xl md:text-5xl font-bold text-emerald-400 mb-2">$5M+</div>
                <div class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Paid Monthly</div>
            </div>
            <div class="stat-card" data-stat>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">99.9%</div>
                <div class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Tracking Accuracy</div>
            </div>
        </div>
    </div>
</section>

<!-- For Affiliates Section -->
<section class="py-24 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center">
            <div class="order-2 lg:order-1">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5">
                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">For Affiliates</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white leading-tight">
                        Monetize Your Traffic with Top Offers
                    </h2>
                    <p class="text-lg text-neutral-400 leading-relaxed">
                        Access exclusive campaigns, earn competitive commissions, and get paid on time. Our platform provides everything you need to maximize your earnings.
                    </p>
                    <ul class="space-y-4 pt-4" id="affiliate-checks">
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Weekly Payments:</span> Get paid every week with low minimum threshold</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">2,000+ Offers:</span> Access diverse campaigns across multiple verticals</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Dedicated Support:</span> Personal affiliate manager to help you succeed</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Marketing Tools:</span> Access creatives, landing pages, and promotional materials</span>
                        </li>
                    </ul>
                    <div class="pt-4">
                        <a href="<?php echo e(route('front.for-affiliates')); ?>" class="group inline-flex items-center gap-2 text-emerald-400 hover:text-emerald-300 font-semibold transition-colors">
                            Learn more about affiliate program
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <div class="dashboard-card rounded-2xl p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm font-medium text-neutral-500">Today's Earnings</span>
                        <span class="badge-success">+23% vs yesterday</span>
                    </div>
                    <div class="text-3xl font-bold text-white mb-1">$1,247.80</div>
                    <div class="text-sm text-neutral-500 mb-6">87 conversions • 2,341 clicks</div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-neutral-800/50 rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">156</div>
                            <div class="text-sm text-neutral-500">Active Links</div>
                        </div>
                        <div class="bg-neutral-800/50 rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">$42K</div>
                            <div class="text-sm text-neutral-500">Total Earned</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- For Advertisers Section -->
<section class="py-24 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center">
            <div class="order-1">
                <div class="dashboard-card rounded-2xl p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm font-medium text-neutral-500">Campaign Performance</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Live
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div>
                            <div class="text-2xl font-bold text-white">12.4K</div>
                            <div class="text-xs text-neutral-500">Clicks</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">847</div>
                            <div class="text-xs text-neutral-500">Conversions</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-emerald-400">6.8%</div>
                            <div class="text-xs text-neutral-500">CVR</div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-neutral-800 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-emerald-500" style="width: 68%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="bg-neutral-800/50 rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">234</div>
                            <div class="text-sm text-neutral-500">Active Affiliates</div>
                        </div>
                        <div class="bg-neutral-800/50 rounded-xl p-4">
                            <div class="text-2xl font-bold text-white">$24.50</div>
                            <div class="text-sm text-neutral-500">Avg. CPA</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-2">
                <div class="space-y-6">
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white leading-tight">
                        Scale Your Business with Quality Traffic
                    </h2>
                    <p class="text-lg text-neutral-400 leading-relaxed">
                        Connect with vetted affiliates who drive real results. Our performance-based model ensures you only pay for actual conversions and quality leads.
                    </p>
                    <ul class="space-y-4 pt-4" id="advertiser-checks">
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Performance-Based:</span> Pay only for validated conversions and results</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Fraud Protection:</span> Advanced AI-powered fraud detection and prevention</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Quality Affiliates:</span> Pre-vetted partners with proven track records</span>
                        </li>
                        <li class="check-item flex items-start gap-3" data-check>
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="text-neutral-300"><span class="text-white font-medium">Real-Time Analytics:</span> Detailed tracking and reporting for optimization</span>
                        </li>
                    </ul>
                    <div class="pt-4">
                        <a href="<?php echo e(route('front.for-advertisers')); ?>" class="group inline-flex items-center gap-2 text-emerald-400 hover:text-emerald-300 font-semibold transition-colors">
                            Learn more about advertiser solutions
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-24 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="space-y-6">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white">
                Ready to Get Started?
            </h2>
            <p class="text-lg text-neutral-400 max-w-2xl mx-auto">
                Join thousands of successful affiliates and advertisers who trust us for their performance marketing needs.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                <a href="<?php echo e(route('register.affiliate')); ?>" class="btn-primary inline-flex items-center justify-center px-8 py-3.5 rounded-lg font-semibold text-base">
                    Sign Up as Affiliate
                </a>
                <a href="<?php echo e(route('register.advertiser')); ?>" class="btn-secondary inline-flex items-center justify-center px-8 py-3.5 rounded-lg font-semibold text-base">
                    Sign Up as Advertiser
                </a>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-4 pt-6 text-sm">
                <span class="inline-flex items-center gap-1.5 text-neutral-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Join in minutes
                </span>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <span class="inline-flex items-center gap-1.5 text-neutral-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    No setup fees
                </span>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <span class="inline-flex items-center gap-1.5 text-neutral-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Cancel anytime
                </span>
            </div>
        </div>
    </div>
</section>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Intersection Observer for scroll-triggered animations
    (function() {
        const observerOptions = { threshold: 0.15, rootMargin: '0px 0px -20px 0px' };

        // Feature cards
        const featureCards = document.querySelectorAll('[data-animate]');
        featureCards.forEach((el, i) => {
            el.style.transitionDelay = (i * 80) + 'ms';
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

        // Stats
        const statCards = document.querySelectorAll('[data-stat]');
        statCards.forEach((el, i) => {
            el.style.transitionDelay = (i * 80) + 'ms';
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

        // Checklist items
        const checkItems = document.querySelectorAll('[data-check]');
        checkItems.forEach((el, i) => {
            el.style.transitionDelay = (i * 60) + 'ms';

            const parentSection = el.closest('section');
            if (parentSection) {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const items = entry.target.querySelectorAll('[data-check]');
                            items.forEach((item, idx) => {
                                setTimeout(() => {
                                    item.classList.add('visible');
                                }, idx * 60);
                            });
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.2 });
                obs.observe(parentSection);
            } else {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.2 });
                obs.observe(el);
            }
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\dealsintel\resources\views/front/home.blade.php ENDPATH**/ ?>