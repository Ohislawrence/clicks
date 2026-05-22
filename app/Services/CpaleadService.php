<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\OfferCategory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CpaleadService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected int $defaultCookieDuration;
    protected float $platformSpreadPercentage;
    protected bool $autoApprove;
    protected int $advertiserId;
    protected bool $disableMissingOffers;

    public function __construct()
    {
        $this->apiKey = config('services.cpalead.api_key');
        $this->baseUrl = rtrim(config('services.cpalead.base_url', ''), '/');
        $this->defaultCookieDuration = (int) config('services.cpalead.default_cookie_duration', 30);
        $this->platformSpreadPercentage = (float) config('services.cpalead.platform_spread_percentage', 10);
        $this->autoApprove = filter_var(config('services.cpalead.auto_approve', true), FILTER_VALIDATE_BOOLEAN);
        $this->advertiserId = (int) config('services.cpalead.advertiser_id');
        $this->disableMissingOffers = filter_var(config('services.cpalead.disable_missing_offers', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->baseUrl) && !empty($this->advertiserId);
    }

    public function fetchOffers(array $params = []): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/offers', $params);

            if (!$response->successful()) {
                Log::warning('CpaleadService: failed to fetch offers', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $payload = $response->json();
            if (is_array($payload) && isset($payload['data'])) {
                return $payload['data'];
            }

            return is_array($payload) ? $payload : [];
        } catch (\Throwable $exception) {
            Log::error('CpaleadService exception while fetching offers', [
                'message' => $exception->getMessage(),
            ]);
            return [];
        }
    }

    public function importOffers(array $rawOffers): array
    {
        $results = [];
        $importedIds = [];

        foreach ($rawOffers as $rawOffer) {
            $mapped = $this->mapCpaleadOffer($rawOffer);
            if (empty($mapped['cpalead_offer_id']) || empty($mapped['name'])) {
                continue;
            }
            $importedIds[] = $mapped['cpalead_offer_id'];

            $existing = Offer::where('cpalead_offer_id', $mapped['cpalead_offer_id'])->first();
            if ($existing) {
                $existing->update($mapped);
                $results[] = ['status' => 'updated', 'offer' => $existing];
                continue;
            }

            $mapped['slug'] = $this->generateUniqueSlug($mapped['name'], $mapped['cpalead_offer_id']);
            $offer = Offer::create($mapped);
            $results[] = ['status' => 'created', 'offer' => $offer];
        }

        if ($this->disableMissingOffers && count($importedIds) > 0) {
            $this->disableMissingCpaleadOffers($importedIds);
        }

        return $results;
    }

    protected function mapCpaleadOffer(array $raw): array
    {
        $offerId = $raw['id'] ?? $raw['offer_id'] ?? null;
        $name = $raw['name'] ?? $raw['title'] ?? null;
        $description = $raw['description'] ?? $raw['offer_description'] ?? $raw['terms'] ?? '';
        $previewUrl = $raw['preview_url'] ?? $raw['preview_link'] ?? $raw['landing_page'] ?? $raw['offer_url'] ?? null;
        $offerUrl = $raw['offer_url'] ?? $raw['redirect_url'] ?? $previewUrl;
        $thumbnail = $raw['thumbnail'] ?? $raw['image_url'] ?? $raw['product_image'] ?? null;
        $commissionRate = (float) ($raw['payout'] ?? $raw['commission_rate'] ?? $raw['amount'] ?? 0);
        $commissionModel = $this->normalizeCommissionModel($raw['payout_type'] ?? $raw['commission_model'] ?? $raw['type'] ?? 'pps');
        $targetCountries = $this->normalizeCountryList($raw['target_countries'] ?? $raw['countries'] ?? $raw['country'] ?? null);
        $targetDevices = $this->normalizeDeviceList($raw['target_devices'] ?? $raw['devices'] ?? null);
        $targetOs = $this->normalizeOsList($raw['target_os'] ?? $raw['operating_systems'] ?? null);
        $categoryName = $raw['category'] ?? $raw['offer_category'] ?? null;
        $categoryId = $this->resolveCategoryId($categoryName);

        $affiliatePayout = $commissionRate;
        $advertiserPayout = $commissionRate;
        if ($this->platformSpreadPercentage > 0) {
            if ($commissionModel === 'revshare') {
                $advertiserPayout = $commissionRate + $this->platformSpreadPercentage;
            } else {
                $advertiserPayout = $commissionRate + ($commissionRate * $this->platformSpreadPercentage / 100);
            }
        }

        return [
            'advertiser_id' => $this->advertiserId,
            'offer_channel' => 'cpalead',
            'network_name' => 'cpalead',
            'network_offer_id' => $offerId,
            'name' => $name,
            'description' => $description,
            'slug' => '',
            'preview_url' => $previewUrl,
            'offer_url' => $offerUrl,
            'thumbnail' => $thumbnail,
            'commission_model' => $commissionModel,
            'commission_rate' => $commissionRate,
            'affiliate_payout' => $affiliatePayout,
            'advertiser_payout' => $advertiserPayout,
            'platform_spread_percentage' => $this->platformSpreadPercentage,
            'cookie_duration' => (int) ($raw['cookie_duration'] ?? $raw['cookie'] ?? $this->defaultCookieDuration),
            'access_type' => 'open',
            'is_active' => true,
            'approval_status' => $this->autoApprove ? 'approved' : 'pending',
            'target_countries' => $targetCountries,
            'target_devices' => $targetDevices,
            'target_os' => $targetOs,
            'terms_and_conditions' => $raw['terms_and_conditions'] ?? $raw['offer_terms'] ?? null,
            'product_image' => $thumbnail,
            'cpalead_offer_id' => $offerId,
            'is_cpalead' => true,
            'category_id' => $categoryId,
            'require_unique_ip' => filter_var($raw['require_unique_ip'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    protected function normalizeCommissionModel(string $value): string
    {
        $key = strtolower(trim($value));
        return match ($key) {
            'cpa', 'pps', 'sale', 'pay per sale' => 'pps',
            'cpl', 'ppl', 'lead', 'pay per lead' => 'ppl',
            'revshare', 'revenue share', 'rev-share' => 'revshare',
            default => 'pps',
        };
    }

    protected function normalizeCountryList($countries): ?array
    {
        if (empty($countries)) {
            return null;
        }

        if (is_string($countries)) {
            $countries = preg_split('/[\s,|;]+/', $countries, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($countries)) {
            return null;
        }

        $normalized = array_values(array_filter(array_map(function ($country) {
            $code = strtoupper(trim($country));
            return strlen($code) === 2 ? $code : null;
        }, $countries)));

        return !empty($normalized) ? array_unique($normalized) : null;
    }

    protected function normalizeDeviceList($devices): ?array
    {
        if (empty($devices)) {
            return null;
        }

        if (is_string($devices)) {
            $devices = preg_split('/[\s,|;]+/', $devices, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($devices)) {
            return null;
        }

        $normalized = array_values(array_filter(array_map(function ($device) {
            $device = strtolower(trim($device));
            return match ($device) {
                'mobile', 'phone', 'android', 'ios' => 'mobile',
                'desktop', 'pc', 'windows', 'mac', 'linux' => 'desktop',
                'tablet', 'ipad' => 'tablet',
                default => null,
            };
        }, $devices)));

        return !empty($normalized) ? array_unique($normalized) : null;
    }

    protected function normalizeOsList($oses): ?array
    {
        if (empty($oses)) {
            return null;
        }

        if (is_string($oses)) {
            $oses = preg_split('/[\s,|;]+/', $oses, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($oses)) {
            return null;
        }

        $normalized = array_values(array_filter(array_map(function ($os) {
            $os = strtolower(trim($os));
            return match ($os) {
                'windows' => 'windows',
                'mac', 'macos', 'osx' => 'mac',
                'linux' => 'linux',
                'android' => 'android',
                'ios', 'iphone', 'ipad' => 'ios',
                default => null,
            };
        }, $oses)));

        return !empty($normalized) ? array_unique($normalized) : null;
    }

    protected function resolveCategoryId(?string $categoryName): ?int
    {
        if (empty($categoryName)) {
            return null;
        }

        $slug = Str::slug($categoryName);

        $category = OfferCategory::firstOrCreate([
            'slug' => $slug,
        ], [
            'name' => $categoryName,
            'is_active' => true,
        ]);

        return $category->id;
    }

    protected function generateUniqueSlug(string $name, string $externalOfferId): string
    {
        $base = Str::slug($name) ?: 'cpalead-offer';
        $slug = $base;
        $counter = 1;

        while (Offer::where('slug', $slug)
            ->where('cpalead_offer_id', '!=', $externalOfferId)
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected function disableMissingCpaleadOffers(array $importedIds): void
    {
        Offer::where('is_cpalead', true)
            ->whereNotIn('cpalead_offer_id', $importedIds)
            ->update(['is_active' => false]);
    }
}
