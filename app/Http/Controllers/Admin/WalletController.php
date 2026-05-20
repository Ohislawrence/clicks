<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * List all wallet transactions across all advertisers, with filters.
     */
    public function index(Request $request)
    {
        $transactions = WalletTransaction::with('user:id,name,email', 'offer:id,name')
            ->when($request->advertiser_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                )->orWhere('reference', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Summary stats
        $stats = [
            'total_deposited'   => WalletTransaction::where('type', 'deposit')->where('status', 'success')->sum('amount'),
            'total_allocated'   => WalletTransaction::whereIn('type', ['offer_allocation', 'offer_topup'])->where('status', 'success')->sum('amount'),
            'total_refunded'    => WalletTransaction::where('type', 'offer_refund')->where('status', 'success')->sum('amount'),
            'pending_deposits'  => WalletTransaction::where('type', 'deposit')->where('status', 'pending')->count(),
        ];

        $advertisers = User::role('advertiser')->orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Wallets/Index', [
            'transactions' => $transactions,
            'stats'        => $stats,
            'advertisers'  => $advertisers,
            'filters'      => $request->only(['advertiser_id', 'type', 'status', 'search']),
        ]);
    }

    /**
     * Show a single advertiser's wallet: balance, offer budgets, and transaction history.
     */
    public function show(User $user)
    {
        abort_if(!$user->hasRole('advertiser'), 404);

        $user->load('offers:id,advertiser_id,name,is_active,budget_limit,spent_budget,total_conversion_cap,total_conversions,auto_pause_on_cap,pause_reason');

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->with('offer:id,name')
            ->latest()
            ->paginate(20);

        $offerBudgetSummary = $user->offers->map(fn ($offer) => [
            'id'                   => $offer->id,
            'name'                 => $offer->name,
            'is_active'            => $offer->is_active,
            'budget_limit'         => (float) $offer->budget_limit,
            'spent_budget'         => (float) $offer->spent_budget,
            'remaining_budget'     => max(0, (float) $offer->budget_limit - (float) $offer->spent_budget),
            'total_conversion_cap' => $offer->total_conversion_cap,
            'total_conversions'    => $offer->total_conversions,
            'auto_pause_on_cap'    => $offer->auto_pause_on_cap,
            'pause_reason'         => $offer->pause_reason,
        ]);

        return Inertia::render('Admin/Wallets/Show', [
            'advertiser'         => $user,
            'balance'            => (float) $user->advertiser_balance,
            'transactions'       => $transactions,
            'offerBudgetSummary' => $offerBudgetSummary,
        ]);
    }

    /**
     * Manually credit an advertiser's wallet (e.g., bank transfer received, adjustment).
     */
    public function credit(Request $request, User $user)
    {
        abort_if(!$user->hasRole('advertiser'), 404);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:10000000',
            'note'   => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->increment('advertiser_balance', $validated['amount']);

            WalletTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $validated['amount'],
                'type'        => 'deposit',
                'status'      => 'success',
                'reference'   => 'ADMIN-CREDIT-' . strtoupper(substr(md5(uniqid()), 0, 12)),
                'description' => 'Admin manual credit: ' . $validated['note'],
                'metadata'    => ['admin_id' => auth()->id(), 'note' => $validated['note']],
            ]);
        });

        return back()->with('success',
            '₦' . number_format($validated['amount'], 2) . ' credited to ' . $user->name . '\'s wallet.'
        );
    }

    /**
     * Manually debit an advertiser's wallet (e.g., chargeback, correction).
     */
    public function debit(Request $request, User $user)
    {
        abort_if(!$user->hasRole('advertiser'), 404);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:10000000',
            'note'   => 'required|string|max:255',
        ]);

        if ($user->advertiser_balance < $validated['amount']) {
            return back()->withErrors([
                'amount' => 'Cannot debit more than the current balance of ₦' .
                    number_format($user->advertiser_balance, 2) . '.',
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->decrement('advertiser_balance', $validated['amount']);

            WalletTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $validated['amount'],
                'type'        => 'offer_refund', // reuses credit-side enum; stored as negative intent via note
                'status'      => 'success',
                'description' => 'Admin manual debit: ' . $validated['note'],
                'metadata'    => ['admin_id' => auth()->id(), 'note' => $validated['note'], 'direction' => 'debit'],
            ]);
        });

        return back()->with('success',
            '₦' . number_format($validated['amount'], 2) . ' debited from ' . $user->name . '\'s wallet.'
        );
    }

    /**
     * Mark a pending deposit as failed (e.g., Paystack glitch, duplicate).
     */
    public function failDeposit(Request $request, WalletTransaction $transaction)
    {
        abort_if($transaction->type !== 'deposit' || $transaction->status !== 'pending', 422);

        $transaction->update([
            'status'   => 'failed',
            'metadata' => array_merge($transaction->metadata ?? [], [
                'failed_by'  => auth()->id(),
                'failed_at'  => now()->toISOString(),
                'fail_note'  => $request->input('note', 'Marked failed by admin'),
            ]),
        ]);

        return back()->with('success', 'Deposit marked as failed.');
    }
}
