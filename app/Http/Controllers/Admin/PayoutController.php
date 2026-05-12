<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPayoutJob;
use App\Models\PayoutRequest;
use App\Notifications\PayoutApprovedNotification;
use App\Notifications\PayoutRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $payouts = PayoutRequest::with('affiliate:id,name,email')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Payouts/Index', [
            'payouts' => $payouts,
            'filters' => [
                'status' => $request->status,
            ],
        ]);
    }

    public function process(PayoutRequest $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be processed.');
        }

        try {
            DB::beginTransaction();

            $payout->update([
                'status' => 'processing',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Send approval notification
            $payout->affiliate->notify(new PayoutApprovedNotification($payout));

            DB::commit();

            // Dispatch job to process payment asynchronously
            ProcessPayoutJob::dispatch($payout);

            return back()->with('success', 'Payout is being processed. You will be notified when completed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PayoutRequest $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($payout, $validated) {
            $payout->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Return funds to affiliate balance
            $payout->affiliate->increment('balance', $payout->amount);

            // Send rejection notification
            $payout->affiliate->notify(new PayoutRejectedNotification($payout, $validated['rejection_reason']));
        });

        return back()->with('success', 'Payout rejected successfully.');
    }

    public function markCompleted(PayoutRequest $payout)
    {
        if ($payout->status !== 'processing') {
            return back()->with('error', 'Only processing payouts can be marked as completed.');
        }

        $payout->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Send processed notification if not already sent
        if (!$payout->transaction_id) {
            $payout->transaction_id = 'MANUAL_' . $payout->id;
            $payout->save();
        }
        $payout->affiliate->notify(new PayoutProcessedNotification($payout));

        return back()->with('success', 'Payout marked as completed.');
    }
}
