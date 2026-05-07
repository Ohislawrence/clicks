<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'ClicksIntel') . ' — Performance Marketing')</title>

    <!-- SEO -->
    <meta name="description" content="@yield('meta_description', 'Performance marketing platform built for scale. Real-time tracking, fraud prevention, and payouts that keep affiliates growing.')">
    <meta name="keywords" content="@yield('meta_keywords', 'affiliate marketing, CPA network, performance marketing, affiliate tracking, fraud protection, advertiser network')">
    <meta name="author" content="{{ config('app.name', 'ClicksIntel') }}">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'ClicksIntel') . ' — Performance Marketing')">
    <meta property="og:description" content="@yield('og_description', 'Performance marketing platform built for scale.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:site_name" content="{{ config('app.name', 'ClicksIntel') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name', 'ClicksIntel') . ' — Performance Marketing')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Performance marketing platform built for scale.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.jpg'))">

    @stack('structured_data')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite('resources/js/app.js')
    @stack('styles')
</head>
<body class="font-sans antialiased bg-neutral-950 text-neutral-100">

    <!-- Navigation -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-neutral-950/95 backdrop-blur-sm border-b border-neutral-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('front.home') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('logo/full-white.png') }}" alt="{{ config('app.name', 'ClicksIntel') }}" class="h-8">
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-0.5">
                    <a href="{{ route('front.for-affiliates') }}" class="px-3.5 py-2 rounded-md text-sm font-medium text-neutral-400 hover:text-white hover:bg-neutral-800/80 transition-all duration-200">
                        For Affiliates
                    </a>
                    <a href="{{ route('front.for-advertisers') }}" class="px-3.5 py-2 rounded-md text-sm font-medium text-neutral-400 hover:text-white hover:bg-neutral-800/80 transition-all duration-200">
                        For Advertisers
                    </a>
                    <a href="{{ route('front.features') }}" class="px-3.5 py-2 rounded-md text-sm font-medium text-neutral-400 hover:text-white hover:bg-neutral-800/80 transition-all duration-200">
                        Features
                    </a>
                    <a href="{{ route('blog.index') }}" class="px-3.5 py-2 rounded-md text-sm font-medium text-neutral-400 hover:text-white hover:bg-neutral-800/80 transition-all duration-200">
                        Blog
                    </a>
                    <a href="{{ route('front.about') }}" class="px-3.5 py-2 rounded-md text-sm font-medium text-neutral-400 hover:text-white hover:bg-neutral-800/80 transition-all duration-200">
                        About
                    </a>
                </div>

                <!-- Desktop Auth -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-neutral-400 hover:text-white transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-neutral-400 hover:text-white transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-all duration-200 shadow-sm shadow-emerald-500/10">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors" aria-controls="mobile-menu" aria-expanded="false">
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
        <div id="mobile-menu" class="hidden md:hidden border-t border-neutral-800 bg-neutral-950">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('front.for-affiliates') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    For Affiliates
                </a>
                <a href="{{ route('front.for-advertisers') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    For Advertisers
                </a>
                <a href="{{ route('front.features') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    Features
                </a>
                <a href="{{ route('blog.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    Blog
                </a>
                <a href="{{ route('front.about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    About
                </a>
                <div class="pt-4 mt-2 border-t border-neutral-800 space-y-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-neutral-400 hover:text-white hover:bg-neutral-800 transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-emerald-500 hover:bg-emerald-600 text-white text-center transition-colors">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-neutral-800 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-12">
                <!-- Brand -->
                <div class="lg:col-span-2">
                    <a href="{{ route('front.home') }}" class="inline-flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('logo/full-white.png') }}" alt="{{ config('app.name', 'ClicksIntel') }}" class="h-8">
                    </a>
                    <p class="text-neutral-400 text-sm leading-relaxed max-w-sm">
                        Performance marketing infrastructure for serious affiliates and advertisers. Built for speed, transparency, and scale.
                    </p>
                </div>

                <!-- Platform -->
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-4">Platform</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('front.for-affiliates') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">For Affiliates</a></li>
                        <li><a href="{{ route('front.for-advertisers') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">For Advertisers</a></li>
                        <li><a href="{{ route('front.features') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Features</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Blog</a></li>
                        <li><a href="{{ route('front.about') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">About Us</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-4">Support</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('front.contact') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Contact</a></li>
                        <li><a href="{{ route('front.faq') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">FAQ</a></li>
                        <li><a href="{{ route('register') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Get Started</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-4">Legal</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('front.privacy') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('front.terms') }}" class="text-neutral-400 hover:text-emerald-400 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-neutral-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-neutral-500">&copy; {{ date('Y') }} {{ config('app.name', 'ClicksIntel') }}. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1.5 text-xs text-neutral-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        All systems operational
                    </span>
                </div>
            </div>
        </div>
    </footer>

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
</body>
</html>
