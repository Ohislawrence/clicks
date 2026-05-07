<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class TrackingService
{
    protected const COOKIE_NAME = 'clicksintel_tracking';

    /**
     * Track a click from an affiliate link
     */
    public function trackClick(string $trackingCode, Request $request): ?Click
    {
        $affiliateLink = AffiliateLink::where('tracking_code', $trackingCode)
            ->where('is_active', true)
            ->with('offer')
            ->first();

        if (!$affiliateLink || !$affiliateLink->offer->is_active) {
            return null;
        }

        // Check for fraud
        $fraudCheck = $this->checkForFraud($request, $affiliateLink);

        // Parse user agent
        $userAgentData = $this->parseUserAgent($request->userAgent());

        // Create click record
        $click = Click::create([
            'affiliate_link_id' => $affiliateLink->id,
            'offer_id' => $affiliateLink->offer_id,
            'affiliate_id' => $affiliateLink->affiliate_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'country_code' => $this->getCountryCode($request->ip()),
            'country_name' => $this->getCountryName($request->ip()),
            'city' => $this->getCity($request->ip()),
            'device_type' => $userAgentData['device'],
            'browser' => $userAgentData['browser'],
            'os' => $userAgentData['os'],
            'is_valid' => $fraudCheck['is_valid'],
            'fraud_reason' => $fraudCheck['reason'] ?? null,
        ]);

        // Update link stats
        $affiliateLink->increment('click_count');
        
        // Update offer stats
        $affiliateLink->offer->increment('total_clicks');

        // Set tracking cookie
        $this->setTrackingCookie($click, $affiliateLink->offer->cookie_duration);

        return $click;
    }

    /**
     * Track a conversion (sale or lead)
     */
    public function trackConversion(array $data): ?Conversion
    {
        $click = null;
        $trackingMethod = 'postback';

        // Try to find click from cookie first
        if ($cookieData = $this->getTrackingCookie()) {
            $click = Click::find($cookieData['click_id']);
            $trackingMethod = 'cookie';
        }

        // If no cookie, try to find by transaction ID or affiliate link
        if (!$click && isset($data['tracking_code'])) {
            $affiliateLink = AffiliateLink::where('tracking_code', $data['tracking_code'])->first();
            if ($affiliateLink) {
                // Find most recent valid click
                $click = Click::where('affiliate_link_id', $affiliateLink->id)
                    ->where('is_valid', true)
                    ->where('is_converted', false)
                    ->latest()
                    ->first();
            }
        }

        if (!$click) {
            return null;
        }

        $affiliateLink = $click->affiliateLink;
        $offer = $affiliateLink->offer;

        // Calculate commission
        $commissionAmount = $this->calculateCommission(
            $offer->commission_model,
            $offer->commission_rate,
            $data['conversion_value'] ?? 0
        );

        // Create conversion
        $conversion = Conversion::create([
            'click_id' => $click->id,
            'affiliate_id' => $affiliateLink->affiliate_id,
            'offer_id' => $offer->id,
            'affiliate_link_id' => $affiliateLink->id,
            'transaction_id' => $data['transaction_id'] ?? Str::uuid(),
            'conversion_value' => $data['conversion_value'] ?? 0,
            'commission_amount' => $commissionAmount,
            'commission_model' => $offer->commission_model,
            'status' => 'pending', // Needs approval
            'tracking_method' => $trackingMethod,
            'postback_data' => isset($data['postback']) ? json_encode($data['postback']) : null,
        ]);

        // Create commission
        Commission::create([
            'affiliate_id' => $affiliateLink->affiliate_id,
            'conversion_id' => $conversion->id,
            'offer_id' => $offer->id,
            'amount' => $commissionAmount,
            'status' => 'pending',
        ]);

        // Mark click as converted
        $click->update([
            'is_converted' => true,
            'converted_at' => now(),
        ]);

        // Update link stats
        $affiliateLink->increment('conversion_count');
        $affiliateLink->increment('total_earnings', $commissionAmount);

        // Update offer stats
        $offer->increment('total_conversions');
        $offer->increment('total_revenue', $data['conversion_value'] ?? 0);

        return $conversion;
    }

    /**
     * Calculate commission based on model
     */
    protected function calculateCommission(string $model, float $rate, float $value): float
    {
        return match ($model) {
            'pps' => $rate, // Pay Per Sale - fixed amount
            'ppl' => $rate, // Pay Per Lead - fixed amount
            'revshare' => ($value * $rate) / 100, // Revenue Share - percentage
            default => 0,
        };
    }

    /**
     * Check for fraudulent activity
     */
    protected function checkForFraud(Request $request, AffiliateLink $affiliateLink): array
    {
        $ip = $request->ip();

        // Check for duplicate clicks from same IP within 1 hour
        $recentClicks = Click::where('ip_address', $ip)
            ->where('affiliate_link_id', $affiliateLink->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentClicks > 5) {
            return [
                'is_valid' => false,
                'reason' => 'Too many clicks from same IP',
            ];
        }

        // Check for suspicious user agent
        if (empty($request->userAgent()) || $this->isBotUserAgent($request->userAgent())) {
            return [
                'is_valid' => false,
                'reason' => 'Suspicious user agent',
            ];
        }

        return ['is_valid' => true];
    }

    /**
     * Check if user agent is a bot
     */
    protected function isBotUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        $botPatterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget'];
        
        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set tracking cookie
     */
    protected function setTrackingCookie(Click $click, int $duration): void
    {
        $cookieData = [
            'click_id' => $click->id,
            'affiliate_id' => $click->affiliate_id,
            'offer_id' => $click->offer_id,
            'created_at' => now()->timestamp,
        ];

        Cookie::queue(
            self::COOKIE_NAME,
            json_encode($cookieData),
            $duration * 24 * 60 // Convert days to minutes
        );
    }

    /**
     * Get tracking cookie data
     */
    protected function getTrackingCookie(): ?array
    {
        $cookie = Cookie::get(self::COOKIE_NAME);
        
        if (!$cookie) {
            return null;
        }

        return json_decode($cookie, true);
    }

    /**
     * Get device type
     */
    protected function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device' => 'unknown',
                'browser' => 'unknown',
                'os' => 'unknown',
            ];
        }

        // Detect device type
        $device = 'desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            $device = 'tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            $device = 'mobile';
        }

        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/Edge\\/([0-9\\._]+)/', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome\\/([0-9\\.]+)/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari\\/([0-9\\.]+)/', $userAgent) && !preg_match('/Chrome/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox\\/([0-9\\.]+)/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/MSIE|Trident/', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // Detect OS
        $os = 'Unknown';
        if (preg_match('/Windows NT ([0-9\\.]+)/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X ([0-9\\._]+)/', $userAgent)) {
            $os = 'Mac OS';
        } elseif (preg_match('/Android ([0-9\\.]+)/', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone OS ([0-9\\._]+)/', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Get country code from IP (placeholder - integrate with IP geolocation service)
     */
    protected function getCountryCode(string $ip): ?string
    {
        // TODO: Integrate with IP geolocation API (MaxMind, IPinfo, etc.)
        return null;
    }

    /**
     * Get country name from IP
     */
    protected function getCountryName(string $ip): ?string
    {
        // TODO: Integrate with IP geolocation API
        return null;
    }

    /**
     * Get city from IP
     */
    protected function getCity(string $ip): ?string
    {
        // TODO: Integrate with IP geolocation API
        return null;
    }
}
