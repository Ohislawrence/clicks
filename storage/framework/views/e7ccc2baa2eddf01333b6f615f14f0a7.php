

<?php $__env->startSection('title', config('app.name') . ' - Nigeria\'s Performance Marketing Platform | CPA Network'); ?>
<?php $__env->startSection('meta_description', 'Nigeria\'s most trusted affiliate marketing platform. Real-time fraud scoring, Naira payouts via Paystack & Flutterwave, and exclusive local offers. Join thousands of affiliates and advertisers earning daily.'); ?>
<?php $__env->startSection('meta_keywords', 'affiliate marketing Nigeria, CPA network Nigeria, performance marketing Nigeria, affiliate network, Naira payouts, fraud protection, affiliate tracking, Nigerian affiliate program, advertiser network Nigeria'); ?>

<?php $__env->startSection('og_title', config('app.name') . ' - Nigeria\'s Smartest Affiliate Marketing Platform'); ?>
<?php $__env->startSection('og_description', 'Connect with top-performing Nigerian affiliates and advertisers. Real-time fraud scoring, Naira payouts, and exclusive fintech and e-commerce offers built for the Nigerian market.'); ?>

<?php $__env->startSection('twitter_title', config('app.name') . ' - Nigeria\'s Performance Marketing Platform'); ?>
<?php $__env->startSection('twitter_description', 'Join thousands of Nigerian affiliates earning daily Naira payouts. Real-time tracking, fraud protection, and local offers. Start free today.'); ?>

