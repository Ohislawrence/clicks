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
        
        // Get basic geo data (you can integrate with a geo-IP service here)
        $geoData = [
            'country_code' => null,
            'country_name' => null,
            'city' => null,
            'region' => null,
        ];
        
        // Get device data from user agent
        $userAgent = $request->userAgent();
        $deviceData = $this->parseUserAgent($userAgent);
        
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
        return redirect()->away($affiliateLink->offer->preview_url);
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
     */
    public function postback(Request $request)
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string',
            'transaction_id' => 'nullable|string',
            'conversion_value' => 'required|numeric|min:0',
            'postback' => 'nullable|array',
        ]);

        // Dispatch conversion processing to queue (async)
        ProcessConversionJob::dispatch(
            $validated['tracking_code'],
            $validated['transaction_id'] ?? null,
            (float) $validated['conversion_value'],
            $validated['postback'] ?? null,
            $request->ip(),
            'postback'
        );

        // Return immediate response (processing happens in background)
        return response()->json([
            'success' => true,
            'message' => 'Conversion is being processed',
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

            if ($conversionValue > 0) {
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
