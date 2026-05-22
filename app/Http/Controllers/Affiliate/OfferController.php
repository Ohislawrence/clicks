<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\OfferAccessRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::with(['category', 'advertiser'])
            ->where('is_active', true)
            ->approved(); // Only show approved offers to affiliates

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by commission model
        if ($request->filled('model')) {
            $query->where('commission_model', $request->model);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by access type
        if ($request->filled('access_type')) {
            $query->where('access_type', $request->access_type);
        }

        // Filter by min commission
        if ($request->filled('min_commission') && is_numeric($request->min_commission)) {
            $query->where('commission_rate', '>=', (float) $request->min_commission);
        }

        // Filter by max commission
        if ($request->filled('max_commission') && is_numeric($request->max_commission)) {
            $query->where('commission_rate', '<=', (float) $request->max_commission);
        }

        // Filter by target country — show worldwide (null) OR offers that include this country
        if ($request->filled('country')) {
            $code = strtoupper($request->country);
            $query->where(function ($q) use ($code) {
                $q->whereNull('target_countries')
                  ->orWhereJsonContains('target_countries', $code);
            });
        }

        // Filter by target device — show all-device (null) OR offers that include this device
        if ($request->filled('device')) {
            $device = strtolower($request->device);
            $query->where(function ($q) use ($device) {
                $q->whereNull('target_devices')
                  ->orWhereJsonContains('target_devices', $device);
            });
        }

        // Filter by offer source
        if ($request->filled('source')) {
            if ($request->source === 'cpalead') {
                $query->where('is_cpalead', true);
            } elseif ($request->source === 'native') {
                $query->where(function ($q) {
                    $q->whereNull('is_cpalead')
                      ->orWhere('is_cpalead', false);
                });
            }
        }

        // Filter accessible offers
        $user = $request->user();
        $query->where(function ($q) use ($user) {
            // Open offers
            $q->where('access_type', 'open')
              // Or private offers the user has been approved for
              ->orWhereHas('accessRequests', function ($query) use ($user) {
                  $query->where('affiliate_id', $user->id)
                        ->where('status', 'approved');
              });
        });

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'commission_high':
                $query->orderByDesc('commission_rate');
                break;
            case 'commission_low':
                $query->orderBy('commission_rate');
                break;
            case 'popular':
                $query->withCount('clicks')->orderByDesc('clicks_count');
                break;
            case 'converting':
                $query->withCount('conversions')->orderByDesc('conversions_count');
                break;
            default: // newest
                $query->latest();
                break;
        }

        $offers = $query->paginate(12)->withQueryString();
        $categories = OfferCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $filterKeys = ['category', 'model', 'search', 'access_type', 'min_commission', 'max_commission', 'country', 'device', 'source', 'sort'];

        return Inertia::render('Affiliate/Offers/Index', [
            'offers' => $offers,
            'categories' => $categories,
            'filters' => $request->only($filterKeys),
        ]);
    }

    public function show(Offer $offer)
    {
        $offer->load(['category', 'advertiser']);

        // Check if user has access
        $user = auth()->user();
        $hasAccess = $offer->access_type === 'open';
        $accessRequest = null;

        if (!$hasAccess) {
            $accessRequest = OfferAccessRequest::where('affiliate_id', $user->id)
                ->where('offer_id', $offer->id)
                ->first();

            $hasAccess = $accessRequest && $accessRequest->status === 'approved';
        }

        // Get affiliate's link if it exists
        $affiliateLink = $user->affiliateLinks()
            ->where('offer_id', $offer->id)
            ->first();

        return Inertia::render('Affiliate/Offers/Show', [
            'offer' => $offer,
            'hasAccess' => $hasAccess,
            'accessRequest' => $accessRequest,
            'affiliateLink' => $affiliateLink,
        ]);
    }

    public function requestAccess(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $user = $request->user();

        // Check if already requested
        $existing = OfferAccessRequest::where('affiliate_id', $user->id)
            ->where('offer_id', $offer->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already requested access to this offer.');
        }

        OfferAccessRequest::create([
            'affiliate_id' => $user->id,
            'offer_id' => $offer->id,
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Access request submitted successfully!');
    }
}
