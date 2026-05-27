<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\OfferApprovedNotification;
use App\Notifications\OfferAvailableNotification;
use App\Notifications\OfferRejectedNotification;
use App\Notifications\OfferRemovedNotification;
use Illuminate\Http\Request;
use App\Services\CpaleadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OfferController extends Controller
{
    /**
     * Show the form for editing the specified offer.
     */
    public function create()
    {
        $categories = OfferCategory::all();
        $advertisers = User::role('advertiser')->get(['id', 'name']);
        return Inertia::render('Admin/Offers/Create', [
            'categories' => $categories,
            'advertisers' => $advertisers,
            'offerChannels' => [
                ['value' => 'platform', 'label' => 'Platform Advertiser'],
                ['value' => 'cpalead', 'label' => 'CPAlead Network'],
                ['value' => 'network', 'label' => 'Other Network'],
            ],
        ]);
    }

    public function edit(Offer $offer)
    {
        $categories = OfferCategory::all();
        $advertisers = User::role('advertiser')->get(['id', 'name']);
        // Convert targeting arrays to comma strings for editing
        $offer->target_countries = $offer->target_countries ? implode(',', $offer->target_countries) : '';
        $offer->target_devices = $offer->target_devices ? implode(',', $offer->target_devices) : '';
        $offer->target_os = $offer->target_os ? implode(',', $offer->target_os) : '';
        $offer->offer_channel = $offer->offer_channel ?: 'platform';
        $offer->network_name = $offer->network_name ?: ($offer->is_cpalead ? 'cpalead' : null);
        return Inertia::render('Admin/Offers/Edit', [
            'offer' => $offer,
            'categories' => $categories,
            'advertisers' => $advertisers,
            'offerChannels' => [
                ['value' => 'platform', 'label' => 'Platform Advertiser'],
                ['value' => 'cpalead', 'label' => 'CPAlead Network'],
                ['value' => 'network', 'label' => 'Other Network'],
            ],
        ]);
    }

    /**
     * Store a newly created offer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:offer_categories,id',
            'advertiser_id' => 'required|exists:users,id',
            'offer_channel' => 'required|in:platform,cpalead,network',
            'network_name' => 'nullable|string|max:100',
            'network_offer_id' => 'nullable|string|max:255',
            'description' => 'required|string',
            'commission_model' => 'required|in:pps,ppl,revshare',
            'commission_rate' => 'required|numeric|min:0',
            'offer_url' => 'nullable|url',
            'product_image' => 'nullable|url',
            'cpalead_offer_id' => 'nullable|string|max:255',
            'is_cpalead' => 'boolean',
            'target_countries' => 'nullable|string',
            'target_devices' => 'nullable|string',
            'target_os' => 'nullable|string',
            'require_unique_ip' => 'boolean',
            'terms_and_conditions' => 'nullable|string',
            'daily_conversion_cap' => 'nullable|integer|min:0',
            'monthly_conversion_cap' => 'nullable|integer|min:0',
        ]);

        $validated['target_countries'] = $validated['target_countries'] ? array_map('trim', explode(',', $validated['target_countries'])) : null;
        $validated['target_devices'] = $validated['target_devices'] ? array_map('trim', explode(',', $validated['target_devices'])) : null;
        $validated['target_os'] = $validated['target_os'] ? array_map('trim', explode(',', $validated['target_os'])) : null;
        $validated['daily_conversion_cap'] = !empty($validated['daily_conversion_cap']) ? $validated['daily_conversion_cap'] : null;
        $validated['monthly_conversion_cap'] = !empty($validated['monthly_conversion_cap']) ? $validated['monthly_conversion_cap'] : null;

        $validated['is_cpalead'] = $validated['offer_channel'] === 'cpalead';
        if ($validated['offer_channel'] === 'cpalead') {
            $validated['network_name'] = 'cpalead';
            $validated['network_offer_id'] = $validated['cpalead_offer_id'] ?? null;
        }

        $slugBase = Str::slug($validated['name']) ?: 'offer';
        $slug = $slugBase;
        $counter = 1;
        while (Offer::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        $offer = Offer::create($validated);

        return redirect()->route('admin.offers.show', $offer->id)->with('success', 'Offer created successfully.');
    }

    /**
     * Update the specified offer in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:offer_categories,id',
            'advertiser_id' => 'required|exists:users,id',
            'offer_channel' => 'required|in:platform,cpalead,network',
            'network_name' => 'nullable|string|max:100',
            'network_offer_id' => 'nullable|string|max:255',
            'description' => 'required|string',
            'commission_model' => 'required|in:pps,ppl,revshare',
            'commission_rate' => 'required|numeric|min:0',
            'offer_url' => 'nullable|url',
            'product_image' => 'nullable|url',
            'cpalead_offer_id' => 'nullable|string|max:255',
            'is_cpalead' => 'boolean',
            'target_countries' => 'nullable|string',
            'target_devices' => 'nullable|string',
            'target_os' => 'nullable|string',
            'require_unique_ip' => 'boolean',
            'terms_and_conditions' => 'nullable|string',
            'daily_conversion_cap' => 'nullable|integer|min:0',
            'monthly_conversion_cap' => 'nullable|integer|min:0',
        ]);

        // Convert comma strings to arrays for JSON fields
        $validated['target_countries'] = $validated['target_countries'] ? array_map('trim', explode(',', $validated['target_countries'])) : null;
        $validated['target_devices'] = $validated['target_devices'] ? array_map('trim', explode(',', $validated['target_devices'])) : null;
        $validated['target_os'] = $validated['target_os'] ? array_map('trim', explode(',', $validated['target_os'])) : null;
        $validated['daily_conversion_cap'] = !empty($validated['daily_conversion_cap']) ? $validated['daily_conversion_cap'] : null;
        $validated['monthly_conversion_cap'] = !empty($validated['monthly_conversion_cap']) ? $validated['monthly_conversion_cap'] : null;
        $validated['is_cpalead'] = $validated['offer_channel'] === 'cpalead';
        if ($validated['offer_channel'] === 'cpalead') {
            $validated['network_name'] = 'cpalead';
            $validated['network_offer_id'] = $validated['cpalead_offer_id'] ?? null;
        }

        $offer->update($validated);

        return redirect()->route('admin.offers.show', $offer->id)->with('success', 'Offer updated successfully.');
    }

    public function syncCpalead(Request $request, CpaleadService $cpaleadService)
    {
        if (!$cpaleadService->isConfigured()) {
            return back()->with('error', 'CPAlead integration is not configured. Please set the required environment values.');
        }

        if ($request->boolean('disable_missing')) {
            config(['services.cpalead.disable_missing_offers' => true]);
        }

        $rawOffers = $cpaleadService->fetchOffers();
        if (empty($rawOffers)) {
            return back()->with('warning', 'No offers were returned from CPAlead. Check your configuration and API connectivity.');
        }

        $results = $cpaleadService->importOffers($rawOffers);
        $created = collect($results)->where('status', 'created')->count();
        $updated = collect($results)->where('status', 'updated')->count();

        return back()->with('success', "CPAlead sync completed: {$created} new offers, {$updated} updated offers.");
    }

    public function index(Request $request)
    {
        $offers = Offer::with(['advertiser', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->when($request->approval_status, function ($query, $approvalStatus) {
                $query->where('approval_status', $approvalStatus);
            })
            ->when($request->advertiser_id, function ($query, $advertiserId) {
                $query->where('advertiser_id', $advertiserId);
            })
            ->withCount(['clicks', 'conversions'])
            ->latest()
            ->paginate(20);

        $categories = OfferCategory::all();
        $advertisers = User::role('advertiser')->get(['id', 'name']);

        // Get counts for each approval status
        $approvalCounts = [
            'pending' => Offer::pending()->count(),
            'approved' => Offer::approved()->count(),
            'rejected' => Offer::rejected()->count(),
        ];

        return Inertia::render('Admin/Offers/Index', [
            'offers' => $offers,
            'categories' => $categories,
            'advertisers' => $advertisers,
            'approvalCounts' => $approvalCounts,
            'filters' => [
                'search' => $request->search,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'approval_status' => $request->approval_status,
                'advertiser_id' => $request->advertiser_id,
            ],
        ]);
    }

    public function show(Offer $offer)
    {
        $offer->load(['advertiser', 'category', 'affiliateLinks.affiliate', 'clicks', 'conversions', 'reviewer', 'creatives']);

        $stats = [
            'total_clicks' => $offer->clicks()->count(),
            'total_conversions' => $offer->conversions()->where('status', '!=', 'rejected')->count(),
            'total_revenue' => $offer->conversions()->where('status', '!=', 'rejected')->sum('conversion_value'),
            'total_commissions' => $offer->conversions()->where('status', 'approved')->sum('commission_amount'),
            'conversion_rate' => $offer->conversion_rate,
            'active_affiliates' => $offer->affiliateLinks()->where('is_active', true)->count(),
        ];

        return Inertia::render('Admin/Offers/Show', [
            'offer' => $offer,
            'stats' => $stats,
        ]);
    }

    public function toggle(Offer $offer)
    {
        $wasActive = $offer->is_active;
        $isApproved = $offer->approval_status === 'approved';

        $offer->update([
            'is_active' => !$offer->is_active,
        ]);

        $status = $offer->is_active ? 'activated' : 'deactivated';

        // Notify affiliates when an approved offer is deactivated
        if ($wasActive && !$offer->is_active && $isApproved) {
            $affiliates = User::role('affiliate')
                ->where('is_active', true)
                ->get();

            Notification::send($affiliates, new OfferRemovedNotification($offer, 'deactivated'));
        }

        return back()->with('success', "Offer {$status} successfully.");
    }

    public function destroy(Offer $offer)
    {
        $wasApproved = $offer->approval_status === 'approved';
        $offerName = $offer->name;

        // Notify affiliates if offer was approved and visible to them
        if ($wasApproved) {
            $affiliates = User::role('affiliate')
                ->where('is_active', true)
                ->get();

            Notification::send($affiliates, new OfferRemovedNotification($offer, 'deleted'));
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully.');
    }

    public function featured(Request $request, Offer $offer)
    {
        $offer->update([
            'is_featured' => !$offer->is_featured,
        ]);

        $status = $offer->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Offer marked as {$status}.");
    }

    /**
     * Approve an offer
     */
    public function approve(Request $request, Offer $offer)
    {
        if ($offer->approval_status === 'approved') {
            return back()->with('info', 'Offer is already approved.');
        }

        // Validate platform spread percentage and optional RevShare recurring settings
        $validated = $request->validate([
            'platform_spread_percentage'  => 'required|numeric|min:0|max:100',
            'revshare_type'               => 'nullable|in:once,recurring',
            'revshare_recurring_duration' => 'nullable|integer|min:1',
            'revshare_recurring_unit'     => 'nullable|in:month,year',
        ]);

        // Set the platform spread percentage
        $offer->platform_spread_percentage = $validated['platform_spread_percentage'];

        // Apply RevShare recurring settings when applicable
        if ($offer->commission_model === 'revshare') {
            $offer->revshare_type = $validated['revshare_type'] ?? $offer->revshare_type ?? 'once';
            $isRecurring = $offer->revshare_type === 'recurring';
            $offer->revshare_recurring_duration = $isRecurring ? ($validated['revshare_recurring_duration'] ?: null) : null;
            $offer->revshare_recurring_unit     = $isRecurring ? ($validated['revshare_recurring_unit'] ?? 'month') : null;
        }

        // Calculate advertiser and affiliate payouts based on spread
        $offer->calculatePayoutsFromSpread();

        // Recalculate conversion cap now that we know the exact advertiser_payout.
        // Skip for revshare: advertiser_payout is a percentage rate, not a fixed amount,
        // so we cannot derive a conversion cap from budget alone.
        if ($offer->budget_limit && $offer->advertiser_payout > 0 && $offer->commission_model !== 'revshare') {
            $remainingBudget = $offer->budget_limit - $offer->spent_budget;
            $newCap          = $offer->total_conversions + (int) floor($remainingBudget / $offer->advertiser_payout);
            $offer->total_conversion_cap = max($offer->total_conversions, $newCap);
        }

        $offer->update([
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notify advertiser — pass their current balance so the email can warn if low
        $offer->advertiser->notify(new OfferApprovedNotification($offer, (float) $offer->advertiser->advertiser_balance));

        // Notify all active affiliates about new offer
        $affiliates = User::role('affiliate')
            ->where('is_active', true)
            ->get();

        Notification::send($affiliates, new OfferAvailableNotification($offer));

        return back()->with('success', 'Offer approved successfully! Advertiser and affiliates have been notified.');
    }

    /**
     * Reject an offer
     */
    public function reject(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($offer->approval_status === 'rejected') {
            return back()->with('info', 'Offer is already rejected.');
        }

        $wasApproved = $offer->approval_status === 'approved';

        $offer->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'is_active' => false, // Deactivate rejected offers
        ]);

        // Notify advertiser
        $offer->advertiser->notify(new OfferRejectedNotification($offer, $validated['rejection_reason']));

        // Refund any remaining unspent budget back to the advertiser's wallet
        $remainingBudget = $offer->budget_limit - $offer->spent_budget;
        if ($remainingBudget > 0) {
            DB::transaction(function () use ($offer, $remainingBudget) {
                $offer->advertiser->increment('advertiser_balance', $remainingBudget);

                WalletTransaction::create([
                    'user_id'     => $offer->advertiser_id,
                    'amount'      => $remainingBudget,
                    'type'        => 'offer_refund',
                    'status'      => 'success',
                    'offer_id'    => $offer->id,
                    'description' => "Budget refunded — offer rejected: {$offer->name}",
                ]);
            });
        }

        // Notify affiliates if offer was previously approved and available
        if ($wasApproved) {
            $affiliates = User::role('affiliate')
                ->where('is_active', true)
                ->get();

            Notification::send($affiliates, new OfferRemovedNotification($offer, 'rejected'));
        }

        return back()->with('success', 'Offer rejected. Advertiser and affiliates have been notified.');
    }

    /**
     * Verify that an offer's integration (S2S postback or pixel) is working.
     * Returns JSON — called directly via axios from the admin show page.
     */
    public function verifyIntegration(Offer $offer)
    {
        $result = [
            'postback_configured'       => !empty($offer->postback_url),
            'postback_reachable'        => null,
            'postback_status_code'      => null,
            'postback_response_snippet' => null,
            'last_conversion_at'        => null,
            'total_conversions'         => 0,
            'integration_verified'      => false,
        ];

        // Conversion history — total + breakdown by tracking method
        $conversionCount          = $offer->conversions()->count();
        $lastConversion           = $offer->conversions()->latest()->first();
        $result['total_conversions']  = $conversionCount;
        $result['last_conversion_at'] = $lastConversion?->created_at?->toISOString();

        // Breakdown by method so admin can see which integration type has fired
        $result['conversions_by_method'] = $offer->conversions()
            ->selectRaw('tracking_method, COUNT(*) as count')
            ->groupBy('tracking_method')
            ->pluck('count', 'tracking_method')
            ->toArray();

        // Test the postback endpoint if one is configured
        if (!empty($offer->postback_url)) {
            try {
                $testTrackingCode = 'TEST_' . strtoupper(Str::random(8));
                $sep     = str_contains($offer->postback_url, '?') ? '&' : '?';
                $testUrl = $offer->postback_url . $sep . http_build_query([
                    'tracking_code'    => $testTrackingCode,
                    'token'            => 'INTEGRATION_TEST',
                    'conversion_value' => '0',
                    'transaction_id'   => 'TEST_TXN_' . time(),
                ]);

                $response = Http::timeout(10)->get($testUrl);

                $result['postback_reachable']        = true;
                $result['postback_status_code']      = $response->status();
                $result['postback_response_snippet'] = substr($response->body(), 0, 300);
            } catch (\Exception $e) {
                $result['postback_reachable']        = false;
                $result['postback_response_snippet'] = $e->getMessage();

                Log::warning('Admin integration test: postback unreachable', [
                    'offer_id'     => $offer->id,
                    'postback_url' => $offer->postback_url,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        // Considered verified if postback is reachable OR conversions have been received
        $result['integration_verified'] =
            ($result['postback_reachable'] === true) || ($conversionCount > 0);

        return response()->json($result);
    }
}

