<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreSubscriptionService
{
    /**
     * Initialize subscription payment.
     *
     * @param Store $store
     * @param StorePlan $plan
     * @param string $billingCycle
     * @return array|null
     */
    public function initializeSubscriptionPayment(Store $store, StorePlan $plan, string $billingCycle): ?array
    {
        $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        // Calculate subscription period
        $periodStart = now();
        $periodEnd = $billingCycle === 'yearly'
            ? $periodStart->copy()->addYear()
            : $periodStart->copy()->addMonth();

        // Create subscription record
        $subscription = StoreSubscription::create([
            'store_id' => $store->id,
            'store_plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'amount' => $amount,
            'payment_reference' => $this->generateReference(),
            'status' => 'pending',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);

        // Check if store uses platform keys or custom keys
        if ($store->payment_method === 'link') {
            return [
                'payment_url' => $store->payment_link,
                'reference' => $subscription->payment_reference,
            ];
        }

        // Initialize payment with custom API keys
        if ($store->payment_provider === 'paystack') {
            return $this->initializePaystackSubscription($subscription, $store);
        }

        if ($store->payment_provider === 'flutterwave') {
            return $this->initializeFlutterwaveSubscription($subscription, $store);
        }

        return null;
    }

    /**
     * Initialize subscription payment using platform keys.
     *
     * @param StorePlan $plan
     * @param string $billingCycle
     * @param string $email
     * @param string $name
     * @param int $userId
     * @param string $provider (paystack or flutterwave)
     * @return array|null
     */
    public function initializePlatformSubscriptionPayment(
        StorePlan $plan,
        string $billingCycle,
        string $email,
        string $name,
        int $userId,
        string $provider = 'paystack'
    ): ?array {
        $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        $reference = $this->generateReference();

        if ($provider === 'paystack') {
            return $this->initializePlatformPaystack($plan, $amount, $reference, $email, $name, $userId, $billingCycle);
        }

        if ($provider === 'flutterwave') {
            return $this->initializePlatformFlutterwave($plan, $amount, $reference, $email, $name, $userId, $billingCycle);
        }

        return null;
    }

    /**
     * Initialize Paystack subscription payment (custom store keys).
     */
    protected function initializePaystackSubscription(StoreSubscription $subscription, Store $store): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $store->payment_secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $store->user->email,
                'amount' => $subscription->amount * 100, // Convert to kobo
                'reference' => $subscription->payment_reference,
                'callback_url' => route('advertiser.store.subscription.verify', [
                    'reference' => $subscription->payment_reference,
                ]),
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'store_id' => $store->id,
                    'plan_name' => $subscription->plan->name,
                    'billing_cycle' => $subscription->billing_cycle,
                ],
            ]);

            if ($response->successful() && $response->json('status')) {
                return [
                    'payment_url' => $response->json('data.authorization_url'),
                    'reference' => $subscription->payment_reference,
                ];
            }

            Log::error('Paystack subscription initialization failed', [
                'subscription_id' => $subscription->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Paystack subscription initialization error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Initialize Flutterwave subscription payment (custom store keys).
     */
    protected function initializeFlutterwaveSubscription(StoreSubscription $subscription, Store $store): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $store->payment_secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $subscription->payment_reference,
                'amount' => $subscription->amount,
                'currency' => 'NGN',
                'redirect_url' => route('advertiser.store.subscription.verify', [
                    'reference' => $subscription->payment_reference,
                ]),
                'customer' => [
                    'email' => $store->user->email,
                    'name' => $store->user->name,
                ],
                'customizations' => [
                    'title' => 'Store Subscription',
                    'description' => $subscription->plan->name . ' - ' . ucfirst($subscription->billing_cycle),
                ],
                'meta' => [
                    'subscription_id' => $subscription->id,
                    'store_id' => $store->id,
                    'plan_name' => $subscription->plan->name,
                ],
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'payment_url' => $response->json('data.link'),
                    'reference' => $subscription->payment_reference,
                ];
            }

            Log::error('Flutterwave subscription initialization failed', [
                'subscription_id' => $subscription->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Flutterwave subscription initialization error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Initialize platform Paystack payment.
     */
    protected function initializePlatformPaystack(
        StorePlan $plan,
        float $amount,
        string $reference,
        string $email,
        string $name,
        int $userId,
        string $billingCycle
    ): ?array {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $reference,
                'callback_url' => route('advertiser.store.subscription.callback', [
                    'reference' => $reference,
                ]),
                'metadata' => [
                    'user_id' => $userId,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'billing_cycle' => $billingCycle,
                ],
            ]);

            if ($response->successful() && $response->json('status')) {
                return [
                    'payment_url' => $response->json('data.authorization_url'),
                    'reference' => $reference,
                ];
            }

            Log::error('Platform Paystack subscription initialization failed', [
                'plan_id' => $plan->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Platform Paystack subscription initialization error', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Initialize platform Flutterwave payment.
     */
    protected function initializePlatformFlutterwave(
        StorePlan $plan,
        float $amount,
        string $reference,
        string $email,
        string $name,
        int $userId,
        string $billingCycle
    ): ?array {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.flutterwave.secret_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $reference,
                'amount' => $amount,
                'currency' => 'NGN',
                'redirect_url' => route('advertiser.store.subscription.callback', [
                    'reference' => $reference,
                ]),
                'customer' => [
                    'email' => $email,
                    'name' => $name,
                ],
                'customizations' => [
                    'title' => 'Store Subscription',
                    'description' => $plan->name . ' - ' . ucfirst($billingCycle),
                ],
                'meta' => [
                    'user_id' => $userId,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'billing_cycle' => $billingCycle,
                ],
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'payment_url' => $response->json('data.link'),
                    'reference' => $reference,
                ];
            }

            Log::error('Platform Flutterwave subscription initialization failed', [
                'plan_id' => $plan->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Platform Flutterwave subscription initialization error', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Process subscription after successful payment.
     *
     * @param StoreSubscription $subscription
     * @return bool
     */
    public function processSubscription(StoreSubscription $subscription): bool
    {
        try {
            $subscription->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $store = $subscription->store;
            $store->update([
                'subscription_status' => 'active',
                'subscription_start_date' => $subscription->period_start,
                'subscription_end_date' => $subscription->period_end,
                'expiry_reminder_sent' => false,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error processing subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate unique payment reference.
     */
    protected function generateReference(): string
    {
        return 'SUB_' . strtoupper(Str::random(20)) . '_' . time();
    }
}
