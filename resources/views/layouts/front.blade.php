<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7YB7FWQLDG"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-7YB7FWQLDG');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.whatsappSupportPhone = "{{ preg_replace('/[^0-9]/', '', Cache::get('whatsapp_support_number', '')) }}";</script>

    <title inertia>@yield('title', config('app.name', 'ClicksIntel'))</title>

    <!-- SEO -->
    <meta name="description" content="@yield('meta_description', 'Performance marketing platform built for scale. Real-time tracking, fraud prevention, and payouts that keep affiliates growing.')">
    <meta name="keywords" content="@yield('meta_keywords', 'affiliate marketing, CPA network, performance marketing, affiliate tracking, fraud protection, advertiser network')">
    <meta name="author" content="{{ config('app.name', 'ClicksIntel') }}">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'ClicksIntel'))">
    <meta property="og:description" content="@yield('og_description', 'Performance marketing platform built for scale.')">
    <meta property="og:image" content="@yield('og_image', asset('images/clicksintel-frontpage.PNG'))">
    <meta property="og:site_name" content="{{ config('app.name', 'ClicksIntel') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name', 'ClicksIntel'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'Performance marketing platform built for scale.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/clicksintel-frontpage.PNG'))">

    @stack('structured_data')

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo/favico-black.png') }}">

    <!-- Fonts - Plus Jakarta Sans for modern fintech aesthetic -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Prevent FOUC -->
    <style>html{background:#ffffff}body{visibility:hidden}</style>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-slate-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <!-- Navigation -->
    <nav class="fixed top-0 inset-x-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('front.home') }}" class="flex items-center gap-2.5 group shrink-0">
                    <img src="{{ asset('logo/black.png') }}" alt="{{ config('app.name', 'ClicksIntel') }}" class="h-8 dark-logo">
                </a>

                <!-- Pill Navigation - Desktop -->
                <div class="hidden lg:flex items-center">
                    <div class="flex items-center gap-0.5 bg-slate-100/80 backdrop-blur-sm rounded-full px-1.5 py-1.5 border border-slate-200/60">
                        <a href="{{ route('front.for-affiliates') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('front.for-affiliates') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            For Affiliates
                        </a>
                        <a href="{{ route('front.for-advertisers') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('front.for-advertisers') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            For Advertisers
                        </a>
                        <a href="{{ route('front.features') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('front.features') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            Features
                        </a>
                        <a href="{{ route('learning.index') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('learning.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            Learning
                        </a>
                        <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('blog.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            Blog
                        </a>
                        <a href="{{ route('front.about') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request()->routeIs('front.about') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }} transition-all duration-200">
                            About
                        </a>
                    </div>
                </div>

                <!-- Desktop Auth -->
                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors px-4 py-2.5 rounded-full hover:bg-slate-100">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors px-4 py-2.5 rounded-full hover:bg-slate-100">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all duration-200">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="lg:hidden inline-flex items-center justify-center p-2.5 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open menu</span>
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-1">
                <a href="{{ route('front.for-affiliates') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('front.for-affiliates') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    For Affiliates
                </a>
                <a href="{{ route('front.for-advertisers') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('front.for-advertisers') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    For Advertisers
                </a>
                <a href="{{ route('front.features') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('front.features') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    Features
                </a>
                <a href="{{ route('learning.index') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('learning.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    Learning
                </a>
                <a href="{{ route('blog.index') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('blog.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    Blog
                </a>
                <a href="{{ route('front.about') }}" class="block px-4 py-3 rounded-xl text-base font-semibold {{ request()->routeIs('front.about') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                    About
                </a>
                <div class="pt-4 mt-2 border-t border-slate-100 space-y-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-3 rounded-xl text-base font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-4 py-3 rounded-xl text-base font-semibold bg-slate-900 hover:bg-slate-800 text-white text-center transition-colors">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <div id="ai-chat-widget-root"></div>

    <!-- Footer -->
    <footer class="bg-slate-50">
        <!-- Top CTA Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="bg-slate-900 rounded-3xl px-8 py-16 sm:px-16 sm:py-20 text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight mb-5 max-w-3xl mx-auto leading-tight">
                    Start building with {{ config('app.name', 'ClicksIntel') }} today
                </h2>
                <p class="text-lg text-slate-400 mb-10 max-w-xl mx-auto">
                    Join thousands of affiliates and advertisers who trust our platform for performance marketing at scale.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-7 py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-900 text-sm font-bold transition-all duration-200 w-full sm:w-auto justify-center">
                        Get Started
                    </a>
                    <a href="{{ route('front.contact') }}" class="inline-flex items-center px-7 py-3.5 rounded-full border border-slate-600 hover:border-slate-400 text-white hover:bg-slate-800 text-sm font-bold transition-all duration-200 w-full sm:w-auto justify-center">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Links Grid -->
        <div class="border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-10">
                    <!-- Brand Column -->
                    <div class="col-span-2 md:col-span-3 lg:col-span-2">
                        <a href="{{ route('front.home') }}" class="inline-flex items-center gap-2.5 mb-5">
                            <img src="{{ asset('logo/black.png') }}" alt="{{ config('app.name', 'ClicksIntel') }}" class="h-8 dark-logo">
                        </a>
                        <p class="text-slate-500 text-sm leading-relaxed max-w-xs mb-6">
                            Performance marketing infrastructure for serious affiliates and advertisers. Built for speed, transparency, and scale.
                        </p>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                All systems operational
                            </span>
                        </div>
                    </div>

                    <!-- Platform -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Platform</h4>
                        <ul class="space-y-3.5 text-sm">
                            <li><a href="{{ route('front.for-affiliates') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">For Affiliates</a></li>
                            <li><a href="{{ route('front.for-advertisers') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">For Advertisers</a></li>
                            <li><a href="{{ route('front.features') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Features</a></li>
                            <li><a href="{{ route('blog.index') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Blog</a></li>
                            <li><a href="{{ route('front.about') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">About Us</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Support</h4>
                        <ul class="space-y-3.5 text-sm">
                            <li><a href="{{ route('front.contact') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Contact</a></li>
                            <li><a href="{{ route('front.faq') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">FAQ</a></li>
                            <li><a href="{{ route('register') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Get Started</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Legal</h4>
                        <ul class="space-y-3.5 text-sm">
                            <li><a href="{{ route('front.privacy') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Privacy Policy</a></li>
                            <li><a href="{{ route('front.terms') }}" class="text-slate-600 hover:text-slate-900 font-medium transition-colors">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-400 font-medium">&copy; {{ date('Y') }} {{ config('app.name', 'ClicksIntel') }}. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('front.privacy') }}" class="text-sm text-slate-400 hover:text-slate-600 font-medium transition-colors">Privacy</a>
                    <a href="{{ route('front.terms') }}" class="text-sm text-slate-400 hover:text-slate-600 font-medium transition-colors">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Banner (NDPR) -->
    <div id="cookie-banner" class="fixed bottom-0 inset-x-0 z-50 bg-white border-t border-slate-200 px-4 py-4 sm:px-6 hidden shadow-[0_-8px_30px_rgba(0,0,0,0.06)]" role="dialog" aria-live="polite" aria-label="Cookie consent">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1 text-sm text-slate-600">
                <span class="font-semibold text-slate-900">We use cookies</span> — including essential, analytics, and affiliate-tracking cookies.
                By clicking <strong>Accept All</strong> you consent to our use of cookies as described in our
                <a href="{{ route('front.privacy') }}" class="text-slate-900 hover:text-slate-700 underline underline-offset-2 font-medium">Privacy Policy</a>.
                You may <strong>Reject Non-Essential</strong> cookies without affecting core functionality.
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button id="cookie-reject" class="px-5 py-2.5 text-sm font-semibold text-slate-600 border border-slate-200 rounded-full hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                    Reject Non-Essential
                </button>
                <button id="cookie-accept" class="px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                    Accept All
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        (function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (!btn || !menu) return;

            btn.addEventListener('click', function() {
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', !expanded);
                menu.classList.toggle('hidden');
                btn.querySelectorAll('svg').forEach(svg => svg.classList.toggle('hidden'));
            });
        })();
    </script>
    @stack('scripts')
    <script>
        // Cookie consent banner (NDPR)
        (function() {
            const banner = document.getElementById('cookie-banner');
            if (!banner) return;
            const consent = localStorage.getItem('ndpr_cookie_consent');
            if (!consent) banner.classList.remove('hidden');

            document.getElementById('cookie-accept').addEventListener('click', function() {
                localStorage.setItem('ndpr_cookie_consent', 'all');
                banner.classList.add('hidden');
            });
            document.getElementById('cookie-reject').addEventListener('click', function() {
                localStorage.setItem('ndpr_cookie_consent', 'essential');
                banner.classList.add('hidden');
            });
        })();
    </script>
    <script type="module">document.body.style.visibility='visible'</script>
</body>
</html>
