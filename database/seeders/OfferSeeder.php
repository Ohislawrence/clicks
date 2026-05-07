<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $advertiser = User::where('email', 'advertiser@test.com')->first();
        
        if (!$advertiser) {
            return;
        }

        $categories = OfferCategory::all();

        $offers = [
            [
                'name' => 'Premium SaaS Tool Pro',
                'description' => 'The ultimate productivity tool for teams. Manage projects, track time, and collaborate seamlessly.',
                'category_id' => $categories->where('slug', 'saas-tools')->first()->id,
                'commission_model' => 'pps',
                'commission_rate' => 5000,
                'cookie_duration' => 30,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/saas-tool',
                'is_active' => true,
            ],
            [
                'name' => 'Digital Marketing Masterclass',
                'description' => 'Learn advanced digital marketing strategies from industry experts. Video courses and certifications included.',
                'category_id' => $categories->where('slug', 'digital-marketing')->first()->id,
                'commission_model' => 'revshare',
                'commission_rate' => 30,
                'cookie_duration' => 60,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/marketing-course',
                'is_active' => true,
            ],
            [
                'name' => 'Video Editing Suite',
                'description' => 'Professional-grade video editing software with AI-powered features. Perfect for content creators.',
                'category_id' => $categories->where('slug', 'content-creation')->first()->id,
                'commission_model' => 'pps',
                'commission_rate' => 7500,
                'cookie_duration' => 45,
                'access_type' => 'request',
                'preview_url' => 'https://example.com/video-editor',
                'is_active' => true,
            ],
            [
                'name' => 'Online Course Platform',
                'description' => 'Create and sell your own courses with our all-in-one platform. Includes hosting, payments, and marketing tools.',
                'category_id' => $categories->where('slug', 'e-learning')->first()->id,
                'commission_model' => 'revshare',
                'commission_rate' => 25,
                'cookie_duration' => 90,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/course-platform',
                'is_active' => true,
            ],
            [
                'name' => 'Task Management App',
                'description' => 'Simple yet powerful task management for individuals and small teams. Stay organized and productive.',
                'category_id' => $categories->where('slug', 'productivity-tools')->first()->id,
                'commission_model' => 'ppl',
                'commission_rate' => 1500,
                'cookie_duration' => 30,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/task-app',
                'is_active' => true,
            ],
            [
                'name' => 'Creator Analytics Dashboard',
                'description' => 'Track your social media performance across all platforms. Deep insights and AI-powered recommendations.',
                'category_id' => $categories->where('slug', 'creator-tools')->first()->id,
                'commission_model' => 'pps',
                'commission_rate' => 3500,
                'cookie_duration' => 60,
                'access_type' => 'request',
                'preview_url' => 'https://example.com/analytics',
                'is_active' => true,
            ],
            [
                'name' => 'Crypto Trading Course',
                'description' => 'Master cryptocurrency trading with our comprehensive course. From basics to advanced strategies.',
                'category_id' => $categories->where('slug', 'finance-crypto')->first()->id,
                'commission_model' => 'revshare',
                'commission_rate' => 40,
                'cookie_duration' => 45,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/crypto-course',
                'is_active' => true,
            ],
            [
                'name' => 'Influencer Collaboration Platform',
                'description' => 'Connect with brands and manage all your collaborations in one place. Contract templates included.',
                'category_id' => $categories->where('slug', 'influencer-services')->first()->id,
                'commission_model' => 'pps',
                'commission_rate' => 2500,
                'cookie_duration' => 30,
                'access_type' => 'open',
                'preview_url' => 'https://example.com/collab-platform',
                'is_active' => true,
            ],
        ];

        foreach ($offers as $offerData) {
            Offer::create(array_merge($offerData, [
                'advertiser_id' => $advertiser->id,
                'slug' => Str::slug($offerData['name']),
            ]));
        }
    }
}
