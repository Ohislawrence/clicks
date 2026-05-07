<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user to be the author
        $author = User::role('admin')->first();

        if (!$author) {
            $this->command->warn('No admin user found. Please create an admin user first.');
            return;
        }

        // Create categories
        $categories = [
            [
                'name' => 'Affiliate Marketing',
                'slug' => 'affiliate-marketing',
                'description' => 'Tips, strategies, and insights for successful affiliate marketing',
                'is_active' => true,
            ],
            [
                'name' => 'CPA Offers',
                'slug' => 'cpa-offers',
                'description' => 'Learn about the best CPA offers and how to promote them',
                'is_active' => true,
            ],
            [
                'name' => 'Traffic Generation',
                'slug' => 'traffic-generation',
                'description' => 'Strategies to drive quality traffic to your offers',
                'is_active' => true,
            ],
            [
                'name' => 'Conversion Optimization',
                'slug' => 'conversion-optimization',
                'description' => 'Improve your conversion rates and maximize revenue',
                'is_active' => true,
            ],
            [
                'name' => 'Industry News',
                'slug' => 'industry-news',
                'description' => 'Latest news and updates from the performance marketing industry',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            BlogCategory::create($categoryData);
        }

        $this->command->info('Blog categories created successfully.');

        // Create sample blog posts
        $affiliateCategory = BlogCategory::where('slug', 'affiliate-marketing')->first();
        $cpaCategory = BlogCategory::where('slug', 'cpa-offers')->first();
        $trafficCategory = BlogCategory::where('slug', 'traffic-generation')->first();
        $conversionCategory = BlogCategory::where('slug', 'conversion-optimization')->first();

        $posts = [
            [
                'category_id' => $affiliateCategory->id,
                'user_id' => $author->id,
                'title' => '10 Proven Strategies to Boost Your Affiliate Revenue in 2026',
                'slug' => '10-proven-strategies-boost-affiliate-revenue-2026',
                'excerpt' => 'Discover the top strategies that successful affiliates are using to maximize their earnings in today\'s competitive landscape.',
                'content' => '<p>Affiliate marketing continues to evolve, and staying ahead of the curve is essential for maximizing your revenue. Here are 10 proven strategies that top affiliates are using in 2026:</p>

<h2>1. Focus on Niche Selection</h2>
<p>Choose a specific niche where you can become an authority. Specialized knowledge builds trust and increases conversion rates.</p>

<h2>2. Leverage Multiple Traffic Sources</h2>
<p>Don\'t rely on a single traffic source. Diversify across SEO, paid ads, social media, and email marketing to reduce risk and increase reach.</p>

<h2>3. Build an Email List</h2>
<p>Email marketing remains one of the highest ROI channels. Focus on building a quality list and nurturing relationships with your subscribers.</p>

<h2>4. Create High-Quality Content</h2>
<p>Invest in creating valuable content that genuinely helps your audience. Quality content attracts organic traffic and builds authority.</p>

<h2>5. Use Data Analytics</h2>
<p>Track everything. Use analytics to understand what\'s working and optimize your campaigns based on data, not gut feelings.</p>

<h2>6. Test Different Offers</h2>
<p>Don\'t settle for the first offer you promote. Test multiple offers in your niche to find the highest-converting ones.</p>

<h2>7. Optimize Landing Pages</h2>
<p>Your landing page can make or break your campaign. A/B test different elements to improve conversion rates continuously.</p>

<h2>8. Build Relationships with Affiliate Managers</h2>
<p>Strong relationships with affiliate managers can lead to exclusive offers, higher payouts, and valuable insider insights.</p>

<h2>9. Stay Updated on Industry Trends</h2>
<p>The affiliate marketing landscape changes rapidly. Stay informed about new technologies, platforms, and strategies.</p>

<h2>10. Scale What Works</h2>
<p>Once you find a winning formula, scale aggressively. Invest more budget, create more content, and expand your reach.</p>

<p><strong>Conclusion:</strong> Success in affiliate marketing requires consistent effort, continuous learning, and strategic execution. Implement these strategies systematically, and you\'ll see significant improvements in your affiliate revenue.</p>',
                'meta_title' => '10 Proven Affiliate Marketing Strategies for 2026 | DealsIntel',
                'meta_description' => 'Learn the top 10 strategies successful affiliates use to maximize revenue in 2026. Boost your affiliate marketing earnings today.',
                'meta_keywords' => 'affiliate marketing strategies, increase affiliate revenue, affiliate marketing tips, 2026 affiliate marketing',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'category_id' => $cpaCategory->id,
                'user_id' => $author->id,
                'title' => 'Understanding CPA Marketing: A Beginner\'s Complete Guide',
                'slug' => 'understanding-cpa-marketing-beginners-guide',
                'excerpt' => 'New to CPA marketing? This comprehensive guide covers everything you need to know to get started and succeed.',
                'content' => '<p>Cost Per Action (CPA) marketing is one of the most profitable forms of affiliate marketing. This guide will walk you through everything you need to know to succeed.</p>

<h2>What is CPA Marketing?</h2>
<p>CPA marketing is a performance-based advertising model where advertisers pay affiliates for specific actions, such as:</p>
<ul>
<li>Lead submissions (email, phone number)</li>
<li>Free trial sign-ups</li>
<li>App installs</li>
<li>Sales</li>
<li>Form completions</li>
</ul>

<h2>Why Choose CPA Marketing?</h2>
<p>CPA marketing offers several advantages:</p>
<ul>
<li><strong>Lower barrier to entry:</strong> Users don\'t need to make a purchase</li>
<li><strong>Higher conversion rates:</strong> Free offers convert better than paid products</li>
<li><strong>Quick payouts:</strong> Get paid for leads, not just sales</li>
<li><strong>Diverse niches:</strong> Opportunities in every industry</li>
</ul>

<h2>How to Get Started</h2>
<p>Follow these steps to begin your CPA marketing journey:</p>

<h3>1. Join a CPA Network</h3>
<p>Sign up with reputable CPA networks like DealsIntel. Look for networks with good reputation, quality offers, and reliable tracking.</p>

<h3>2. Choose Your Niche</h3>
<p>Select a niche you\'re interested in or have knowledge about. Popular niches include:</p>
<ul>
<li>Finance and insurance</li>
<li>Health and wellness</li>
<li>Dating and relationships</li>
<li>Education and e-learning</li>
<li>Gaming and entertainment</li>
</ul>

<h3>3. Select Offers</h3>
<p>Choose offers that match your traffic source and audience. Consider payout, conversion rate, and offer quality.</p>

<h3>4. Generate Traffic</h3>
<p>Drive targeted traffic using methods like:</p>
<ul>
<li>Content marketing and SEO</li>
<li>Paid advertising (Google Ads, Facebook Ads)</li>
<li>Social media marketing</li>
<li>Email marketing</li>
<li>Influencer partnerships</li>
</ul>

<h2>Best Practices for Success</h2>
<ul>
<li>Always follow the network\'s terms and conditions</li>
<li>Test multiple offers to find winners</li>
<li>Track and analyze your campaigns</li>
<li>Focus on quality traffic over quantity</li>
<li>Build relationships with affiliate managers</li>
<li>Stay compliant with advertising regulations</li>
</ul>

<p><strong>Ready to start?</strong> Join DealsIntel today and get access to thousands of high-converting CPA offers!</p>',
                'meta_title' => 'CPA Marketing Guide for Beginners | DealsIntel',
                'meta_description' => 'Complete beginner\'s guide to CPA marketing. Learn what CPA is, how it works, and how to get started earning today.',
                'meta_keywords' => 'CPA marketing, cost per action, CPA guide, affiliate marketing for beginners',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'category_id' => $trafficCategory->id,
                'user_id' => $author->id,
                'title' => 'The Ultimate Guide to Driving Quality Traffic in 2026',
                'slug' => 'ultimate-guide-driving-quality-traffic-2026',
                'excerpt' => 'Learn the most effective traffic generation strategies for affiliate marketers in 2026.',
                'content' => '<p>Traffic is the lifeblood of affiliate marketing. But not all traffic is created equal. Here\'s how to drive quality traffic that converts.</p>

<h2>Free Traffic Methods</h2>

<h3>1. Search Engine Optimization (SEO)</h3>
<p>SEO remains one of the best long-term traffic strategies. Focus on creating high-quality content optimized for your target keywords.</p>

<h3>2. Social Media Marketing</h3>
<p>Build a presence on platforms where your audience spends time. Create engaging content and build a community around your niche.</p>

<h3>3. YouTube Marketing</h3>
<p>Video content is incredibly powerful. Create tutorials, reviews, and informative videos related to your offers.</p>

<h2>Paid Traffic Methods</h2>

<h3>1. Google Ads</h3>
<p>Search ads can deliver highly targeted traffic. Start with long-tail keywords to reduce costs and improve ROI.</p>

<h3>2. Facebook Ads</h3>
<p>Facebook\'s targeting options are unmatched. Test different audiences, creatives, and ad formats to find what works.</p>

<h3>3. Native Advertising</h3>
<p>Native ads blend with content and can deliver high-quality traffic at scale. Popular platforms include Taboola and Outbrain.</p>

<h2>Traffic Quality Factors</h2>
<p>Focus on these factors to ensure traffic quality:</p>
<ul>
<li><strong>Relevance:</strong> Traffic should match your offer</li>
<li><strong>Intent:</strong> Users should be actively looking for solutions</li>
<li><strong>Demographics:</strong> Target the right age, location, and interests</li>
<li><strong>Device:</strong> Optimize for mobile or desktop based on your offer</li>
</ul>

<p><strong>Pro Tip:</strong> Always track your traffic sources and focus on what converts. Quality over quantity wins every time!</p>',
                'meta_title' => 'How to Drive Quality Traffic for Affiliates in 2026',
                'meta_description' => 'Master traffic generation with this ultimate guide. Learn free and paid traffic methods that convert in 2026.',
                'meta_keywords' => 'traffic generation, affiliate traffic, drive website traffic, quality traffic sources',
                'is_published' => true,
                'published_at' => now()->subDays(15),
            ],
        ];

        foreach ($posts as $postData) {
            BlogPost::create($postData);
        }

        $this->command->info('Sample blog posts created successfully.');
    }
}
