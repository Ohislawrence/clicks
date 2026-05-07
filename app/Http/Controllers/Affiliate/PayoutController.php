<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\Commission;
use App\Notifications\PayoutRequestReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $payouts = PayoutRequest::where('affiliate_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return Inertia::render('Affiliate/Payouts/Index', [
            'payouts' => $payouts,
            'balance' => $request->user()->balance,
            'pendingBalance' => $request->user()->pending_balance,
            'lifetimeEarnings' => $request->user()->lifetime_earnings,
            'minimumPayout' => 5000, // ₦5,000 minimum
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->balance < 5000) {
            return back()->with('error', 'Minimum payout amount is ₦5,000');
        }

        return Inertia::render('Affiliate/Payouts/Create', [
            'balance' => $user->balance,
            'minimumPayout' => 5000,
            'paymentDetails' => $user->payment_details,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:5000|max:' . $user->balance,
            'payment_method' => 'required|in:paystack,flutterwave,bank_transfer',
            'payment_details' => 'required|array',
            'payment_details.account_name' => 'required|string|max:255',
            'payment_details.account_number' => 'required|string|size:10',
            'payment_details.bank_name' => 'required|string|max:255',
            'payment_details.bank_code' => 'nullable|string|max:10',
        ]);

        // Check if user has a pending payout
        $hasPending = PayoutRequest::where('affiliate_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending payout request.');
        }

        // Check balance
        if ($validated['amount'] > $user->balance) {
            return back()->with('error', 'Insufficient balance.');
        }

        DB::transaction(function () use ($validated, $user, &$payout) {
            // Create payout request
            $payout = PayoutRequest::create([
                'affiliate_id' => $user->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_details' => $validated['payment_details'],
                'status' => 'pending',
            ]);

            // Get approved commissions to link
            $commissions = Commission::where('affiliate_id', $user->id)
                ->where('status', 'approved')
                ->whereNull('payout_request_id')
                ->get();

            // Link commissions to payout
            $totalLinked = 0;
            foreach ($commissions as $commission) {
                if ($totalLinked + $commission->amount <= $validated['amount']) {
                    $commission->update(['payout_request_id' => $payout->id]);
                    $totalLinked += $commission->amount;
                }
                if ($totalLinked >= $validated['amount']) {
                    break;
                }
            }

            // Deduct from balance
            $user->update([
                'balance' => DB::raw('balance - ' . $validated['amount']),
            ]);
        });

        // Send notification
        $user->notify(new PayoutRequestReceivedNotification($payout));

        return redirect()->route('affiliate.payouts.index')
            ->with('success', 'Payout request submitted successfully!');
    }

    public function cancel(PayoutRequest $payout)
    {
        // Check ownership
        if ($payout->affiliate_id !== auth()->id()) {
            abort(403);
        }

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be cancelled.');
        }

        DB::transaction(function () use ($payout) {
            // Unlink commissions
            Commission::where('payout_request_id', $payout->id)
                ->update(['payout_request_id' => null]);

            // Return to balance
            $payout->affiliate->update([
                'balance' => DB::raw('balance + ' . $payout->amount),
            ]);

            // Update payout status
            $payout->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Payout request cancelled.');
    }
}
