<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\Conversion;
use App\Models\Offer;
use App\Jobs\ProcessConversionJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManualConversionController extends Controller
{
    /**
     * Display the manual conversion reporting page
     */
    public function index(Request $request)
    {
        $query = Conversion::with(['offer', 'click', 'affiliate:id,affiliate_code'])
            ->whereHas('offer', function ($q) use ($request) {
                $q->where('advertiser_id', $request->user()->id);
            })
            ->where('is_manual', true)
            ->latest();

        // Apply filters
        if ($request->filled('offer_id')) {
            $query->where('offer_id', $request->offer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('click', function ($clickQuery) use ($search) {
                    $clickQuery->where('click_id', 'like', "%{$search}%");
                });
            });
        }

        $conversions = $query->paginate(20);

        $offers = Offer::where('advertiser_id', $request->user()->id)
            ->where('enable_whatsapp_tracking', true)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Advertiser/ManualConversions/Index', [
            'conversions' => $conversions,
            'offers' => $offers,
            'filters' => $request->only(['offer_id', 'search']),
        ]);
    }

    /**
     * Report a manual conversion (from WhatsApp chat)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'click_id' => 'required|string',
            'conversion_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Find the click by click_id
        $click = Click::where('click_id', $validated['click_id'])->first();

        if (!$click) {
            return back()->withErrors([
                'click_id' => 'Click ID not found. Please verify the reference code from the WhatsApp message.'
            ]);
        }

        // Verify the click belongs to advertiser's offer
        $offer = Offer::where('id', $click->offer_id)
            ->where('advertiser_id', $request->user()->id)
            ->first();

        if (!$offer) {
            return back()->withErrors([
                'click_id' => 'This click does not belong to any of your offers.'
            ]);
        }

        // Check if already converted
        if ($click->is_converted) {
            return back()->withErrors([
                'click_id' => 'This click has already been converted.'
            ]);
        }

        // Check if offer has WhatsApp tracking enabled
        if (!$offer->enable_whatsapp_tracking) {
            return back()->withErrors([
                'click_id' => 'WhatsApp tracking is not enabled for this offer.'
            ]);
        }

        // Create the conversion
        $conversion = Conversion::create([
            'offer_id' => $click->offer_id,
            'affiliate_id' => $click->affiliate_id,
            'affiliate_link_id' => $click->affiliate_link_id,
            'click_id' => $click->id,
            'conversion_id' => 'CONV-' . strtoupper(\Str::random(10)),
            'conversion_amount' => $validated['conversion_amount'] ?? 0,
            'status' => 'pending',
            'is_manual' => true,
            'manual_notes' => $validated['notes'] ?? null,
            'ip_address' => $click->ip_address,
        ]);

        // Mark click as converted
        $click->update([
            'is_converted' => true,
            'converted_at' => now(),
        ]);

        // Dispatch job to process the conversion (calculate commissions, etc.)
        ProcessConversionJob::dispatch($conversion);

        return back()->with('success', 'Manual conversion reported successfully! Commission will be processed shortly.');
    }

    /**
     * Bulk import manual conversions from CSV
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data);

        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            $clickId = $row[0] ?? null;
            $amount = $row[1] ?? 0;
            $notes = $row[2] ?? null;

            if (!$clickId) {
                $errors[] = "Row skipped: Missing click_id";
                continue;
            }

            try {
                $click = Click::where('click_id', $clickId)->first();

                if (!$click) {
                    $errors[] = "Click ID {$clickId} not found";
                    continue;
                }

                $offer = Offer::where('id', $click->offer_id)
                    ->where('advertiser_id', $request->user()->id)
                    ->first();

                if (!$offer) {
                    $errors[] = "Click ID {$clickId} doesn't belong to your offers";
                    continue;
                }

                if ($click->is_converted) {
                    $errors[] = "Click ID {$clickId} already converted";
                    continue;
                }

                $conversion = Conversion::create([
                    'offer_id' => $click->offer_id,
                    'affiliate_id' => $click->affiliate_id,
                    'affiliate_link_id' => $click->affiliate_link_id,
                    'click_id' => $click->id,
                    'conversion_id' => 'CONV-' . strtoupper(\Str::random(10)),
                    'conversion_amount' => $amount,
                    'status' => 'pending',
                    'is_manual' => true,
                    'manual_notes' => $notes,
                    'ip_address' => $click->ip_address,
                ]);

                $click->update([
                    'is_converted' => true,
                    'converted_at' => now(),
                ]);

                ProcessConversionJob::dispatch($conversion);
                $imported++;

            } catch (\Exception $e) {
                $errors[] = "Error with {$clickId}: " . $e->getMessage();
            }
        }

        $message = "Successfully imported {$imported} conversions.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
        }

        return back()->with('success', $message);
    }
}
