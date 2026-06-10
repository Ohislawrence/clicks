<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreOrder;
use App\Models\StoreProduct;
use App\Notifications\StoreOrderConfirmationNotification;
use App\Notifications\StoreOrderReceivedNotification;
use App\Services\StorePaymentSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StorePaymentWebhookController extends Controller
{
    public function __construct(private StorePaymentSplitService $splitService) {}

    /**
     * Handle Paystack webhook for store order payments.
     * Secured via HMAC-SHA512 signature verification.
     */
    public function paystack(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Paystack-Signature');
        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey || !$signature) {
            return response('Unauthorized', 401);
        }

        $computed = hash_hmac('sha512', $payload, $secretKey);
        if (!hash_equals($computed, $signature)) {
            Log::warning('Paystack webhook: invalid signature');
            return response('Unauthorized', 401);
        }

        $event = json_decode($payload, true);

        if (($event['event'] ?? '') !== 'charge.success') {
            return response('OK', 200);
        }

        $reference = $event['data']['reference'] ?? null;
        if (!$reference) {
            return response('OK', 200);
        }

        $order = StoreOrder::where('payment_reference', $reference)->first();

        if (!$order || $order->payment_status === 'paid') {
            return response('OK', 200);
        }

        $this->settleOrder($order, $request);

        return response('OK', 200);
    }

    /**
     * Handle Flutterwave webhook for store order payments.
     * Secured via secret hash header.
     */
    public function flutterwave(Request $request)
    {
        $secretHash = config('services.flutterwave.webhook_secret');
        $signature  = $request->header('verif-hash');

        if ($secretHash && $signature !== $secretHash) {
            Log::warning('Flutterwave webhook: invalid signature');
            return response('Unauthorized', 401);
        }

        $payload = $request->all();

        if (($payload['status'] ?? '') !== 'successful') {
            return response('OK', 200);
        }

        $reference = $payload['txRef'] ?? $payload['tx_ref'] ?? null;
        if (!$reference) {
            return response('OK', 200);
        }

        $order = StoreOrder::where('payment_reference', $reference)->first();

        if (!$order || $order->payment_status === 'paid') {
            return response('OK', 200);
        }

        $this->settleOrder($order, $request);

        return response('OK', 200);
    }

    private function settleOrder(StoreOrder $order, Request $request): void
    {
        DB::beginTransaction();
        try {
            $order->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            // Decrement stock for each product
            foreach ($order->items as $item) {
                $product = StoreProduct::find($item['product_id']);
                if ($product && $product->stock_quantity !== null) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Platform split settlement
            $store = $order->store;
            if ($store && $store->hasPlatformMode()) {
                // Load the affiliate link from the order — it was stored at checkout time
                // when the customer's browser made the request (cookie was available then).
                // Webhooks are server-to-server: cookies from the customer browser are NEVER
                // present, so we must not try to read them here.
                $affiliateLink = $order->affiliate_link_id
                    ? \App\Models\AffiliateLink::with('offer')->find($order->affiliate_link_id)
                    : null;
                $this->splitService->settle($order, $affiliateLink);
            }

            // Send notifications
            $this->sendOrderNotifications($order);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Webhook order settlement failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

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

        if ($link->offer->advertiser_id !== $store->user_id) {
            return null;
        }

        return $link;
    }

    private function sendOrderNotifications(StoreOrder $order): void
    {
        try {
            if ($order->customer_email) {
                Notification::route('mail', $order->customer_email)
                    ->notify(new StoreOrderConfirmationNotification($order));
            }
            $order->store->user->notify(new StoreOrderReceivedNotification($order));
        } catch (\Throwable $e) {
            Log::error('Webhook: failed to send order notifications', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
