<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserPayout;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdvertiserPayoutController extends Controller
{
    /**
     * List all payout requests with optional status filter.
     */
    public function index(Request $request)
    {
        $payouts = AdvertiserPayout::with('user:id,name,email')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $stats = [
            'pending'    => AdvertiserPayout::where('status', 'pending')->count(),
            'processing' => AdvertiserPayout::where('status', 'processing')->count(),
            'total_paid' => AdvertiserPayout::where('status', 'completed')->sum('amount'),
        ];

        return Inertia::render('Admin/AdvertiserPayouts/Index', [
            'payouts'  => $payouts,
            'stats'    => $stats,
            'filters'  => $request->only(['status']),
        ]);
    }

    /**
     * Mark a payout as completed (manual transfer done).
     */
    public function approve(Request $request, AdvertiserPayout $payout)
    {
        if (!in_array($payout->status, ['pending', 'processing'])) {
            return back()->withErrors(['error' => 'Payout cannot be approved in its current state.']);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $payout->update([
            'status'       => 'completed',
            'processed_at' => now(),
            'notes'        => $validated['notes'] ?? null,
        ]);

        // Update the corresponding wallet transaction to success
        WalletTransaction::where('reference', $payout->reference)->update(['status' => 'success']);

        return back()->with('success', 'Payout marked as completed.');
    }

    /**
     * Reject a payout and return funds to the advertiser's balance.
     */
    public function reject(Request $request, AdvertiserPayout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending payouts can be rejected.']);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($payout, $validated) {
            $payout->update([
                'status' => 'rejected',
                'notes'  => $validated['notes'],
            ]);

            // Refund balance
            $advertiser = User::findOrFail($payout->user_id);
            $advertiser->increment('advertiser_balance', $payout->amount);

            WalletTransaction::create([
                'user_id'     => $payout->user_id,
                'amount'      => $payout->amount,
                'type'        => 'store_sale', // credit back
                'status'      => 'success',
                'reference'   => 'REJECT-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                'description' => 'Withdrawal rejected by admin — funds returned. Reason: ' . $validated['notes'],
                'metadata'    => ['payout_id' => $payout->id, 'admin_id' => auth()->id()],
            ]);

            // Mark original withdrawal transaction as failed
            WalletTransaction::where('reference', $payout->reference)->update(['status' => 'failed']);
        });

        return back()->with('success', 'Payout rejected and funds returned to advertiser balance.');
    }
}

