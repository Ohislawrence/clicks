<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreSubscription;
use App\Models\StoreTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoreController extends Controller
{
    /**
     * Show all stores owned by advertiser
     */
    public function index()
    {
        $user = auth()->user();
        $stores = $user->stores()->with(['plan', 'theme'])->latest()->get();

        return Inertia::render('Advertiser/Store/Index', [
            'stores' => $stores,
        ]);
    }

    /**
     * Show store setup wizard
     */
    public function setup()
    {
        // Get active plans and themes
        $plans = StorePlan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        $themes = StoreTheme::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Advertiser/Store/Setup', [
            'plans' => $plans,
            'themes' => $themes,
        ]);
    }

    /**
     * Create a new store
     */
    public function createStore(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'store_plan_id' => 'required|exists:store_plans,id',
            'store_theme_id' => 'required|exists:store_themes,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'theme_customization' => 'nullable|array',
            'theme_customization.colors' => 'nullable|array',
            'theme_customization.typography' => 'nullable|array',
            'theme_customization.layout' => 'nullable|array',
            'payment_method' => 'required|in:api',
            'payment_provider' => 'nullable|in:paystack,flutterwave',
            'payment_public_key' => 'nullable|string',
            'payment_secret_key' => 'nullable|string',
        ]);

        // Get the selected plan
        $plan = StorePlan::findOrFail($validated['store_plan_id']);

        // Get the selected theme and copy its config
        $theme = \App\Models\StoreTheme::findOrFail($validated['store_theme_id']);
        $themeCustomization = $theme->config ?? [];

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure uniqueness
            $count = 1;
            $originalSlug = $validated['slug'];
            while (Store::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        DB::beginTransaction();
        try {
            // Calculate subscription dates (temporary - will be updated after payment)
            $subscriptionStart = now();
            $subscriptionEnd = $validated['billing_cycle'] === 'monthly' 
                ? now()->addMonth() 
                : now()->addYear();

            // Encrypt payment secret key if provided
            $paymentSecretKey = null;
            if (!empty($validated['payment_secret_key'])) {
                $paymentSecretKey = Crypt::encryptString($validated['payment_secret_key']);
            }

            // Create the store
            $store = Store::create([
                'user_id' => $user->id,
                'store_plan_id' => $validated['store_plan_id'],
                'billing_cycle' => $validated['billing_cycle'],
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                'store_theme_id' => $validated['store_theme_id'],
                'theme_customization' => $themeCustomization, // Use theme's config
                'payment_method' => 'api',
                'payment_provider' => $validated['payment_provider'],
                'payment_link' => null,
                'payment_public_key' => $validated['payment_public_key'] ?? null,
                'payment_secret_key' => $paymentSecretKey,
                'subscription_start_date' => $subscriptionStart,
                'subscription_end_date' => $subscriptionEnd,
                'subscription_status' => 'expired', // Will be activated after first payment
                'is_active' => false, // Not active until first payment
            ]);

            DB::commit();

            // If payment was skipped, send to the store dashboard with a notice
            if (empty($validated['payment_provider'])) {
                return redirect()->route('advertiser.store.dashboard', $store->id)
                    ->with('success', 'Store created! Configure payment in Edit Store Settings to start accepting orders.');
            }

            // --- Initiate first subscription payment immediately ---
            $amount = $validated['billing_cycle'] === 'monthly'
                ? $store->plan->monthly_price
                : $store->plan->yearly_price;

            $reference = 'SUB-' . strtoupper(uniqid());

            // Use the admin-configured subscription gateway (defaults to paystack)
            $gateway = $store->subscription_payment_gateway ?? 'paystack';

            $subscription = $store->subscriptions()->create([
                'store_plan_id'     => $store->store_plan_id,
                'amount'            => $amount,
                'billing_cycle'     => $validated['billing_cycle'],
                'payment_reference' => $reference,
                'payment_gateway'   => $gateway,
                'status'            => 'pending',
                'period_start'      => now(),
                'period_end'        => $validated['billing_cycle'] === 'monthly' ? now()->addMonth() : now()->addYear(),
            ]);

            $callbackUrl = route('advertiser.store.subscription.verify', [
                'store'     => $store->id,
                'reference' => $reference,
            ]);

            if ($gateway === 'paystack') {
                $paystackKey = config('services.paystack.secret_key');
                if (!$paystackKey) {
                    return redirect()->route('advertiser.store.subscription.index', $store->id)
                        ->with('warning', 'Store created! Paystack is not configured on the platform — contact support to activate your subscription.');
                }
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $paystackKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://api.paystack.co/transaction/initialize', [
                    'email'        => $user->email,
                    'amount'       => $amount * 100,
                    'reference'    => $reference,
                    'callback_url' => $callbackUrl,
                    'metadata'     => [
                        'subscription_id' => $subscription->id,
                        'store_id'        => $store->id,
                        'user_id'         => $user->id,
                    ],
                ]);
                if ($response->successful() && $response->json('status')) {
                    return Inertia::location($response->json('data.authorization_url'));
                }
                return redirect()->route('advertiser.store.subscription.index', $store->id)
                    ->withErrors(['error' => 'Store created but payment initialisation failed. Please retry from the Subscription page.']);
            } else {
                $flutterwaveKey = config('services.flutterwave.secret_key');
                if (!$flutterwaveKey) {
                    return redirect()->route('advertiser.store.subscription.index', $store->id)
                        ->with('warning', 'Store created! Flutterwave is not configured on the platform — contact support to activate your subscription.');
                }
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $flutterwaveKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://api.flutterwave.com/v3/payments', [
                    'tx_ref'       => $reference,
                    'amount'       => $amount,
                    'currency'     => 'NGN',
                    'redirect_url' => $callbackUrl,
                    'customer'     => ['email' => $user->email, 'name' => $user->name],
                    'customizations' => [
                        'title'       => 'Store Subscription',
                        'description' => 'Store subscription payment',
                    ],
                    'meta' => [
                        'subscription_id' => $subscription->id,
                        'store_id'        => $store->id,
                        'user_id'         => $user->id,
                    ],
                ]);
                if ($response->successful() && $response->json('status') === 'success') {
                    return Inertia::location($response->json('data.link'));
                }
                return redirect()->route('advertiser.store.subscription.index', $store->id)
                    ->withErrors(['error' => 'Store created but payment initialisation failed. Please retry from the Subscription page.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create store: ' . $e->getMessage()]);
        }
    }

    /**
     * Show store dashboard
     */
    public function dashboard($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Load relationships and stats
        $store->load(['plan', 'theme', 'subscriptions' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Get store statistics
        $stats = [
            'total_products' => $store->products()->count(),
            'active_products' => $store->products()->where('is_active', true)->count(),
            'total_orders' => $store->orders()->count(),
            'pending_orders' => $store->orders()->where('fulfillment_status', 'pending')->count(),
            'total_revenue' => $store->orders()->where('payment_status', 'paid')->sum('total'),
            'days_until_expiry' => $store->days_until_expiry,
        ];

        // Recent orders
        $recentOrders = $store->orders()
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Advertiser/Store/Dashboard', [
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'storeUrl' => url('/store/' . $store->slug),
        ]);
    }

    /**
     * Show store edit form
     */
    public function edit($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $store->load(['plan', 'theme']);

        return Inertia::render('Advertiser/Store/Edit', [
            'store' => $store,
        ]);
    }

    /**
     * Update store settings
     */
    public function update(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug,' . $store->id,
            'description' => 'nullable|string',
            'about_content' => 'nullable|string',
            'theme_customization' => 'nullable|array',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'payment_provider' => 'nullable|in:paystack,flutterwave',
            'payment_public_key' => 'nullable|string',
            'payment_secret_key' => 'nullable|string',
            'payment_webhook_secret' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'about_content' => $validated['about_content'] ?? null,
            'theme_customization' => $validated['theme_customization'] ?? $store->theme_customization,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
        ];

        // Only update payment fields if provider is given
        if (!empty($validated['payment_provider'])) {
            $updateData['payment_provider'] = $validated['payment_provider'];
        }
        if (!empty($validated['payment_public_key'])) {
            $updateData['payment_public_key'] = $validated['payment_public_key'];
        }
        if (!empty($validated['payment_secret_key'])) {
            $updateData['payment_secret_key'] = Crypt::encryptString($validated['payment_secret_key']);
        }
        if (!empty($validated['payment_webhook_secret'])) {
            $updateData['payment_webhook_secret'] = Crypt::encryptString($validated['payment_webhook_secret']);
        }

        $store->update($updateData);

        return back()->with('success', 'Store updated successfully!');
    }

    /**
     * Show store preview
     */
    public function preview($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $store->load(['plan', 'theme', 'products' => function ($query) {
            $query->where('is_active', true)->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
        }]);

        return Inertia::render('Advertiser/Store/Preview', [
            'store' => $store,
            'products' => $store->products,
        ]);
    }

    /**
     * Publish store
     */
    public function publish($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        // Check if subscription is active
        if (!$store->isSubscriptionActive()) {
            return back()->withErrors(['error' => 'Cannot publish store. Your subscription is not active.']);
        }

        $store->update([
            'is_active' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Store published successfully!');
    }

    /**
     * Unpublish store
     */
    public function unpublish($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $store->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Store unpublished successfully!');
    }

    /**
     * Show theme customizer for a store
     */
    public function themeCustomizer($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->with(['theme'])->findOrFail($storeId);

        $themes = StoreTheme::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        // Build the public store URL
        $storeUrl = $store->custom_domain
            ? 'https://' . $store->custom_domain
            : route('storefront.show', $store->slug);

        return Inertia::render('Advertiser/Store/ThemeCustomizer', [
            'store'    => $store,
            'themes'   => $themes,
            'storeUrl' => $storeUrl,
        ]);
    }

    /**
     * Save theme + customization for a store
     */
    public function updateTheme(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        $validated = $request->validate([
            'store_theme_id'              => 'required|exists:store_themes,id',
            'remove_hero_image'           => 'nullable|boolean',
            'hero_image'                  => 'nullable|image|max:5120',
            'colors'                      => 'nullable|array',
            'colors.primary'              => 'nullable|string|max:20',
            'colors.secondary'            => 'nullable|string|max:20',
            'colors.background'           => 'nullable|string|max:20',
            'colors.text'                 => 'nullable|string|max:20',
            'colors.header_bg'            => 'nullable|string|max:20',
            'colors.footer_bg'            => 'nullable|string|max:20',
            'header'                      => 'nullable|array',
            'header.tagline'              => 'nullable|string|max:80',
            'header.announcement_text'    => 'nullable|string|max:120',
            'hero'                        => 'nullable|array',
            'hero.heading'               => 'nullable|string|max:80',
            'hero.subheading'            => 'nullable|string|max:150',
            'hero.button_text'           => 'nullable|string|max:40',
            'hero.image_url'             => 'nullable|string|max:500',
            'hero.overlay_opacity'       => 'nullable|integer|min:0|max:90',
            'footer'                     => 'nullable|array',
            'footer.tagline'             => 'nullable|string|max:150',
            'footer.facebook'            => 'nullable|url|max:255',
            'footer.instagram'           => 'nullable|url|max:255',
            'footer.twitter'             => 'nullable|url|max:255',
        ]);

        // Merge into existing customization
        $customization = $store->theme_customization ?? [];

        foreach (['colors', 'header', 'hero', 'footer'] as $section) {
            if (!empty($validated[$section])) {
                $customization[$section] = array_merge(
                    $customization[$section] ?? [],
                    $validated[$section]
                );
            }
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/store-hero/' . $store->id);
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);
            $customization['hero']['image_url'] = '/storage/store-hero/' . $store->id . '/' . $fileName;
        } elseif ($request->boolean('remove_hero_image')) {
            $customization['hero']['image_url'] = null;
        }

        $store->update([
            'store_theme_id'    => $validated['store_theme_id'],
            'theme_customization' => $customization,
        ]);

        return back()->with('success', 'Theme updated successfully!');
    }
}
