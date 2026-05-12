<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Store;
use App\Models\StoreCategory;
use App\Models\StoreProduct;
use App\Models\StoreOrder;
use App\Models\StoreSubscription;
use App\Notifications\StoreOrderConfirmationNotification;
use App\Notifications\StoreOrderReceivedNotification;
use App\Services\StorePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StorefrontController extends Controller
{
    protected $paymentService;

    public function __construct(StorePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    private function getStates(): array
    {
        return State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
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
            // Single product store - get the first active product
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
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'description' => $store->description,
                    'logo' => $store->logo,
                    'email' => $store->email,
                    'phone' => $store->phone,
                    'whatsapp_number' => $store->whatsapp_number,
                    'theme' => $store->theme,
                    'theme_customization' => $store->theme_customization,
                    'meta_title' => $store->meta_title,
                    'meta_description' => $store->meta_description,
                    'meta_image' => $store->meta_image ? asset('storage/' . $store->meta_image) : ($store->logo ? asset('storage/' . $store->logo) : null),
                    'store_type' => 'single',
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
                ],
                'states' => $this->getStates(),
                'previewMode' => $previewMode,
                'subscriptionStatus' => $store->subscription_status,
            ]);
        } else {
            // Multi-product store - get all active products
            $products = StoreProduct::where('store_id', $store->id)
                ->where('is_active', true)
                ->orderBy('is_featured', 'desc')
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => Str::limit(strip_tags($product->description), 150),
                        'price' => $product->price,
                        'compare_at_price' => $product->compare_at_price,
                        'images' => $product->images,
                        'is_featured' => $product->is_featured,
                        'is_in_stock' => $product->isInStock(),
                        'has_discount' => $product->hasDiscount(),
                        'discount_percentage' => $product->discount_percentage,
                        'delivery_fees' => $product->delivery_fees ?? [],
                    ];
                });

            $categories = StoreCategory::where('store_id', $store->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->withCount('products')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'products_count' => $c->products_count,
                ]);

            return Inertia::render('Storefront/Multi', [
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'description' => $store->description,
                    'about_content' => $store->about_content,
                    'logo' => $store->logo,
                    'email' => $store->email,
                    'phone' => $store->phone,
                    'whatsapp_number' => $store->whatsapp_number,
                    'address' => $store->address,
                    'theme' => $store->theme,
                    'theme_customization' => $store->theme_customization,
                    'meta_title' => $store->meta_title,
                    'meta_description' => $store->meta_description,
                    'meta_image' => $store->meta_image ? asset('storage/' . $store->meta_image) : ($store->logo ? asset('storage/' . $store->logo) : null),
                    'store_type' => 'multi',
                ],
                'products' => $products,
                'categories' => $categories,
                'states' => $this->getStates(),
                'previewMode' => $previewMode,
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
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => Str::limit(strip_tags($product->description), 150),
                    'price' => $product->price,
                    'compare_at_price' => $product->compare_at_price,
                    'images' => $product->images,
                    'is_featured' => $product->is_featured,
                    'is_in_stock' => $product->isInStock(),
                    'has_discount' => $product->hasDiscount(),
                    'discount_percentage' => $product->discount_percentage,
                    'categories' => $product->categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug]),
                    'delivery_fees' => $product->delivery_fees ?? [],
                ];
            });

        $categories = StoreCategory::where('store_id', $store->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('products')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'products_count' => $c->products_count,
            ]);

        return Inertia::render('Storefront/Shop', [
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'description' => $store->description,
                'logo' => $store->logo,
                'email' => $store->email,
                'phone' => $store->phone,
                'whatsapp_number' => $store->whatsapp_number,
                'address' => $store->address,
                'theme' => $store->theme,
                'theme_customization' => $store->theme_customization,
                'meta_title' => $store->meta_title,
                'meta_description' => $store->meta_description,
                'meta_image' => $store->meta_image ? asset('storage/' . $store->meta_image) : ($store->logo ? asset('storage/' . $store->logo) : null),
                'store_type' => 'multi',
            ],
            'products' => $products,
            'categories' => $categories,
            'states' => $this->getStates(),
            'previewMode' => $previewMode,
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
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'description' => $store->description,
                'about_content' => $store->about_content,
                'logo' => $store->logo,
                'email' => $store->email,
                'phone' => $store->phone,
                'whatsapp_number' => $store->whatsapp_number,
                'address' => $store->address,
                'theme' => $store->theme,
                'theme_customization' => $store->theme_customization,
                'meta_title' => $store->meta_title,
                'meta_description' => $store->meta_description,
                'meta_image' => $store->meta_image ? asset('storage/' . $store->meta_image) : ($store->logo ? asset('storage/' . $store->logo) : null),
                'store_type' => 'multi',
            ],
            'previewMode' => $previewMode,
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
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:store_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'state_id' => 'nullable|integer|exists:states,id',
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
            // Calculate order totals
            $subtotal = 0;
            $shippingFee = 0;
            $orderItems = [];
            $processedForShipping = [];
            $stateId = isset($validated['state_id']) ? (int) $validated['state_id'] : null;

            foreach ($validated['items'] as $item) {
                $product = StoreProduct::where('id', $item['product_id'])
                    ->where('store_id', $store->id)
                    ->where('is_active', true)
                    ->firstOrFail();

                // Check stock
                if (!$product->isInStock() || ($product->stock_quantity && $product->stock_quantity < $item['quantity'])) {
                    DB::rollBack();
                    return response()->json(['error' => "Product '{$product->name}' is out of stock."], 400);
                }

                // Calculate shipping fee for this product (once per unique product)
                if ($stateId && !in_array($product->id, $processedForShipping)) {
                    foreach ($product->delivery_fees ?? [] as $zone) {
                        if ((int) ($zone['state_id'] ?? 0) === $stateId) {
                            $shippingFee += (float) ($zone['fee'] ?? 0);
                            break;
                        }
                    }
                    $processedForShipping[] = $product->id;
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                ];
            }

            $total = $subtotal + $shippingFee;

            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // Create order
            $order = StoreOrder::create([
                'store_id' => $store->id,
                'order_number' => $orderNumber,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'items' => $orderItems,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_reference' => $orderNumber,
                'payment_status' => 'pending',
                'fulfillment_status' => 'pending',
            ]);

            // Initialize payment via API (Paystack or Flutterwave)
            $paymentUrl = $this->initializePayment($store, $order);

            DB::commit();

            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'order_number' => $orderNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to process order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Initialize payment with Paystack or Flutterwave
     */
    private function initializePayment(Store $store, StoreOrder $order): string
    {
        $paymentData = $this->paymentService->initializeOrderPayment($order, $store);

        if (!$paymentData || !isset($paymentData['payment_url'])) {
            throw new \Exception('Failed to initialize payment');
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
            $verificationData = null;

            if ($store->payment_provider === 'paystack') {
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
        $order = StoreOrder::where('order_number', $orderNumber)
            ->where('store_id', $store->id)
            ->firstOrFail();

        return Inertia::render('Storefront/ThankYou', [
            'store' => [
                'name' => $store->name,
                'slug' => $store->slug,
                'logo' => $store->logo,
                'email' => $store->email,
                'phone' => $store->phone,
                'whatsapp_number' => $store->whatsapp_number,
            ],
            'order' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'items' => $order->items,
                'subtotal' => $order->subtotal,
                'shipping_fee' => $order->shipping_fee,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }
}
