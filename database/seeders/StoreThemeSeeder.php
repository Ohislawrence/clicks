<?php

namespace Database\Seeders;

use App\Models\StoreTheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Ocean Breeze',
                'slug' => 'ocean-breeze',
                'description' => 'Fresh and modern with calming blue gradients perfect for lifestyle brands',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#0EA5E9',
                        'secondary' => '#06B6D4',
                        'accent' => '#38BDF8',
                        'text' => '#0F172A',
                        'background' => '#F8FAFC',
                    ],
                    'typography' => [
                        'heading_font' => 'Plus Jakarta Sans',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sunset Glow',
                'slug' => 'sunset-glow',
                'description' => 'Warm and inviting with vibrant orange and pink tones for creative businesses',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#F97316',
                        'secondary' => '#EC4899',
                        'accent' => '#FB923C',
                        'text' => '#1F2937',
                        'background' => '#FFF7ED',
                    ],
                    'typography' => [
                        'heading_font' => 'Urbanist',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Forest Green',
                'slug' => 'forest-green',
                'description' => 'Natural and organic with earthy green tones perfect for eco-friendly brands',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#10B981',
                        'secondary' => '#059669',
                        'accent' => '#34D399',
                        'text' => '#064E3B',
                        'background' => '#F0FDF4',
                    ],
                    'typography' => [
                        'heading_font' => 'Space Grotesk',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Royal Purple',
                'slug' => 'royal-purple',
                'description' => 'Luxurious and elegant with deep purple gradients for premium products',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#8B5CF6',
                        'secondary' => '#7C3AED',
                        'accent' => '#A78BFA',
                        'text' => '#1F2937',
                        'background' => '#FAF5FF',
                    ],
                    'typography' => [
                        'heading_font' => 'Playfair Display',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Midnight Dark',
                'slug' => 'midnight-dark',
                'description' => 'Sleek dark mode design with neon accents for tech and gaming products',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'left-aligned',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#06B6D4',
                        'secondary' => '#8B5CF6',
                        'accent' => '#EC4899',
                        'text' => '#F9FAFB',
                        'background' => '#111827',
                    ],
                    'typography' => [
                        'heading_font' => 'Orbitron',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Rose Gold',
                'slug' => 'rose-gold',
                'description' => 'Feminine and sophisticated with rose gold accents for beauty and fashion',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '4-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#EC4899',
                        'secondary' => '#F472B6',
                        'accent' => '#FCA5A5',
                        'text' => '#500724',
                        'background' => '#FFF1F2',
                    ],
                    'typography' => [
                        'heading_font' => 'Cormorant Garamond',
                        'body_font' => 'Lato',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Electric Blue',
                'slug' => 'electric-blue',
                'description' => 'Bold and energetic with electric blue for modern tech brands',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'left-aligned',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#3B82F6',
                        'secondary' => '#2563EB',
                        'accent' => '#60A5FA',
                        'text' => '#1E3A8A',
                        'background' => '#EFF6FF',
                    ],
                    'typography' => [
                        'heading_font' => 'Montserrat',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => true,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Minimalist White',
                'slug' => 'minimalist-white',
                'description' => 'Clean and pure white design with subtle accents for modern aesthetics',
                'thumbnail' => null,
                'config' => [
                    'layout' => [
                        'header_style' => 'centered',
                        'product_grid' => '3-column',
                        'sidebar_position' => 'none',
                    ],
                    'colors' => [
                        'primary' => '#18181B',
                        'secondary' => '#52525B',
                        'accent' => '#A1A1AA',
                        'text' => '#09090B',
                        'background' => '#FFFFFF',
                    ],
                    'typography' => [
                        'heading_font' => 'Inter',
                        'body_font' => 'Inter',
                        'heading_size' => 'large',
                    ],
                    'components' => [
                        'show_breadcrumbs' => true,
                        'show_social_share' => true,
                        'show_related_products' => true,
                        'show_reviews' => false,
                    ],
                    'features' => [
                        'sticky_header' => true,
                        'quick_view' => true,
                        'product_zoom' => true,
                    ],
                ],
                'store_type' => 'both',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($themes as $theme) {
            StoreTheme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }

        // Add the 3 core layout themes required by the new theme system
        $layoutThemes = [
            [
                'name'        => 'Marketplace Pro',
                'slug'        => 'marketplace',
                'description' => 'Alibaba/Jumia style – announcement bar, search header, multi-column layout',
                'config' => [
                    'colors' => [
                        'primary'    => '#FF6000',
                        'secondary'  => '#E53E00',
                        'background' => '#F5F5F5',
                        'text'       => '#1F2937',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#1C1C2E',
                    ],
                    'header' => [
                        'tagline'           => '',
                        'announcement_text' => '🎉 Free delivery on orders over ₦50,000!',
                    ],
                    'hero' => [
                        'heading'         => 'Shop the Best Deals',
                        'subheading'      => 'Discover thousands of products at amazing prices.',
                        'button_text'     => 'Shop Now',
                        'image_url'       => null,
                        'overlay_opacity' => 55,
                    ],
                    'footer' => [
                        'tagline'   => 'Your trusted online marketplace.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                ],
                'store_type' => 'multi',
                'is_active'  => true,
                'sort_order' => 0,
            ],
            [
                'name'        => 'Boutique Elegance',
                'slug'        => 'boutique',
                'description' => 'Luxury editorial style – centered logo, serif fonts, elegant split hero',
                'config' => [
                    'colors' => [
                        'primary'    => '#7C3AED',
                        'secondary'  => '#DB2777',
                        'background' => '#FAFAFA',
                        'text'       => '#1F2937',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#F9F5FF',
                    ],
                    'header' => [
                        'tagline'           => 'Curated with care.',
                        'announcement_text' => '',
                    ],
                    'hero' => [
                        'heading'         => 'Timeless Style, Modern Edge',
                        'subheading'      => 'Handpicked collections for the discerning shopper.',
                        'button_text'     => 'Explore Collection',
                        'image_url'       => null,
                        'overlay_opacity' => 40,
                    ],
                    'footer' => [
                        'tagline'   => 'Curated collections, delivered with love.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                ],
                'store_type' => 'both',
                'is_active'  => true,
                'sort_order' => 0,
            ],
            [
                'name'        => 'Minimal Clean',
                'slug'        => 'minimal',
                'description' => 'Clean and simple – uncluttered layout, bold typography, fast load',
                'config' => [
                    'colors' => [
                        'primary'    => '#3B82F6',
                        'secondary'  => '#1D4ED8',
                        'background' => '#FFFFFF',
                        'text'       => '#111827',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#F9FAFB',
                    ],
                    'header' => [
                        'tagline'           => '',
                        'announcement_text' => '',
                    ],
                    'hero' => [
                        'heading'         => 'Welcome to Our Store',
                        'subheading'      => 'Quality products, simple experience.',
                        'button_text'     => 'Browse Products',
                        'image_url'       => null,
                        'overlay_opacity' => 30,
                    ],
                    'footer' => [
                        'tagline'   => 'Quality products, simple experience.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                ],
                'store_type' => 'both',
                'is_active'  => true,
                'sort_order' => 0,
            ],
        ];

        foreach ($layoutThemes as $theme) {
            StoreTheme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }

        // ── Single-product themes ─────────────────────────────────────────────
        $singleThemes = [
            [
                'name'        => 'Product Spotlight',
                'slug'        => 'product-spotlight',
                'description' => 'Conversion-focused single-product page: large hero image, bold CTA, feature highlights and social proof',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#6366F1',
                        'secondary'  => '#4F46E5',
                        'background' => '#FAFAFA',
                        'text'       => '#111827',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#1E1B4B',
                    ],
                    'header' => [
                        'tagline'           => '',
                        'announcement_text' => '🚀 Limited time offer – order today!',
                    ],
                    'hero' => [
                        'heading'         => 'One Product. Endless Possibilities.',
                        'subheading'      => 'Designed to solve your problem, built to last.',
                        'button_text'     => 'Get Yours Now',
                        'image_url'       => null,
                        'overlay_opacity' => 45,
                    ],
                    'footer' => [
                        'tagline'   => 'Quality you can trust, delivered to your door.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'single_product' => [
                        'show_countdown'     => true,
                        'show_trust_badges'  => true,
                        'show_testimonials'  => true,
                        'cta_sticky'         => true,
                    ],
                ],
                'store_type' => 'single',
                'is_active'  => true,
                'sort_order' => 10,
            ],
            [
                'name'        => 'Launch Pad',
                'slug'        => 'launch-pad',
                'description' => 'High-urgency launch page with countdown, benefits list and strong buy button – ideal for new product drops',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#EF4444',
                        'secondary'  => '#DC2626',
                        'background' => '#FFF7F7',
                        'text'       => '#1F2937',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#7F1D1D',
                    ],
                    'header' => [
                        'tagline'           => 'The wait is over.',
                        'announcement_text' => '🔥 Flash Sale – ends in 24 hours!',
                    ],
                    'hero' => [
                        'heading'         => 'Introducing Our Breakthrough Product',
                        'subheading'      => 'Join thousands who already love it. Get yours before stock runs out.',
                        'button_text'     => 'Buy Now – Limited Stock',
                        'image_url'       => null,
                        'overlay_opacity' => 50,
                    ],
                    'footer' => [
                        'tagline'   => 'Secure checkout. Fast delivery. 100% satisfaction guaranteed.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'single_product' => [
                        'show_countdown'     => true,
                        'show_trust_badges'  => true,
                        'show_testimonials'  => true,
                        'cta_sticky'         => true,
                    ],
                ],
                'store_type' => 'single',
                'is_active'  => true,
                'sort_order' => 11,
            ],
            [
                'name'        => 'Luxury Showcase',
                'slug'        => 'luxury-showcase',
                'description' => 'Dark premium theme for high-end single products – understated elegance with gold accents',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#D4AF37',
                        'secondary'  => '#B8962A',
                        'background' => '#0D0D0D',
                        'text'       => '#F5F5F5',
                        'header_bg'  => '#111111',
                        'footer_bg'  => '#050505',
                    ],
                    'header' => [
                        'tagline'           => 'Crafted for the few who expect the best.',
                        'announcement_text' => '',
                    ],
                    'hero' => [
                        'heading'         => 'Exceptional by Design',
                        'subheading'      => 'Uncompromising quality. A product made for those who demand perfection.',
                        'button_text'     => 'Acquire Now',
                        'image_url'       => null,
                        'overlay_opacity' => 60,
                    ],
                    'footer' => [
                        'tagline'   => 'Handcrafted with precision. Delivered with care.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'single_product' => [
                        'show_countdown'     => false,
                        'show_trust_badges'  => true,
                        'show_testimonials'  => true,
                        'cta_sticky'         => false,
                    ],
                ],
                'store_type' => 'single',
                'is_active'  => true,
                'sort_order' => 12,
            ],
            [
                'name'        => 'Clean Sell',
                'slug'        => 'clean-sell',
                'description' => 'Minimal white single-product page that keeps the focus entirely on the product with zero distraction',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#14B8A6',
                        'secondary'  => '#0D9488',
                        'background' => '#FFFFFF',
                        'text'       => '#0F172A',
                        'header_bg'  => '#F8FAFC',
                        'footer_bg'  => '#F1F5F9',
                    ],
                    'header' => [
                        'tagline'           => '',
                        'announcement_text' => '✅ Free returns within 30 days',
                    ],
                    'hero' => [
                        'heading'         => 'Simple. Effective. Yours.',
                        'subheading'      => 'Everything you need. Nothing you don\'t.',
                        'button_text'     => 'Order Now',
                        'image_url'       => null,
                        'overlay_opacity' => 25,
                    ],
                    'footer' => [
                        'tagline'   => 'Simple checkout. Fast shipping.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'single_product' => [
                        'show_countdown'     => false,
                        'show_trust_badges'  => true,
                        'show_testimonials'  => false,
                        'cta_sticky'         => true,
                    ],
                ],
                'store_type' => 'single',
                'is_active'  => true,
                'sort_order' => 13,
            ],
        ];

        foreach ($singleThemes as $theme) {
            StoreTheme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }

        // ── Multi-product themes ──────────────────────────────────────────────
        $multiThemes = [
            [
                'name'        => 'Grid Market',
                'slug'        => 'grid-market',
                'description' => 'Dense product grid with category filters and sidebar – ideal for stores with large catalogues',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#2563EB',
                        'secondary'  => '#1D4ED8',
                        'background' => '#F3F4F6',
                        'text'       => '#111827',
                        'header_bg'  => '#1E3A8A',
                        'footer_bg'  => '#1E3A8A',
                    ],
                    'header' => [
                        'tagline'           => 'Your one-stop shop.',
                        'announcement_text' => '🛒 Free shipping on orders over ₦30,000!',
                    ],
                    'hero' => [
                        'heading'         => 'Find Everything You Need',
                        'subheading'      => 'Hundreds of products at unbeatable prices.',
                        'button_text'     => 'Shop All Products',
                        'image_url'       => null,
                        'overlay_opacity' => 55,
                    ],
                    'footer' => [
                        'tagline'   => 'Fast delivery. Easy returns. Trusted sellers.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'multi_product' => [
                        'grid_columns'        => 4,
                        'show_filters'        => true,
                        'show_category_nav'   => true,
                        'show_featured_banner' => true,
                    ],
                ],
                'store_type' => 'multi',
                'is_active'  => true,
                'sort_order' => 20,
            ],
            [
                'name'        => 'Fashion Lookbook',
                'slug'        => 'fashion-lookbook',
                'description' => 'Editorial fashion store with large imagery, collections and lifestyle hero – perfect for clothing and accessories',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#BE185D',
                        'secondary'  => '#9D174D',
                        'background' => '#FFF0F6',
                        'text'       => '#1F2937',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#500724',
                    ],
                    'header' => [
                        'tagline'           => 'Style that speaks for itself.',
                        'announcement_text' => '✨ New collection just dropped – shop now!',
                    ],
                    'hero' => [
                        'heading'         => 'Wear Your Story',
                        'subheading'      => 'Curated fashion that moves with you.',
                        'button_text'     => 'Explore the Collection',
                        'image_url'       => null,
                        'overlay_opacity' => 40,
                    ],
                    'footer' => [
                        'tagline'   => 'Fashion delivered to your doorstep.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'multi_product' => [
                        'grid_columns'        => 3,
                        'show_filters'        => true,
                        'show_category_nav'   => true,
                        'show_featured_banner' => true,
                    ],
                ],
                'store_type' => 'multi',
                'is_active'  => true,
                'sort_order' => 21,
            ],
            [
                'name'        => 'Tech Hub',
                'slug'        => 'tech-hub',
                'description' => 'Electronics and gadgets store with dark accents, spec highlights and comparison-friendly layout',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#06B6D4',
                        'secondary'  => '#0891B2',
                        'background' => '#F0F9FF',
                        'text'       => '#0C4A6E',
                        'header_bg'  => '#0F172A',
                        'footer_bg'  => '#0F172A',
                    ],
                    'header' => [
                        'tagline'           => 'Power up your life.',
                        'announcement_text' => '⚡ Weekend deals – up to 40% off select gadgets!',
                    ],
                    'hero' => [
                        'heading'         => 'Next-Gen Tech at Your Fingertips',
                        'subheading'      => 'The latest gadgets, accessories and electronics – all in one place.',
                        'button_text'     => 'Shop Tech Now',
                        'image_url'       => null,
                        'overlay_opacity' => 60,
                    ],
                    'footer' => [
                        'tagline'   => 'Authentic products. Fast delivery. Expert support.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'multi_product' => [
                        'grid_columns'        => 4,
                        'show_filters'        => true,
                        'show_category_nav'   => true,
                        'show_featured_banner' => true,
                    ],
                ],
                'store_type' => 'multi',
                'is_active'  => true,
                'sort_order' => 22,
            ],
            [
                'name'        => 'Fresh Grocery',
                'slug'        => 'fresh-grocery',
                'description' => 'Bright and appetite-friendly multi-product theme for food, grocery and FMCG stores',
                'thumbnail'   => null,
                'config'      => [
                    'colors' => [
                        'primary'    => '#16A34A',
                        'secondary'  => '#15803D',
                        'background' => '#F0FDF4',
                        'text'       => '#14532D',
                        'header_bg'  => '#FFFFFF',
                        'footer_bg'  => '#14532D',
                    ],
                    'header' => [
                        'tagline'           => 'Fresh picks, delivered fast.',
                        'announcement_text' => '🥦 Same-day delivery available in your area!',
                    ],
                    'hero' => [
                        'heading'         => 'Fresh. Local. Delivered.',
                        'subheading'      => 'Shop fresh groceries and everyday essentials without leaving home.',
                        'button_text'     => 'Start Shopping',
                        'image_url'       => null,
                        'overlay_opacity' => 35,
                    ],
                    'footer' => [
                        'tagline'   => 'Fresh produce. Unbeatable value. Right to your door.',
                        'facebook'  => '',
                        'instagram' => '',
                        'twitter'   => '',
                    ],
                    'multi_product' => [
                        'grid_columns'        => 4,
                        'show_filters'        => true,
                        'show_category_nav'   => true,
                        'show_featured_banner' => false,
                    ],
                ],
                'store_type' => 'multi',
                'is_active'  => true,
                'sort_order' => 23,
            ],
        ];

        foreach ($multiThemes as $theme) {
            StoreTheme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }
    }
}
