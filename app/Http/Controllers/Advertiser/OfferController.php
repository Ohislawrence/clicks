<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\OfferCreative;
use App\Models\StoreProduct;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // Budget & caps
            'budget'               => 'nullable|numeric|min:0',
            'daily_conversion_cap' => 'nullable|integer|min:0',
            'monthly_conversion_cap' => 'nullable|integer|min:0',
            'creatives' => 'nullable|array',
            'creatives.*.type' => 'required|in:banner,image,text,video',
            'creatives.*.name' => 'required|string|max:255',
            'creatives.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
            'creatives.*.content' => 'nullable|string',
            // Sales forecast & wallet
            'expected_sales'          => 'nullable|integer|min:0',
            'product_cost'            => 'nullable|numeric|min:0',
            'minimum_wallet_required' => 'nullable|numeric|min:0',
            // RevShare recurring settings
            'revshare_type'               => 'nullable|in:once,recurring',
            'revshare_recurring_duration' => 'nullable|integer|min:1',
            'revshare_recurring_unit'     => 'nullable|in:month,year',
            // Targeting
            'offer_url'          => 'nullable|url|max:2048',
            'product_image'      => 'nullable|url|max:2048',
            'target_countries'   => 'nullable|array',
            'target_countries.*' => 'string|size:2',
            'target_devices'     => 'nullable|array',
            'target_devices.*'   => 'in:desktop,mobile,tablet',
            'target_os'          => 'nullable|array',
            'target_os.*'        => 'in:windows,mac,linux,android,ios',
            'require_unique_ip'  => 'nullable|boolean',
        ]);

        // Validate budget against wallet balance before creating the offer
        $budget = isset($validated['budget']) ? (float) $validated['budget'] : 0;
        if ($budget > 0 && $request->user()->advertiser_balance < $budget) {
            return back()->withErrors([
                'budget' => 'Insufficient wallet balance (₦' .
                    number_format($request->user()->advertiser_balance, 2) .
                    ' available). Please top up your wallet first.',
            ])->withInput();
        }

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
            'expected_sales'          => $validated['expected_sales'] ?? null,
            'product_cost'            => $validated['product_cost'] ?? null,
            'minimum_wallet_required' => $validated['minimum_wallet_required'] ?? null,
            'revshare_type'               => $validated['commission_model'] === 'revshare'
                ? ($validated['revshare_type'] ?? 'once') : null,
            'revshare_recurring_duration' => ($validated['commission_model'] === 'revshare' && ($validated['revshare_type'] ?? 'once') === 'recurring')
                ? ($validated['revshare_recurring_duration'] ?? null) : null,
            'revshare_recurring_unit'     => ($validated['commission_model'] === 'revshare' && ($validated['revshare_type'] ?? 'once') === 'recurring')
                ? ($validated['revshare_recurring_unit'] ?? 'month') : null,
            // Targeting fields
            'offer_url'        => $validated['offer_url'] ?? null,
            'product_image'    => $validated['product_image'] ?? null,
            'target_countries' => !empty($validated['target_countries']) ? $validated['target_countries'] : null,
            'target_devices'   => !empty($validated['target_devices']) ? $validated['target_devices'] : null,
            'target_os'        => !empty($validated['target_os']) ? $validated['target_os'] : null,
            'require_unique_ip' => $validated['require_unique_ip'] ?? false,
        ]);

        // Allocate budget from advertiser wallet if provided
        if ($budget > 0) {
            // Auto-calculate conversion cap: budget ÷ CPA rate
            $rate = (float) $validated['commission_rate'];
            $conversionCap = ($rate > 0) ? (int) floor($budget / $rate) : null;

            $dailyConversionCap = !empty($validated['daily_conversion_cap']) ? $validated['daily_conversion_cap'] : null;
            $monthlyConversionCap = !empty($validated['monthly_conversion_cap']) ? $validated['monthly_conversion_cap'] : null;

            $offer->update([
                'budget_limit'         => $budget,
                'spent_budget'         => 0,
                'total_conversion_cap' => $conversionCap,
                'daily_conversion_cap' => $dailyConversionCap,
                'monthly_conversion_cap' => $monthlyConversionCap,
                'auto_pause_on_cap'    => true,
            ]);

            DB::transaction(function () use ($request, $offer, $budget) {
                // Deduct from wallet (funds are now locked to this offer)
                $request->user()->decrement('advertiser_balance', $budget);

                WalletTransaction::create([
                    'user_id'     => $request->user()->id,
                    'amount'      => $budget,
                    'type'        => 'offer_allocation',
                    'status'      => 'success',
                    'offer_id'    => $offer->id,
                    'description' => "Budget allocated for offer: {$offer->name}",
                ]);
            });
        } else {
            // Still apply optional caps if provided, but no wallet deduction
            $capData = array_filter([
                'daily_conversion_cap'   => !empty($validated['daily_conversion_cap']) ? $validated['daily_conversion_cap'] : null,
                'monthly_conversion_cap' => !empty($validated['monthly_conversion_cap']) ? $validated['monthly_conversion_cap'] : null,
            ]);
            if ($capData) {
                $offer->update(array_merge($capData, ['auto_pause_on_cap' => true]));
            }
        }

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

                    if (!$file->isValid()) {
                        continue;
                    }

                    $extension = $file->guessExtension() ?? $file->getClientOriginalExtension() ?? '';
                    $filename  = Str::random(40) . ($extension !== '' ? '.' . $extension : '');
                    $destDir   = storage_path('app/public/creatives');

                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0755, true);
                    }

                    $file->move($destDir, $filename);
                    $path = 'creatives/' . $filename;

                    $creativesData['file_path'] = $path;
                    $creativesData['size'] = $this->formatFileSize(
                        file_exists($destDir . '/' . $filename) ? filesize($destDir . '/' . $filename) : 0
                    );
                    $creativesData['format'] = strtoupper($extension);

                    // Get image dimensions if it's an image
                    if (in_array($creativeData['type'], ['banner', 'image'])) {
                        $imagePath = $destDir . '/' . $filename;
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

    public function show($id)
    {
        // Debugging info
        \Illuminate\Support\Facades\Log::info("Viewing offer ID: {$id} for user: " . auth()->id());

        $offer = Offer::withTrashed()->find($id);

        if (!$offer) {
            \Illuminate\Support\Facades\Log::error("Offer not found in DB with ID: {$id}");
            abort(404, "Offer not found in database.");
        }

        // Check ownership
        if ($offer->advertiser_id !== (int) auth()->id()) {
            \Illuminate\Support\Facades\Log::warning("Ownership mismatch: Offer owner {$offer->advertiser_id}, Current user " . auth()->id());

            // If they are admin or impersonating, they should be able to see it?
            // Better to allow it if impersonating
            if (!session()->has('impersonate')) {
                abort(403, "You do not own this offer.");
            }
        }

        if ($offer->deleted_at) {
            return Inertia::render('Advertiser/Offers/Show', [
                'offer' => $offer,
                'error' => 'This offer has been deleted.',
            ]);
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

        return redirect()->route('advertiser.offers.show', $offer)
            ->with('error', 'Offer editing is disabled. Please contact admin to make changes.');
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
            // Sales forecast & wallet
            'expected_sales'          => 'nullable|integer|min:0',
            'product_cost'            => 'nullable|numeric|min:0',
            'minimum_wallet_required' => 'nullable|numeric|min:0',
            'daily_conversion_cap'     => 'nullable|integer|min:0',
            'monthly_conversion_cap'   => 'nullable|integer|min:0',
            // RevShare recurring settings
            'revshare_type'               => 'nullable|in:once,recurring',
            'revshare_recurring_duration' => 'nullable|integer|min:1',
            'revshare_recurring_unit'     => 'nullable|in:month,year',
            // Targeting
            'offer_url'          => 'nullable|url|max:2048',
            'product_image'      => 'nullable|url|max:2048',
            'target_countries'   => 'nullable|array',
            'target_countries.*' => 'string|size:2',
            'target_devices'     => 'nullable|array',
            'target_devices.*'   => 'in:desktop,mobile,tablet',
            'target_os'          => 'nullable|array',
            'target_os.*'        => 'in:windows,mac,linux,android,ios',
            'require_unique_ip'  => 'nullable|boolean',
        ]);

        return redirect()->route('advertiser.offers.show', $offer)
            ->with('error', 'Offer editing is disabled. Please contact admin to make changes.');
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

    public function regeneratePostbackSecret(Offer $offer)
    {
        if ($offer->advertiser_id !== auth()->id()) {
            abort(403);
        }

        $offer->update(['postback_secret' => Str::random(48)]);

        return back()->with('success', 'Postback secret regenerated. Update your postback URL with the new token.');
    }
}

