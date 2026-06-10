<?php

namespace Database\Seeders;

use App\Models\LmsCourse;
use App\Models\LmsLesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LmsCoursesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first() ?? User::first();
        $adminId = $admin?->id ?? 1;

        // ─────────────────────────────────────────────────────────────────
        // COURSE 1: Affiliate Marketing Fundamentals
        // ─────────────────────────────────────────────────────────────────
        $course1 = LmsCourse::firstOrCreate(
            ['slug' => 'affiliate-marketing-fundamentals'],
            [
                'title'           => 'Affiliate Marketing Fundamentals',
                'description'     => 'Everything you need to know to launch your affiliate career on DealsintelNG — from understanding how affiliate marketing works, picking the right offers, generating your first link, and getting paid. Perfect if you\'re brand new or want to fill in the gaps.',
                'what_you_learn'  => [
                    'How affiliate marketing works and why it\'s the most accessible online income model',
                    'The difference between CPA, CPL, and revenue-share offers — and which to prioritise',
                    'How to find, evaluate, and pick high-converting offers on DealsintelNG',
                    'Generate and manage your affiliate links like a pro',
                    'Drive targeted traffic without spending money on ads',
                    'Track your clicks, conversions, and commissions in your dashboard',
                    'Avoid the most common rookie mistakes that kill earnings',
                    'Set up your payment details and withdraw your commissions',
                ],
                'category'         => 'Affiliate Marketing',
                'audience'         => 'affiliate',
                'level'            => 'beginner',
                'duration_minutes' => 0,
                'is_published'     => true,
                'is_featured'      => true,
                'order'            => 1,
                'created_by'       => $adminId,
            ]
        );

        $lessons1 = [
            [
                'title'           => 'What Is Affiliate Marketing and How Does It Work?',
                'slug'            => 'what-is-affiliate-marketing',
                'content'         => "## Welcome to the Learning Center!\n\nAffiliate marketing is a performance-based model where you (the affiliate) earn a commission every time someone takes a specific action — like making a purchase, signing up, or filling a form — through your unique link.\n\n### The Three Players\n- **Advertiser** – the brand or business with a product/service to sell.\n- **Affiliate (you)** – you promote the offer and drive customers.\n- **Platform (DealsintelNG)** – tracks everything and handles payouts.\n\n### How a Conversion Happens\n1. You grab your unique affiliate link from your dashboard.\n2. You share it — via social, WhatsApp, blog, email, etc.\n3. Someone clicks your link and completes the required action.\n4. The platform records the conversion and credits your account.\n5. Once approved, the commission moves to your balance.\n6. You request a payout and get paid.\n\n### Why Affiliate Marketing Works in Nigeria\n- Zero upfront cost — no product to create or stock\n- You earn even while you sleep\n- Works with any audience size\n- Commissions can be 5% to 50%+ depending on the offer\n\nIn the next lesson, we'll break down the different commission types so you know what you're signing up for.",
                'duration_minutes' => 8,
                'order'            => 1,
                'is_published'     => true,
                'is_free_preview'  => true,
            ],
            [
                'title'           => 'Commission Types: CPA, CPL, and Revenue Share Explained',
                'slug'            => 'commission-types-cpa-cpl-revenue-share',
                'content'         => "## Know What You're Earning Before You Promote\n\nNot all affiliate offers pay the same way. Understanding the model helps you pick offers that match your audience and effort level.\n\n### CPA — Cost Per Action\nYou earn a fixed amount each time someone completes a specific action (usually a purchase or signup).\n\n**Example:** ₦2,000 per confirmed order from your link.\n\n**Best for:** Audiences ready to buy. Higher per-conversion value.\n\n### CPL — Cost Per Lead\nYou earn a smaller fixed amount for each qualified lead — someone who fills a form, registers, or books a demo.\n\n**Example:** ₦300 per valid email signup.\n\n**Best for:** Large audiences, even if not ready to buy yet.\n\n### Revenue Share\nYou earn a percentage of every sale — forever, or for a set period.\n\n**Example:** 15% of each sale, every month a customer keeps paying.\n\n**Best for:** Subscription products. Builds recurring income.\n\n### Which Should You Choose?\n| Type | Payout | Risk | Best For |\n|------|--------|------|----------|\n| CPA | High (fixed) | Medium | Buyers |\n| CPL | Low (fixed) | Low | Any audience |\n| RevShare | Variable | Low short-term | Loyal audiences |\n\nStart with CPL or CPA offers that match your niche. We'll cover offer selection in Lesson 4.",
                'duration_minutes' => 7,
                'order'            => 2,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Navigating Your Affiliate Dashboard',
                'slug'            => 'navigating-your-affiliate-dashboard',
                'content'         => "## Your Control Room\n\nOnce approved as an affiliate on DealsintelNG, your dashboard is where everything happens. Let's walk through each section.\n\n### Key Sections\n\n**Dashboard Overview**\nShows your total clicks, conversions, pending commissions, and paid earnings at a glance.\n\n**Offers**\nBrowse all available advertiser offers. Each listing shows the commission, payout model, category, and whether you need to request access.\n\n**Links**\nAll your generated affiliate links in one place. You can create new links, see click counts, and toggle links on/off.\n\n**Reports**\nFilter by date range, offer, and status to see exactly where your commissions are coming from.\n\n**Payouts**\nTrack approved commissions, request withdrawals, and see your payment history.\n\n### First Things to Do\n1. Complete your profile and set up payment details (Settings → Profile).\n2. Browse available offers and request access to those that match your audience.\n3. Generate your first link and start sharing.\n\nIn the next lesson, we'll cover how to evaluate an offer before you promote it.",
                'duration_minutes' => 6,
                'order'            => 3,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'How to Pick Offers That Actually Convert',
                'slug'            => 'how-to-pick-winning-offers',
                'content'         => "## Not All Offers Are Created Equal\n\nChoosing the wrong offer wastes your time and your audience's trust. Here's how to evaluate before you promote.\n\n### Checklist: A Good Offer Has…\n- ✅ A clear, believable value proposition for the customer\n- ✅ A landing page that loads fast and looks professional\n- ✅ A realistic commission that makes promotion worthwhile\n- ✅ A target audience that overlaps with yours\n- ✅ A product or service Nigerians actually need\n\n### Red Flags to Avoid\n- ❌ Vague landing pages with no pricing or details\n- ❌ Extremely high commissions with no cap — often means low conversion\n- ❌ Products with bad reviews or no social proof\n- ❌ Offers requiring upfront payment from the customer with zero trust signals\n\n### The Research Process (5 minutes per offer)\n1. Click the offer's preview URL — would *you* buy this?\n2. Check the advertiser name — are they credible?\n3. Read the offer description carefully for the exact conversion criteria\n4. Calculate your minimum viable traffic: if conversion rate is 1% and payout is ₦1,500, you need 70 conversions from 7,000 clicks to earn ₦105,000.\n\n### Match Offer to Audience\n| Your Audience | Good Offer Types |\n|--------------|------------------|\n| Students | Free tools, online courses, job boards |\n| Entrepreneurs | SaaS, business services, loans |\n| Shoppers | E-commerce, fashion, food delivery |\n| General | Fintech, telco, utilities |\n\nOnce you've found a good match, Lesson 5 covers how to create and manage your links.",
                'duration_minutes' => 9,
                'order'            => 4,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Generating, Sharing, and Managing Your Affiliate Links',
                'slug'            => 'generating-and-managing-affiliate-links',
                'content'         => "## Your Link Is Your Money-Making Asset\n\nEvery affiliate link on DealsintelNG is unique to you. When someone clicks it and converts, the commission is credited to your account automatically.\n\n### How to Generate a Link\n1. Go to **Offers** in your dashboard.\n2. Find an offer you've been approved for.\n3. Click **Generate Link**.\n4. Your unique link appears under **My Links**.\n\n### Best Practices for Sharing Links\n\n**WhatsApp (most effective in Nigeria)**\n- Don't just drop a raw link. Write a short message explaining the benefit.\n- Example: *\"I found this free tool that helped me track my business sales — completely free to try: [your link]\"*\n- Use WhatsApp Status for passive reach.\n\n**Social Media**\n- Instagram bio (one high-value link only)\n- Twitter/X threads with value first, link at end\n- Facebook groups related to the niche\n\n**Content**\n- WhatsApp Broadcast messages\n- Email newsletters\n- YouTube video descriptions\n- Blog posts or Medium articles\n\n### Link Management Tips\n- Use descriptive names when saving links so you know which campaign they belong to.\n- Pause underperforming links rather than deleting them — historical data is valuable.\n- Never share the same link on irrelevant channels; low-quality traffic hurts your account standing.\n\n### Tracking Your Links\nEach link shows **Clicks** and **Conversions** in real time. Check Reports weekly to spot your best-performing channels.",
                'duration_minutes' => 8,
                'order'            => 5,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Free Traffic Strategies That Work in Nigeria',
                'slug'            => 'free-traffic-strategies-nigeria',
                'content'         => "## You Don't Need to Pay for Ads to Earn Commissions\n\nThe best affiliates in Nigeria build sustainable income using free channels. Here are the most effective ones.\n\n### WhatsApp Marketing\nNigeria has one of the highest WhatsApp penetration rates in Africa. Use it.\n\n- **Broadcast Lists**: Build lists by niche (e.g., students, side-hustlers, traders). Send value-first messages 3–4x a week, with your link as a natural recommendation.\n- **WhatsApp Status**: Post testimonials, product demos, or tips daily. Include your link in the caption.\n- **WhatsApp Groups**: Join niche groups. Contribute genuinely. Share offers only when directly relevant.\n\n### Facebook Groups\nSearch for groups around your offer's niche (entrepreneurship, fashion, health, etc.). Read group rules first. Add value in comments before posting offers.\n\n### Twitter/X Threads\nWrite 5–8 tweet threads giving actionable tips. End with: *\"If you want [result], this tool/offer made it easy for me: [link]\"*.\n\n### TikTok (fastest growth)\nShort, honest review videos or \"before and after\" content. Add your link in bio.\n\n### Referral Networks\nYour #1 free traffic source: people you already know. Tell 10 people about the offer this week.\n\n### The Consistency Formula\n- Post content: 5x per week\n- Engage with replies/comments: daily\n- Track results: weekly\n- Iterate: monthly\n\nFree traffic takes 30–60 days to build momentum. Stay consistent.",
                'duration_minutes' => 10,
                'order'            => 6,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Understanding Your Reports and Tracking Commissions',
                'slug'            => 'understanding-reports-and-tracking-commissions',
                'content'         => "## Data Is Your Best Friend\n\nMost affiliates share links and hope for the best. Top affiliates read their data and optimize. Here's how.\n\n### The Reports Dashboard\nGo to **Affiliate → Reports** to see:\n- **Clicks** — how many people visited your link\n- **Conversions** — how many completed the required action\n- **Conversion Rate** — conversions ÷ clicks × 100\n- **Earnings** — pending vs. approved commissions\n\n### Key Metrics to Monitor\n\n**Conversion Rate (CR)**\nA CR below 0.5% usually means: wrong audience, weak offer landing page, or misleading promotion.\nA CR above 3% means you've found a winning combination — double down.\n\n**Click Volume**\nLow clicks = traffic problem. High clicks but low conversions = offer/audience mismatch.\n\n**Earnings per Click (EPC)**\nEPC = Total Earnings ÷ Total Clicks.\nIf your EPC is ₦50, you earn ₦50 for every click — whether or not they convert. This helps you decide if a traffic source is worth continuing.\n\n### Weekly Review Habit (10 minutes)\n1. Which offer got the most clicks?\n2. Which channel drove the most conversions?\n3. What's my EPC by offer?\n4. What should I stop, start, or do more of?\n\nConsistency in reviewing data separates earners from wishful thinkers.",
                'duration_minutes' => 7,
                'order'            => 7,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Getting Paid: Payouts, Timelines, and Best Practices',
                'slug'            => 'getting-paid-payouts-and-timelines',
                'content'         => "## Your Commissions, Your Money\n\nHere's everything you need to know about turning your earnings into cash in your bank account.\n\n### Before You Can Get Paid\n1. **Set up your payment details** — Go to your profile settings and add your bank account (account name, number, bank).\n2. **Wait for commission approval** — Conversions are reviewed by the advertiser before becoming available for withdrawal. This typically takes 7–30 days depending on the offer.\n3. **Meet the minimum payout threshold** — Check each offer's minimum withdrawal amount.\n\n### How to Request a Payout\n1. Go to **Affiliate → Payouts → Request Payout**.\n2. Enter the amount (must meet minimum threshold).\n3. Submit. The admin reviews and processes within 1–5 business days.\n\n### Commission Status Explained\n| Status | Meaning |\n|--------|----------|\n| Pending | Conversion recorded, awaiting advertiser approval |\n| Approved | Commission confirmed, available for withdrawal |\n| Paid | Withdrawn and sent to your bank |\n| Rejected | Conversion was fraudulent or reversed |\n\n### Best Practices\n- Always use your real bank details — mismatches cause payment delays.\n- Don't artificially inflate clicks or use VPNs to fake conversions — accounts are permanently banned.\n- Request payouts weekly or bi-weekly to maintain healthy cash flow.\n\n### Congratulations!\nYou've completed the Affiliate Marketing Fundamentals course. You now have everything you need to start earning. Go generate your first link and start sharing!",
                'duration_minutes' => 7,
                'order'            => 8,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
        ];

        foreach ($lessons1 as $lesson) {
            LmsLesson::firstOrCreate(
                ['lms_course_id' => $course1->id, 'slug' => $lesson['slug']],
                array_merge($lesson, ['lms_course_id' => $course1->id])
            );
        }

        $course1->recalculateDuration();

        // ─────────────────────────────────────────────────────────────────
        // COURSE 2: WhatsApp Marketing for Affiliate Income
        // ─────────────────────────────────────────────────────────────────
        $course2 = LmsCourse::firstOrCreate(
            ['slug' => 'whatsapp-marketing-for-affiliate-income'],
            [
                'title'           => 'WhatsApp Marketing for Affiliate Income',
                'description'     => 'WhatsApp is the #1 marketing channel in Nigeria — and most affiliates are using it wrong. This course teaches you how to build an engaged WhatsApp audience, craft messages people actually read, and turn conversations into consistent affiliate commissions without spamming anyone.',
                'what_you_learn'  => [
                    'Why WhatsApp beats every other free traffic channel for Nigerian affiliates',
                    'How to set up your WhatsApp Business profile for trust and credibility',
                    'Build a targeted broadcast list from scratch — even with zero followers',
                    'Write persuasive messages that get clicks without feeling spammy',
                    'Use WhatsApp Status as a passive income engine',
                    'Handle objections and close conversions in chat',
                    'Structure your week so WhatsApp marketing takes under 30 minutes per day',
                    'Scale from ₦10k/month to ₦100k+/month using only WhatsApp',
                ],
                'category'         => 'Traffic & Promotion',
                'audience'         => 'affiliate',
                'level'            => 'beginner',
                'duration_minutes' => 0,
                'is_published'     => true,
                'is_featured'      => true,
                'order'            => 2,
                'created_by'       => $adminId,
            ]
        );

        $lessons2 = [
            [
                'title'           => 'Why WhatsApp Is the #1 Affiliate Channel in Nigeria',
                'slug'            => 'why-whatsapp-is-number-one',
                'content'         => "## The Channel Everyone Has But Few Use Well\n\nOver 50 million Nigerians use WhatsApp daily. It's the primary communication app for families, businesses, and social groups. That makes it the most powerful — and most underused — affiliate marketing channel in the country.\n\n### WhatsApp vs. Other Channels\n| Channel | Open Rate | Cost | Trust Level |\n|---------|-----------|------|-------------|\n| Email | 15–25% | Low | Medium |\n| Instagram | 2–5% | Low-Medium | Low |\n| WhatsApp | **70–90%** | Free | **High** |\n\nPeople open WhatsApp messages. They don't open Instagram DMs from strangers.\n\n### Why the Trust Factor Matters\nAffiliate marketing lives and dies by trust. When you share a link on WhatsApp, it comes from *you* — someone people already have in their contacts. That implied endorsement is worth more than any paid ad.\n\n### The Opportunity\nMost affiliates drop links in groups and get ignored or banned. This course teaches a different approach — one based on value, relationships, and strategic messaging.\n\nBy the end of this course, you'll have a WhatsApp marketing system running in under 30 minutes per day that consistently generates affiliate income.",
                'duration_minutes' => 6,
                'order'            => 1,
                'is_published'     => true,
                'is_free_preview'  => true,
            ],
            [
                'title'           => 'Setting Up WhatsApp Business for Credibility',
                'slug'            => 'setting-up-whatsapp-business',
                'content'         => "## Your WhatsApp Profile Is Your Storefront\n\nBefore sending a single message, optimise your profile. People decide in seconds whether to trust you.\n\n### WhatsApp Business vs. Regular WhatsApp\nDownload **WhatsApp Business** (free on Android and iOS). It adds:\n- A business profile with description, website, and category\n- Quick replies for common questions\n- Away messages\n- Labels to organise contacts\n- Broadcast lists (already in regular WhatsApp, easier to manage here)\n\n### Profile Setup Checklist\n- ✅ **Profile photo** — a clear, professional photo of your face. Not a logo. Not a cartoon. People trust people.\n- ✅ **Name** — your real name or a consistent brand name.\n- ✅ **Business description** — one line about what value you provide. E.g., *\"I share verified deals, tools, and resources for Nigerian entrepreneurs.\"*\n- ✅ **Category** — choose something relevant (Shopping, Education, etc.)\n- ✅ **Website** — your DealsintelNG affiliate referral link or a Linktree with your top offers.\n\n### Status Tips\n- Update your status daily (covered in detail in Lesson 4).\n- Your profile picture and status are the two things contacts see before deciding whether to read your messages.\n\n### Set Up Quick Replies\nSave canned responses for common questions:\n- *\"How do I sign up?\"* → send your registration link\n- *\"Is this legit?\"* → send a testimonial or screenshots\n- *\"How much does it cost?\"* → send the offer details\n\nThis saves time and ensures you never miss a conversion.",
                'duration_minutes' => 7,
                'order'            => 2,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Building Your Broadcast List From Zero',
                'slug'            => 'building-your-broadcast-list',
                'content'         => "## Your List Is Your Business\n\nA WhatsApp Broadcast lets you send one message to up to 256 contacts at once. Each recipient sees it as a private message from you — not a group chat. This is powerful.\n\n### The Golden Rule\nOnly people who have saved your number can receive your broadcasts. So your job is to get people to save your number.\n\n### How to Build a Targeted List\n\n**Method 1: Your Existing Contacts**\nStart here. Go through your phonebook. Identify contacts who match the audience for your chosen offers (entrepreneurs, students, working professionals, etc.). You already have 50–200 qualified contacts.\n\n**Method 2: Facebook Groups**\n1. Join 5–10 groups related to your niche.\n2. Post valuable content (tips, not offers).\n3. When people react or comment, message them: *\"Hey, I share exclusive deals and resources for [niche] people on WhatsApp. Want me to add you?\"*\n4. When they say yes, ask them to save your number first.\n\n**Method 3: WhatsApp Groups**\nJoin groups in your niche. Be genuinely helpful. Don't spam offers. DM interested members to join your broadcast.\n\n**Method 4: Create a WhatsApp Link**\nGo to wa.me/[yourphonenumber] — share this link everywhere (Instagram bio, email signature, Twitter/X profile). Anyone who clicks is opted in.\n\n### List Hygiene\n- Remove contacts who never open your messages (use read receipts)\n- Segment by interest once you have 100+ contacts\n- Aim for 200 quality contacts before expecting consistent commissions\n\n### Weekly Growth Target\n10 new contacts per week = 520 by the end of a year. That's a serious income asset.",
                'duration_minutes' => 9,
                'order'            => 3,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Crafting Messages That Get Clicks (Without Spamming)',
                'slug'            => 'crafting-messages-that-get-clicks',
                'content'         => "## The Difference Between Spam and a Welcome Message\n\nThe reason most affiliates get blocked on WhatsApp: they lead with the offer. The reason top affiliates earn consistently: they lead with value.\n\n### The Value-First Framework\nEvery message should follow this structure:\n1. **Hook** — an interesting fact, question, or statement relevant to your audience\n2. **Value** — a tip, insight, or resource that helps them\n3. **Bridge** — a natural transition to the offer\n4. **CTA** — a single, clear call to action\n\n### Example: Affiliate Offer (Fintech App)\n❌ **Bad:** \"Download this app and earn money! [link]\"\n\n✅ **Good:**\n*\"Most people don't know they're leaving money on the table with their savings.\n\nI've been using [App Name] for 3 months and my savings earn 18% interest automatically — no stress.\n\nIf you want the same, download it through my link and get a ₦500 bonus when you fund your account for the first time: [link]\n\nLet me know if you have questions 🙂\"*\n\n### Message Frequency\n- **3–4 messages per week** is the sweet spot.\n- More than 5/week = people start ignoring you.\n- Less than 1/week = people forget who you are.\n\n### Content Mix (Weekly)\n| Day | Content Type |\n|-----|--------------|\n| Mon | Tip or insight (no offer) |\n| Wed | Offer recommendation with personal story |\n| Fri | Testimonial or result (yours or someone else's) |\n| Sat | Reminder or limited-time nudge |\n\n### Tone Tips\n- Write how you talk. Be natural, not corporate.\n- Use one emoji per message (2 max).\n- Keep messages under 150 words — people skim.\n- Always end with a question or CTA, not just a link.",
                'duration_minutes' => 10,
                'order'            => 4,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Using WhatsApp Status as a Passive Income Engine',
                'slug'            => 'whatsapp-status-passive-income',
                'content'         => "## 200 Views Per Status = Money While You Sleep\n\nWhatsApp Status (like Instagram Stories) disappears after 24 hours. That scarcity creates urgency. And because it's passive — you post once, people come to you.\n\n### Who Sees Your Status?\nAnyone in your contacts who has saved your number. The more contacts you have (from your broadcast list building), the more views you get.\n\n### What to Post on Status\n\n**Daily rotation:**\n- **Monday:** A tip or fact (text card — use Canva to make it look good)\n- **Tuesday:** Your personal activity/result (\"Just hit ₦15,000 this week from [offer] 🎯\")\n- **Wednesday:** An offer or deal recommendation\n- **Thursday:** A question or poll (\"Have you tried [product]? Yes/No\")\n- **Friday:** A testimonial — screenshot with name blurred\n- **Weekend:** Behind-the-scenes or personal (builds trust)\n\n### The Click Magnet Formula\nFor offer statuses:\n1. Show the benefit visually (before/after, earnings screenshot, product image)\n2. Add a short text: *\"This is how I earned ₦8,000 last Tuesday 👆\"*\n3. End with: *\"Message me for the link\"* — DO NOT put the link directly in status. This filters serious people and starts a conversation.\n\n### Turning Viewers into Buyers\nWhen someone messages asking for the link:\n1. Respond quickly (within 2 hours)\n2. Ask one qualifying question: *\"Have you heard of [product] before?\"*\n3. Based on their answer, give context or just the link\n4. Follow up once 24 hours later if they haven't clicked\n\n### Tools for Better Status Content\n- **Canva** (free) — design professional-looking text cards\n- **CapCut** (free) — short product demo videos\n- **Screenshot** — your earnings dashboard is your best proof of concept",
                'duration_minutes' => 8,
                'order'            => 5,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Handling Objections and Closing Conversions in Chat',
                'slug'            => 'handling-objections-in-chat',
                'content'         => "## Every Objection Is a Question in Disguise\n\nWhen someone objects, they're not saying no — they're asking for more information or reassurance. Your job is to provide it calmly and honestly.\n\n### The 5 Most Common Objections\n\n**1. \"Is this legit / is this a scam?\"**\nResponse: *\"Totally fair question. [Product name] has been around since [year] and has [X] users in Nigeria. I've personally earned/used it. Here's a quick overview: [link to offer landing page or review]\"*\n\n**2. \"I don't have money right now\"**\nResponse: *\"No problem! [If it's a free offer]: Actually, it's completely free to sign up — no payment needed. [If paid]: Totally fine, I'll remind you when you're ready. What's a good time?\"*\n\n**3. \"I'm not interested\"**\nResponse: *\"No worries at all 🙂 I'll keep sharing other things that might be relevant. What kind of deals would actually be useful for you?\"*\n(This turns a rejection into market research.)\n\n**4. \"I tried something similar before and it didn't work\"**\nResponse: *\"Sorry to hear that. This one is different because [specific differentiator]. Happy to share more details if you're curious — no pressure though.\"*\n\n**5. \"Send me more information\"**\nResponse: Send a voice note (60 seconds max). Voice notes feel personal and convert better than long texts.\n\n### The Follow-Up Rule\n- Follow up once, 24–48 hours later\n- After a second no, move on\n- Never be pushy — your reputation is worth more than one commission\n\n### Using Voice Notes\nVoice notes get 3x more responses than text in Nigerian WhatsApp culture. Use them for:\n- Explaining a complex offer\n- Personalising your pitch\n- Following up on conversations",
                'duration_minutes' => 8,
                'order'            => 6,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
            [
                'title'           => 'Your 30-Minute Daily WhatsApp Marketing System',
                'slug'            => 'daily-whatsapp-marketing-system',
                'content'         => "## Build a System, Not a Hustle\n\nThe affiliates who earn consistently aren't the ones who spend 8 hours on their phone. They're the ones who follow a daily system.\n\n### The 30-Minute Daily Routine\n\n**Morning (15 minutes)**\n- Check reports: Any new conversions overnight? (2 min)\n- Reply to all messages from the previous day (8 min)\n- Post your WhatsApp Status for the day (5 min)\n\n**Afternoon (10 minutes)**\n- Add 2 new contacts to your list (from group interactions or referrals)\n- Send today's broadcast message if it's a broadcast day (3x/week)\n\n**Evening (5 minutes)**\n- Engage with any replies from your broadcast or status\n- Note what worked today (1 line in your phone notes)\n\n### Weekly Schedule Template\n| Day | Broadcast? | Status Theme | List Building |\n|-----|-----------|--------------|---------------|\n| Mon | ✅ Value tip | Tip | Facebook group |\n| Tue | ❌ | Personal result | — |\n| Wed | ✅ Offer | Offer | WhatsApp group |\n| Thu | ❌ | Question | — |\n| Fri | ✅ Testimonial | Social proof | Instagram DM |\n| Sat | ❌ | Personal | — |\n| Sun | ❌ | Rest or recap | — |\n\n### Monthly Review (1 hour)\n- Which offers got the most clicks?\n- Which message style got the best response?\n- How many new contacts did I add?\n- What's my total earned this month vs. last?\n\n### Scaling Beyond ₦100,000/month\nOnce your system is running smoothly:\n1. Add a second high-performing offer\n2. Double your list-building efforts\n3. Start a niche-specific WhatsApp group where you're the expert\n4. Consider building a simple website or blog to send pre-sold traffic to your links\n\n### You're Done! 🎉\nCongratulations on completing **WhatsApp Marketing for Affiliate Income**. You now have a complete, repeatable system for earning affiliate commissions using nothing but your phone and WhatsApp. Go implement lesson by lesson — one step is enough to start.",
                'duration_minutes' => 9,
                'order'            => 7,
                'is_published'     => true,
                'is_free_preview'  => false,
            ],
        ];

        foreach ($lessons2 as $lesson) {
            LmsLesson::firstOrCreate(
                ['lms_course_id' => $course2->id, 'slug' => $lesson['slug']],
                array_merge($lesson, ['lms_course_id' => $course2->id])
            );
        }

        $course2->recalculateDuration();

        $this->command->info('✅ Created course: ' . $course1->title . ' (' . $course1->lessons()->count() . ' lessons, ' . $course1->duration_minutes . 'min)');
        $this->command->info('✅ Created course: ' . $course2->title . ' (' . $course2->lessons()->count() . ' lessons, ' . $course2->duration_minutes . 'min)');
    }
}
