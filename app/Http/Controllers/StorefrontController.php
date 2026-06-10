<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Store;
use App\Models\StoreCategory;
use App\Models\StoreDiscountCode;
use App\Models\StoreProduct;
use App\Models\StoreOrder;
use App\Notifications\StoreOrderConfirmationNotification;
use App\Notifications\StoreOrderReceivedNotification;
use App\Services\StorePaymentService;
use App\Services\StorePaymentSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    protected $paymentService;
    protected $splitService;

    public function __construct(StorePaymentService $paymentService, StorePaymentSplitService $splitService)
    {
        $this->paymentService = $paymentService;
        $this->splitService = $splitService;
    }

    private function getStates(): array
    {
        return State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    /**
     * Build a consistent store data array for Inertia responses.
     */
    private function formatStore(Store $store): array
    {
        return [
            'id'                 => $store->id,
            'name'               => $store->name,
            'slug'               => $store->slug,
            'description'        => $store->description,
            'about_content'      => $store->about_content ?? null,
            'logo'               => $store->logo,
            'email'              => $store->email,
            'phone'              => $store->phone,
            'whatsapp_number'    => $store->whatsapp_number,
            'address'            => $store->address ?? null,
            'theme'              => $store->theme,
            'theme_customization'=> $store->theme_customization,
            'meta_title'         => $store->meta_title,
            'meta_description'   => $store->meta_description,
            'meta_image'         => $store->meta_image
                ? asset('storage/' . $store->meta_image)
                : ($store->logo ? asset('storage/' . $store->logo) : null),
            'store_type'         => $store->plan?->store_type ?? 'multi',
            'payment_provider'   => $store->payment_provider,
            'payment_public_key' => $store->payment_public_key,
            'currency'           => $store->currency ?? 'NGN',
        ];
    }
    /**
     * Display store homepage (single or multi-product layout)
     */
    public function show(string $slug)
    {
        // Find store by slug - check ownership first before applying restrictions
        $store = Store::with(['plan', 'theme', 'user'])
            ->where('slug', $slug)
            ->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        // Check if current user is the store owner
        $isOwner = auth()->check() && auth()->id() === $store->user_id;

        // If NOT owner, enforce active + active subscription requirements
        if (!$isOwner && (!$store->is_active || !$store->isSubscriptionActive())) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable. Please check back later.',
            ]);
        }

        // Check if subscription is active
        $subscriptionActive = $store->isSubscriptionActive();

        // If subscription is not active and user is NOT the owner, show unavailable
        if (!$subscriptionActive && !$isOwner) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable. Please check back later.',
            ]);
        }

        // Owner can see store even if subscription expired/unpaid (with preview notice)
        $previewMode = $isOwner && (!$store->is_active || !$subscriptionActive);

        // Get products based on store type
        if ($store->plan->store_type === 'single') {
            $product = StoreProduct::where('store_id', $store->id)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return Inertia::render('Storefront/Unavailable', [
                    'storeName' => $store->name,
                    'message' => 'No products available at this time.',
                ]);
            }

            return Inertia::render('Storefront/Single', [
                'store'              => $this->formatStore($store),
                'product'            => $this->formatProduct($product),
                'states'             => $this->getStates(),
                'previewMode'        => $previewMode,
                'subscriptionStatus' => $store->subscription_status,
            ]);
        } else {
            $products = StoreProduct::where('store_id', $store->id)
                ->where('is_active', true)
                ->with('categories:id,name,slug')
                ->orderBy('is_featured', 'desc')
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($p) => $this->formatProductCard($p));

            $categories = $this->getStoreCategories($store->id);

            return Inertia::render('Storefront/Multi', [
                'store'              => $this->formatStore($store),
                'products'           => $products,
                'categories'         => $categories,
                'states'             => $this->getStates(),
                'previewMode'        => $previewMode,
                'subscriptionStatus' => $store->subscription_status,
            ]);
        }
    }

    /**
     * Display the shop page (all products + category/price filters)
     */
    public function shop(string $slug)
    {
        $store = Store::with(['plan', 'theme', 'user'])
            ->where('slug', $slug)
            ->first();

        if (!$store) abort(404, 'Store not found');

        $isOwner = auth()->check() && auth()->id() === $store->user_id;

        if (!$isOwner && (!$store->is_active || !$store->isSubscriptionActive())) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable.',
            ]);
        }

        $previewMode = $isOwner && (!$store->is_active || !$store->isSubscriptionActive());

        $products = StoreProduct::where('store_id', $store->id)
            ->where('is_active', true)
            ->with('categories:id,name,slug')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProductCard($p));

        $categories = $this->getStoreCategories($store->id);

        return Inertia::render('Storefront/Shop', [
            'store'              => $this->formatStore($store),
            'products'           => $products,
            'categories'         => $categories,
            'states'             => $this->getStates(),
            'previewMode'        => $previewMode,
            'subscriptionStatus' => $store->subscription_status,
        ]);
    }

    /**
     * Display About Us page for a multi-product store
     */
    public function about(string $slug)
    {
        $store = Store::with(['plan', 'theme', 'user'])
            ->where('slug', $slug)
            ->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        $isOwner = auth()->check() && auth()->id() === $store->user_id;

        if (!$isOwner && (!$store->is_active || !$store->isSubscriptionActive())) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable. Please check back later.',
            ]);
        }

        $subscriptionActive = $store->isSubscriptionActive();
        $previewMode = $isOwner && (!$store->is_active || !$subscriptionActive);

        return Inertia::render('Storefront/About', [
            'store'              => $this->formatStore($store),
            'previewMode'        => $previewMode,
            'subscriptionStatus' => $store->subscription_status,
        ]);
    }
    public function product(string $slug, string $productSlug)
    {
        // Find store - check ownership first before applying restrictions
        $store = Store::with(['plan', 'theme'])
            ->where('slug', $slug)
            ->first();

        if (!$store) {
            abort(404, 'Store not found');
        }

        // Check if current user is the store owner
        $isOwner = auth()->check() && auth()->id() === $store->user_id;

        // If NOT owner, enforce active + active subscription requirements
        if (!$isOwner && (!$store->is_active || !$store->isSubscriptionActive())) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable.',
            ]);
        }

        // Check subscription status
        $subscriptionActive = $store->isSubscriptionActive();

        // If subscription is not active and user is NOT the owner, show unavailable
        if (!$subscriptionActive && !$isOwner) {
            return Inertia::render('Storefront/Unavailable', [
                'storeName' => $store->name,
                'message' => 'This store is currently unavailable.',
            ]);
        }

        // Owner can see product even if subscription expired/unpaid (with preview notice)
        $previewMode = $isOwner && (!$store->is_active || !$subscriptionActive);

        // Find product
        $product = StoreProduct::where('store_id', $store->id)
            ->where('slug', $productSlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related products (same store, different product, limit 4)
        $relatedProducts = StoreProduct::where('store_id', $store->id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'compare_at_price' => $p->compare_at_price,
                    'images' => $p->images,
                    'is_in_stock' => $p->isInStock(),
                    'has_discount' => $p->hasDiscount(),
                    'discount_percentage' => $p->discount_percentage,
                    'product_type' => $p->product_type ?? 'tangible',
                ];
            });

        return Inertia::render('Storefront/Product', [
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'logo' => $store->logo,
                'theme' => $store->theme,
                'theme_customization' => $store->theme_customization,
                'meta_title' => $store->meta_title,
                'meta_description' => $store->meta_description,
                'meta_image' => $store->meta_image ? asset('storage/' . $store->meta_image) : ($store->logo ? asset('storage/' . $store->logo) : null),
                'whatsapp_number' => $store->whatsapp_number,
                'currency'        => $store->currency ?? 'NGN',
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'images' => $product->images,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'is_in_stock' => $product->isInStock(),
                'has_discount' => $product->hasDiscount(),
                'discount_percentage' => $product->discount_percentage,
                'delivery_fees' => $product->delivery_fees ?? [],
                'product_type' => $product->product_type ?? 'tangible',
            ],
            'states' => $this->getStates(),
            'relatedProducts' => $relatedProducts,
            'previewMode' => $previewMode,
            'subscriptionStatus' => $store->subscription_status,
        ]);
    }

    /**
     * Process checkout
     */
    public function checkout(Request $request, string $slug)
    {
        // Determine if all requested products are digital (no shipping needed)
        $requestedProductIds = collect($request->input('items', []))->pluck('product_id')->filter();
        $allDigital = $requestedProductIds->isNotEmpty()
            && StoreProduct::whereIn('id', $requestedProductIds)->pluck('product_type')
                ->every(fn($type) => $type === 'digital');

        $validated = $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:store_products,id',
            'items.*.quantity'     => 'required|integer|min:1|max:100',
            'customer_name'        => 'required|string|max:255',
            'customer_email'       => 'required|email|max:255',
            'customer_phone'       => 'required|string|max:20',
            'shipping_address'     => $allDigital ? 'nullable|string|max:1000' : 'required|string|max:1000',
            'state_id'             => 'nullable|integer|exists:states,id',
            'discount_code'        => 'nullable|string|max:50',
        ]);

        // Find store
        $store = Store::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Check subscription
        if (!$store->isSubscriptionActive()) {
            return response()->json(['error' => 'Store is currently unavailable.'], 403);
        }

        DB::beginTransaction();
        try {
            $subtotal           = 0;
            $shippingFee        = 0;
            $orderItems         = [];
            $processedForShipping = [];
            $stateId            = isset($validated['state_id']) ? (int) $validated['state_id'] : null;

            foreach ($validated['items'] as $item) {
                // Lock the product row to prevent concurrent overselling
                $product = StoreProduct::where('id', $item['product_id'])
                    ->where('store_id', $store->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->isInStock() || ($product->stock_quantity !== null && $product->stock_quantity < $item['quantity'])) {
                    DB::rollBack();
                    return response()->json(['error' => "'{$product->name}' is out of stock or insufficient quantity."], 422);
                }

                if ($stateId && !in_array($product->id, $processedForShipping)) {
                    foreach ($product->delivery_fees ?? [] as $zone) {
                        if ((int) ($zone['state_id'] ?? 0) === $stateId) {
                            $shippingFee += (float) ($zone['fee'] ?? 0);
                            break;
                        }
                    }
                    $processedForShipping[] = $product->id;
                }

                $itemTotal   = $product->price * $item['quantity'];
                $subtotal   += $itemTotal;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'quantity'   => $item['quantity'],
                    'total'      => $itemTotal,
                ];
            }

            // Apply discount code if provided
            $discountAmount = 0;
            $discountCode   = null;
            if (!empty($validated['discount_code'])) {
                $code = StoreDiscountCode::where('store_id', $store->id)
                    ->where('code', strtoupper(trim($validated['discount_code'])))
                    ->lockForUpdate()
                    ->first();

                if ($code && $code->isValid($subtotal)) {
                    $discountAmount = $code->calculateDiscount($subtotal + $shippingFee);
                    $discountCode   = $code->code;
                    $code->increment('uses_count');
                } else {
                    DB::rollBack();
                    return response()->json(['error' => 'Invalid or expired discount code.'], 422);
                }
            }

            $total       = max(0, $subtotal + $shippingFee - $discountAmount);
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // Resolve affiliate attribution from the customer's browser cookie NOW,
            // while this is still a browser request (cookie is available).
            // We cannot do this at webhook/callback time — webhooks are server-to-server
            // and carry no browser cookies.
            $affiliateLink = $store->hasPlatformMode()
                ? $this->resolveAffiliateLinkFromCookie($request, $store)
                : null;

            $order = StoreOrder::create([
                'store_id'          => $store->id,
                'order_number'      => $orderNumber,
                'customer_name'     => $validated['customer_name'],
                'customer_email'    => $validated['customer_email'],
                'customer_phone'    => $validated['customer_phone'],
                'shipping_address'  => $validated['shipping_address'] ?? null,
                'items'             => $orderItems,
                'subtotal'          => $subtotal,
                'shipping_fee'      => $shippingFee,
                'discount_code'     => $discountCode,
                'discount_amount'   => $discountAmount,
                'total'             => $total,
                'payment_reference' => $orderNumber,
                'payment_status'    => 'pending',
                'payment_mode'      => $store->payment_mode,
                'currency'          => $store->currency ?? 'NGN',
                'fulfillment_status'=> 'pending',
                // Store affiliate attribution at order creation so webhook settlement
                // can use it without needing a browser cookie.
                'affiliate_link_id' => $affiliateLink?->id,
                'affiliate_id'      => $affiliateLink?->affiliate_id,
            ]);

            $paymentUrl = $this->resolvePaymentUrl($store, $order);

            DB::commit();

            return response()->json([
                'success'      => true,
                'payment_url'  => $paymentUrl,
                'order_number' => $orderNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error', ['slug' => $slug, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process order. Please try again.'], 500);
        }
    }

    /**
     * Resolve the payment URL — routes to platform or direct gateway depending on store mode.
     */
    private function resolvePaymentUrl(Store $store, StoreOrder $order): string
    {
        if ($store->hasPlatformMode()) {
            return $this->paymentService->initializePlatformPayment($order, $store);
        }

        $paymentData = $this->paymentService->initializeOrderPayment($order, $store);

        if (!$paymentData || !isset($paymentData['payment_url'])) {
            throw new \RuntimeException('Failed to initialize payment. Please try again.');
        }

        return $paymentData['payment_url'];
    }

    /**
     * Verify payment after callback
     */
    public function verifyPayment(Request $request, string $slug)
    {
        $reference = $request->query('reference') ?? $request->query('tx_ref');

        if (!$reference) {
            return redirect()->route('storefront.show', $slug)
                ->with('error', 'Invalid payment reference');
        }

        // Find order
        $order = StoreOrder::where('payment_reference', $reference)->firstOrFail();
        $store = $order->store;

        // If already paid, redirect to thank you page
        if ($order->payment_status === 'paid') {
            return redirect()->route('storefront.thank-you', [
                'slug' => $slug,
                'orderNumber' => $order->order_number,
            ]);
        }

        // Verify payment
        DB::beginTransaction();
        try {
            $verified = false;

            if ($store->hasPlatformMode()) {
                // Verify against platform's own Paystack key
                $platformKey = config('services.paystack.secret_key');
                if ($platformKey) {
                    $verificationData = $this->paymentService->verifyPaystackPayment($reference, $platformKey);
                    $verified = $verificationData && $verificationData['success'];
                }
            } elseif ($store->payment_provider === 'paystack') {
                $verificationData = $this->paymentService->verifyPaystackPayment($reference, $store->payment_secret_key);
                $verified = $verificationData && $verificationData['success'];
            } elseif ($store->payment_provider === 'flutterwave') {
                $transactionId = $request->query('transaction_id');
                if ($transactionId) {
                    $verificationData = $this->paymentService->verifyFlutterwavePayment($transactionId, $store->payment_secret_key);
                    $verified = $verificationData && $verificationData['success'];
                }
            }

            if ($verified) {
                // Update order status
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Update product stock
                foreach ($order->items as $item) {
                    $product = StoreProduct::find($item['product_id']);
                    if ($product && $product->stock_quantity !== null) {
                        $product->decrement('stock_quantity', $item['quantity']);
                    }
                }

                DB::commit();

                // For platform mode: settle the split — load the affiliate link from the
                // order (stored at checkout time) rather than re-reading the cookie.
                if ($store->hasPlatformMode()) {
                    $affiliateLink = $order->affiliate_link_id
                        ? \App\Models\AffiliateLink::with('offer')->find($order->affiliate_link_id)
                        : null;
                    $this->splitService->settle($order, $affiliateLink);
                }

                // Send notifications
                $this->sendOrderNotifications($order);

                return redirect()->route('storefront.thank-you', [
                    'slug' => $slug,
                    'orderNumber' => $order->order_number,
                ]);
            } else {
                DB::rollBack();
                return redirect()->route('storefront.show', $slug)
                    ->with('error', 'Payment verification failed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment verification error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('storefront.show', $slug)
                ->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }

    /**
     * Resolve the AffiliateLink from the tracking cookie for affiliate attribution.
     * Returns null if no valid tracking cookie is present or offer doesn't match the store.
     */
    private function resolveAffiliateLinkFromCookie(Request $request, Store $store): ?\App\Models\AffiliateLink
    {
        $cookieRaw = $request->cookie('clicksintel_tracking');
        if (!$cookieRaw) {
            return null;
        }

        $cookieData = json_decode($cookieRaw, true);
        if (!$cookieData || empty($cookieData['tracking_code'])) {
            return null;
        }

        $link = \App\Models\AffiliateLink::with('offer')
            ->where('tracking_code', $cookieData['tracking_code'])
            ->where('is_active', true)
            ->first();

        if (!$link || !$link->offer) {
            return null;
        }

        // Only attribute if the offer is linked to this store
        if ($link->offer->store_product_id) {
            $product = \App\Models\StoreProduct::find($link->offer->store_product_id);
            if (!$product || $product->store_id !== $store->id) {
                return null;
            }
        } else {
            // Offer has no specific product — still allow if same advertiser owns the store
            if ($link->offer->advertiser_id !== $store->user_id) {
                return null;
            }
        }

        return $link;
    }

    /**
     * Send order notifications to customer and store owner.
     */
    private function sendOrderNotifications(StoreOrder $order): void
    {
        try {
            $store = $order->store;

            // Notify customer
            if ($order->customer_email) {
                Notification::route('mail', $order->customer_email)
                    ->notify(new StoreOrderConfirmationNotification($order));
            }

            // Notify store owner
            $store->user->notify(new StoreOrderReceivedNotification($order));

            Log::info('Order notifications sent', ['order_id' => $order->id]);

        } catch (\Exception $e) {
            Log::error('Error sending order notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display order confirmation page
     */
    public function thankYou(string $slug, string $orderNumber)
    {
        // Find store
        $store = Store::where('slug', $slug)->firstOrFail();

        // Find order
        $store->load(['theme', 'plan']);
        $order = StoreOrder::where('order_number', $orderNumber)
            ->where('store_id', $store->id)
            ->firstOrFail();

        return Inertia::render('Storefront/ThankYou', [
            'store' => [
                'name'            => $store->name,
                'slug'            => $store->slug,
                'logo'            => $store->logo,
                'email'           => $store->email,
                'phone'           => $store->phone,
                'whatsapp_number' => $store->whatsapp_number,
                'theme'              => $store->theme,
                'theme_customization'=> $store->theme_customization,
                'currency'           => $store->currency ?? 'NGN',
            ],
            'order' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'items' => $order->items,
                'subtotal' => $order->subtotal,
                'shipping_fee' => $order->shipping_fee,
                'total' => $order->total,
                'payment_status'  => $order->payment_status,
                'discount_code'   => $order->discount_code,
                'discount_amount' => $order->discount_amount,
                'currency'        => $order->currency ?? $store->currency ?? 'NGN',
                'created_at'      => $order->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }

    /**
     * Format a full product for the detail/single page.
     */
    private function formatProduct(StoreProduct $product): array
    {
        return [
            'id'                  => $product->id,
            'name'                => $product->name,
            'slug'                => $product->slug,
            'description'         => $product->description,
            'price'               => $product->price,
            'compare_at_price'    => $product->compare_at_price,
            'images'              => $product->images ?? [],
            'sku'                 => $product->sku,
            'stock_quantity'      => $product->stock_quantity,
            'is_in_stock'         => $product->isInStock(),
            'has_discount'        => $product->hasDiscount(),
            'discount_percentage' => $product->discount_percentage,
            'delivery_fees'       => $product->delivery_fees ?? [],
            'product_type'        => $product->product_type ?? 'tangible',
        ];
    }

    /**
     * Format a product for card/listing use (lighter payload).
     */
    private function formatProductCard(StoreProduct $product): array
    {
        return [
            'id'                  => $product->id,
            'name'                => $product->name,
            'slug'                => $product->slug,
            'description'         => Str::limit(strip_tags($product->description ?? ''), 150),
            'price'               => $product->price,
            'compare_at_price'    => $product->compare_at_price,
            'images'              => $product->images ?? [],
            'is_featured'         => $product->is_featured,
            'is_in_stock'         => $product->isInStock(),
            'has_discount'        => $product->hasDiscount(),
            'discount_percentage' => $product->discount_percentage,
            'product_type'        => $product->product_type ?? 'tangible',
            'delivery_fees'       => $product->delivery_fees ?? [],
            'categories'          => $product->relationLoaded('categories')
                ? $product->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug])
                : [],
        ];
    }

    /**
     * Get active categories for a store with product counts.
     */
    private function getStoreCategories(int $storeId): array
    {
        return StoreCategory::where('store_id', $storeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('products')
            ->get()
            ->map(fn($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'slug'           => $c->slug,
                'products_count' => $c->products_count,
            ])
            ->toArray();
    }
}

