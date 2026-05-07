<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Conversion;
use App\Models\Offer;
use App\Models\User;
use App\Models\Click;
use App\Models\Commission;
use App\Notifications\ConversionApprovedNotification;
use App\Notifications\ConversionRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ConversionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $offerIds = Offer::where('advertiser_id', $user->id)->pluck('id');

        $query = Conversion::with(['offer', 'click', 'affiliate:id,affiliate_code'])
            ->whereIn('offer_id', $offerIds);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by offer
        if ($request->has('offer_id') && $request->offer_id) {
            $query->where('offer_id', $request->offer_id);
        }

        // Search by transaction ID only
        if ($request->has('search') && $request->search) {
            $query->where('transaction_id', 'like', '%' . $request->search . '%');
        }

        $conversions = $query->latest()->paginate(20);

        $offers = Offer::where('advertiser_id', $user->id)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Advertiser/Conversions/Index', [
            'conversions' => $conversions,
            'offers' => $offers,
            'filters' => $request->only(['status', 'offer_id', 'search']),
        ]);
    }

    public function approve(Request $request, Conversion $conversion)
    {
        // Check ownership
        $offer = Offer::findOrFail($conversion->offer_id);
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        if ($conversion->status !== 'pending') {
            return back()->with('error', 'Only pending conversions can be approved.');
        }

        DB::transaction(function () use ($conversion) {
            $conversion->update(['status' => 'approved']);

            // Update commission status
            $conversion->commission?->update(['status' => 'approved']);

            // Update affiliate balance
            $affiliate = $conversion->affiliate;
            $affiliate->update([
                'pending_balance' => DB::raw('pending_balance - ' . $conversion->commission_amount),
                'balance' => DB::raw('balance + ' . $conversion->commission_amount),
            ]);

            // Send notification to affiliate
            $affiliate->notify(new ConversionApprovedNotification($conversion));
        });

        return back()->with('success', 'Conversion approved successfully!');
    }

    public function reject(Request $request, Conversion $conversion)
    {
        // Check ownership
        $offer = Offer::findOrFail($conversion->offer_id);
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        if ($conversion->status !== 'pending') {
            return back()->with('error', 'Only pending conversions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($conversion, $validated) {
            $conversion->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Update commission status
            $conversion->commission?->update(['status' => 'rejected']);

            // Update affiliate pending balance
            $affiliate = $conversion->affiliate;
            $affiliate->update([
                'pending_balance' => DB::raw('pending_balance - ' . $conversion->commission_amount),
            ]);

            // Send notification to affiliate
            $affiliate->notify(new ConversionRejectedNotification($conversion, $validated['rejection_reason']));
        });

        return back()->with('success', 'Conversion rejected.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'conversion_ids' => 'required|array',
            'conversion_ids.*' => 'exists:conversions,id',
        ]);

        $user = $request->user();
        $offerIds = Offer::where('advertiser_id', $user->id)->pluck('id');

        $conversions = Conversion::whereIn('id', $validated['conversion_ids'])
            ->whereIn('offer_id', $offerIds)
            ->where('status', 'pending')
            ->get();

        DB::transaction(function () use ($conversions) {
            foreach ($conversions as $conversion) {
                $conversion->update(['status' => 'approved']);
                $conversion->commission?->update(['status' => 'approved']);

                $affiliate = $conversion->affiliate;
                $affiliate->update([
                    'pending_balance' => DB::raw('pending_balance - ' . $conversion->commission_amount),
                    'balance' => DB::raw('balance + ' . $conversion->commission_amount),
                ]);
            }
        });

        return back()->with('success', count($conversions) . ' conversions approved successfully!');
    }

    public function create(Request $request)
    {
        $user = $request->user();

        // Get advertiser's offers
        $offers = Offer::where('advertiser_id', $user->id)
            ->where('is_active', true)
            ->select('id', 'name', 'commission_model', 'commission_rate')
            ->get();

        // Get only affiliates who are actively promoting this advertiser's offers
        // (have affiliate links for this advertiser's offers)
        $offerIds = $offers->pluck('id');
        $affiliates = User::whereHas('roles', function ($query) {
                $query->where('name', 'affiliate');
            })
            ->whereHas('affiliateLinks', function ($query) use ($offerIds) {
                $query->whereIn('offer_id', $offerIds)
                      ->where('is_active', true);
            })
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Advertiser/Conversions/Create', [
            'offers' => $offers,
            'affiliates' => $affiliates,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'offer_id' => 'required|exists:offers,id',
            'affiliate_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'required|string|max:255',
            'conversion_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify offer ownership
        $offer = Offer::findOrFail($validated['offer_id']);
        if ($offer->advertiser_id !== $user->id) {
            abort(403, 'Unauthorized access to this offer.');
        }

        // Verify affiliate role
        $affiliate = User::findOrFail($validated['affiliate_id']);
        if (!$affiliate->hasRole('affiliate')) {
            return back()->withErrors(['affiliate_id' => 'Selected user is not an affiliate.']);
        }

        // Check for duplicate transaction_id
        $existingConversion = Conversion::where('transaction_id', $validated['transaction_id'])
            ->where('offer_id', $validated['offer_id'])
            ->first();

        if ($existingConversion) {
            return back()->withErrors(['transaction_id' => 'A conversion with this transaction ID already exists.']);
        }

        // Calculate commission
        $commissionAmount = 0;
        if ($offer->commission_model === 'revshare') {
            $commissionAmount = ($validated['amount'] * $offer->commission_rate) / 100;
        } else {
            $commissionAmount = $offer->commission_rate;
        }

        DB::transaction(function () use ($validated, $offer, $affiliate, $commissionAmount, $user) {
            // Create a manual click record for tracking
            $click = Click::create([
                'affiliate_id' => $affiliate->id,
                'offer_id' => $offer->id,
                'click_id' => 'MANUAL_' . Str::random(16),
                'ip_address' => $validated['notes'] ? 'Manual Entry' : null,
                'user_agent' => 'Manual Conversion Entry',
                'referrer' => null,
                'landing_page' => null,
                'is_unique' => true,
                'created_at' => $validated['conversion_date'],
            ]);

            // Create conversion
            $conversion = Conversion::create([
                'click_id' => $click->id,
                'affiliate_id' => $affiliate->id,
                'offer_id' => $offer->id,
                'transaction_id' => $validated['transaction_id'],
                'amount' => $validated['amount'],
                'commission_amount' => $commissionAmount,
                'status' => 'approved', // Manual conversions are auto-approved
                'currency' => 'NGN',
                'conversion_data' => json_encode([
                    'manual_entry' => true,
                    'entered_by' => $user->name,
                    'notes' => $validated['notes'] ?? null,
                ]),
                'created_at' => $validated['conversion_date'],
                'updated_at' => now(),
            ]);

            // Create commission record
            Commission::create([
                'affiliate_id' => $affiliate->id,
                'conversion_id' => $conversion->id,
                'offer_id' => $offer->id,
                'amount' => $commissionAmount,
                'status' => 'approved',
                'type' => 'direct',
                'created_at' => $validated['conversion_date'],
            ]);

            // Update affiliate balance
            $affiliate->update([
                'balance' => DB::raw('balance + ' . $commissionAmount),
            ]);

            // Send notification to affiliate
            $affiliate->notify(new ConversionApprovedNotification($conversion));
        });

        return redirect()->route('advertiser.conversions.index')
            ->with('success', 'Manual conversion recorded successfully!');
    }
}
