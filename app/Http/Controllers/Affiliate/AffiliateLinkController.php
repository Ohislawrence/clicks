<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Offer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AffiliateLinkController extends Controller
{
    public function index(Request $request)
    {
        $links = AffiliateLink::with(['offer'])
            ->where('affiliate_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Affiliate/Links/Index', [
            'links' => $links,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'offer_id' => 'required|exists:offers,id',
            'custom_slug' => 'nullable|string|max:50|unique:affiliate_links,custom_slug',
            'discount_enabled' => 'nullable|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = $request->user();
        $offer = Offer::findOrFail($validated['offer_id']);

        // Check if offer is active
        if (!$offer->is_active) {
            return back()->with('error', 'This offer is not currently active.');
        }

        // Check if user already has a link for this offer
        $existing = AffiliateLink::where('affiliate_id', $user->id)
            ->where('offer_id', $offer->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a link for this offer.');
        }

        $link = AffiliateLink::create([
            'affiliate_id' => $user->id,
            'offer_id' => $offer->id,
            'custom_slug' => $validated['custom_slug'] ?? null,
            'discount_enabled' => $validated['discount_enabled'] ?? false,
            'discount_percentage' => $validated['discount_percentage'] ?? null,
        ]);

        return back()->with('success', 'Affiliate link created successfully!');
    }

    public function toggle(AffiliateLink $affiliateLink)
    {
        // Check ownership
        if ($affiliateLink->affiliate_id !== auth()->id()) {
            abort(403);
        }

        $affiliateLink->update([
            'is_active' => !$affiliateLink->is_active,
        ]);

        return back()->with('success', 'Link status updated successfully!');
    }

    public function destroy(AffiliateLink $affiliateLink)
    {
        // Check ownership
        if ($affiliateLink->affiliate_id !== auth()->id()) {
            abort(403);
        }

        $affiliateLink->delete();

        return back()->with('success', 'Link deleted successfully!');
    }

    /**
     * Generate WhatsApp Click-to-Chat URL
     */
    public function generateWhatsApp(Request $request, AffiliateLink $affiliateLink)
    {
        // Check ownership
        if ($affiliateLink->affiliate_id !== auth()->id()) {
            abort(403);
        }

        // Check if offer has WhatsApp tracking enabled
        if (!$affiliateLink->offer->enable_whatsapp_tracking) {
            return back()->withErrors([
                'whatsapp' => 'WhatsApp tracking is not enabled for this offer.'
            ]);
        }

        // Validate WhatsApp number is set
        if (empty($affiliateLink->offer->whatsapp_number)) {
            return back()->withErrors([
                'whatsapp' => 'WhatsApp number is not configured for this offer.'
            ]);
        }

        try {
            $whatsappData = $affiliateLink->generateWhatsAppUrl($request->custom_message);

            return back()->with([
                'success' => 'WhatsApp link generated successfully!',
                'whatsapp_data' => $whatsappData,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'whatsapp' => 'Failed to generate WhatsApp link: ' . $e->getMessage()
            ]);
        }
    }
}
