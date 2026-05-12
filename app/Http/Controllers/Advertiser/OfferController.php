<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\OfferCreative;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $offers = Offer::with('category')
            ->where('advertiser_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return Inertia::render('Advertiser/Offers/Index', [
            'offers' => $offers,
        ]);
    }

    public function create(Request $request)
    {
        $categories = OfferCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $prefill = null;

        if ($request->filled('product_id')) {
            $product = StoreProduct::with('store')
                ->whereHas('store', fn ($q) => $q->where('user_id', $request->user()->id))
                ->findOrFail($request->integer('product_id'));

            $store = $product->store;

            $prefill = [
                'store_product_id' => $product->id,
                'name'             => $product->name,
                'description'      => $product->description ?? '',
                'preview_url'      => url("/store/{$store->slug}"),
                'whatsapp_number'  => $store->whatsapp_number ?? '',
                'product_price'    => (float) $product->price,
                'product_image'    => isset($product->images[0]) ? $product->images[0] : null,
                'store_name'       => $store->name,
            ];
        }

        return Inertia::render('Advertiser/Offers/Create', [
            'categories' => $categories,
            'prefill'    => $prefill,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_product_id'          => 'nullable|integer|exists:store_products,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:offer_categories,id',
            'commission_model' => 'required|in:pps,ppl,revshare',
            'commission_rate' => 'required|numeric|min:0',
            'cookie_duration' => 'required|integer|min:1|max:365',
            'access_type' => 'required|in:open,request',
            'preview_url' => 'required|url',
            'terms_and_conditions' => 'nullable|string',
            'postback_url' => 'nullable|url',
            'enable_whatsapp_tracking' => 'nullable|boolean',
            'whatsapp_number' => 'required_if:enable_whatsapp_tracking,true|nullable|string|max:20',
            'whatsapp_message_template' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'creatives' => 'nullable|array',
            'creatives.*.type' => 'required|in:banner,image,text,video',
            'creatives.*.name' => 'required|string|max:255',
            'creatives.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
            'creatives.*.content' => 'nullable|string',
        ]);

        // If linked to a store product, verify the advertiser owns it
        if (!empty($validated['store_product_id'])) {
            StoreProduct::whereHas('store', fn ($q) => $q->where('user_id', $request->user()->id))
                ->findOrFail($validated['store_product_id']);
        }

        $offer = Offer::create([
            'advertiser_id'    => $request->user()->id,
            'store_product_id' => $validated['store_product_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'commission_model' => $validated['commission_model'],
            'commission_rate' => $validated['commission_rate'],
            'cookie_duration' => $validated['cookie_duration'],
            'access_type' => $validated['access_type'],
            'preview_url' => $validated['preview_url'],
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'postback_url' => $validated['postback_url'] ?? null,
            'enable_whatsapp_tracking' => $validated['enable_whatsapp_tracking'] ?? false,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'whatsapp_message_template' => $validated['whatsapp_message_template'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'approval_status' => 'pending', // Requires admin approval
        ]);

        // Process creatives if provided
        if (!empty($validated['creatives'])) {
            foreach ($validated['creatives'] as $index => $creativeData) {
                $creativesData = [
                    'offer_id' => $offer->id,
                    'type' => $creativeData['type'],
                    'name' => $creativeData['name'],
                    'content' => $creativeData['content'] ?? null,
                    'is_active' => true,
                ];

                // Handle file upload
                if ($request->hasFile("creatives.{$index}.file")) {
                    $file = $request->file("creatives.{$index}.file");
                    $path = $file->store('creatives', 'public');

                    $creativesData['file_path'] = $path;
                    $creativesData['size'] = $this->formatFileSize($file->getSize());
                    $creativesData['format'] = strtoupper($file->getClientOriginalExtension());

                    // Get image dimensions if it's an image
                    if (in_array($creativeData['type'], ['banner', 'image'])) {
                        $imagePath = storage_path('app/public/' . $path);
                        if (file_exists($imagePath)) {
                            $imageInfo = getimagesize($imagePath);
                            if ($imageInfo) {
                                $creativesData['width'] = $imageInfo[0];
                                $creativesData['height'] = $imageInfo[1];
                            }
                        }
                    }
                }

                OfferCreative::create($creativesData);
            }
        }

        return redirect()->route('advertiser.offers.show', $offer)
            ->with('success', 'Offer created successfully! It is pending admin approval before going live.');
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function show(Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $offer->load(['category', 'accessRequests.affiliate', 'reviewer']);

        // Get performance stats
        $stats = [
            'totalClicks' => $offer->total_clicks,
            'totalConversions' => $offer->total_conversions,
            'totalRevenue' => $offer->total_revenue,
            'conversionRate' => $offer->total_clicks > 0
                ? round(($offer->total_conversions / $offer->total_clicks) * 100, 2)
                : 0,
            'averageCommission' => $offer->total_conversions > 0
                ? round($offer->conversions()->sum('commission_amount') / $offer->total_conversions, 2)
                : 0,
        ];

        return Inertia::render('Advertiser/Offers/Show', [
            'offer' => $offer,
            'stats' => $stats,
        ]);
    }

    public function edit(Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $categories = OfferCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Advertiser/Offers/Edit', [
            'offer' => $offer,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:offer_categories,id',
            'commission_model' => 'required|in:pps,ppl,revshare',
            'commission_rate' => 'required|numeric|min:0',
            'cookie_duration' => 'required|integer|min:1|max:365',
            'access_type' => 'required|in:open,request',
            'preview_url' => 'required|url',
            'terms_and_conditions' => 'nullable|string',
            'postback_url' => 'nullable|url',
            'enable_whatsapp_tracking' => 'nullable|boolean',
            'whatsapp_number' => 'required_if:enable_whatsapp_tracking,true|nullable|string|max:20',
            'whatsapp_message_template' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $offer->update($validated);

        return redirect()->route('advertiser.offers.show', $offer)
            ->with('success', 'Offer updated successfully!');
    }

    public function destroy(Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $offer->delete();

        return redirect()->route('advertiser.offers.index')
            ->with('success', 'Offer deleted successfully!');
    }

    public function toggle(Offer $offer)
    {
        // Check ownership
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $offer->update([
            'is_active' => !$offer->is_active,
        ]);

        return back()->with('success', 'Offer status updated successfully!');
    }
}
