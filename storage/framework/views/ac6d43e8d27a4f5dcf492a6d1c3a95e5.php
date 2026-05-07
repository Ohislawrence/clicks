<?php $__env->startSection('title', 'Privacy Policy | ' . config('app.name')); ?>
<?php $__env->startSection('meta_description', 'Read our privacy policy to understand how ' . config('app.name') . ' collects, uses, and protects your personal information. GDPR and CCPA compliant.'); ?>
<?php $__env->startSection('meta_keywords', 'privacy policy, data protection, GDPR, CCPA, personal information, data security'); ?>
<?php $__env->startSection('meta_robots', 'noindex, follow'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .policy-section {
        scroll-margin-top: 80px;
    }

    .policy-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
        transition: all 0.2s ease;
    }

    .policy-sidebar {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
    }

    .policy-sidebar-link {
        display: block;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        color: #a3a3a3;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .policy-sidebar-link:hover {
        background: #1f1f1f;
        color: #10b981;
    }
    .policy-sidebar-link.active {
        background: #1f1f1f;
        color: #10b981;
        border-left: 2px solid #10b981;
    }

    .prose-custom {
        color: #d4d4d4;
        line-height: 1.75;
    }
    .prose-custom h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .prose-custom h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #e5e5e5;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .prose-custom p {
        margin-bottom: 1rem;
    }
    .prose-custom ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .prose-custom li {
        margin-bottom: 0.25rem;
    }
    .prose-custom a {
        color: #10b981;
        transition: color 0.2s ease;
    }
    .prose-custom a:hover {
        color: #34d399;
    }

    .info-box {
        background: #1a1a1a;
        border: 1px solid #2c2c2c;
        border-radius: 16px;
    }

    .badge-gdpr {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 500;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
        <div class="max-w-3xl">
            <div class="flex flex-wrap gap-3 mb-6">
                <span class="badge-gdpr">Updated: <?php echo e(date('F d, Y')); ?></span>
                <span class="badge-gdpr">GDPR Compliant</span>
                <span class="badge-gdpr">CCPA Compliant</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Privacy Policy
            </h1>
            <p class="text-lg text-neutral-400 leading-relaxed">
                At <?php echo e(config('app.name')); ?>, we take your privacy seriously. This Privacy Policy explains how we collect,
                use, disclose, and safeguard your information when you use our platform.
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
                <div class="policy-sidebar p-4 sticky top-24">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-800">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                        <span class="text-sm font-semibold text-white">On this page</span>
                    </div>
                    <nav class="space-y-1">
                        <a href="#collect" class="policy-sidebar-link">1. Information We Collect</a>
                        <a href="#use" class="policy-sidebar-link">2. How We Use Your Information</a>
                        <a href="#sharing" class="policy-sidebar-link">3. Information Sharing</a>
                        <a href="#security" class="policy-sidebar-link">4. Data Security</a>
                        <a href="#retention" class="policy-sidebar-link">5. Data Retention</a>
                        <a href="#rights" class="policy-sidebar-link">6. Your Rights</a>
                        <a href="#cookies" class="policy-sidebar-link">7. Cookies & Tracking</a>
                        <a href="#transfers" class="policy-sidebar-link">8. International Transfers</a>
                        <a href="#children" class="policy-sidebar-link">9. Children's Privacy</a>
                        <a href="#thirdparty" class="policy-sidebar-link">10. Third-Party Links</a>
                        <a href="#changes" class="policy-sidebar-link">11. Changes to This Policy</a>
                        <a href="#contact" class="policy-sidebar-link">12. Contact Us</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <div class="policy-card p-6 md:p-8">
                    <div class="prose-custom max-w-none">

                        <!-- Section 1 -->
                        <h2 id="collect">1. Information We Collect</h2>

                        <h3>1.1 Personal Information</h3>
                        <p>We collect personal information that you provide to us, including:</p>
                        <ul>
                            <li>Name and contact information (email address, phone number, mailing address)</li>
                            <li>Account credentials (username and password)</li>
                            <li>Payment information (bank account details, PayPal information)</li>
                            <li>Tax identification information (as required by law)</li>
                            <li>Business information (company name, website URL)</li>
                        </ul>

                        <h3>1.2 Automatically Collected Information</h3>
                        <p>When you use our platform, we automatically collect:</p>
                        <ul>
                            <li>Device information (IP address, browser type, operating system)</li>
                            <li>Usage data (pages visited, time spent, clicks)</li>
                            <li>Tracking data (cookies, web beacons, pixels)</li>
                            <li>Location information (derived from IP address)</li>
                        </ul>

                        <h3>1.3 Third-Party Information</h3>
                        <p>
                            We may receive information about you from third-party partners, such as analytics providers,
                            advertising networks, and identity verification services.
                        </p>

                        <!-- Section 2 -->
                        <h2 id="use">2. How We Use Your Information</h2>
                        <p>We use your information to:</p>
                        <ul>
                            <li>Provide, operate, and maintain our platform and services</li>
                            <li>Process transactions and send payment notifications</li>
                            <li>Track clicks, conversions, and commissions</li>
                            <li>Communicate with you about your account and offers</li>
                            <li>Detect and prevent fraud, abuse, and security incidents</li>
                            <li>Analyze usage patterns and improve our services</li>
                            <li>Comply with legal obligations and enforce our Terms of Service</li>
                            <li>Send marketing communications (with your consent)</li>
                        </ul>

                        <!-- Section 3 -->
                        <h2 id="sharing">3. Information Sharing and Disclosure</h2>

                        <h3>3.1 With Your Consent</h3>
                        <p>We may share your information when you give us explicit consent to do so.</p>

                        <h3>3.2 Service Providers</h3>
                        <p>
                            We share information with third-party vendors who perform services on our behalf, including
                            payment processors, analytics providers, email service providers, and hosting services.
                        </p>

                        <h3>3.3 Business Partners</h3>
                        <p>
                            We may share aggregated or de-identified information with advertisers and affiliates to facilitate
                            campaign management and tracking.
                        </p>

                        <h3>3.4 Legal Requirements</h3>
                        <p>
                            We may disclose your information if required by law, court order, or governmental request, or to
                            protect our rights, property, or safety.
                        </p>

                        <h3>3.5 Business Transfers</h3>
                        <p>
                            In the event of a merger, acquisition, or sale of assets, your information may be transferred to
                            the acquiring entity.
                        </p>

                        <!-- Section 4 -->
                        <h2 id="security">4. Data Security</h2>
                        <p>We implement appropriate technical and organizational measures to protect your information, including:</p>
                        <ul>
                            <li>Encryption of data in transit and at rest</li>
                            <li>Regular security assessments and audits</li>
                            <li>Access controls and authentication mechanisms</li>
                            <li>Employee training on data protection</li>
                            <li>Incident response procedures</li>
                        </ul>
                        <p>
                            However, no method of transmission over the Internet is 100% secure, and we cannot guarantee
                            absolute security.
                        </p>

                        <!-- Section 5 -->
                        <h2 id="retention">5. Data Retention</h2>
                        <p>
                            We retain your information for as long as necessary to fulfill the purposes outlined in this
                            Privacy Policy, unless a longer retention period is required by law. Account information is
                            retained while your account is active and for a reasonable period thereafter for legal and
                            operational purposes.
                        </p>

                        <!-- Section 6 -->
                        <h2 id="rights">6. Your Rights and Choices</h2>
                        <p>Depending on your jurisdiction, you may have the following rights:</p>
                        <ul>
                            <li><strong>Access:</strong> Request access to your personal information</li>
                            <li><strong>Correction:</strong> Request correction of inaccurate information</li>
                            <li><strong>Deletion:</strong> Request deletion of your information</li>
                            <li><strong>Portability:</strong> Request a copy of your data in a portable format</li>
                            <li><strong>Opt-Out:</strong> Opt out of marketing communications</li>
                            <li><strong>Objection:</strong> Object to certain processing activities</li>
                        </ul>
                        <p>
                            To exercise these rights, please contact us at <a href="mailto:privacy@clicksintel.com">privacy@clicksintel.com</a>.
                        </p>

                        <!-- Section 7 -->
                        <h2 id="cookies">7. Cookies and Tracking Technologies</h2>
                        <p>We use cookies and similar tracking technologies to:</p>
                        <ul>
                            <li>Remember your preferences and settings</li>
                            <li>Track affiliate clicks and conversions</li>
                            <li>Analyze site traffic and user behavior</li>
                            <li>Deliver targeted advertising</li>
                        </ul>
                        <p>
                            You can control cookies through your browser settings, but disabling cookies may limit certain
                            features of our platform.
                        </p>

                        <!-- Section 8 -->
                        <h2 id="transfers">8. International Data Transfers</h2>
                        <p>
                            Your information may be transferred to and processed in countries other than your country of
                            residence. We ensure appropriate safeguards are in place to protect your information in accordance
                            with this Privacy Policy and applicable laws.
                        </p>

                        <!-- Section 9 -->
                        <h2 id="children">9. Children's Privacy</h2>
                        <p>
                            Our platform is not intended for individuals under the age of 18. We do not knowingly collect
                            personal information from children. If we become aware that we have collected information from
                            a child, we will take steps to delete it.
                        </p>

                        <!-- Section 10 -->
                        <h2 id="thirdparty">10. Third-Party Links</h2>
                        <p>
                            Our platform may contain links to third-party websites. We are not responsible for the privacy
                            practices of these external sites. We encourage you to review their privacy policies.
                        </p>

                        <!-- Section 11 -->
                        <h2 id="changes">11. Changes to This Privacy Policy</h2>
                        <p>
                            We may update this Privacy Policy from time to time. We will notify you of any material changes
                            by posting the new policy on this page and updating the "Last Updated" date. Your continued use
                            of the platform after changes constitutes acceptance of the updated policy.
                        </p>

                        <!-- Section 12 -->
                        <h2 id="contact">12. Contact Us</h2>
                        <p>
                            If you have questions or concerns about this Privacy Policy, please contact us:
                        </p>

                        <div class="info-box p-6 mt-4">
                            <p class="font-semibold text-white mb-3"><?php echo e(config('app.name')); ?></p>
                            <p class="text-neutral-400 mb-2">
                                <span class="text-neutral-500">Email:</span>
                                <a href="mailto:privacy@clicksintel.com" class="text-emerald-400 hover:text-emerald-300">privacy@clicksintel.com</a>
                            </p>
                            <p class="text-neutral-400">
                                <span class="text-neutral-500">Address:</span> 123 Performance Way, Suite 500, San Francisco, CA 94105
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Last Updated Note -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-neutral-600">
                        This Privacy Policy was last updated on <?php echo e(date('F d, Y')); ?>. Previous versions are available upon request.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Sidebar navigation - highlight active section on scroll
    (function() {
        const sections = document.querySelectorAll('.prose-custom h2');
        const navLinks = document.querySelectorAll('.policy-sidebar-link');

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\dealsintel\resources\views/front/privacy.blade.php ENDPATH**/ ?>