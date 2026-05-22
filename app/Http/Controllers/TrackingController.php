<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessClickJob;
use App\Jobs\ProcessConversionJob;
use App\Models\AffiliateLink;
use App\Services\SmartLinkService;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class TrackingController extends Controller
{
    protected SmartLinkService $smartLinkService;
    protected FraudDetectionService $fraudDetectionService;

    public function __construct(SmartLinkService $smartLinkService, FraudDetectionService $fraudDetectionService)
    {
        $this->smartLinkService = $smartLinkService;
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Track click and redirect to offer
     */
    public function track(string $trackingCode, Request $request)
    {
        // Quick validation - get affiliate link
        $affiliateLink = Cache::remember(
            "link:{$trackingCode}",
            now()->addMinutes(10),
            fn() => AffiliateLink::where('tracking_code', $trackingCode)
                ->where('is_active', true)
                ->with('offer')
                ->first()
        );

        if (!$affiliateLink || !$affiliateLink->offer->is_active) {
            abort(404, 'Invalid tracking link');
        }

        // Resolve real visitor IP
        $visitorIp = $request->ip();

        // Geo-IP lookup via ip-api.com (free, cached per IP for 24 h to respect rate limits)
        $geoData = Cache::remember(
            'geo:' . md5($visitorIp),
            now()->addHours(24),
            function () use ($visitorIp) {
                try {
                    $position = Location::get($visitorIp);
                    if ($position) {
                        return [
                            'country_code' => $position->countryCode ?? null,
                            'country_name' => $position->countryName ?? null,
                            'city'         => $position->cityName ?? null,
                            'region'       => $position->regionName ?? null,
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning('Geo-IP lookup failed', ['ip' => $visitorIp, 'error' => $e->getMessage()]);
                }
                return ['country_code' => null, 'country_name' => null, 'city' => null, 'region' => null];
            }
        );

        // Get device data from user agent
        $userAgent = $request->userAgent();
        $deviceData = $this->parseUserAgent($userAgent);

        // ── Targeting Gate ────────────────────────────────────────────────
        $offer = $affiliateLink->offer;

        if (!$offer->isTargetedTo($geoData['country_code'], $deviceData['device'], $deviceData['os'])) {
            Log::info('Click blocked by targeting rules', [
                'offer_id'     => $offer->id,
                'country_code' => $geoData['country_code'],
                'device'       => $deviceData['device'],
                'os'           => $deviceData['os'],
            ]);

            $fallback = $offer->findFallbackOffer();
            if ($fallback) {
                $sep = str_contains($fallback->preview_url, '?') ? '&' : '?';
                return redirect()->away($fallback->preview_url . $sep . 'ref=targeting');
            }
            abort(451, 'This offer is not available in your region or on your device.');
        }

        // Unique-IP conversion enforcement
        if ($offer->require_unique_ip) {
            $alreadyConverted = \App\Models\Click::where('offer_id', $offer->id)
                ->where('ip_address', $visitorIp)
                ->where('is_converted', true)
                ->exists();

            if ($alreadyConverted) {
                abort(409, 'This offer has already been completed from your connection.');
            }
        }
        // ─────────────────────────────────────────────────────────────────

        // Smart Link Selection (if rotation is enabled)
        if ($affiliateLink->enable_rotation && $affiliateLink->rotationGroup) {
            $context = array_merge($geoData, $deviceData, [
                'offer_id' => $affiliateLink->offer_id,
                'affiliate_id' => $affiliateLink->affiliate_id,
            ]);

            $selectedLink = $this->smartLinkService->selectLink($context);

            if ($selectedLink) {
                $affiliateLink = $selectedLink;
                $this->smartLinkService->recordRotation($selectedLink);
            }
        }

        // Dispatch click processing to queue (async) with fraud detection context
        ProcessClickJob::dispatch(
            $affiliateLink->tracking_code,
            $request->ip(),
            $userAgent,
            $request->header('referer'),
            array_merge($geoData, $deviceData, [
                'screen_resolution' => $request->input('sr'),
                'timezone' => $request->input('tz'),
                'language' => $request->input('lang'),
            ])
        );

        // Increment fraud detection counters
        $this->fraudDetectionService->incrementIpClicks($request->ip());

        // Set tracking cookie for conversion attribution
        $cookieData = [
            'tracking_code' => $affiliateLink->tracking_code,
            'affiliate_id' => $affiliateLink->affiliate_id,
            'offer_id' => $affiliateLink->offer_id,
            'timestamp' => now()->timestamp,
        ];

        Cookie::queue(
            'dealsintel_tracking',
            json_encode($cookieData),
            $affiliateLink->offer->cookie_duration * 24 * 60 // Convert days to minutes
        );

        // Redirect immediately (don't wait for queue processing)
        // Append tracking_code to the offer URL so advertisers can capture it
        // for S2S postback attribution (e.g. ?tracking_code=ABC123).

        // Check advertiser wallet balance against the offer's minimum_wallet_required.
        // If insufficient, divert traffic to another offer in the same category.
        if ($offer->minimum_wallet_required > 0) {
            // Load advertiser fresh (not from cache) so balance is always current
            $offer->load('advertiser');

            if ($offer->advertiserHasInsufficientBalance()) {
                Log::info('Offer traffic diverted: advertiser balance below minimum', [
                    'offer_id'                => $offer->id,
                    'minimum_wallet_required' => $offer->minimum_wallet_required,
                    'advertiser_balance'      => $offer->advertiser?->advertiser_balance,
                ]);

                $fallback = $offer->findFallbackOffer();

                if ($fallback) {
                    $sep = str_contains($fallback->preview_url, '?') ? '&' : '?';
                    return redirect()->away($fallback->preview_url . $sep . 'tracking_code=ref_' . urlencode($affiliateLink->tracking_code));
                }

                // No fallback available in this category
                abort(410, 'This offer is temporarily unavailable.');
            }
        }

        // Use offer_url (actual landing page) if set, otherwise fall back to preview_url
        $offerUrl = $offer->offer_url ?: $offer->preview_url;
        $separator = str_contains($offerUrl, '?') ? '&' : '?';
        return redirect()->away($offerUrl . $separator . 'tracking_code=' . urlencode($affiliateLink->tracking_code));
    }

    /**
     * Parse user agent for device information
     */
    protected function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'device' => 'unknown',
                'os' => 'unknown',
                'browser' => 'unknown',
            ];
        }

        $ua = strtolower($userAgent);

        // Detect device type
        $device = 'desktop';
        if (preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i', $ua)) {
            if (preg_match('/(ipad|tablet)/i', $ua)) {
                $device = 'tablet';
            } else {
                $device = 'mobile';
            }
        }

        // Detect OS
        $os = 'unknown';
        if (str_contains($ua, 'windows')) $os = 'windows';
        elseif (str_contains($ua, 'mac')) $os = 'mac';
        elseif (str_contains($ua, 'linux')) $os = 'linux';
        elseif (str_contains($ua, 'android')) $os = 'android';
        elseif (str_contains($ua, 'ios') || str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) $os = 'ios';

        // Detect browser
        $browser = 'unknown';
        if (str_contains($ua, 'edge')) $browser = 'edge';
        elseif (str_contains($ua, 'chrome')) $browser = 'chrome';
        elseif (str_contains($ua, 'firefox')) $browser = 'firefox';
        elseif (str_contains($ua, 'safari')) $browser = 'safari';
        elseif (str_contains($ua, 'opera')) $browser = 'opera';

        return compact('device', 'os', 'browser');
    }

    /**
     * Handle postback conversion tracking (S2S)
     *
     * Security: requires a per-offer shared secret (`token` param).
     * Rate limited to 60 req/min per IP (throttle middleware on the route).
     * Replay attacks prevented via cache-keyed transaction_id deduplication.
     */
    public function postback(Request $request)
    {
        $validated = $request->validate([
            'tracking_code'    => 'required|string|max:100',
            'token'            => 'required|string',
            'transaction_id'   => 'nullable|string|max:255',
            'conversion_value' => 'required|numeric|min:0|max:999999',
            'customer_id'      => 'nullable|string|max:255', // For recurring RevShare subscriptions
        ]);

        // Look up affiliate link to get the offer and its postback_secret
        $affiliateLink = AffiliateLink::where('tracking_code', $validated['tracking_code'])
            ->with('offer')
            ->first();

        // Use constant-time comparison regardless of whether link/offer exists
        // to avoid timing-based oracle attacks
        $expectedSecret = $affiliateLink?->offer?->postback_secret ?? '';
        $providedToken  = $validated['token'];

        if (!$affiliateLink || !$affiliateLink->offer || !hash_equals($expectedSecret, $providedToken)) {
            Log::warning('Postback: authentication failure', [
                'ip'             => $request->ip(),
                'tracking_code'  => $validated['tracking_code'],
            ]);
            // Return 200 with generic message to avoid revealing which part failed
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 401);
        }

        // Enforce commission-model-specific rules:
        // PPS and RevShare require a real sale amount; PPL allows zero (no purchase needed).
        $commissionModel = $affiliateLink->offer->commission_model;
        if (in_array($commissionModel, ['pps', 'revshare']) && (float) $validated['conversion_value'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => strtoupper($commissionModel) . ' offers require conversion_value > 0. '
                    . 'Please pass the actual sale amount (e.g. "conversion_value": 15000).',
            ], 422);
        }

        // For recurring RevShare: check if the customer has reached the duration cap.
        $offer = $affiliateLink->offer;
        if (
            $commissionModel === 'revshare'
            && $offer->revshare_type === 'recurring'
            && !empty($validated['customer_id'])
            && $offer->revshare_recurring_duration !== null
        ) {
            $occurrenceCount = \App\Models\Conversion::where('offer_id', $offer->id)
                ->where('customer_id', $validated['customer_id'])
                ->whereIn('status', ['pending', 'approved', 'paid'])
                ->count();

            if ($occurrenceCount >= $offer->revshare_recurring_duration) {
                Log::info('Postback: recurring revshare duration cap reached', [
                    'offer_id'    => $offer->id,
                    'customer_id' => $validated['customer_id'],
                    'occurrences' => $occurrenceCount,
                    'cap'         => $offer->revshare_recurring_duration,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Recurring commission duration cap reached for this customer. '
                        . "Maximum {$offer->revshare_recurring_duration} {$offer->revshare_recurring_unit}(s) of commissions have been paid.",
                ], 422);
            }
        }

        // Replay attack prevention: deduplicate by transaction_id within 30 days
        if (!empty($validated['transaction_id'])) {
            $cacheKey = 'pb_txn:' . sha1($validated['tracking_code'] . '|' . $validated['transaction_id']);
            if (Cache::has($cacheKey)) {
                Log::info('Postback: duplicate transaction_id skipped', [
                    'tracking_code'  => $validated['tracking_code'],
                    'transaction_id' => $validated['transaction_id'],
                ]);
                return response()->json(['success' => true, 'message' => 'Already processed.']);
            }
            Cache::put($cacheKey, true, now()->addDays(30));
        }

        // Dispatch conversion processing to queue (async)
        ProcessConversionJob::dispatch(
            $validated['tracking_code'],
            $validated['transaction_id'] ?? null,
            (float) $validated['conversion_value'],
            null,
            $request->ip(),
            'postback',
            $validated['customer_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Conversion received.',
        ]);
    }

    /**
     * Handle pixel tracking (image pixel)
     */
    public function pixel(Request $request)
    {
        // Get tracking data from cookie
        $cookieData = json_decode($request->cookie('dealsintel_tracking'), true);

        if ($cookieData && isset($cookieData['tracking_code'])) {
            // Validate required parameters
            $conversionValue = $request->query('value', 0);
            $transactionId = $request->query('txn_id');

            // For PPL offers, fire even with zero value (no purchase required).
            // For PPS and RevShare, a sale amount > 0 is required.
            $pixelLink = AffiliateLink::where('tracking_code', $cookieData['tracking_code'])
                ->with('offer')
                ->first();
            $pixelModel = $pixelLink?->offer?->commission_model;

            if ($pixelModel === 'ppl' || $conversionValue > 0) {
                // Dispatch conversion processing
                ProcessConversionJob::dispatch(
                    $cookieData['tracking_code'],
                    $transactionId,
                    (float) $conversionValue,
                    null,
                    $request->ip(),
                    'cookie'
                );
            }
        }

        // Return 1x1 transparent GIF
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
