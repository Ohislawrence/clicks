<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\OfferAccessRequest;
use App\Models\Offer;
use App\Notifications\OfferAccessApprovedNotification;
use App\Notifications\OfferAccessRejectedNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccessRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $offerIds = Offer::where('advertiser_id', $user->id)->pluck('id');

        $query = OfferAccessRequest::with(['offer', 'affiliate:id,name,email,tier'])
            ->whereIn('offer_id', $offerIds);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by offer
        if ($request->has('offer_id') && $request->offer_id) {
            $query->where('offer_id', $request->offer_id);
        }

        $accessRequests = $query->latest()->paginate(20);

        $offers = Offer::where('advertiser_id', $user->id)
            ->where('access_type', 'request')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Advertiser/AccessRequests/Index', [
            'accessRequests' => $accessRequests,
            'offers' => $offers,
            'filters' => $request->only(['status', 'offer_id']),
        ]);
    }

    public function approve(OfferAccessRequest $accessRequest)
    {
        // Check ownership
        $offer = Offer::findOrFail($accessRequest->offer_id);
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $accessRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send notification to affiliate
        $accessRequest->affiliate->notify(new OfferAccessApprovedNotification($accessRequest));

        return back()->with('success', 'Access request approved successfully!');
    }

    public function reject(Request $request, OfferAccessRequest $accessRequest)
    {
        // Check ownership
        $offer = Offer::findOrFail($accessRequest->offer_id);
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        if ($accessRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $accessRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send notification to affiliate
        $accessRequest->affiliate->notify(new OfferAccessRejectedNotification($accessRequest, $validated['rejection_reason']));

        return back()->with('success', 'Access request rejected.');
    }
}
