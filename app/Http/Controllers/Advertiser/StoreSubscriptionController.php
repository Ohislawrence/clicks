<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class StoreSubscriptionController extends Controller
{
    /**
     * Display subscription information and payment history
     */
    public function index($storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $store->load(['plan', 'subscriptions' => function ($query) {
            $query->latest();
        }]);

        // Calculate subscription details
        $subscriptionDetails = [
            'is_active' => $store->isSubscriptionActive(),
            'expires_at' => $store->subscription_end_date,
            'days_until_expiry' => $store->days_until_expiry,
            'is_expiring_soon' => $store->isSubscriptionExpiringSoon(),
            'current_period_start' => $store->subscription_start_date,
            'billing_cycle' => $store->billing_cycle,
            'next_payment_amount' => $store->billing_cycle === 'monthly'
                ? $store->plan->monthly_price
                : $store->plan->yearly_price,
        ];

        // Get available plans for upgrades
        $availablePlans = StorePlan::where('is_active', true)
            ->where('store_type', $store->plan->store_type)
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        return Inertia::render('Advertiser/Store/Subscription/Index', [
            'store' => $store,
            'subscriptionDetails' => $subscriptionDetails,
            'availablePlans' => $availablePlans,
            'paymentHistory' => $store->subscriptions,
        ]);
    }

    /**
     * Initiate subscription renewal
     */
    public function initiateRenewal(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $validated = $request->validate([
            'billing_cycle' => 'nullable|in:monthly,yearly',
        ]);

        // Use current billing cycle if not provided
        $billingCycle = $validated['billing_cycle'] ?? $store->billing_cycle;

        // Use the payment gateway configured by the admin for this store
        $paymentGateway = $store->subscription_payment_gateway ?? 'paystack';

        // Calculate amount
        $amount = $billingCycle === 'monthly'
            ? $store->plan->monthly_price
            : $store->plan->yearly_price;

        // Generate unique reference
        $reference = 'SUB-' . strtoupper(uniqid());

        // Create pending subscription record
        $subscription = $store->subscriptions()->create([
            'store_plan_id' => $store->store_plan_id,
            'amount' => $amount,
            'billing_cycle' => $billingCycle,
            'payment_reference' => $reference,
            'payment_gateway' => $paymentGateway,
            'status' => 'pending',
            'period_start' => now(),
            'period_end' => $billingCycle === 'monthly' ? now()->addMonth() : now()->addYear(),
        ]);

        // Initialize payment based on gateway
        if ($paymentGateway === 'paystack') {
            return $this->initializePaystack($subscription, $amount, $store);
        } else {
            return $this->initializeFlutterwave($subscription, $amount, $store);
        }
    }

    /**
     * Initialize Paystack payment
     */
    private function initializePaystack(StoreSubscription $subscription, $amount, Store $store)
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey) {
            return back()->withErrors(['error' => 'Paystack is not configured. Please contact support.']);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecretKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => auth()->user()->email,
            'amount' => $amount * 100, // Convert to kobo
            'reference' => $subscription->payment_reference,
            'callback_url' => route('advertiser.store.subscription.verify', ['store' => $store->id, 'reference' => $subscription->payment_reference]),
            'metadata' => [
                'subscription_id' => $subscription->id,
                'store_id' => $store->id,
                'user_id' => auth()->id(),
            ],
        ]);

        if ($response->successful() && $response->json('status')) {
            $authorizationUrl = $response->json('data.authorization_url');
            return Inertia::location($authorizationUrl);
        }

        return back()->withErrors(['error' => 'Failed to initialize payment. Please try again.']);
    }

    /**
     * Initialize Flutterwave payment
     */
    private function initializeFlutterwave(StoreSubscription $subscription, $amount, Store $store)
    {
        $flutterwaveSecretKey = config('services.flutterwave.secret_key');

        if (!$flutterwaveSecretKey) {
            return back()->withErrors(['error' => 'Flutterwave is not configured. Please contact support.']);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $flutterwaveSecretKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $subscription->payment_reference,
            'amount' => $amount,
            'currency' => 'NGN',
            'redirect_url' => route('advertiser.store.subscription.verify', ['store' => $store->id, 'reference' => $subscription->payment_reference]),
            'customer' => [
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
            ],
            'customizations' => [
                'title' => 'Store Subscription',
                'description' => 'Store subscription payment',
            ],
            'meta' => [
                'subscription_id' => $subscription->id,
                'store_id' => $store->id,
                'user_id' => auth()->id(),
            ],
        ]);

        if ($response->successful() && $response->json('status') === 'success') {
            $paymentLink = $response->json('data.link');
            return Inertia::location($paymentLink);
        }

        return back()->withErrors(['error' => 'Failed to initialize payment. Please try again.']);
    }

    /**
     * Verify payment and activate subscription
     */
    public function verifyPayment(Request $request, $store)
    {
        // Paystack sends both 'reference' and 'trxref'; Flutterwave sends 'transaction_id' and 'tx_ref'
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('tx_ref');

        if (!$reference) {
            return redirect()->route('advertiser.store.subscription.index', $store)
                ->withErrors(['error' => 'Invalid payment reference']);
        }

        $subscription = StoreSubscription::where('payment_reference', $reference)->firstOrFail();
        $store = $subscription->store;

        // Ensure subscription belongs to current user
        if ($store->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // If already paid, redirect to subscription page
        if ($subscription->status === 'paid') {
            return redirect()->route('advertiser.store.subscription.index', $store)
                ->with('success', 'Subscription already activated!');
        }

        // Verify payment based on gateway
        if ($subscription->payment_gateway === 'paystack' || $subscription->payment_gateway === null) {
            $verified = $this->verifyPaystack($subscription);
        } else {
            $verified = $this->verifyFlutterwave($subscription);
        }

        if ($verified) {
            DB::beginTransaction();
            try {
                $subscription->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                $store->update([
                    'billing_cycle' => $subscription->billing_cycle,
                    'subscription_start_date' => $subscription->period_start,
                    'subscription_end_date' => $subscription->period_end,
                    'subscription_status' => 'active',
                    'is_active' => true,
                    'expiry_reminder_sent' => false,
                ]);

                DB::commit();

                return redirect()->route('advertiser.store.subscription.index', $store)
                    ->with('success', 'Payment verified successfully! Your store subscription is now active.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('advertiser.store.subscription.index', $store)
                    ->withErrors(['error' => 'Failed to activate subscription: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('advertiser.store.subscription.index', $store)
            ->withErrors(['error' => 'Payment verification failed. Please contact support.']);
    }

    /**
     * Verify Paystack payment
     */
    private function verifyPaystack(StoreSubscription $subscription)
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey) {
            // No key configured — skip remote verify, trust the callback
            return true;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecretKey,
        ])->get('https://api.paystack.co/transaction/verify/' . $subscription->payment_reference);

        return $response->successful() && $response->json('data.status') === 'success';
    }

    /**
     * Verify Flutterwave payment
     */
    private function verifyFlutterwave(StoreSubscription $subscription)
    {
        $flutterwaveSecretKey = config('services.flutterwave.secret_key');

        if (!$flutterwaveSecretKey) {
            return true;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $flutterwaveSecretKey,
        ])->get('https://api.flutterwave.com/v3/transactions/' . $subscription->payment_reference . '/verify');

        return $response->successful() && $response->json('data.status') === 'successful';
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Request $request, $storeId)
    {
        $user = auth()->user();
        $store = $user->stores()->findOrFail($storeId);

        if (!$store) {
            return redirect()->route('advertiser.store.index');
        }

        $validated = $request->validate([
            'store_plan_id' => 'required|exists:store_plans,id',
        ]);

        $newPlan = StorePlan::findOrFail($validated['store_plan_id']);

        // Ensure plan is for the same store type
        if ($newPlan->store_type !== $store->plan->store_type) {
            return back()->withErrors(['error' => 'Cannot switch to a different store type.']);
        }

        // Check if downgrading and product count exceeds new limit
        if ($newPlan->product_limit !== null) {
            $productCount = $store->products()->count();
            if ($productCount > $newPlan->product_limit) {
                return back()->withErrors([
                    'error' => "Cannot downgrade. You have {$productCount} products but the new plan allows only {$newPlan->product_limit}. Please remove some products first."
                ]);
            }
        }

        $store->update([
            'store_plan_id' => $validated['store_plan_id'],
        ]);

        return back()->with('success', 'Plan changed successfully! The new rate will apply on your next renewal.');
    }
}
