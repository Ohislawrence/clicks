<?php

namespace Database\Seeders;

use App\Models\StorePlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Single Product Store',
                'slug' => 'single-product',
                'store_type' => 'single',
                'product_limit' => 1,
                'monthly_price' => 5000,
                'yearly_price' => 50000,
                'yearly_discount_percent' => 17,
                'features' => [
                    'One product page',
                    'Custom domain support',
                    'Theme customization',
                    'Payment gateway integration',
                    'Order management',
                    'Customer analytics',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Multi-Product Bronze',
                'slug' => 'multi-bronze',
                'store_type' => 'multi',
                'product_limit' => 10,
                'monthly_price' => 15000,
                'yearly_price' => 150000,
                'yearly_discount_percent' => 17,
                'features' => [
                    'Up to 10 products',
                    'Product categories',
                    'Custom domain support',
                    'Theme customization',
                    'Payment gateway integration',
                    'Order management',
                    'Customer analytics',
                    'Inventory tracking',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Multi-Product Silver',
                'slug' => 'multi-silver',
                'store_type' => 'multi',
                'product_limit' => 50,
                'monthly_price' => 30000,
                'yearly_price' => 300000,
                'yearly_discount_percent' => 17,
                'features' => [
                    'Up to 50 products',
                    'Product categories',
                    'Custom domain support',
                    'Advanced theme customization',
                    'Payment gateway integration',
                    'Order management',
                    'Advanced analytics',
                    'Inventory tracking',
                    'Bulk product import',
                    'SEO optimization',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Multi-Product Gold',
                'slug' => 'multi-gold',
                'store_type' => 'multi',
                'product_limit' => 200,
                'monthly_price' => 50000,
                'yearly_price' => 500000,
                'yearly_discount_percent' => 17,
                'features' => [
                    'Up to 200 products',
                    'Unlimited categories',
                    'Custom domain support',
                    'Premium theme customization',
                    'Payment gateway integration',
                    'Advanced order management',
                    'Comprehensive analytics',
                    'Inventory tracking',
                    'Bulk product import/export',
                    'Advanced SEO optimization',
                    'Priority support',
                    'Custom shipping rules',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            StorePlan::create($plan);
        }
    }
}
