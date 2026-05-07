<?php $__env->startSection('title', 'About Us - Our Mission & Values | ' . config('app.name')); ?>
<?php $__env->startSection('meta_description', 'Learn about ' . config('app.name') . ' - founded in 2020, serving 15,000+ affiliates and 500+ advertisers worldwide. Our mission: transparent, efficient performance marketing.'); ?>
<?php $__env->startSection('meta_keywords', 'about us, company history, our mission, our values, performance marketing company, CPA network, affiliate marketing company'); ?>

<?php $__env->startSection('og_title', 'About ' . config('app.name') . ' - Trusted Performance Marketing Platform'); ?>
<?php $__env->startSection('og_description', 'Empowering digital marketers since 2020. Transparent tracking, fair business practices, and innovative technology.'); ?>

<?php $__env->startPush('structured_data'); ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "mainEntity": {
    "@type": "Organization",
    "name": "<?php echo e(config('app.name')); ?>",
    "foundingDate": "2020",
    "description": "Performance marketing platform connecting advertisers with affiliates",
    "numberOfEmployees": {
      "@type": "QuantitativeValue",
      "value": "50-100"
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .fade-up {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1), transform 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .fade-up.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .stat-item {
        background: #171717;
        border: 1px solid #262626;
        transition: all 0.2s ease;
    }
    .stat-item:hover {
        border-color: #333333;
        background: #1c1c1c;
    }

    .value-card {
        background: #171717;
        border: 1px solid #262626;
        transition: all 0.2s ease;
    }
    .value-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
    }

    .about-hero {
        position: relative;
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero Section -->
<section class="about-hero bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">About Us</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Your trusted partner in<br />
                <span class="text-emerald-400">performance marketing</span>
            </h1>
            <p class="text-lg md:text-xl text-neutral-400 leading-relaxed max-w-2xl">
                Founded in 2020 with a simple vision: to create a transparent, efficient,
                and profitable marketplace where advertisers and affiliates can grow together.
            </p>
        </div>
    </div>
</section>

<!-- Stats Row -->
<section class="py-12 bg-neutral-900 border-y border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-item rounded-xl p-5 text-center">
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mb-1">15,000+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Active Affiliates</div>
            </div>
            <div class="stat-item rounded-xl p-5 text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">500+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Advertisers</div>
            </div>
            <div class="stat-item rounded-xl p-5 text-center">
                <div class="text-3xl md:text-4xl font-bold text-white mb-1">2,000+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Active Campaigns</div>
            </div>
            <div class="stat-item rounded-xl p-5 text-center">
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mb-1">$60M+</div>
                <div class="text-sm text-neutral-500 font-medium uppercase tracking-wide">Total Paid Out</div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <div class="fade-up" data-animate>
                <div class="inline-flex items-center gap-2 mb-4">
                    <span class="text-sm font-semibold uppercase tracking-wider text-emerald-400/80">Our Story</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-5">
                    Built for the modern<br />marketer
                </h2>
                <div class="space-y-4 text-neutral-400 leading-relaxed">
                    <p>
                        Founded in 2020, <?php echo e(config('app.name')); ?> emerged from a simple vision: to create a transparent,
                        efficient, and profitable marketplace where advertisers and affiliates can grow together.
                    </p>
                    <p>
                        Today, we're proud to be one of the fastest-growing performance marketing platforms, serving thousands
                        of marketers worldwide with cutting-edge technology and unwavering commitment to their success.
                    </p>
                </div>
            </div>

            <div class="fade-up" data-animate data-delay="100">
                <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-8">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-neutral-300">Our Mission</span>
                    </div>
                    <p class="text-neutral-400 leading-relaxed">
                        We're dedicated to empowering digital marketers with cutting-edge technology, transparent tracking,
                        and fair business practices. Our mission is to build the most trusted and innovative performance
                        marketing platform where both advertisers and affiliates can achieve their goals.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What Sets Us Apart -->
<section class="py-20 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-4">
                What Sets Us Apart
            </h2>
            <p class="text-lg text-neutral-400">
                We've built our platform differently — with you in mind.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 fade-up" data-animate>
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Technology First</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    Our proprietary tracking platform provides 99.9% accuracy with real-time reporting and advanced fraud detection powered by machine learning.
                </p>
            </div>

            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 fade-up" data-animate data-delay="100">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Transparency</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    Complete visibility into your campaigns with detailed analytics, clear terms, and honest communication. No hidden fees or surprises.
                </p>
            </div>

            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 fade-up" data-animate data-delay="200">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Quality Focus</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    We carefully vet all affiliates and monitor traffic quality to ensure advertisers receive genuine, high-converting traffic.
                </p>
            </div>

            <div class="bg-neutral-950 border border-neutral-800 rounded-xl p-6 fade-up" data-animate data-delay="300">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Dedicated Support</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    Our experienced team provides personalized support to help you optimize campaigns and maximize your ROI.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="py-20 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-4">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Core Values</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-4">
                What we believe in
            </h2>
            <p class="text-lg text-neutral-400">
                Our values shape everything we do — from product development to customer support.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="value-card bg-neutral-900 border border-neutral-800 rounded-xl p-6 text-center fade-up" data-animate>
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Transparency</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    Complete transparency in all our dealings. From tracking to payments, everything is clear and visible.
                </p>
            </div>

            <div class="value-card bg-neutral-900 border border-neutral-800 rounded-xl p-6 text-center fade-up" data-animate data-delay="100">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Innovation</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    We continuously invest in technology and innovation to provide the best tools and features for our partners.
                </p>
            </div>

            <div class="value-card bg-neutral-900 border border-neutral-800 rounded-xl p-6 text-center fade-up" data-animate data-delay="200">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Integrity</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    We maintain the highest standards of integrity in everything we do, building lasting relationships based on trust.
                </p>
            </div>

            <div class="value-card bg-neutral-900 border border-neutral-800 rounded-xl p-6 text-center fade-up" data-animate data-delay="300">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Partnership</h3>
                <p class="text-sm text-neutral-400 leading-relaxed">
                    We view our clients as partners. Your success is our success, and we're committed to helping you achieve your goals.
                </p>
            </div>
        </div>
    </div>
</section>



<!-- CTA Section -->
<section class="py-20 bg-neutral-950 border-t border-neutral-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-10 md:p-12">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-4">
                Join Our Community
            </h2>
            <p class="text-lg text-neutral-400 mb-8 max-w-xl mx-auto">
                Be part of a growing network of successful marketers.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('register.affiliate')); ?>" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3.5 rounded-lg font-semibold transition-all duration-200">
                    Join as Affiliate
                </a>
                <a href="<?php echo e(route('register.advertiser')); ?>" class="inline-flex items-center justify-center bg-neutral-800 hover:bg-neutral-700 text-white px-8 py-3.5 rounded-lg font-semibold border border-neutral-700 transition-all duration-200">
                    Join as Advertiser
                </a>
            </div>
            <p class="text-sm text-neutral-500 mt-6">
                Join in minutes • No setup fees • Cancel anytime
            </p>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Intersection Observer for fade-up animations
    (function() {
        const observerOptions = { threshold: 0.15, rootMargin: '0px 0px -20px 0px' };

        const elements = document.querySelectorAll('[data-animate]');
        elements.forEach((el) => {
            const delay = el.getAttribute('data-delay');
            if (delay) {
                el.style.transitionDelay = delay + 'ms';
            }

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\dealsintel\resources\views/front/about.blade.php ENDPATH**/ ?>