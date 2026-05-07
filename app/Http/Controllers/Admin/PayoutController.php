<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Notifications\PayoutApprovedNotification;
use App\Notifications\PayoutProcessedNotification;
use App\Notifications\PayoutRejectedNotification;
use App\Services\PaystackService;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    protected $paystackService;
    protected $flutterwaveService;

    public function __construct(PaystackService $paystackService, FlutterwaveService $flutterwaveService)
    {
        $this->paystackService = $paystackService;
        $this->flutterwaveService = $flutterwaveService;
    }

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

            $result = null;

            // Process based on payment method
            if ($payout->payment_method === 'paystack') {
                $result = $this->processPaystack($payout);
            } elseif ($payout->payment_method === 'flutterwave') {
                $result = $this->processFlutterwave($payout);
            } else {
                // For bank transfer, just mark as processing
                $payout->update([
                    'status' => 'processing',
                    'gateway_response' => ['message' => 'Manual bank transfer initiated'],
                ]);

                DB::commit();
                return back()->with('success', 'Payout marked as processing. Complete the bank transfer manually.');
            }

            if ($result['success']) {
                $payout->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'gateway_response' => $result['data'],
                ]);

                // Send processed notification
                $transactionId = $result['data']['reference'] ?? $result['data']['id'] ?? 'N/A';
                $payout->transaction_id = $transactionId;
                $payout->save();
                $payout->affiliate->notify(new PayoutProcessedNotification($payout));

                DB::commit();
                return back()->with('success', 'Payout processed successfully!');
            } else {
                $payout->update([
                    'status' => 'failed',
                    'gateway_response' => ['error' => $result['message']],
                ]);

                // Return funds to affiliate balance
                $payout->affiliate->update([
                    'balance' => DB::raw('balance + ' . $payout->amount),
                ]);

                DB::commit();
                return back()->with('error', 'Payout failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    protected function processPaystack(PayoutRequest $payout)
    {
        $paymentDetails = $payout->payment_details;

        // For Paystack, we need to create recipient and initiate transfer
        if (isset($paymentDetails['account_number']) && isset($paymentDetails['bank_code'])) {
            // Bank transfer via Paystack
            $recipient = $this->paystackService->createTransferRecipient(
                $paymentDetails['account_number'],
                $paymentDetails['account_name'],
                $paymentDetails['bank_code']
            );

            if (!$recipient['success']) {
                return $recipient;
            }

            return $this->paystackService->initiateTransfer(
                $payout->amount,
                $recipient['data']['recipient_code'],
                'Affiliate Payout #' . $payout->id
            );
        }

        return [
            'success' => false,
            'message' => 'Invalid payment details for Paystack',
        ];
    }

    protected function processFlutterwave(PayoutRequest $payout)
    {
        $paymentDetails = $payout->payment_details;

        if (isset($paymentDetails['account_number']) && isset($paymentDetails['bank_code'])) {
            return $this->flutterwaveService->initiateTransfer(
                $payout->amount,
                $paymentDetails['account_number'],
                $paymentDetails['bank_code'],
                $paymentDetails['account_name'],
                'PAYOUT_' . $payout->id . '_' . time()
            );
        }

        return [
            'success' => false,
            'message' => 'Invalid payment details for Flutterwave',
        ];
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
            $payout->affiliate->update([
                'balance' => DB::raw('balance + ' . $payout->amount),
            ]);

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
