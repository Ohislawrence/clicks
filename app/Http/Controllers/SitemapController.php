<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     */
    public function index()
    {
        $urls = [
            [
                'loc' => route('front.home'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => route('front.for-affiliates'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'loc' => route('front.for-advertisers'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'loc' => route('front.features'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('front.about'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('front.faq'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('front.contact'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => route('front.privacy'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'yearly',
                'priority' => '0.3'
            ],
            [
                'loc' => route('front.terms'),
                'lastmod' => now()->toW3cString(),
                'changefreq' => 'yearly',
                'priority' => '0.3'
            ],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