<?php $__env->startPush('structured_data'); ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php echo e(config('app.name')); ?>",
  "url": "<?php echo e(url('/')); ?>",
  "logo": "<?php echo e(asset('logo/full-white.png')); ?>",
  "description": "Nigeria's leading performance marketing platform connecting affiliates and advertisers with real-time fraud protection and Naira payouts.",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "1250"
  },
  "sameAs": [
    "https://www.facebook.com/clicksintel",
    "https://twitter.com/clicksintel",
    "https://www.linkedin.com/company/clicksintel"
  ]
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    :root {
        --ink:    #0a0c0b;
        --ink-2:  #111410;
        --ink-3:  #181c16;
        --ink-4:  #1e231b;
        --em:     #10b981;
        --em-l:   #34d399;
        --em-dim: #064e3b;
        --snow:   #f0f5f1;
        --ash:    #9aab9e;
        --stone:  #566b5b;
        --wire:   rgba(16,185,129,0.12);
        --wire-s: rgba(16,185,129,0.22);
        --fd: 'Inter', sans-serif;
        --fb: 'Inter', sans-serif;
    }

    html { scroll-behavior: smooth; }

    /* Grid texture overlay */
    body::before {
        content: '';
        position: fixed; inset: 0;
        background-image:
            linear-gradient(rgba(16,185,129,0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(16,185,129,0.025) 1px, transparent 1px);
        background-size: 52px 52px;
        pointer-events: none;
        z-index: 0;
    }

    .section { position: relative; z-index: 1; }

    .display {
        font-family: var(--fd);
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1.0;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 11px;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--em);
        font-weight: 500;
    }
    .eyebrow::before {
        content: '';
        display: block;
        width: 24px; height: 1.5px;
        background: var(--em);
        flex-shrink: 0;
    }

    .pulse-dot {
        width: 7px; height: 7px;
        background: var(--em);
        border-radius: 50%;
        position: relative;
        flex-shrink: 0;
    }
    .pulse-dot::after {
        content: '';
        position: absolute; inset: -4px;
        border-radius: 50%;
        border: 1.5px solid var(--em);
        opacity: 0;
        animation: pulse-ring 2s ease-out infinite;
    }
    @keyframes pulse-ring {
        0%   { opacity: 0.7; transform: scale(1); }
        100% { opacity: 0;   transform: scale(2.4); }
    }

    .ticker-track {
        display: flex;
        gap: 0;
        white-space: nowrap;
        animation: ticker 30s linear infinite;
    }
    .ticker-track:hover { animation-play-state: paused; }
    @keyframes ticker {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }

    .card-lift {
        transition: transform 0.25s cubic-bezier(.4,0,.2,1),
                    border-color 0.25s ease;
    }
    .card-lift:hover {
        transform: translateY(-4px);
        border-color: var(--wire-s) !important;
    }

    .btn-em {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: var(--em);
        color: var(--ink);
        font-family: var(--fb);
        font-size: 14px;
        font-weight: 500;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        white-space: nowrap;
    }
    .btn-em:hover {
        background: var(--em-l);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16,185,129,0.28);
        color: var(--ink);
        text-decoration: none;
    }
    .btn-em:active { transform: translateY(0); }

    .btn-ghost-em {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 26px;
        background: transparent;
        color: var(--snow);
        font-family: var(--fb);
        font-size: 14px;
        font-weight: 400;
        border-radius: 8px;
        border: 1px solid var(--wire-s);
        cursor: pointer;
        text-decoration: none;
        transition: border-color 0.2s ease, color 0.2s ease, transform 0.15s ease;
        white-space: nowrap;
    }
    .btn-ghost-em:hover {
        border-color: var(--em);
        color: var(--em-l);
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sep { border: none; border-top: 1px solid var(--wire); }

    .num-big {
        font-family: var(--fd);
        font-weight: 800;
        letter-spacing: -0.04em;
        color: var(--snow);
        line-height: 1;
    }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: var(--ink); }
    ::-webkit-scrollbar-thumb { background: var(--em-dim); border-radius: 3px; }

    .reveal {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .reveal.in-view {
        opacity: 1;
        transform: translateY(0);
    }

    .step-num {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: rgba(16,185,129,0.08);
        border: 1px solid var(--wire-s);
        display: grid;
        place-items: center;
        font-family: var(--fd);
        font-size: 13px;
        font-weight: 700;
        color: var(--em);
        flex-shrink: 0;
    }

    .quote-mark {
        font-family: var(--fd);
        font-size: 64px;
        line-height: 0.6;
        color: var(--em);
        font-weight: 800;
        user-select: none;
    }

    .mock-wrap {
        background: var(--ink-4);
        border: 1px solid var(--wire);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    }
    .mock-bar {
        background: var(--ink-3);
        border-bottom: 1px solid var(--wire);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mock-dot { width: 10px; height: 10px; border-radius: 50%; }

    .b-bar {
        flex: 1;
        background: rgba(16,185,129,0.1);
        border: 1px solid var(--wire);
        border-radius: 4px 4px 0 0;
        transition: background 0.3s;
    }
    .b-bar:hover { background: rgba(16,185,129,0.3); }
    .b-bar.hi { background: var(--em); border-color: var(--em); }

    @media (max-width: 640px) {
        .display { letter-spacing: -0.03em; }
        .mock-wrap { font-size: 90%; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<section class="section min-h-screen flex flex-col justify-center pt-4 pb-16 overflow-hidden" aria-label="Hero">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 w-full">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            
            <div>
                
                <div class="inline-flex items-center gap-3 px-4 py-2 mb-10"
                     style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.2);border-radius:6px;">
                    <div class="pulse-dot"></div>
                    <span style="font-size:12px;color:var(--em-l);letter-spacing:0.06em;">Platform live &middot; Accepting affiliates now</span>
                </div>

                
                <h1 class="display mb-6" style="font-size:clamp(38px,5.5vw,72px);color:var(--snow);">
                    Nigeria's smartest<br>
                    affiliate<br>
                    <span style="color:var(--em);">marketing</span><br>
                    platform
                </h1>

                
                <p class="mb-10 max-w-md leading-relaxed" style="font-size:17px;color:var(--ash);font-weight:300;">
                    Stop wasting budget on fake clicks. <?php echo e(config('app.name')); ?> gives advertisers real-time fraud scoring and affiliates Naira payouts - built for the Nigerian market from day one.
                </p>

                
                <div class="flex flex-wrap gap-4 mb-14">
                    <a href="<?php echo e(route('register')); ?>" class="btn-em">
                        Start earning free
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M3 7.5h9M8 4l3.5 3.5L8 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="<?php echo e(route('front.for-advertisers')); ?>" class="btn-ghost-em">
                        I'm an advertiser
                    </a>
                </div>

                
                <hr class="sep mb-8">
                <div class="grid grid-cols-3 gap-0">
                    <div style="padding-right:24px;border-right:1px solid var(--wire);">
                        <div class="num-big" style="font-size:clamp(22px,3vw,32px);">15K<span style="color:var(--em);">+</span></div>
                        <div style="font-size:12px;color:var(--stone);margin-top:4px;letter-spacing:0.04em;">Active affiliates</div>
                    </div>
                    <div style="padding:0 24px;border-right:1px solid var(--wire);">
                        <div class="num-big" style="font-size:clamp(22px,3vw,32px);">&#8358;<span style="color:var(--em);">2.4B</span></div>
                        <div style="font-size:12px;color:var(--stone);margin-top:4px;letter-spacing:0.04em;">Commissions paid</div>
                    </div>
                    <div style="padding-left:24px;">
                        <div class="num-big" style="font-size:clamp(22px,3vw,32px);">98<span style="color:var(--em);">%</span></div>
                        <div style="font-size:12px;color:var(--stone);margin-top:4px;letter-spacing:0.04em;">Uptime SLA</div>
                    </div>
                </div>
            </div>

            
            <div class="hidden lg:block reveal">
                <div class="mock-wrap">
                    <div class="mock-bar">
                        <div class="mock-dot" style="background:#ef4444;"></div>
                        <div class="mock-dot" style="background:#f59e0b;"></div>
                        <div class="mock-dot" style="background:#10b981;"></div>
                        <span style="font-size:11px;color:var(--stone);margin-left:10px;font-family:var(--fb);"><?php echo e(config('app.name')); ?> - Affiliate Dashboard</span>
                        <div class="ml-auto flex items-center gap-2">
                            <div class="pulse-dot" style="width:5px;height:5px;"></div>
                            <span style="font-size:10px;color:var(--em);">LIVE</span>
                        </div>
                    </div>
                    <div style="padding:20px;">
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:14px;">
                                <div style="font-size:10px;color:var(--stone);letter-spacing:0.06em;margin-bottom:6px;">TODAY'S EARNINGS</div>
                                <div style="font-family:var(--fd);font-size:22px;font-weight:700;color:var(--em);">&#8358;148K</div>
                                <div style="font-size:10px;color:var(--em);margin-top:4px;">&#8593; 12% vs yesterday</div>
                            </div>
                            <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:14px;">
                                <div style="font-size:10px;color:var(--stone);letter-spacing:0.06em;margin-bottom:6px;">CLICKS TODAY</div>
                                <div style="font-family:var(--fd);font-size:22px;font-weight:700;color:var(--snow);">4,821</div>
                                <div style="font-size:10px;color:var(--stone);margin-top:4px;">EPC: &#8358;30.70</div>
                            </div>
                            <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:14px;">
                                <div style="font-size:10px;color:var(--stone);letter-spacing:0.06em;margin-bottom:6px;">FRAUD SCORE</div>
                                <div style="font-family:var(--fd);font-size:22px;font-weight:700;color:var(--em);">97</div>
                                <div style="font-size:10px;color:var(--em);margin-top:4px;">Low risk</div>
                            </div>
                        </div>
                        <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:16px;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                                <span style="font-size:11px;color:var(--stone);">Conversions this week</span>
                                <span style="font-size:10px;color:var(--em);">+23.4%</span>
                            </div>
                            <div style="display:flex;align-items:flex-end;gap:5px;height:70px;" aria-hidden="true">
                                <div class="b-bar" style="height:38%;"></div>
                                <div class="b-bar" style="height:55%;"></div>
                                <div class="b-bar" style="height:42%;"></div>
                                <div class="b-bar" style="height:78%;"></div>
                                <div class="b-bar" style="height:60%;"></div>
                                <div class="b-bar hi" style="height:92%;"></div>
                                <div class="b-bar" style="height:74%;"></div>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-top:6px;" aria-hidden="true">
                                <span style="font-size:9px;color:var(--stone);">Mon</span>
                                <span style="font-size:9px;color:var(--stone);">Tue</span>
                                <span style="font-size:9px;color:var(--stone);">Wed</span>
                                <span style="font-size:9px;color:var(--stone);">Thu</span>
                                <span style="font-size:9px;color:var(--stone);">Fri</span>
                                <span style="font-size:9px;color:var(--em);">Sat</span>
                                <span style="font-size:9px;color:var(--stone);">Sun</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:12px;">
                                <div style="font-size:10px;color:var(--stone);margin-bottom:8px;letter-spacing:0.06em;">TOP OFFER</div>
                                <div style="font-size:12px;color:var(--snow);font-weight:500;margin-bottom:4px;">Fintech - App Install</div>
                                <div style="font-size:11px;color:var(--em);">&#8358;3,500 / lead</div>
                            </div>
                            <div style="background:var(--ink-3);border:1px solid var(--wire);border-radius:10px;padding:12px;">
                                <div style="font-size:10px;color:var(--stone);margin-bottom:6px;letter-spacing:0.06em;">TIER STATUS</div>
                                <div style="font-size:12px;color:var(--snow);font-weight:500;margin-bottom:6px;">Gold &middot; 10% bonus</div>
                                <div style="height:3px;background:var(--ink-4);border-radius:2px;">
                                    <div style="width:68%;height:100%;background:var(--em);border-radius:2px;"></div>
                                </div>
                                <div style="font-size:9px;color:var(--stone);margin-top:3px;">68% to Platinum</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="section overflow-hidden" style="border-top:1px solid var(--wire);border-bottom:1px solid var(--wire);background:var(--ink-2);" aria-label="Live activity" role="marquee">
    <div class="py-4">
        <div class="flex" style="overflow:hidden;">
            <div class="ticker-track">
                <?php $__currentLoopData = [
                    ['&#8358;148,000 earned today &middot; Gold affiliate &middot; Lagos', '4.2%', 'CR'],
                    ['247 conversions &middot; Fintech offer &middot; This week', '&#8358;3,500', 'EPL'],
                    ['Fraud blocked &middot; 312 bot clicks stopped &middot; Today', '97/100', 'Quality'],
                    ['New advertiser &middot; Digital course &middot; &#8358;2,000/lead', '500', 'Cap/day'],
                    ['&#8358;890,000 payout processed &middot; Paystack &middot; 2hr ago', '15K+', 'Affiliates'],
                    ['New offer &middot; E-commerce &middot; 8% commission &middot; Lagos', '30-day', 'Cookie'],
                    ['&#8358;148,000 earned today &middot; Gold affiliate &middot; Lagos', '4.2%', 'CR'],
                    ['247 conversions &middot; Fintech offer &middot; This week', '&#8358;3,500', 'EPL'],
                    ['Fraud blocked &middot; 312 bot clicks stopped &middot; Today', '97/100', 'Quality'],
                    ['New advertiser &middot; Digital course &middot; &#8358;2,000/lead', '500', 'Cap/day'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tick): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 px-8" style="border-right:1px solid var(--wire);flex-shrink:0;">
                    <div class="pulse-dot" style="width:5px;height:5px;"></div>
                    <span style="font-size:12px;color:var(--ash);"><?php echo $tick[0]; ?></span>
                    <span style="font-size:11px;color:var(--em);font-family:var(--fd);font-weight:700;"><?php echo $tick[1]; ?></span>
                    <span style="font-size:10px;color:var(--stone);letter-spacing:0.08em;"><?php echo e($tick[2]); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>


<section class="section py-28" aria-label="Problem statement">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="max-w-3xl mx-auto text-center mb-20 reveal">
            <div class="eyebrow mb-5" style="justify-content:center;">The problem</div>
            <h2 class="display mb-6" style="font-size:clamp(30px,4vw,54px);">
                Nigerian advertisers lose<br>
                <span style="color:var(--em);">30 — 60%</span> of affiliate budget<br>
                to fraud every month
            </h2>
            <p style="font-size:16px;color:var(--ash);line-height:1.8;font-weight:300;">
                International platforms don't understand our traffic patterns. They miss VPN farms, click rings, and the proxy networks unique to West African mobile data providers.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <?php $__currentLoopData = [
                ['num'=>'01','title'=>'Fake clicks drain budgets','body'=>'Bot farms and VPN rings generate thousands of worthless clicks that look real until your conversion rate tells the truth.','stat'=>'60%','stat_label'=>'of clicks can be fraudulent'],
                ['num'=>'02','title'=>'Naira payouts are a nightmare','body'=>'Getting paid from foreign networks in Nigeria means wire fees, conversion losses, and multi-week delays. Your money sits overseas.','stat'=>'3 wks','stat_label'=>'average payout delay elsewhere'],
                ['num'=>'03','title'=>'Zero local advertiser network','body'=>'Global platforms have no Nigerian fintech, e-commerce, or digital product offers. Affiliates promote irrelevant products to the wrong audience.','stat'=>'0','stat_label'=>'local NG offers on global nets'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-lift reveal" style="background:var(--ink-2);border:1px solid var(--wire);border-radius:14px;padding:28px;transition-delay:<?php echo e($i * 80); ?>ms;">
                <div style="font-family:var(--fd);font-size:11px;font-weight:700;color:var(--stone);letter-spacing:0.12em;margin-bottom:20px;"><?php echo e($card['num']); ?></div>
                <div style="font-family:var(--fd);font-size:32px;font-weight:800;color:var(--em);line-height:1;margin-bottom:6px;"><?php echo e($card['stat']); ?></div>
                <div style="font-size:11px;color:var(--stone);letter-spacing:0.06em;margin-bottom:20px;"><?php echo e($card['stat_label']); ?></div>
                <hr class="sep mb-5">
                <h3 style="font-family:var(--fd);font-size:17px;font-weight:700;color:var(--snow);margin-bottom:10px;"><?php echo e($card['title']); ?></h3>
                <p style="font-size:14px;color:var(--ash);line-height:1.75;font-weight:300;"><?php echo e($card['body']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section py-28" style="background:var(--ink-2);border-top:1px solid var(--wire);" aria-label="How it works">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="grid lg:grid-cols-2 gap-20 items-start">
            <div class="reveal">
                <div class="eyebrow mb-6">How it works</div>
                <h2 class="display mb-12" style="font-size:clamp(28px,3.5vw,48px);">
                    From sign-up to<br>
                    <span style="color:var(--em);">Naira in your account</span><br>
                    in 24 hours
                </h2>
                <div style="display:flex;flex-direction:column;gap:0;">
                    <?php $__currentLoopData = [
                        ['1','Create your account','Sign up as an affiliate or advertiser. Approval is same-day. No waiting weeks for a manual review.'],
                        ['2','Browse & select offers','Filter by category, payout type, and commission rate. Fintech, e-commerce, digital products - all Nigerian-relevant.'],
                        ['3','Get your unique tracking link','One click generates your tracking URL with full UTM support, sub-ID tracking, and cookie attribution.'],
                        ['4','Promote and track live','Every click is fraud-scored in real time. Your dashboard updates as conversions happen - no 24-hour lag.'],
                        ['5','Receive Naira payouts','Weekly or monthly via Paystack or Flutterwave directly to your Nigerian bank account. Minimum &#8358;5,000.'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;gap:20px;padding:24px 0;<?php echo e($i > 0 ? 'border-top:1px solid var(--wire);' : ''); ?>">
                        <div class="step-num"><?php echo e($step[0]); ?></div>
                        <div>
                            <div style="font-family:var(--fd);font-size:16px;font-weight:700;color:var(--snow);margin-bottom:6px;"><?php echo e($step[1]); ?></div>
                            <div style="font-size:14px;color:var(--ash);line-height:1.75;font-weight:300;"><?php echo $step[2]; ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="reveal lg:sticky lg:top-28">
                <div class="mock-wrap mb-5">
                    <div class="mock-bar">
                        <div class="mock-dot" style="background:#ef4444;"></div>
                        <div class="mock-dot" style="background:#f59e0b;"></div>
                        <div class="mock-dot" style="background:#10b981;"></div>
                        <span style="font-size:11px;color:var(--stone);margin-left:10px;">Fraud Detection Engine</span>
                    </div>
                    <div style="padding:24px;">
                        <div style="text-align:center;margin-bottom:24px;" aria-label="Quality score 97 - Low risk">
                            <div style="font-family:var(--fd);font-size:72px;font-weight:800;color:var(--em);line-height:1;" aria-hidden="true">97</div>
                            <div style="font-size:13px;color:var(--ash);margin-top:4px;">Quality score - Low risk</div>
                        </div>
                        <?php $__currentLoopData = [
                            ['IP reputation','&#10003; Clean residential IP',true],
                            ['VPN / Proxy','&#10003; No proxy detected',true],
                            ['Bot detection','&#10003; Human behaviour confirmed',true],
                            ['Click velocity','&#10003; Normal rate (2.1/min)',true],
                            ['Device fingerprint','&#10003; Unique device',true],
                            ['Conversion time','&#9888; Fast - monitoring',false],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;<?php echo e(!$loop->last ? 'border-bottom:1px solid var(--wire);' : ''); ?>">
                            <span style="font-size:13px;color:var(--ash);"><?php echo e($check[0]); ?></span>
                            <span style="font-size:12px;color:<?php echo e($check[2] ? 'var(--em)' : '#f59e0b'); ?>;font-weight:500;"><?php echo $check[1]; ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div style="margin-top:16px;padding:12px 14px;background:rgba(16,185,129,0.06);border:1px solid var(--wire-s);border-radius:8px;display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:12px;color:var(--ash);">Auto action</span>
                            <span style="font-size:12px;font-weight:600;color:var(--em);background:rgba(16,185,129,0.12);padding:3px 10px;border-radius:20px;">&#10003; APPROVED</span>
                        </div>
                    </div>
                </div>
                <p style="font-size:12px;color:var(--stone);text-align:center;">Every click scored in &lt;50ms. Zero manual work.</p>
            </div>
        </div>
    </div>
</section>


<section class="section py-28" aria-label="For affiliates">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="mb-16 reveal">
            <div class="eyebrow mb-5">For affiliates</div>
            <div class="flex flex-wrap items-end justify-between gap-6">
                <h2 class="display" style="font-size:clamp(26px,3.5vw,50px);">
                    Turn your audience<br>into <span style="color:var(--em);">daily Naira income</span>
                </h2>
                <a href="<?php echo e(route('front.for-affiliates')); ?>" class="btn-ghost-em" style="align-self:flex-end;">
                    Everything for affiliates &rarr;
                </a>
            </div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = [
                ['&#128202;','Real-time dashboard','See clicks, conversions, and earnings update live. No waiting until tomorrow to know how your campaign performed.'],
                ['&#127919;','Multi-offer tracking','Track performance across multiple offers with sub-ID support. Identify your best traffic sources with precision.'],
                ['&#128737;','Quality score protection','Your fraud score protects you too. Advertisers cannot reject commissions on clean traffic without evidence.'],
                ['&#128176;','Tier bonuses','Bronze &rarr; Silver &rarr; Gold &rarr; Platinum. Hit conversion milestones and earn automatic commission bonuses up to +15%.'],
                ['&#128101;','Sub-affiliate program','Refer other affiliates and earn a percentage of their commissions. Build passive income on top of active income.'],
                ['&#9889;','Fast Naira payouts','Minimum &#8358;5,000 withdrawal via Paystack or Flutterwave. Money in your Nigerian bank account within 48 hours.'],
                ['&#127968;','Earn on advertiser store sales','When a customer clicks your affiliate link and buys from a platform-managed advertiser store, you automatically earn your commission — no extra setup needed.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-lift reveal" style="background:var(--ink-2);border:1px solid var(--wire);border-radius:14px;padding:28px;transition-delay:<?php echo e(($i % 3) * 60); ?>ms;">
                <div style="font-size:28px;margin-bottom:16px;" aria-hidden="true"><?php echo $feat[0]; ?></div>
                <h3 style="font-family:var(--fd);font-size:16px;font-weight:700;color:var(--snow);margin-bottom:10px;"><?php echo $feat[1]; ?></h3>
                <p style="font-size:14px;color:var(--ash);line-height:1.75;font-weight:300;"><?php echo $feat[2]; ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="mt-16 reveal">
            <div style="background:var(--ink-2);border:1px solid var(--wire);border-radius:14px;overflow:hidden;">
                <div style="padding:20px 28px;border-bottom:1px solid var(--wire);">
                    <div style="font-family:var(--fd);font-size:14px;font-weight:700;color:var(--snow);">Affiliate tier system</div>
                    <div style="font-size:13px;color:var(--stone);margin-top:4px;">Higher tiers earn automatic bonuses on every conversion</div>
                </div>
                <div class="overflow-x-auto">
                    <table style="width:100%;border-collapse:collapse;" aria-label="Affiliate tier comparison">
                        <thead>
                            <tr style="border-bottom:1px solid var(--wire);">
                                <th scope="col" style="text-align:left;padding:14px 28px;font-size:11px;color:var(--stone);font-weight:500;letter-spacing:0.08em;">TIER</th>
                                <th scope="col" style="text-align:left;padding:14px 20px;font-size:11px;color:var(--stone);font-weight:500;letter-spacing:0.08em;">CONVERSIONS</th>
                                <th scope="col" style="text-align:left;padding:14px 20px;font-size:11px;color:var(--stone);font-weight:500;letter-spacing:0.08em;">BONUS</th>
                                <th scope="col" style="text-align:left;padding:14px 20px;font-size:11px;color:var(--stone);font-weight:500;letter-spacing:0.08em;">PERKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = [
                                ['Bronze','Starter','0%','Basic dashboard, standard offers','#b97832'],
                                ['Silver','50+ conversions','+5%','Priority support, advanced reports','#9ca3af'],
                                ['Gold','200+ conversions','+10%','Exclusive offers, dedicated account manager','#d4a017'],
                                ['Platinum','500+ conversions','+15%','Custom rates, weekly payouts, API access','#10b981'],
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom:1px solid var(--wire);">
                                <td style="padding:16px 28px;font-size:14px;font-weight:500;color:var(--snow);"><?php echo e($tier[0]); ?></td>
                                <td style="padding:16px 20px;font-size:13px;color:var(--ash);"><?php echo e($tier[1]); ?></td>
                                <td style="padding:16px 20px;">
                                    <span style="font-family:var(--fd);font-size:15px;font-weight:700;color:<?php echo e($tier[4]); ?>;"><?php echo e($tier[2]); ?></span>
                                </td>
                                <td style="padding:16px 20px;font-size:13px;color:var(--ash);"><?php echo e($tier[3]); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section py-28" style="background:var(--ink-2);border-top:1px solid var(--wire);" aria-label="For advertisers">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="grid lg:grid-cols-2 gap-20 items-center">
            <div class="reveal">
                <div class="eyebrow mb-6">For advertisers</div>
                <h2 class="display mb-6" style="font-size:clamp(26px,3.5vw,50px);">
                    Pay only for<br>
                    <span style="color:var(--em);">verified results</span>.<br>
                    Not fake clicks.
                </h2>
                <p style="font-size:16px;color:var(--ash);line-height:1.8;font-weight:300;margin-bottom:32px;">
                    Set your offer, choose your payout type - CPA, CPL, or RevShare - and let <?php echo e(config('app.name')); ?>'s fraud engine ensure every naira you spend drives real customers.
                </p>
                <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:36px;">
                    <?php $__currentLoopData = [
                        'Prepaid balance model - spend only what you load',
                        'Daily and monthly conversion caps to control budget',
                        'Real-time ROI dashboard with cost-per-acquisition tracking',
                        'Approve affiliates manually or open access to all',
                        'S2S postback tracking for any platform or CRM',
                        'Auto-pause when budget limit is reached',
                        'Platform-Managed store payments with automatic revenue splitting',
                        'Sales wallet & bank withdrawal for platform-mode store earnings',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:18px;height:18px;background:rgba(16,185,129,0.1);border:1px solid var(--wire-s);border-radius:50%;display:grid;place-items:center;flex-shrink:0;" aria-hidden="true">
                            <svg width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M1.5 4.5l2 2 4-4" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <span style="font-size:14px;color:var(--ash);"><?php echo e($point); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a href="<?php echo e(route('front.for-advertisers')); ?>" class="btn-em">
                    Post your first offer
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                        <path d="M3 7.5h9M8 4l3.5 3.5L8 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="reveal">
                <div class="mock-wrap">
                    <div class="mock-bar">
                        <div class="mock-dot" style="background:#ef4444;"></div>
                        <div class="mock-dot" style="background:#f59e0b;"></div>
                        <div class="mock-dot" style="background:#10b981;"></div>
                        <span style="font-size:11px;color:var(--stone);margin-left:10px;">Advertiser ROI Dashboard</span>
                    </div>
                    <div style="padding:20px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                            <div>
                                <div style="font-size:10px;color:var(--stone);letter-spacing:0.06em;margin-bottom:4px;">CAMPAIGN ROI THIS MONTH</div>
                                <div style="font-family:var(--fd);font-size:28px;font-weight:800;color:var(--em);">340%</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:10px;color:var(--stone);margin-bottom:4px;">Budget used</div>
                                <div style="font-family:var(--fd);font-size:18px;font-weight:700;color:var(--snow);">&#8358;450K</div>
                                <div style="font-size:10px;color:var(--em);">of &#8358;600K</div>
                            </div>
                        </div>
                        <div style="height:6px;background:var(--ink-3);border-radius:3px;margin-bottom:4px;" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            <div style="width:75%;height:100%;background:var(--em);border-radius:3px;"></div>
                        </div>
                        <div style="font-size:10px;color:var(--stone);margin-bottom:16px;">75% budget spent &middot; &#8358;150K remaining</div>
                        <hr class="sep mb-4">
                        <div style="font-size:10px;color:var(--stone);letter-spacing:0.06em;margin-bottom:10px;">TOP PERFORMING OFFERS</div>
                        <?php $__currentLoopData = [
                            ['Mobile app install','&#8358;800/install','1,240 installs','89%'],
                            ['Account signup','&#8358;1,200/lead','382 leads','94%'],
                            ['Product purchase','8% commission','67 sales','97%'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--wire);">
                            <div>
                                <div style="font-size:12px;color:var(--snow);font-weight:500;"><?php echo e($offer[0]); ?></div>
                                <div style="font-size:10px;color:var(--stone);"><?php echo $offer[1]; ?> &middot; <?php echo e($offer[2]); ?></div>
                            </div>
                            <div style="font-size:11px;color:var(--em);background:rgba(16,185,129,0.1);padding:3px 9px;border-radius:20px;"><?php echo e($offer[3]); ?> quality</div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div style="margin-top:14px;padding:12px 14px;background:rgba(16,185,129,0.04);border:1px solid var(--wire);border-radius:8px;display:flex;align-items:center;justify-content:space-between;">
                            <div>
                                <div style="font-size:11px;color:var(--snow);font-weight:500;">Fraud blocked this month</div>
                                <div style="font-size:10px;color:var(--stone);margin-top:2px;">2,847 fraudulent clicks stopped</div>
                            </div>
                            <div style="font-family:var(--fd);font-size:16px;font-weight:700;color:var(--em);">&#8358;68K<br><span style="font-size:9px;font-weight:400;color:var(--stone);">saved</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section py-28" aria-label="Offer categories">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="text-center mb-16 reveal">
            <div class="eyebrow mb-5" style="justify-content:center;">Offer categories</div>
            <h2 class="display mb-5" style="font-size:clamp(26px,3.5vw,48px);">
                Offers built for<br><span style="color:var(--em);">Nigerian audiences</span>
            </h2>
            <p style="font-size:16px;color:var(--ash);max-width:520px;margin:0 auto;font-weight:300;line-height:1.75;">
                Every offer category is curated for relevance to Nigerian consumers and affiliates.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            <?php $__currentLoopData = [
                ['&#127974;','Fintech &amp; Banking','&#8358;800—&#8358;5,000 / lead','Hot'],
                ['&#128717;','E-commerce','5—12% commission',''],
                ['&#128218;','Digital Products','&#8358;500—&#8358;3,000 / sale','New'],
                ['&#128241;','Mobile Apps','&#8358;200—&#8358;800 / install','Hot'],
                ['&#127973;','Health &amp; Wellness','&#8358;400—&#8358;2,000 / lead',''],
                ['&#127891;','Online Education','&#8358;600—&#8358;4,000 / enrol','New'],
                ['&#127968;','Real Estate','&#8358;2,000—&#8358;15,000 / lead',''],
                ['&#9889;','Crypto &amp; Forex','&#8358;1,000—&#8358;8,000 / lead',''],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-lift reveal" style="background:var(--ink-2);border:1px solid var(--wire);border-radius:12px;padding:22px;position:relative;">
                <?php if($cat[3] === 'Hot'): ?>
                    <span style="position:absolute;top:14px;right:14px;font-size:9px;font-weight:600;letter-spacing:0.08em;color:#ef4444;background:rgba(239,68,68,0.1);padding:2px 8px;border-radius:10px;">HOT</span>
                <?php elseif($cat[3] === 'New'): ?>
                    <span style="position:absolute;top:14px;right:14px;font-size:9px;font-weight:600;letter-spacing:0.08em;color:var(--em);background:rgba(16,185,129,0.1);padding:2px 8px;border-radius:10px;">NEW</span>
                <?php endif; ?>
                <div style="font-size:28px;margin-bottom:12px;" aria-hidden="true"><?php echo $cat[0]; ?></div>
                <div style="font-family:var(--fd);font-size:14px;font-weight:700;color:var(--snow);margin-bottom:6px;"><?php echo $cat[1]; ?></div>
                <div style="font-size:12px;color:var(--em);"><?php echo $cat[2]; ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section py-28" style="background:var(--ink-2);border-top:1px solid var(--wire);" aria-label="Testimonials">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="mb-14 reveal">
            <div class="eyebrow mb-5">What they say</div>
            <h2 class="display" style="font-size:clamp(26px,3.5vw,48px);">
                Nigerian marketers<br>already <span style="color:var(--em);">winning</span>
            </h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <?php $__currentLoopData = [
                ['quote'=>'I was spending &#8358;200K a month on affiliates and couldn\'t tell who was real. <?php echo e(config(\'app.name\')); ?> showed me 40% of my traffic was fraudulent. That money now goes to affiliates that actually convert.','name'=>'Tunde Okafor','role'=>'Founder, FinEdge Nigeria','type'=>'advertiser','stat'=>'&#8358;80K saved / mo'],
                ['quote'=>'I promoted CPA offers on three different networks before <?php echo e(config(\'app.name\')); ?>. Nothing else pays weekly to Paystack. I cleared &#8358;340,000 last month alone from fintech offers.','name'=>'Chioma Adeleke','role'=>'Digital creator &middot; 45K YouTube subs','type'=>'affiliate','stat'=>'&#8358;340K last month'],
                ['quote'=>'The real-time tracking changed everything. I can see exactly which traffic source converts for which offer. My conversion rate went from 2.1% to 6.8% once I optimised properly.','name'=>'Emeka Nwosu','role'=>'Performance marketer &middot; Lagos','type'=>'affiliate','stat'=>'6.8% CR rate'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-lift reveal" style="background:var(--ink-3);border:1px solid var(--wire);border-radius:14px;padding:28px;transition-delay:<?php echo e($i * 80); ?>ms;">
                <div class="quote-mark" style="margin-bottom:16px;" aria-hidden="true">"</div>
                <blockquote>
                    <p style="font-size:14px;color:var(--ash);line-height:1.85;font-weight:300;margin-bottom:24px;font-style:italic;"><?php echo $test['quote']; ?></p>
                </blockquote>
                <hr class="sep mb-4">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:14px;font-weight:500;color:var(--snow);"><?php echo e($test['name']); ?></div>
                        <div style="font-size:12px;color:var(--stone);margin-top:2px;"><?php echo $test['role']; ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-family:var(--fd);font-size:14px;font-weight:700;color:var(--em);"><?php echo $test['stat']; ?></div>
                        <div style="font-size:10px;color:var(--stone);margin-top:2px;text-transform:uppercase;letter-spacing:0.06em;"><?php echo e($test['type']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section py-24" aria-label="Platform trust">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php $__currentLoopData = [
                ['&#128272;','Bank-grade security','Role-based access control, CSRF protection, SQL injection prevention, and XSS filtering on every request.'],
                ['&#9889;','10,000+ clicks/hour','Async queue processing with Redis caching. Your campaign traffic never slows the platform down.'],
                ['&#128225;','Paystack + Flutterwave','Direct integration with Nigeria\'s two leading payment processors. Naira payouts with no third-party wallets.'],
                ['&#127758;','Optimised for Nigeria','Infrastructure tuned for West African latency. Sub-100ms click tracking for Nigerian mobile traffic.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-lift reveal" style="background:var(--ink-2);border:1px solid var(--wire);border-radius:14px;padding:24px;">
                <div style="font-size:26px;margin-bottom:14px;" aria-hidden="true"><?php echo $trust[0]; ?></div>
                <div style="font-family:var(--fd);font-size:15px;font-weight:700;color:var(--snow);margin-bottom:8px;"><?php echo e($trust[1]); ?></div>
                <p style="font-size:13px;color:var(--ash);line-height:1.75;font-weight:300;"><?php echo e($trust[2]); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="section py-28" style="background:var(--ink-2);border-top:1px solid var(--wire);" aria-label="Call to action">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div style="background:var(--ink-3);border:1px solid var(--wire-s);border-radius:20px;padding:clamp(40px,6vw,80px);text-align:center;" class="reveal">
            <div class="eyebrow mb-8" style="justify-content:center;">Start today - it's free</div>
            <h2 class="display mb-6" style="font-size:clamp(28px,5vw,64px);">
                Nigeria's performance<br>
                marketing platform<br>
                <span style="color:var(--em);">is ready for you</span>
            </h2>
            <p style="font-size:17px;color:var(--ash);max-width:540px;margin:0 auto 40px;font-weight:300;line-height:1.75;">
                Join thousands of Nigerian affiliates and advertisers already earning and growing on <?php echo e(config('app.name')); ?>.
            </p>
            <div class="flex flex-wrap gap-4 justify-center mb-14">
                <a href="<?php echo e(route('register')); ?>?type=affiliate" class="btn-em" style="padding:16px 36px;font-size:15px;">
                    Join as affiliate - it's free
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                        <path d="M3 7.5h9M8 4l3.5 3.5L8 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="<?php echo e(route('register')); ?>?type=advertiser" class="btn-ghost-em" style="padding:15px 36px;font-size:15px;">
                    Post an offer as advertiser
                </a>
            </div>
            <div class="flex flex-wrap justify-center gap-8">
                <?php $__currentLoopData = ['No setup fees','Same-day approval','Naira payouts','Cancel anytime']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;gap:7px;">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7l3 3 7-6" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span style="font-size:13px;color:var(--stone);"><?php echo e($trust); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        var reveals = document.querySelectorAll('.reveal');
        if (!reveals.length) return;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('in-view');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (r) { io.observe(r); });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\dealsintel\resources\views/front/home.blade.php ENDPATH**/ ?>