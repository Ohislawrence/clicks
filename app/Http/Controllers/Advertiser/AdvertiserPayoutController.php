<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserPayout;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdvertiserPayoutController extends Controller
{
    /**
     * List the authenticated advertiser's payout requests.
     */
    public function index()
    {
        $user = auth()->user();

        $payouts = AdvertiserPayout::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Advertiser/Payouts/Index', [
            'balance' => (float) $user->advertiser_balance,
            'payouts' => $payouts,
        ]);
    }

    /**
     * Submit a new withdrawal request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:500',
            'payment_method'  => 'required|string|in:bank_transfer',
            'bank_name'       => 'required|string|max:100',
            'account_number'  => 'required|string|max:20',
            'account_name'    => 'required|string|max:100',
        ]);

        // Check balance
        if ($user->advertiser_balance < $validated['amount']) {
            return back()->withErrors([
                'amount' => 'Requested amount exceeds your current balance of ₦' .
                    number_format($user->advertiser_balance, 2) . '.',
            ]);
        }

        // Check for existing pending payout
        $pendingExists = AdvertiserPayout::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($pendingExists) {
            return back()->withErrors([
                'amount' => 'You already have a pending withdrawal request. Please wait for it to be processed.',
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            // Reserve amount (deduct from balance immediately so it can't be double-spent)
            $user->decrement('advertiser_balance', $validated['amount']);

            $payout = AdvertiserPayout::create([
                'user_id'         => $user->id,
                'amount'          => $validated['amount'],
                'status'          => 'pending',
                'payment_method'  => $validated['payment_method'],
                'payment_details' => json_encode([
                    'bank_name'      => $validated['bank_name'],
                    'account_number' => $validated['account_number'],
                    'account_name'   => $validated['account_name'],
                ]),
                'reference' => 'PAYOUT-' . strtoupper(Str::random(12)),
            ]);

            WalletTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $validated['amount'],
                'type'        => 'store_withdrawal',
                'status'      => 'pending',
                'reference'   => $payout->reference,
                'description' => 'Withdrawal request #' . $payout->reference,
                'metadata'    => ['payout_id' => $payout->id],
            ]);
        });

        return back()->with('success', 'Withdrawal request submitted successfully. We will process it within 2-3 business days.');
    }

    /**
     * Cancel a pending withdrawal (refund back to balance).
     */
    public function cancel(AdvertiserPayout $payout)
    {
        $user = auth()->user();

        if ($payout->user_id !== $user->id) {
            abort(403);
        }

        if ($payout->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending withdrawals can be cancelled.']);
        }

        DB::transaction(function () use ($user, $payout) {
            $payout->update(['status' => 'rejected', 'notes' => 'Cancelled by advertiser']);

            // Refund balance
            $user->increment('advertiser_balance', $payout->amount);

            WalletTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $payout->amount,
                'type'        => 'store_sale', // credit back
                'status'      => 'success',
                'reference'   => 'CANCEL-' . strtoupper(Str::random(10)),
                'description' => 'Withdrawal cancelled — funds returned to balance',
                'metadata'    => ['payout_id' => $payout->id],
            ]);
        });

        return back()->with('success', 'Withdrawal cancelled and funds returned to your balance.');
    }
}

