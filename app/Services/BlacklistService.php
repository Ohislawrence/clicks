<?php

namespace App\Services;

use App\Models\Blacklist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlacklistService
{
    protected const CACHE_PREFIX = 'blacklist:';
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Check if a value is blacklisted
     */
    public function isBlacklisted(
        string $type,
        string $value,
        ?int $offerId = null,
        ?int $affiliateId = null
    ): array {
        $cacheKey = $this->getCacheKey($type, $value, $offerId, $affiliateId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($type, $value, $offerId, $affiliateId) {
            $query = Blacklist::active()->byType($type);

            // Apply scope filters
            if ($offerId) {
                $query->forOffer($offerId);
            } elseif ($affiliateId) {
                $query->forAffiliate($affiliateId);
            } else {
                $query->global();
            }

            $blacklists = $query->get();

            foreach ($blacklists as $blacklist) {
                if ($this->matchesPattern($value, $blacklist->value, $blacklist->match_type)) {
                    $blacklist->recordHit();

                    return [
                        'is_blacklisted' => true,
                        'blacklist_id' => $blacklist->id,
                        'action' => $blacklist->action,
                        'quality_penalty' => $blacklist->quality_penalty,
                        'severity' => $blacklist->severity,
                        'reason' => $blacklist->reason ?? 'Blacklisted',
                    ];
                }
            }

            return [
                'is_blacklisted' => false,
            ];
        });
    }

    /**
     * Check IP address against blacklist
     */
    public function checkIp(string $ip, ?int $offerId = null, ?int $affiliateId = null): array
    {
        // Check single IP
        $result = $this->isBlacklisted('ip', $ip, $offerId, $affiliateId);
        if ($result['is_blacklisted']) {
            return $result;
        }

        // Check IP ranges
        return $this->checkIpRange($ip, $offerId, $affiliateId);
    }

    /**
     * Check IP against CIDR ranges
     */
    protected function checkIpRange(string $ip, ?int $offerId = null, ?int $affiliateId = null): array
    {
        $query = Blacklist::active()->byType('ip_range');

        if ($offerId) {
            $query->forOffer($offerId);
        } elseif ($affiliateId) {
            $query->forAffiliate($affiliateId);
        } else {
            $query->global();
        }

        $ranges = $query->get();

        foreach ($ranges as $blacklist) {
            if ($this->ipInRange($ip, $blacklist->value)) {
                $blacklist->recordHit();

                return [
                    'is_blacklisted' => true,
                    'blacklist_id' => $blacklist->id,
                    'action' => $blacklist->action,
                    'quality_penalty' => $blacklist->quality_penalty,
                    'severity' => $blacklist->severity,
                    'reason' => $blacklist->reason ?? 'IP range blacklisted',
                ];
            }
        }

        return ['is_blacklisted' => false];
    }

    /**
     * Check user agent against blacklist
     */
    public function checkUserAgent(?string $userAgent, ?int $offerId = null, ?int $affiliateId = null): array
    {
        if (!$userAgent) {
            return ['is_blacklisted' => false];
        }

        return $this->isBlacklisted('user_agent', $userAgent, $offerId, $affiliateId);
    }

    /**
     * Check referrer against blacklist
     */
    public function checkReferrer(?string $referrer, ?int $offerId = null, ?int $affiliateId = null): array
    {
        if (!$referrer) {
            return ['is_blacklisted' => false];
        }

        // Extract domain from referrer URL
        $domain = parse_url($referrer, PHP_URL_HOST);
        if (!$domain) {
            return ['is_blacklisted' => false];
        }

        return $this->isBlacklisted('referrer', $domain, $offerId, $affiliateId);
    }

    /**
     * Check device fingerprint against blacklist
     */
    public function checkDeviceFingerprint(?string $fingerprint, ?int $offerId = null, ?int $affiliateId = null): array
    {
        if (!$fingerprint) {
            return ['is_blacklisted' => false];
        }

        return $this->isBlacklisted('device_fingerprint', $fingerprint, $offerId, $affiliateId);
    }

    /**
     * Check country against blacklist
     */
    public function checkCountry(?string $countryCode, ?int $offerId = null, ?int $affiliateId = null): array
    {
        if (!$countryCode) {
            return ['is_blacklisted' => false];
        }

        return $this->isBlacklisted('country', strtoupper($countryCode), $offerId, $affiliateId);
    }

    /**
     * Check ASN (Autonomous System Number) against blacklist
     */
    public function checkAsn(?string $asn, ?int $offerId = null, ?int $affiliateId = null): array
    {
        if (!$asn) {
            return ['is_blacklisted' => false];
        }

        return $this->isBlacklisted('asn', $asn, $offerId, $affiliateId);
    }

    /**
     * Comprehensive blacklist check for a click
     */
    public function checkClick(array $clickData, ?int $offerId = null, ?int $affiliateId = null): array
    {
        $violations = [];
        $totalPenalty = 0;
        $maxSeverity = 'low';
        $shouldBlock = false;

        // Check IP
        if (!empty($clickData['ip'])) {
            $ipCheck = $this->checkIp($clickData['ip'], $offerId, $affiliateId);
            if ($ipCheck['is_blacklisted']) {
                $violations[] = $ipCheck;
                $totalPenalty += $ipCheck['quality_penalty'];
                $maxSeverity = $this->getHigherSeverity($maxSeverity, $ipCheck['severity']);
                if ($ipCheck['action'] === 'block') {
                    $shouldBlock = true;
                }
            }
        }

        // Check user agent
        if (!empty($clickData['user_agent'])) {
            $uaCheck = $this->checkUserAgent($clickData['user_agent'], $offerId, $affiliateId);
            if ($uaCheck['is_blacklisted']) {
                $violations[] = $uaCheck;
                $totalPenalty += $uaCheck['quality_penalty'];
                $maxSeverity = $this->getHigherSeverity($maxSeverity, $uaCheck['severity']);
                if ($uaCheck['action'] === 'block') {
                    $shouldBlock = true;
                }
            }
        }

        // Check referrer
        if (!empty($clickData['referrer'])) {
            $refCheck = $this->checkReferrer($clickData['referrer'], $offerId, $affiliateId);
            if ($refCheck['is_blacklisted']) {
                $violations[] = $refCheck;
                $totalPenalty += $refCheck['quality_penalty'];
                $maxSeverity = $this->getHigherSeverity($maxSeverity, $refCheck['severity']);
                if ($refCheck['action'] === 'block') {
                    $shouldBlock = true;
                }
            }
        }

        // Check device fingerprint
        if (!empty($clickData['device_fingerprint'])) {
            $fpCheck = $this->checkDeviceFingerprint($clickData['device_fingerprint'], $offerId, $affiliateId);
            if ($fpCheck['is_blacklisted']) {
                $violations[] = $fpCheck;
                $totalPenalty += $fpCheck['quality_penalty'];
                $maxSeverity = $this->getHigherSeverity($maxSeverity, $fpCheck['severity']);
                if ($fpCheck['action'] === 'block') {
                    $shouldBlock = true;
                }
            }
        }

        // Check country
        if (!empty($clickData['country_code'])) {
            $countryCheck = $this->checkCountry($clickData['country_code'], $offerId, $affiliateId);
            if ($countryCheck['is_blacklisted']) {
                $violations[] = $countryCheck;
                $totalPenalty += $countryCheck['quality_penalty'];
                $maxSeverity = $this->getHigherSeverity($maxSeverity, $countryCheck['severity']);
                if ($countryCheck['action'] === 'block') {
                    $shouldBlock = true;
                }
            }
        }

        return [
            'has_violations' => count($violations) > 0,
            'should_block' => $shouldBlock,
            'violations' => $violations,
            'total_penalty' => $totalPenalty,
            'max_severity' => $maxSeverity,
            'violation_count' => count($violations),
        ];
    }

    /**
     * Add entry to blacklist
     */
    public function add(array $data): Blacklist
    {
        $blacklist = Blacklist::create($data);
        
        // Clear relevant caches
        $this->clearCache($blacklist->type);
        
        return $blacklist;
    }

    /**
     * Update blacklist entry
     */
    public function update(Blacklist $blacklist, array $data): bool
    {
        $updated = $blacklist->update($data);
        
        if ($updated) {
            $this->clearCache($blacklist->type);
        }
        
        return $updated;
    }

    /**
     * Delete blacklist entry
     */
    public function delete(Blacklist $blacklist): bool
    {
        $type = $blacklist->type;
        $deleted = $blacklist->delete();
        
        if ($deleted) {
            $this->clearCache($type);
        }
        
        return $deleted;
    }

    /**
     * Import blacklist from array
     */
    public function import(array $entries, int $createdBy): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($entries as $index => $entry) {
            try {
                $this->add(array_merge($entry, ['created_by' => $createdBy]));
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row {$index}: {$e->getMessage()}";
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Get blacklist statistics
     */
    public function getStats(): array
    {
        return [
            'total' => Blacklist::count(),
            'active' => Blacklist::active()->count(),
            'by_type' => [
                'ip' => Blacklist::byType('ip')->count(),
                'ip_range' => Blacklist::byType('ip_range')->count(),
                'user_agent' => Blacklist::byType('user_agent')->count(),
                'referrer' => Blacklist::byType('referrer')->count(),
                'device_fingerprint' => Blacklist::byType('device_fingerprint')->count(),
                'country' => Blacklist::byType('country')->count(),
                'asn' => Blacklist::byType('asn')->count(),
            ],
            'by_severity' => [
                'low' => Blacklist::where('severity', 'low')->count(),
                'medium' => Blacklist::where('severity', 'medium')->count(),
                'high' => Blacklist::where('severity', 'high')->count(),
                'critical' => Blacklist::where('severity', 'critical')->count(),
            ],
            'top_hits' => Blacklist::orderBy('hit_count', 'desc')->limit(10)->get([
                'id', 'type', 'value', 'hit_count', 'severity', 'reason'
            ]),
        ];
    }

    /**
     * Match value against pattern
     */
    protected function matchesPattern(string $value, string $pattern, string $matchType): bool
    {
        return match ($matchType) {
            'exact' => strcasecmp($value, $pattern) === 0,
            'contains' => stripos($value, $pattern) !== false,
            'regex' => @preg_match($pattern, $value) === 1,
            'wildcard' => fnmatch($pattern, $value, FNM_CASEFOLD),
            default => false,
        };
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function ipInRange(string $ip, string $cidr): bool
    {
        if (strpos($cidr, '/') === false) {
            return $ip === $cidr;
        }

        [$subnet, $mask] = explode('/', $cidr);
        
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int) $mask);
        
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Get higher severity level
     */
    protected function getHigherSeverity(string $current, string $new): string
    {
        $levels = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        return ($levels[$new] ?? 0) > ($levels[$current] ?? 0) ? $new : $current;
    }

    /**
     * Get cache key
     */
    protected function getCacheKey(string $type, string $value, ?int $offerId, ?int $affiliateId): string
    {
        $scope = $offerId ? "offer:{$offerId}" : ($affiliateId ? "affiliate:{$affiliateId}" : 'global');
        return self::CACHE_PREFIX . "{$type}:{$scope}:" . md5($value);
    }

    /**
     * Clear cache for type
     */
    protected function clearCache(string $type): void
    {
        Cache::tags(['blacklist', "blacklist:{$type}"])->flush();
    }
}