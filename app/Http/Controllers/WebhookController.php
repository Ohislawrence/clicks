<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\StoreProduct;
use App\Models\StoreSubscription;
use App\Notifications\StoreOrderConfirmationNotification;
use App\Notifications\StoreOrderReceivedNotification;
use App\Notifications\StoreSubscriptionRenewedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class WebhookController extends Controller
{
    /**
     * Handle Paystack webhook for store orders.
     */
    public function paystackStoreOrder(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');

        // Only process successful charges
        if ($event !== 'charge.success') {
            return response()->json(['message' => 'Event not processed'], 200);
        }

        $reference = $data['reference'] ?? null;
        if (!$reference) {
            return response()->json(['error' => 'No reference'], 400);
        }

        try {
            // Pre-check without lock to avoid unnecessary lock contention
            $orderCheck = StoreOrder::with('store')->where('payment_reference', $reference)->first();

            if (!$orderCheck) {
                Log::warning('Order not found for Paystack webhook', ['reference' => $reference]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Verify signature before acquiring the lock
            if (!$this->verifyPaystackSignature($request, $orderCheck->store->webhook_secret)) {
                Log::warning('Invalid Paystack webhook signature for store', [
                    'store_id' => $orderCheck->store_id,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            DB::beginTransaction();

            // Re-fetch inside transaction with pessimistic lock so concurrent
            // webhooks for the same order are serialised, not double-processed.
            $order = StoreOrder::where('payment_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$order || $order->payment_status === 'paid') {
                DB::rollBack();
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Deduct stock for each item
            foreach ($order->items as $item) {
                $product = StoreProduct::lockForUpdate()->find($item['product_id']);
                if ($product && $product->stock_quantity !== null) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Send notifications (outside transaction — already committed)
            $order->load('store');
            $this->sendOrderNotifications($order);

            Log::info('Paystack order webhook processed successfully', [
                'order_id' => $order->id,
                'reference' => $reference
            ]);

            return response()->json(['message' => 'Webhook processed'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Paystack order webhook', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle Flutterwave webhook for store orders.
     */
    public function flutterwaveStoreOrder(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');

        // Only process successful charges
        if ($event !== 'charge.completed') {
            return response()->json(['message' => 'Event not processed'], 200);
        }

        $txRef = $data['tx_ref'] ?? null;
        if (!$txRef) {
            return response()->json(['error' => 'No transaction reference'], 400);
        }

        try {
            // Pre-check without lock to avoid unnecessary lock contention
            $orderCheck = StoreOrder::with('store')->where('payment_reference', $txRef)->first();

            if (!$orderCheck) {
                Log::warning('Order not found for Flutterwave webhook', ['tx_ref' => $txRef]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Verify signature before acquiring the lock
            if (!$this->verifyFlutterwaveSignature($request, $orderCheck->store->webhook_secret)) {
                Log::warning('Invalid Flutterwave webhook signature for store', [
                    'store_id' => $orderCheck->store_id,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            DB::beginTransaction();

            // Re-fetch inside transaction with pessimistic lock so concurrent
            // webhooks for the same order are serialised, not double-processed.
            $order = StoreOrder::where('payment_reference', $txRef)
                ->lockForUpdate()
                ->first();

            if (!$order || $order->payment_status === 'paid') {
                DB::rollBack();
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Deduct stock for each item
            foreach ($order->items as $item) {
                $product = StoreProduct::lockForUpdate()->find($item['product_id']);
                if ($product && $product->stock_quantity !== null) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Send notifications (outside transaction — already committed)
            $order->load('store');
            $this->sendOrderNotifications($order);

            Log::info('Flutterwave order webhook processed successfully', [
                'order_id' => $order->id,
                'tx_ref' => $txRef
            ]);

            return response()->json(['message' => 'Webhook processed'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Flutterwave order webhook', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle Paystack webhook for store subscriptions.
     */
    public function paystackStoreSubscription(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');

        // Only process successful charges
        if ($event !== 'charge.success') {
            return response()->json(['message' => 'Event not processed'], 200);
        }

        $reference = $data['reference'] ?? null;
        if (!$reference) {
            return response()->json(['error' => 'No reference'], 400);
        }

        try {
            $subCheck = StoreSubscription::with('store')->where('payment_reference', $reference)->first();

            if (!$subCheck) {
                Log::warning('Subscription not found for Paystack webhook', ['reference' => $reference]);
                return response()->json(['error' => 'Subscription not found'], 404);
            }

            if (!$this->verifyPaystackSignature($request, $subCheck->store->webhook_secret)) {
                Log::warning('Invalid Paystack subscription webhook signature for store', [
                    'store_id' => $subCheck->store_id,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            DB::beginTransaction();

            $subscription = StoreSubscription::where('payment_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$subscription || $subscription->status === 'paid') {
                DB::rollBack();
                return response()->json(['message' => 'Already processed'], 200);
            }

            $subscription->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $store = Store::lockForUpdate()->find($subscription->store_id);
            $store->update([
                'subscription_status' => 'active',
                'is_active' => true,
                'subscription_start_date' => $subscription->period_start,
                'subscription_end_date' => $subscription->period_end,
                'expiry_reminder_sent' => false,
            ]);

            DB::commit();

            $store->user->notify(new StoreSubscriptionRenewedNotification($store, $subscription));

            Log::info('Paystack subscription webhook processed successfully', [
                'subscription_id' => $subscription->id,
                'reference' => $reference
            ]);

            return response()->json(['message' => 'Webhook processed'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Paystack subscription webhook', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle Flutterwave webhook for store subscriptions.
     */
    public function flutterwaveStoreSubscription(Request $request)
    {
        $event = $request->input('event');
        $data = $request->input('data');

        // Only process successful charges
        if ($event !== 'charge.completed') {
            return response()->json(['message' => 'Event not processed'], 200);
        }

        $txRef = $data['tx_ref'] ?? null;
        if (!$txRef) {
            return response()->json(['error' => 'No transaction reference'], 400);
        }

        try {
            $subCheck = StoreSubscription::with('store')->where('payment_reference', $txRef)->first();

            if (!$subCheck) {
                Log::warning('Subscription not found for Flutterwave webhook', ['tx_ref' => $txRef]);
                return response()->json(['error' => 'Subscription not found'], 404);
            }

            if (!$this->verifyFlutterwaveSignature($request, $subCheck->store->webhook_secret)) {
                Log::warning('Invalid Flutterwave subscription webhook signature for store', [
                    'store_id' => $subCheck->store_id,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            DB::beginTransaction();

            $subscription = StoreSubscription::where('payment_reference', $txRef)
                ->lockForUpdate()
                ->first();

            if (!$subscription || $subscription->status === 'paid') {
                DB::rollBack();
                return response()->json(['message' => 'Already processed'], 200);
            }

            $subscription->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $store = Store::lockForUpdate()->find($subscription->store_id);
            $store->update([
                'subscription_status' => 'active',
                'is_active' => true,
                'subscription_start_date' => $subscription->period_start,
                'subscription_end_date' => $subscription->period_end,
                'expiry_reminder_sent' => false,
            ]);

            DB::commit();

            $store->user->notify(new StoreSubscriptionRenewedNotification($store, $subscription));

            Log::info('Flutterwave subscription webhook processed successfully', [
                'subscription_id' => $subscription->id,
                'tx_ref' => $txRef
            ]);

            return response()->json(['message' => 'Webhook processed'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Flutterwave subscription webhook', [
                'tx_ref' => $txRef,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Verify Paystack webhook signature using store's secret key.
     *
     * @param Request $request
     * @param string $secretKey The store's Paystack secret key
     * @return bool
     */
    protected function verifyPaystackSignature(Request $request, string $secretKey): bool
    {
        $signature = $request->header('X-Paystack-Signature');
        if (!$signature) {
            return false;
        }

        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);

        return hash_equals($computedSignature, $signature);
    }

    /**
     * Verify Flutterwave webhook signature using store's secret key.
     *
     * Note: Flutterwave uses a static secret hash (not the API secret key).
     * Advertisers must configure this in their Flutterwave dashboard and
     * provide it when setting up their store.
     *
     * @param Request $request
     * @param string $secretHash The store's Flutterwave secret hash
     * @return bool
     */
    protected function verifyFlutterwaveSignature(Request $request, string $secretHash): bool
    {
        $signature = $request->header('verif-hash');
        if (!$signature) {
            return false;
        }

        return hash_equals($secretHash, $signature);
    }

    /**
     * Send order notifications to customer and store owner.
     */
    protected function sendOrderNotifications(StoreOrder $order): void
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

        } catch (\Exception $e) {
            Log::error('Error sending order notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
