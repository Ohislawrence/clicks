<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\User;
use App\Notifications\BlacklistHitNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class FraudDetectionService
{
    // Quality score thresholds
    const QUALITY_EXCELLENT = 80;
    const QUALITY_GOOD = 60;
    const QUALITY_FAIR = 40;
    const QUALITY_POOR = 20;
    
    // Risk levels
    const RISK_LOW = 'low';
    const RISK_MEDIUM = 'medium';
    const RISK_HIGH = 'high';
    const RISK_CRITICAL = 'critical';
    
    protected BlacklistService $blacklistService;
    
    public function __construct(BlacklistService $blacklistService)
    {
        $this->blacklistService = $blacklistService;
    }
    
    /**
     * Analyze click and assign quality score
     */
    public function analyzeClick(Click $click, array $context = []): array
    {
        $indicators = [];
        $score = 100; // Start with perfect score
        
        // 0. Check blacklist FIRST (can block or penalize)
        $blacklistCheck = $this->blacklistService->checkClick([
            'ip' => $click->ip_address,
            'user_agent' => $click->user_agent,
            'referrer' => $click->referrer,
            'device_fingerprint' => $click->device_fingerprint,
            'country_code' => $click->country_code,
        ], $click->offer_id, $click->affiliate_id);
        
        if ($blacklistCheck['has_violations']) {
            // Send notification for high/critical severity violations
            $this->notifyBlacklistViolations($blacklistCheck, [
                'ip' => $click->ip_address,
                'user_agent' => $click->user_agent,
                'referrer' => $click->referrer,
                'country_code' => $click->country_code,
                'offer_id' => $click->offer_id,
                'affiliate_id' => $click->affiliate_id,
            ]);
            
            // Apply penalties from blacklist
            $score -= $blacklistCheck['total_penalty'];
            
            foreach ($blacklistCheck['violations'] as $violation) {
                $indicators[] = "Blacklisted: {$violation['reason']}";
            }
            
            // If should block, set score to 0
            if ($blacklistCheck['should_block']) {
                $indicators[] = 'BLOCKED by blacklist';
                return [
                    'quality_score' => 0,
                    'risk_level' => self::RISK_CRITICAL,
                    'fraud_indicators' => $indicators,
                    'needs_manual_review' => true,
                    'blacklist_violations' => $blacklistCheck['violations'],
                ];
            }
        }
        
        // 1. IP Analysis (30 points max deduction)
        $ipScore = $this->analyzeIp($click->ip_address, $indicators);
        $score -= (30 - $ipScore);
        
        // 2. User Agent Analysis (20 points max deduction)
        $uaScore = $this->analyzeUserAgent($click->user_agent, $indicators);
        $score -= (20 - $uaScore);
        
        // 3. Click Velocity Analysis (20 points max deduction)
        $velocityScore = $this->analyzeClickVelocity($click, $indicators);
        $score -= (20 - $velocityScore);
        
        // 4. Device Fingerprint Analysis (15 points max deduction)
        $fingerprintScore = $this->analyzeDeviceFingerprint($click, $context, $indicators);
        $score -= (15 - $fingerprintScore);
        
        // 5. Referrer Analysis (15 points max deduction)
        $referrerScore = $this->analyzeReferrer($click->referrer, $indicators);
        $score -= (15 - $referrerScore);
        
        $score = max(0, min(100, $score));
        
        // Determine risk level
        $riskLevel = $this->calculateRiskLevel($score, $indicators);
        
        $result = [
            'quality_score' => (int) $score,
            'risk_level' => $riskLevel,
            'fraud_indicators' => $indicators,
            'needs_manual_review' => $riskLevel === self::RISK_CRITICAL || $score < self::QUALITY_POOR,
        ];
        
        // Include blacklist info if violations exist
        if ($blacklistCheck['has_violations']) {
            $result['blacklist_violations'] = $blacklistCheck['violations'];
        }
        
        return $result;
    }
    
    /**
     * Analyze IP address for fraud indicators
     */
    protected function analyzeIp(string $ipAddress, array &$indicators): int
    {
        $score = 30;
        
        // Check if IP is in our cache (repeated offender)
        $ipReputation = Cache::get("ip_reputation:{$ipAddress}", 100);
        
        if ($ipReputation < 50) {
            $indicators[] = 'Low IP reputation';
            $score -= 10;
        }
        
        // Check click rate from this IP (rate limiting check)
        $recentClicks = Cache::get("ip_clicks:{$ipAddress}:hour", 0);
        
        if ($recentClicks > 50) {
            $indicators[] = 'High click rate from IP';
            $score -= 15;
        } elseif ($recentClicks > 20) {
            $indicators[] = 'Elevated click rate from IP';
            $score -= 5;
        }
        
        // Check for known VPN/Proxy (would require external API)
        // For now, using simple checks
        if ($this->isKnownVpnRange($ipAddress)) {
            $indicators[] = 'VPN/Proxy detected';
            $score -= 5;
        }
        
        return max(0, $score);
    }
    
    /**
     * Analyze user agent for bot indicators
     */
    protected function analyzeUserAgent(?string $userAgent, array &$indicators): int
    {
        $score = 20;
        
        if (empty($userAgent)) {
            $indicators[] = 'Missing user agent';
            return 0;
        }
        
        // Common bot signatures
        $botSignatures = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python', 'java', 'http', 'ruby', 'perl', 'go-http',
        ];
        
        $lowerUa = strtolower($userAgent);
        
        foreach ($botSignatures as $signature) {
            if (str_contains($lowerUa, $signature)) {
                $indicators[] = 'Bot signature detected';
                $score -= 15;
                break;
            }
        }
        
        // Check for suspicious patterns
        if (strlen($userAgent) < 20) {
            $indicators[] = 'Suspicious user agent length';
            $score -= 5;
        }
        
        // Check for common browsers
        $commonBrowsers = ['chrome', 'firefox', 'safari', 'edge', 'opera'];
        $hasCommonBrowser = false;
        
        foreach ($commonBrowsers as $browser) {
            if (str_contains($lowerUa, $browser)) {
                $hasCommonBrowser = true;
                break;
            }
        }
        
        if (!$hasCommonBrowser) {
            $indicators[] = 'Uncommon browser';
            $score -= 3;
        }
        
        return max(0, $score);
    }
    
    /**
     * Analyze click velocity and patterns
     */
    protected function analyzeClickVelocity(Click $click, array &$indicators): int
    {
        $score = 20;
        
        // Get recent clicks from same affiliate link
        $recentClicks = Click::where('affiliate_link_id', $click->affiliate_link_id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();
        
        if ($recentClicks > 100) {
            $indicators[] = 'Abnormally high click velocity';
            $score -= 15;
        } elseif ($recentClicks > 50) {
            $indicators[] = 'High click velocity';
            $score -= 8;
        } elseif ($recentClicks > 25) {
            $indicators[] = 'Elevated click velocity';
            $score -= 3;
        }
        
        // Check for click patterns (same time intervals)
        if ($this->hasUniformClickPattern($click->affiliate_link_id)) {
            $indicators[] = 'Uniform click pattern detected';
            $score -= 5;
        }
        
        return max(0, $score);
    }
    
    /**
     * Analyze device fingerprint
     */
    protected function analyzeDeviceFingerprint(Click $click, array $context, array &$indicators): int
    {
        $score = 15;
        
        // Generate device fingerprint if provided in context
        if (isset($context['screen_resolution'], $context['timezone'], $context['language'])) {
            $fingerprint = $this->generateDeviceFingerprint($context);
            
            // Check if this fingerprint has generated many clicks
            $fingerprintClicks = Cache::get("fingerprint_clicks:{$fingerprint}:day", 0);
            
            if ($fingerprintClicks > 100) {
                $indicators[] = 'Device fingerprint seen too many times';
                $score -= 10;
            } elseif ($fingerprintClicks > 50) {
                $indicators[] = 'Device fingerprint moderately repeated';
                $score -= 5;
            }
        } else {
            $indicators[] = 'Missing device fingerprint data';
            $score -= 5;
        }
        
        return max(0, $score);
    }
    
    /**
     * Analyze referrer for suspicious patterns
     */
    protected function analyzeReferrer(?string $referrer, array &$indicators): int
    {
        $score = 15;
        
        if (empty($referrer)) {
            $indicators[] = 'Missing referrer';
            $score -= 5;
            return max(0, $score);
        }
        
        // Check for suspicious referrer patterns
        $suspiciousPatterns = [
            'localhost', '127.0.0.1', 'iframe', 'popup',
            'click.php', 'redirect.php', 'traffic', 'bot',
        ];
        
        $lowerReferrer = strtolower($referrer);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (str_contains($lowerReferrer, $pattern)) {
                $indicators[] = 'Suspicious referrer pattern';
                $score -= 8;
                break;
            }
        }
        
        // Check for traffic broker domains (would need a list)
        if ($this->isKnownTrafficBroker($referrer)) {
            $indicators[] = 'Known traffic broker';
            $score -= 5;
        }
        
        return max(0, $score);
    }
    
    /**
     * Calculate risk level based on score and indicators
     */
    protected function calculateRiskLevel(int $score, array $indicators): string
    {
        $criticalIndicators = [
            'Bot signature detected',
            'Abnormally high click velocity',
            'Missing user agent',
        ];
        
        // Check for critical indicators
        foreach ($criticalIndicators as $critical) {
            if (in_array($critical, $indicators)) {
                return self::RISK_CRITICAL;
            }
        }
        
        if ($score >= self::QUALITY_EXCELLENT) {
            return self::RISK_LOW;
        } elseif ($score >= self::QUALITY_GOOD) {
            return self::RISK_LOW;
        } elseif ($score >= self::QUALITY_FAIR) {
            return self::RISK_MEDIUM;
        } elseif ($score >= self::QUALITY_POOR) {
            return self::RISK_HIGH;
        } else {
            return self::RISK_CRITICAL;
        }
    }
    
    /**
     * Analyze conversion for fraud
     */
    public function analyzeConversion(Conversion $conversion, Click $click): array
    {
        $indicators = [];
        $score = $click->quality_score ?? 50;
        
        // 1. Time to conversion analysis
        $timeToConversion = $conversion->created_at->diffInSeconds($click->created_at);
        
        if ($timeToConversion < 5) {
            $indicators[] = 'Conversion too fast (< 5 seconds)';
            $score -= 20;
        } elseif ($timeToConversion < 15) {
            $indicators[] = 'Conversion very fast (< 15 seconds)';
            $score -= 10;
        }
        
        // 2. Check for duplicate conversions
        $duplicates = Conversion::where('click_id', '!=', $click->id)
            ->where('transaction_id', $conversion->transaction_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        if ($duplicates > 0) {
            $indicators[] = 'Duplicate transaction ID';
            $score -= 25;
        }
        
        // 3. Check affiliate conversion pattern
        $affiliateConversions = Conversion::where('affiliate_id', $conversion->affiliate_id)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        if ($affiliateConversions > 20) {
            $indicators[] = 'High conversion rate from affiliate';
            $score -= 15;
        }
        
        $score = max(0, min(100, $score));
        $riskLevel = $this->calculateRiskLevel($score, array_merge($click->fraud_indicators ?? [], $indicators));
        
        return [
            'quality_score' => (int) $score,
            'risk_level' => $riskLevel,
            'fraud_indicators' => $indicators,
            'time_to_conversion' => $timeToConversion,
            'needs_manual_review' => $riskLevel === self::RISK_CRITICAL || $score < self::QUALITY_POOR,
        ];
    }
    
    /**
     * Update IP reputation based on behavior
     */
    public function updateIpReputation(string $ipAddress, bool $positiveAction): void
    {
        $reputation = Cache::get("ip_reputation:{$ipAddress}", 50);
        
        if ($positiveAction) {
            $reputation = min(100, $reputation + 5);
        } else {
            $reputation = max(0, $reputation - 10);
        }
        
        Cache::put("ip_reputation:{$ipAddress}", $reputation, now()->addDays(30));
    }
    
    /**
     * Increment IP click counter
     */
    public function incrementIpClicks(string $ipAddress): void
    {
        $key = "ip_clicks:{$ipAddress}:hour";
        $clicks = Cache::get($key, 0);
        Cache::put($key, $clicks + 1, now()->addHour());
    }
    
    /**
     * Increment device fingerprint counter
     */
    public function incrementFingerprintClicks(string $fingerprint): void
    {
        $key = "fingerprint_clicks:{$fingerprint}:day";
        $clicks = Cache::get($key, 0);
        Cache::put($key, $clicks + 1, now()->addDay());
    }
    
    /**
     * Generate device fingerprint from context
     */
    protected function generateDeviceFingerprint(array $context): string
    {
        $data = implode('|', [
            $context['screen_resolution'] ?? '',
            $context['timezone'] ?? '',
            $context['language'] ?? '',
            $context['platform'] ?? '',
            $context['color_depth'] ?? '',
        ]);
        
        return hash('sha256', $data);
    }
    
    /**
     * Check if IP is in known VPN range
     */
    protected function isKnownVpnRange(string $ipAddress): bool
    {
        // This would typically check against a database or API
        // For now, simple implementation
        $knownVpnRanges = Cache::get('known_vpn_ranges', []);
        
        foreach ($knownVpnRanges as $range) {
            if (str_starts_with($ipAddress, $range)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if domain is a known traffic broker
     */
    protected function isKnownTrafficBroker(string $referrer): bool
    {
        $brokers = [
            'popads.net',
            'propellerads.com',
            'popunder.net',
            'exoclick.com',
        ];
        
        foreach ($brokers as $broker) {
            if (str_contains($referrer, $broker)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for uniform click patterns (bot behavior)
     */
    protected function hasUniformClickPattern(int $affiliateLinkId): bool
    {
        $clicks = Click::where('affiliate_link_id', $affiliateLinkId)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->orderBy('created_at')
            ->pluck('created_at')
            ->toArray();
        
        if (count($clicks) < 5) {
            return false;
        }
        
        // Calculate intervals between clicks
        $intervals = [];
        for ($i = 1; $i < count($clicks); $i++) {
            $diff = Carbon::parse($clicks[$i])->diffInSeconds(Carbon::parse($clicks[$i - 1]));
            $intervals[] = $diff;
        }
        
        // Check if intervals are too uniform (variance < 2 seconds)
        $avgInterval = array_sum($intervals) / count($intervals);
        $variance = 0;
        
        foreach ($intervals as $interval) {
            $variance += pow($interval - $avgInterval, 2);
        }
        
        $variance = $variance / count($intervals);
        $stdDev = sqrt($variance);
        
        // If standard deviation is very low, pattern is uniform (likely bot)
        return $stdDev < 2;
    }
    
    /**
     * Get fraud statistics for a period
     */
    public function getFraudStats(Carbon $from, Carbon $to): array
    {
        $totalClicks = Click::whereBetween('created_at', [$from, $to])->count();
        
        if ($totalClicks === 0) {
            return [
                'total_clicks' => 0,
                'fraud_rate' => 0,
                'average_quality_score' => 0,
                'risk_breakdown' => [],
            ];
        }
        
        $fraudulentClicks = Click::whereBetween('created_at', [$from, $to])
            ->where('is_valid', false)
            ->count();
        
        $avgQualityScore = Click::whereBetween('created_at', [$from, $to])
            ->avg('quality_score');
        
        $riskBreakdown = [
            'low' => Click::whereBetween('created_at', [$from, $to])->where('risk_level', 'low')->count(),
            'medium' => Click::whereBetween('created_at', [$from, $to])->where('risk_level', 'medium')->count(),
            'high' => Click::whereBetween('created_at', [$from, $to])->where('risk_level', 'high')->count(),
            'critical' => Click::whereBetween('created_at', [$from, $to])->where('risk_level', 'critical')->count(),
        ];
        
        return [
            'total_clicks' => $totalClicks,
            'fraudulent_clicks' => $fraudulentClicks,
            'fraud_rate' => round(($fraudulentClicks / $totalClicks) * 100, 2),
            'average_quality_score' => round($avgQualityScore, 2),
            'risk_breakdown' => $riskBreakdown,
            'risk_percentage' => [
                'low' => round(($riskBreakdown['low'] / $totalClicks) * 100, 2),
                'medium' => round(($riskBreakdown['medium'] / $totalClicks) * 100, 2),
                'high' => round(($riskBreakdown['high'] / $totalClicks) * 100, 2),
                'critical' => round(($riskBreakdown['critical'] / $totalClicks) * 100, 2),
            ],
        ];
    }
    
    /**
     * Send notification for blacklist violations
     * Only sends for high/critical severity violations
     */
    public function notifyBlacklistViolations(array $blacklistCheck, array $clickData): void
    {
        // Only notify for high/critical severity violations
        if (!in_array($blacklistCheck['max_severity'], ['high', 'critical'])) {
            return;
        }
        
        // Check notification throttle to avoid spam
        $cacheKey = "blacklist_notification:{$clickData['ip']}:" . date('Y-m-d-H');
        if (Cache::has($cacheKey)) {
            return; // Already notified about this IP this hour
        }
        
        // Get admin users to notify
        $admins = User::role('admin')->get();
        
        if ($admins->isEmpty()) {
            Log::warning('No admin users found to notify about blacklist violation', [
                'violations' => $blacklistCheck['violations'],
                'click_data' => $clickData,
            ]);
            return;
        }
        
        // Send notification
        try {
            Notification::send(
                $admins,
                new BlacklistHitNotification(
                    $blacklistCheck['violations'],
                    $clickData,
                    $blacklistCheck['max_severity'],
                    $blacklistCheck['should_block']
                )
            );
            
            // Cache to prevent duplicate notifications for same IP within the hour
            Cache::put($cacheKey, true, 3600);
            
            Log::info('Blacklist violation notification sent', [
                'severity' => $blacklistCheck['max_severity'],
                'ip' => $clickData['ip'],
                'violation_count' => count($blacklistCheck['violations']),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send blacklist notification', [
                'error' => $e->getMessage(),
                'violations' => $blacklistCheck['violations'],
            ]);
        }
    }
}
