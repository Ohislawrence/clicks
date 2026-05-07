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
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by commission model
        if ($request->has('model') && $request->model) {
            $query->where('commission_model', $request->model);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
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

        $offers = $query->paginate(12);
        $categories = OfferCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Affiliate/Offers/Index', [
            'offers' => $offers,
            'categories' => $categories,
            'filters' => $request->only(['category', 'model', 'search']),
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
