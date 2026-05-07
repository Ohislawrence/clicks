<?php

namespace App\Jobs;

use App\Models\AffiliateLink;
use App\Models\Click;
use App\Services\FraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $trackingCode,
        public string $ipAddress,
        public ?string $userAgent,
        public ?string $referrer,
        public array $geoData = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FraudDetectionService $fraudDetectionService): void
    {
        $affiliateLink = AffiliateLink::where('tracking_code', $this->trackingCode)
            ->where('is_active', true)
            ->with('offer')
            ->first();

        if (!$affiliateLink || !$affiliateLink->offer->is_active) {
            return;
        }

        // Parse user agent
        $userAgentData = $this->parseUserAgent($this->userAgent);

        // Create preliminary click record
        $click = Click::create([
            'affiliate_link_id' => $affiliateLink->id,
            'offer_id' => $affiliateLink->offer_id,
            'affiliate_id' => $affiliateLink->affiliate_id,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'referrer' => $this->referrer,
            'country_code' => $this->geoData['country_code'] ?? null,
            'country_name' => $this->geoData['country_name'] ?? null,
            'city' => $this->geoData['city'] ?? null,
            'device_type' => $userAgentData['device'],
            'browser' => $userAgentData['browser'],
            'os' => $userAgentData['os'],
            'device_fingerprint' => $this->geoData['screen_resolution'] ?? null,
            'device_details' => array_filter([
                'screen_resolution' => $this->geoData['screen_resolution'] ?? null,
                'timezone' => $this->geoData['timezone'] ?? null,
                'language' => $this->geoData['language'] ?? null,
            ]),
            // Defaults before fraud analysis
            'is_valid' => true,
            'quality_score' => 50,
            'risk_level' => 'medium',
        ]);

        // Perform fraud detection analysis
        $fraudAnalysis = $fraudDetectionService->analyzeClick($click, $this->geoData);

        // Update click with fraud analysis results
        $click->update([
            'quality_score' => $fraudAnalysis['quality_score'],
            'risk_level' => $fraudAnalysis['risk_level'],
            'fraud_indicators' => $fraudAnalysis['fraud_indicators'],
            'needs_manual_review' => $fraudAnalysis['needs_manual_review'],
            'is_valid' => $fraudAnalysis['quality_score'] >= 40, // Threshold for valid click
        ]);

        // Update link stats (only count valid clicks)
        if ($click->is_valid) {
            $affiliateLink->increment('click_count');
            $affiliateLink->offer->increment('total_clicks');
        }

        // Cache click data for conversion tracking (30 days TTL)
        $cookieDuration = $affiliateLink->offer->cookie_duration;
        Cache::put(
            "click:{$this->trackingCode}:{$this->ipAddress}",
            [
                'click_id' => $click->id,
                'affiliate_id' => $click->affiliate_id,
                'offer_id' => $click->offer_id,
                'affiliate_link_id' => $affiliateLink->id,
                'created_at' => now()->timestamp,
                'quality_score' => $click->quality_score,
            ],
            now()->addDays($cookieDuration)
        );
    }

    /**
     * Parse user agent for device, browser, OS
     */
    protected function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return ['device' => 'unknown', 'browser' => 'unknown', 'os' => 'unknown'];
        }

        // Simple detection
        $device = 'desktop';
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $userAgent)) {
            $device = preg_match('/tablet|ipad/i', $userAgent) ? 'tablet' : 'mobile';
        }

        $browser = 'unknown';
        if (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        }

        $os = 'unknown';
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}
