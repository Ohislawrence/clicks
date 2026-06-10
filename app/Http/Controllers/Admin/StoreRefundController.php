<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserPayout;
use App\Models\Conversion;
use App\Models\StoreOrder;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StoreRefundController extends Controller
{
    /**
     * List orders with a pending refund request.
     */
    public function index(Request $request)
    {
        $refunds = StoreOrder::with(['store.user:id,name,email', 'affiliate:id,name,email'])
            ->where('refund_status', 'requested')
            ->latest('refund_requested_at')
            ->paginate(30)
            ->withQueryString();

        $stats = [
            'requested' => StoreOrder::where('refund_status', 'requested')->count(),
            'approved'  => StoreOrder::where('refund_status', 'approved')->count(),
            'rejected'  => StoreOrder::where('refund_status', 'rejected')->count(),
        ];

        return Inertia::render('Admin/StoreRefunds/Index', [
            'orders' => $refunds,
            'stats'  => $stats,
        ]);
    }

    /**
     * Approve a refund request.
     * - Debit the advertiser's balance by the advertiser_net_amount.
     * - Claw back affiliate commission (debit affiliate balance, floored at 0).
     * - Attempt a Paystack refund via platform key.
     * - Update order and conversion records.
     */
    public function approve(Request $request, StoreOrder $order)
    {
        if ($order->refund_status !== 'requested') {
            return back()->withErrors(['error' => 'This order is not in a refund-requested state.']);
        }

        if (!$order->isPlatformManaged()) {
            return back()->withErrors(['error' => 'Only platform-managed orders can be refunded through this system.']);
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $advertiser = User::findOrFail($order->store->user_id);

            // 1. Debit advertiser net amount (clawback what was credited on sale)
            $advertiserDeduct = min((float) $order->advertiser_net_amount, (float) $advertiser->advertiser_balance);
            if ($advertiserDeduct > 0) {
                $advertiser->decrement('advertiser_balance', $advertiserDeduct);

                WalletTransaction::create([
                    'user_id'     => $advertiser->id,
                    'amount'      => $advertiserDeduct,
                    'type'        => 'store_refund_clawback',
                    'status'      => 'success',
                    'reference'   => 'REFUND-ADV-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                    'description' => 'Refund clawback for order ' . $order->order_number,
                    'metadata'    => ['order_id' => $order->id, 'admin_id' => auth()->id()],
                ]);
            }

            // 2. Claw back affiliate commission
            if ($order->affiliate_id && $order->affiliate_commission_amount > 0) {
                $affiliate = User::find($order->affiliate_id);
                if ($affiliate) {
                    $affiliateDeduct = min((float) $order->affiliate_commission_amount, (float) $affiliate->balance);
                    if ($affiliateDeduct > 0) {
                        $affiliate->decrement('balance', $affiliateDeduct);
                    }
                }

                // Mark conversion as rejected
                if ($order->conversion_id) {
                    Conversion::find($order->conversion_id)?->update([
                        'status'           => 'rejected',
                        'rejection_reason' => 'Order refunded',
                    ]);
                }
            }

            // 3. Mark order as refunded
            $order->update([
                'refund_status'     => 'approved',
                'refund_approved_at' => now(),
                'refund_note'       => $validated['note'] ?? null,
            ]);
        });

        // 4. Attempt Paystack refund (non-blocking — log failure but don't abort)
        // For platform-mode orders, use the platform key.
        // For direct-mode orders, the customer paid directly to the store's Paystack account;
        // we cannot refund via our platform key. Log this so it can be handled manually.
        try {
            if ($order->payment_mode === 'direct') {
                Log::warning('StoreRefundController: direct-mode order requires manual refund via store owner Paystack account', [
                    'order_id'  => $order->id,
                    'reference' => $order->payment_reference,
                    'store_id'  => $order->store_id,
                ]);
            } else {
                $platformKey = config('services.paystack.secret_key');
                if ($platformKey && $order->payment_reference) {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $platformKey,
                        'Content-Type'  => 'application/json',
                    ])->post('https://api.paystack.co/refund', [
                        'transaction' => $order->payment_reference,
                        'amount'      => (int) ($order->total * 100), // kobo
                    ]);

                    if (!$response->successful() || !$response->json('status')) {
                        Log::warning('StoreRefundController: Paystack refund failed', [
                            'order_id'  => $order->id,
                            'response'  => $response->json(),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('StoreRefundController: Paystack refund exception', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Refund approved and balances adjusted.');
    }

    /**
     * Reject a refund request (no money moves).
     */
    public function reject(Request $request, StoreOrder $order)
    {
        if ($order->refund_status !== 'requested') {
            return back()->withErrors(['error' => 'This order is not in a refund-requested state.']);
        }

        $validated = $request->validate([
            'note' => 'required|string|max:500',
        ]);

        $order->update([
            'refund_status' => 'rejected',
            'refund_note'   => $validated['note'],
        ]);

        return back()->with('success', 'Refund request rejected.');
    }
}

