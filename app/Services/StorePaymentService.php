<?php

namespace App\Services;

use App\Models\Store;
use App\Models\StoreOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorePaymentService
{
    /**
     * Initialize payment for a store order.
     *
     * @param StoreOrder $order
     * @param Store $store
     * @return array|null
     */
    public function initializeOrderPayment(StoreOrder $order, Store $store): ?array
    {
        if ($store->payment_provider === 'paystack') {
            return $this->initializePaystackPayment($order, $store);
        }

        if ($store->payment_provider === 'flutterwave') {
            return $this->initializeFlutterwavePayment($order, $store);
        }

        return null;
    }

    /**
     * Initialize Paystack payment.
     *
     * @param StoreOrder $order
     * @param Store $store
     * @return array|null
     */
    protected function initializePaystackPayment(StoreOrder $order, Store $store): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $store->payment_secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $order->customer_email,
                'amount' => $order->total * 100, // Convert to kobo
                'reference' => $order->payment_reference,
                'callback_url' => route('storefront.checkout.verify', [
                    'slug' => $store->slug,
                    'reference' => $order->payment_reference,
                ]),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'store_id' => $store->id,
                    'customer_name' => $order->customer_name,
                ],
            ]);

            if ($response->successful() && $response->json('status')) {
                return [
                    'payment_url' => $response->json('data.authorization_url'),
                    'reference' => $order->payment_reference,
                ];
            }

            Log::error('Paystack payment initialization failed', [
                'order_id' => $order->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Paystack payment initialization error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Initialize Flutterwave payment.
     *
     * @param StoreOrder $order
     * @param Store $store
     * @return array|null
     */
    protected function initializeFlutterwavePayment(StoreOrder $order, Store $store): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $store->payment_secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $order->payment_reference,
                'amount' => $order->total,
                'currency' => 'NGN',
                'redirect_url' => route('storefront.checkout.verify', [
                    'slug' => $store->slug,
                    'reference' => $order->payment_reference,
                ]),
                'customer' => [
                    'email' => $order->customer_email,
                    'name' => $order->customer_name,
                    'phonenumber' => $order->customer_phone,
                ],
                'customizations' => [
                    'title' => $store->name,
                    'description' => 'Order #' . $order->order_number,
                    'logo' => $store->logo ? asset('storage/' . $store->logo) : null,
                ],
                'meta' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'store_id' => $store->id,
                ],
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'payment_url' => $response->json('data.link'),
                    'reference' => $order->payment_reference,
                ];
            }

            Log::error('Flutterwave payment initialization failed', [
                'order_id' => $order->id,
                'response' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Flutterwave payment initialization error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify Paystack payment.
     *
     * @param string $reference
     * @param string $secretKey
     * @return array|null
     */
    public function verifyPaystackPayment(string $reference, string $secretKey): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                return [
                    'success' => $data['status'] === 'success',
                    'amount' => $data['amount'] / 100, // Convert from kobo
                    'reference' => $data['reference'],
                    'paid_at' => $data['paid_at'] ?? now(),
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Paystack verification error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify Flutterwave payment.
     *
     * @param string $transactionId
     * @param string $secretKey
     * @return array|null
     */
    public function verifyFlutterwavePayment(string $transactionId, string $secretKey): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json('data');

                return [
                    'success' => $data['status'] === 'successful',
                    'amount' => $data['amount'],
                    'reference' => $data['tx_ref'],
                    'paid_at' => $data['created_at'] ?? now(),
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Flutterwave verification error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
