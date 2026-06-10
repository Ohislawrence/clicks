<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\WalletTransaction;
use App\Notifications\WalletDepositNotification;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WalletController extends Controller
{
    public function __construct(protected PaystackService $paystack) {}

    /**
     * Show wallet dashboard: balance, transaction history, and top-up form.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->with('offer:id,name')
            ->latest()
            ->paginate(20);

        return Inertia::render('Advertiser/Wallet/Index', [
            'balance'      => (float) $user->advertiser_balance,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Initiate a Paystack deposit into the advertiser wallet.
     */
    public function initiateDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000|max:10000000',
        ]);

        $user      = $request->user();
        $amount    = (float) $validated['amount'];
        $reference = 'WALLET-' . strtoupper(Str::random(16));

        // Create a pending wallet transaction so we can look it up on callback.
        WalletTransaction::create([
            'user_id'     => $user->id,
            'amount'      => $amount,
            'type'        => 'deposit',
            'status'      => 'pending',
            'reference'   => $reference,
            'description' => 'Wallet top-up via Paystack',
        ]);

        // Return metadata to the frontend so Paystack's inline JS can open the popup directly.
        // This avoids a server-to-server call to api.paystack.co (which may be blocked by CDN/firewall).
        return response()->json([
            'reference'  => $reference,
            'public_key' => config('services.paystack.public_key'),
            'email'      => $user->email,
            'amount'     => (int) ($amount * 100), // kobo
            'verify_url' => route('advertiser.wallet.verify'),
        ]);
    }

    /**
     * Paystack callback: verify the transaction and credit the wallet.
     */
    public function verifyDeposit(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference) {
            return redirect()->route('advertiser.wallet.index')
                ->with('error', 'Invalid callback — no reference provided.');
        }

        $transaction = WalletTransaction::where('reference', $reference)
            ->where('type', 'deposit')
            ->first();

        if (!$transaction) {
            return redirect()->route('advertiser.wallet.index')
                ->with('error', 'Transaction not found.');
        }

        // Idempotency: already processed
        if ($transaction->status === 'success') {
            return redirect()->route('advertiser.wallet.index')
                ->with('success', 'Deposit already credited to your wallet.');
        }

        $result = $this->paystack->verifyPayment($reference);

        if (!$result['success'] || !$result['paid']) {
            $transaction->update(['status' => 'failed']);

            return redirect()->route('advertiser.wallet.index')
                ->with('error', 'Payment could not be verified. If funds were deducted, please contact support.');
        }

        DB::transaction(function () use ($transaction, $result) {
            $transaction->update([
                'status'   => 'success',
                'metadata' => $result['data'],
            ]);

            // Credit advertiser wallet
            $transaction->user->increment('advertiser_balance', $transaction->amount);
        });

        // Notify advertiser
        $transaction->user->notify(new WalletDepositNotification($transaction->amount));

        return redirect()->route('advertiser.wallet.index')
            ->with('success', 'Your wallet has been credited with ₦' . number_format($transaction->amount, 2) . '.');
    }

    /**
     * Top up an offer's budget using the advertiser's wallet balance.
     * Re-activates a paused offer if the pause reason was budget exhaustion.
     */
    public function topUpOffer(Request $request, Offer $offer)
    {
        if ((int) $offer->advertiser_id !== (int) $request->user()->id && !session()->has('impersonate')) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user   = $request->user();
        $amount = (float) $validated['amount'];

        if ($user->advertiser_balance < $amount) {
            return back()->withErrors([
                'amount' => 'Insufficient wallet balance. Please deposit funds before topping up.',
            ]);
        }

        // Recalculate the conversion cap addition
        $payoutPerConversion = $offer->affiliate_payout ?? $offer->commission_rate;
        $additionalCap = ($payoutPerConversion > 0)
            ? (int) floor($amount / $payoutPerConversion)
            : 0;

        DB::transaction(function () use ($user, $offer, $amount, $additionalCap) {
            // Deduct from wallet
            $user->decrement('advertiser_balance', $amount);

            // Record transaction
            WalletTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $amount,
                'type'        => 'offer_topup',
                'status'      => 'success',
                'offer_id'    => $offer->id,
                'description' => "Budget top-up for offer: {$offer->name}",
            ]);

            // Update offer: increase budget and cap
            $offer->increment('budget_limit', $amount);

            if ($additionalCap > 0) {
                $offer->increment('total_conversion_cap', $additionalCap);
            }

            // Re-activate if paused due to budget/cap exhaustion
            $pauseReasons = [
                'Budget limit reached',
                'Total conversion cap reached',
                'Daily conversion cap reached',
                'Monthly conversion cap reached',
            ];

            if (!$offer->is_active && in_array($offer->pause_reason, $pauseReasons)) {
                $offer->update([
                    'is_active'    => true,
                    'pause_reason' => null,
                ]);
            }
        });

        return back()->with('success', "₦" . number_format($amount, 2) . " added to offer budget. " .
            ($additionalCap > 0 ? "Conversion cap increased by {$additionalCap}." : ''));
    }
}

