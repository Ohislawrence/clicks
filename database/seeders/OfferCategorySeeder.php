<?php

namespace Database\Seeders;

use App\Models\OfferCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfferCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'SaaS Tools',
                'slug' => 'saas-tools',
                'description' => 'Software as a Service products including project management, CRM, and analytics tools',
                'icon' => 'cloud',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'SEO tools, email marketing platforms, social media management, and advertising solutions',
                'icon' => 'chart-bar',
                'color' => '#8B5CF6',
                'sort_order' => 2,
            ],
            [
                'name' => 'Content Creation',
                'slug' => 'content-creation',
                'description' => 'Video editing software, graphic design tools, AI writing assistants, and content platforms',
                'icon' => 'video-camera',
                'color' => '#EC4899',
                'sort_order' => 3,
            ],
            [
                'name' => 'E-learning',
                'slug' => 'e-learning',
                'description' => 'Online courses, certifications, tutorials, and educational platforms',
                'icon' => 'academic-cap',
                'color' => '#10B981',
                'sort_order' => 4,
            ],
            [
                'name' => 'Productivity Tools',
                'slug' => 'productivity-tools',
                'description' => 'Note-taking apps, time tracking, automation tools, and workflow management',
                'icon' => 'lightning-bolt',
                'color' => '#F59E0B',
                'sort_order' => 5,
            ],
            [
                'name' => 'Creator Tools',
                'slug' => 'creator-tools',
                'description' => 'Link-in-bio tools, monetization platforms, analytics for creators, and influencer tools',
                'icon' => 'user-circle',
                'color' => '#EF4444',
                'sort_order' => 6,
            ],
            [
                'name' => 'Finance & Crypto',
                'slug' => 'finance-crypto',
                'description' => 'Banking apps, investment platforms, cryptocurrency exchanges, and financial tools',
                'icon' => 'currency-dollar',
                'color' => '#14B8A6',
                'sort_order' => 7,
            ],
            [
                'name' => 'Influencer Services',
                'slug' => 'influencer-services',
                'description' => 'Social media analytics, sponsorship platforms, brand collaboration tools',
                'icon' => 'sparkles',
                'color' => '#F97316',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            OfferCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Offer categories created successfully!');
    }
}
