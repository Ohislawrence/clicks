@extends('layouts.front')

@section('title', 'Contact Us - Get in Touch | ' . config('app.name'))
@section('meta_description', 'Contact ' . config('app.name') . ' support team. Get help with your account, campaigns, or general inquiries. Available Monday-Sunday with 24-hour response time.')
@section('meta_keywords', 'contact us, customer support, help desk, contact support, get in touch, affiliate support, advertiser support')

@section('og_title', 'Contact Us - We\'re Here to Help')
@section('og_description', 'Get in touch with our support team. We typically respond within 24 hours.')

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ContactPage",
  "mainEntity": {
    "@@type": "Organization",
    "name": "{{ config('app.name') }}",
    "contactPoint": {
      "@@type": "ContactPoint",
      "contactType": "Customer Support",
      "email": "support@clicksintel.com",
      "availableLanguage": "English"
    }
  }
}
</script>
@endpush

@push('styles')
<style>
    .contact-card {
        background: #171717;
        border: 1px solid #262626;
        transition: all 0.2s ease;
    }
    .contact-card:hover {
        border-color: #333333;
        background: #1c1c1c;
    }

    .input-dark {
        background: #171717;
        border: 1px solid #2c2c2c;
        transition: all 0.2s ease;
    }
    .input-dark:focus {
        border-color: #10b981;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
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
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Get in Touch</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                We'd love to<br />
                <span class="text-emerald-400">hear from you</span>
            </h1>
            <p class="text-lg text-neutral-400 leading-relaxed">
                Have questions about our platform, need support, or want to discuss partnership opportunities?
                Our team is ready to help.
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

            <!-- Contact Form -->
            <div class="fade-up" data-animate>
                <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Send us a Message</h2>
                    <p class="text-neutral-400 mb-6">Fill out the form and we'll get back to you within 24 hours.</p>

                    @if(session('success'))
                        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-6 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('front.contact.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-300 mb-2">
                                Your Name <span class="text-emerald-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="John Doe"
                                class="input-dark w-full px-4 py-2.5 rounded-xl text-white placeholder-neutral-500 @error('name') border-red-500 @enderror"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-300 mb-2">
                                Your Email <span class="text-emerald-400">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="john@example.com"
                                class="input-dark w-full px-4 py-2.5 rounded-xl text-white placeholder-neutral-500 @error('email') border-red-500 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-neutral-300 mb-2">
                                Subject <span class="text-emerald-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="subject"
                                id="subject"
                                value="{{ old('subject') }}"
                                required
                                placeholder="How can we help?"
                                class="input-dark w-full px-4 py-2.5 rounded-xl text-white placeholder-neutral-500 @error('subject') border-red-500 @enderror"
                            >
                            @error('subject')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-neutral-300 mb-2">
                                Message <span class="text-emerald-400">*</span>
                            </label>
                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                required
                                placeholder="Please provide details about your inquiry..."
                                class="input-dark w-full px-4 py-2.5 rounded-xl text-white placeholder-neutral-500 resize-none @error('message') border-red-500 @enderror"
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2 group"
                        >
                            Send Message
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-6">
                <div class="fade-up" data-animate data-delay="100">
                    <h2 class="text-2xl font-bold text-white mb-6">Get in Touch</h2>

                    <div class="space-y-5">
                        <!-- Email -->
                        <div class="contact-card rounded-xl p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-white mb-1">Email Us</h3>
                                    <a href="mailto:support@clicksintel.com" class="text-emerald-400 hover:text-emerald-300 transition-colors">
                                        support@clicksintel.com
                                    </a>
                                    <p class="text-sm text-neutral-500 mt-1">We typically respond within 24 hours</p>
                                </div>
                            </div>
                        </div>

                        <!-- Support Hours -->
                        <div class="contact-card rounded-xl p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-white mb-1">Support Hours</h3>
                                    <p class="text-neutral-400">Monday - Friday: 9:00 AM - 6:00 PM EST</p>
                                    <p class="text-neutral-500 text-sm mt-1">Saturday - Sunday: 10:00 AM - 4:00 PM EST</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="contact-card rounded-xl p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-white mb-1">Quick Resources</h3>
                                    <div class="space-y-1">
                                        <a href="#" class="block text-neutral-400 hover:text-emerald-400 text-sm transition-colors">Help Center & Documentation →</a>
                                        <a href="#" class="block text-neutral-400 hover:text-emerald-400 text-sm transition-colors">API Documentation →</a>
                                        <a href="{{ route('front.faq') }}" class="block text-neutral-400 hover:text-emerald-400 text-sm transition-colors">Frequently Asked Questions →</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="contact-card rounded-xl p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-white mb-2">Follow Us</h3>
                                    <div class="flex gap-4">
                                        <a href="#" class="text-neutral-500 hover:text-emerald-400 transition-colors" aria-label="Twitter">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="text-neutral-500 hover:text-emerald-400 transition-colors" aria-label="LinkedIn">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451c.979 0 1.771-.773 1.771-1.729V1.729C24 .774 23.204 0 22.225 0z"/>
                                            </svg>
                                        </a>
                                        <a href="#" class="text-neutral-500 hover:text-emerald-400 transition-colors" aria-label="Instagram">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Center Card -->
                <div class="fade-up" data-animate data-delay="200">
                    <div class="bg-neutral-900 border border-neutral-800 rounded-xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-white mb-1">Need Immediate Help?</h3>
                                <p class="text-neutral-400 text-sm mb-4">
                                    Check out our comprehensive documentation and FAQ section for quick answers to common questions.
                                </p>
                                <a href="#" class="inline-flex items-center gap-2 text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                    Visit Help Center
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map / Office Section -->
<section class="py-16 bg-neutral-900 border-t border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="fade-up" data-animate>
                <div class="inline-flex items-center gap-2 bg-neutral-800/50 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Our Location</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                    Visit Our Headquarters
                </h2>
                <div class="space-y-3 text-neutral-400">
                    <p class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>123 Performance Way, Suite 500<br />San Francisco, CA 94105</span>
                    </p>
                    <p class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span>+1 (555) 123-4567</span>
                    </p>
                </div>
            </div>

            <div class="fade-up" data-animate data-delay="100">
                <div class="bg-neutral-800/30 rounded-2xl p-4 border border-neutral-800">
                    <div class="bg-neutral-800 rounded-xl h-48 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-neutral-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <p class="text-neutral-500 text-sm">Map View — San Francisco, CA</p>
                        </div>
                    </div>
                </div>
            </div>
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
@endpush
