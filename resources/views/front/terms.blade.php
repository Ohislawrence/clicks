@extends('layouts.front')

@section('title', 'Terms of Service | ' . config('app.name'))
@section('meta_description', 'Read the terms of service for ' . config('app.name') . '. Understand the rules, guidelines, and policies for using our performance marketing platform.')
@section('meta_keywords', 'terms of service, terms and conditions, user agreement, platform rules, legal terms')
@section('meta_robots', 'noindex, follow')

@push('styles')
<style>
    .terms-section {
        scroll-margin-top: 80px;
    }

    .terms-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
        transition: all 0.2s ease;
    }

    .terms-sidebar {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
    }

    .terms-sidebar-link {
        display: block;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        color: #a3a3a3;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .terms-sidebar-link:hover {
        background: #1f1f1f;
        color: #10b981;
    }
    .terms-sidebar-link.active {
        background: #1f1f1f;
        color: #10b981;
        border-left: 2px solid #10b981;
    }

    .prose-terms {
        color: #d4d4d4;
        line-height: 1.75;
    }
    .prose-terms h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .prose-terms h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #e5e5e5;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .prose-terms p {
        margin-bottom: 1rem;
    }
    .prose-terms ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .prose-terms li {
        margin-bottom: 0.25rem;
    }
    .prose-terms a {
        color: #10b981;
        transition: color 0.2s ease;
    }
    .prose-terms a:hover {
        color: #34d399;
    }
    .prose-terms strong {
        color: white;
        font-weight: 600;
    }

    .info-box {
        background: #1a1a1a;
        border: 1px solid #2c2c2c;
        border-radius: 16px;
    }

    .badge-terms {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .warning-callout {
        background: rgba(245, 158, 11, 0.08);
        border-left: 3px solid #f59e0b;
        padding: 1rem;
        border-radius: 12px;
        margin: 1rem 0;
    }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <div class="flex flex-wrap gap-3 mb-6">
                <span class="badge-terms">Updated: {{ date('F d, Y') }}</span>
                <span class="badge-terms">Legally Binding</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Terms of Service
            </h1>
            <p class="text-lg text-neutral-400 leading-relaxed">
                These Terms of Service govern your access to and use of {{ config('app.name') }}'s platform
                and services. By using our platform, you agree to be bound by these Terms.
            </p>
        </div>
    </div>
</section>

<!-- Content with Sidebar Navigation -->
<section class="py-16 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="terms-sidebar p-4 sticky top-24">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-800">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        <span class="text-sm font-semibold text-white">Contents</span>
                    </div>
                    <nav class="space-y-1">
                        <a href="#acceptance" class="terms-sidebar-link">1. Acceptance of Terms</a>
                        <a href="#registration" class="terms-sidebar-link">2. Account Registration</a>
                        <a href="#roles" class="terms-sidebar-link">3. User Roles & Responsibilities</a>
                        <a href="#prohibited" class="terms-sidebar-link">4. Prohibited Activities</a>
                        <a href="#payments" class="terms-sidebar-link">5. Payments & Commissions</a>
                        <a href="#intellectual" class="terms-sidebar-link">6. Intellectual Property</a>
                        <a href="#tracking" class="terms-sidebar-link">7. Tracking & Data</a>
                        <a href="#fraud" class="terms-sidebar-link">8. Compliance & Fraud Prevention</a>
                        <a href="#termination" class="terms-sidebar-link">9. Account Termination</a>
                        <a href="#disclaimers" class="terms-sidebar-link">10. Disclaimers</a>
                        <a href="#liability" class="terms-sidebar-link">11. Limitation of Liability</a>
                        <a href="#indemnification" class="terms-sidebar-link">12. Indemnification</a>
                        <a href="#disputes" class="terms-sidebar-link">13. Dispute Resolution</a>
                        <a href="#law" class="terms-sidebar-link">14. Governing Law</a>
                        <a href="#changes" class="terms-sidebar-link">15. Changes to Terms</a>
                        <a href="#misc" class="terms-sidebar-link">16. Miscellaneous</a>
                        <a href="#contact" class="terms-sidebar-link">17. Contact Information</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="terms-card p-6 md:p-8">
                    <div class="prose-terms max-w-none">

                        <!-- Section 1 -->
                        <h2 id="acceptance">1. Acceptance of Terms</h2>
                        <p>
                            By creating an account or using our services, you agree to these Terms, our
                            <a href="{{ route('front.privacy') }}">Privacy Policy</a>, and any additional terms that apply to specific features.
                            If you do not agree, you may not use our platform.
                        </p>

                        <!-- Section 2 -->
                        <h2 id="registration">2. Account Registration</h2>

                        <h3>2.1 Eligibility</h3>
                        <p>To use our platform, you must:</p>
                        <ul>
                            <li>Be at least 18 years of age</li>
                            <li>Have the legal capacity to enter into binding contracts</li>
                            <li>Provide accurate and complete registration information</li>
                            <li>Not be prohibited from using our services under applicable laws</li>
                        </ul>

                        <h3>2.2 Account Security</h3>
                        <p>
                            You are responsible for maintaining the confidentiality of your account credentials and for all
                            activities that occur under your account. You must notify us immediately of any unauthorized access
                            or security breach.
                        </p>

                        <h3>2.3 Account Verification</h3>
                        <p>
                            We reserve the right to verify your identity and business information. You must provide accurate
                            documentation when requested. Failure to verify may result in account suspension or termination.
                        </p>

                        <!-- Section 3 -->
                        <h2 id="roles">3. User Roles and Responsibilities</h2>

                        <h3>3.1 Affiliates</h3>
                        <p>As an affiliate, you agree to:</p>
                        <ul>
                            <li>Promote offers in a legal and ethical manner</li>
                            <li>Comply with all applicable advertising laws and regulations</li>
                            <li>Not engage in fraudulent activities, including fake clicks or conversions</li>
                            <li>Accurately represent offers and not make false claims</li>
                            <li>Disclose your affiliate relationship as required by law</li>
                            <li>Not use spam, misleading content, or prohibited traffic sources</li>
                            <li>Respect trademark and intellectual property rights</li>
                        </ul>

                        <h3>3.2 Advertisers</h3>
                        <p>As an advertiser, you agree to:</p>
                        <ul>
                            <li>Provide accurate offer details and promotional materials</li>
                            <li>Honor commissions for valid conversions</li>
                            <li>Not engage in conversion scrubbing or unjustified chargebacks</li>
                            <li>Comply with all applicable laws regarding your products/services</li>
                            <li>Maintain functional tracking and landing pages</li>
                            <li>Provide timely conversion data and reports</li>
                        </ul>

                        <!-- Section 4 -->
                        <h2 id="prohibited">4. Prohibited Activities</h2>
                        <p>You may not:</p>
                        <ul>
                            <li>Generate fraudulent clicks, leads, or conversions</li>
                            <li>Use bots, scripts, or automated tools to manipulate tracking</li>
                            <li>Engage in cookie stuffing, forced clicks, or incentivized traffic (unless approved)</li>
                            <li>Use misleading or deceptive advertising practices</li>
                            <li>Violate any applicable laws, including CAN-SPAM, GDPR, and CCPA</li>
                            <li>Infringe on intellectual property rights</li>
                            <li>Reverse engineer or attempt to access our systems unauthorized</li>
                            <li>Share account credentials or transfer accounts without approval</li>
                            <li>Promote illegal products, services, or content</li>
                            <li>Engage in adult content promotion (unless specifically authorized)</li>
                        </ul>

                        <!-- Section 5 -->
                        <h2 id="payments">5. Payments and Commissions</h2>

                        <h3>5.1 Affiliate Payments</h3>
                        <p>
                            Affiliates earn commissions based on valid conversions as defined in each offer's terms.
                            Payment terms include:
                        </p>
                        <ul>
                            <li>Minimum payout threshold: As specified in your account settings</li>
                            <li>Payment schedule: Weekly, bi-weekly, or monthly (based on offer terms)</li>
                            <li>Payment methods: Bank transfer, PayPal, or other approved methods</li>
                            <li>Tax compliance: You are responsible for all applicable taxes</li>
                        </ul>

                        <h3>5.2 Advertiser Payments</h3>
                        <p>
                            Advertisers must fund their accounts and maintain sufficient balance to cover commissions.
                            Invoicing terms are established during account setup.
                        </p>

                        <h3>5.3 Chargebacks and Adjustments</h3>
                        <p>
                            We reserve the right to adjust commissions for invalid, fraudulent, or reversed conversions.
                            Negative balances resulting from adjustments must be repaid.
                        </p>

                        <!-- Section 6 -->
                        <h2 id="intellectual">6. Intellectual Property</h2>
                        <p>
                            All content, trademarks, logos, and materials on our platform are owned by {{ config('app.name') }}
                            or our licensors. You may not use our intellectual property without express written permission,
                            except as necessary for authorized promotion of offers.
                        </p>

                        <!-- Section 7 -->
                        <h2 id="tracking">7. Tracking and Data</h2>
                        <p>
                            Our platform uses tracking technologies to record clicks, conversions, and other metrics. You
                            acknowledge that tracking data is the basis for commission calculations and dispute resolution.
                            We make reasonable efforts to ensure tracking accuracy but do not guarantee 100% accuracy.
                        </p>

                        <!-- Section 8 -->
                        <h2 id="fraud">8. Compliance and Fraud Prevention</h2>
                        <p>We employ fraud detection systems to protect all parties. We reserve the right to:</p>
                        <ul>
                            <li>Monitor traffic quality and conversion patterns</li>
                            <li>Request additional verification or documentation</li>
                            <li>Suspend or terminate accounts suspected of fraud</li>
                            <li>Withhold payments pending investigation</li>
                            <li>Report illegal activities to authorities</li>
                        </ul>

                        <!-- Section 9 -->
                        <h2 id="termination">9. Account Suspension and Termination</h2>

                        <h3>9.1 By You</h3>
                        <p>
                            You may terminate your account at any time by contacting support. Earned commissions will be
                            paid according to the normal schedule, subject to verification.
                        </p>

                        <h3>9.2 By Us</h3>
                        <p>
                            We may suspend or terminate your account immediately for violation of these Terms, fraudulent
                            activity, or any reason at our discretion. Upon termination, you forfeit pending commissions
                            associated with invalid activity.
                        </p>

                        <!-- Section 10 -->
                        <h2 id="disclaimers">10. Disclaimers and Warranties</h2>
                        <div class="warning-callout">
                            <p class="text-sm text-amber-400 mb-0">IMPORTANT DISCLAIMER</p>
                        </div>
                        <p>
                            OUR PLATFORM IS PROVIDED "AS IS" WITHOUT WARRANTIES OF ANY KIND. WE DISCLAIM ALL WARRANTIES,
                            EXPRESS OR IMPLIED, INCLUDING:
                        </p>
                        <ul>
                            <li>Merchantability and fitness for a particular purpose</li>
                            <li>Uninterrupted or error-free service</li>
                            <li>Accuracy or completeness of tracking data</li>
                            <li>Specific commission earnings or results</li>
                        </ul>

                        <!-- Section 11 -->
                        <h2 id="liability">11. Limitation of Liability</h2>
                        <p>
                            TO THE MAXIMUM EXTENT PERMITTED BY LAW, {{ strtoupper(config('app.name')) }} SHALL NOT BE LIABLE FOR
                            ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING LOST PROFITS,
                            REVENUE, OR DATA. OUR TOTAL LIABILITY SHALL NOT EXCEED THE AMOUNT PAID TO OR BY YOU IN THE
                            THREE MONTHS PRECEDING THE CLAIM.
                        </p>

                        <!-- Section 12 -->
                        <h2 id="indemnification">12. Indemnification</h2>
                        <p>
                            You agree to indemnify and hold harmless {{ config('app.name') }}, its affiliates, and personnel
                            from any claims, damages, losses, or expenses arising from your use of the platform, violation
                            of these Terms, or infringement of third-party rights.
                        </p>

                        <!-- Section 13 -->
                        <h2 id="disputes">13. Dispute Resolution</h2>
                        <p>
                            Any disputes arising from these Terms shall be resolved through binding arbitration in accordance
                            with the rules of the American Arbitration Association. You waive the right to participate in class actions.
                            The prevailing party may recover attorney's fees.
                        </p>

                        <!-- Section 14 -->
                        <h2 id="law">14. Governing Law</h2>
                        <p>
                            These Terms are governed by the laws of the State of Delaware, without regard to conflict of law
                            principles. Any legal actions must be brought in the courts of Wilmington, Delaware.
                        </p>

                        <!-- Section 15 -->
                        <h2 id="changes">15. Changes to Terms</h2>
                        <p>
                            We may modify these Terms at any time. Material changes will be notified via email or platform
                            notice. Your continued use after changes constitutes acceptance. If you disagree with changes,
                            you must discontinue use.
                        </p>

                        <!-- Section 16 -->
                        <h2 id="misc">16. Miscellaneous</h2>
                        <p><strong>Entire Agreement:</strong> These Terms constitute the entire agreement between you and {{ config('app.name') }}.</p>
                        <p><strong>Severability:</strong> If any provision is found unenforceable, the remaining provisions remain in effect.</p>
                        <p><strong>No Waiver:</strong> Our failure to enforce any right does not constitute a waiver.</p>

                        <!-- Section 17 -->
                        <h2 id="contact">17. Contact Information</h2>
                        <p>For questions about these Terms, contact us:</p>

                        <div class="info-box p-6 mt-4">
                            <p class="font-semibold text-white mb-3">{{ config('app.name') }}</p>
                            <p class="text-neutral-400 mb-2">
                                <span class="text-neutral-500">Email:</span>
                                <a href="mailto:legal@clicksintel.com" class="text-emerald-400 hover:text-emerald-300">legal@clicksintel.com</a>
                            </p>
                            <p class="text-neutral-400">
                                <span class="text-neutral-500">Address:</span> 123 Performance Way, Suite 500, Wilmington, DE 19801
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Last Updated Note -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-neutral-600">
                        These Terms of Service were last updated on {{ date('F d, Y') }}. Previous versions are available upon request.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Sidebar navigation - highlight active section on scroll
    (function() {
        const sections = document.querySelectorAll('.prose-terms h2');
        const navLinks = document.querySelectorAll('.terms-sidebar-link');

        function updateActiveLink() {
            let current = '';
            const scrollPosition = window.scrollY + 120;

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

        window.addEventListener('scroll', updateActiveLink);
        updateActiveLink();
    })();
</script>
@endpush
