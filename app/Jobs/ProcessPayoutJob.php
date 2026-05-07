<?php

namespace App\Jobs;

use App\Models\PayoutRequest;
use App\Notifications\PayoutProcessedNotification;
use App\Services\PaystackService;
use App\Services\FlutterwaveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    public $backoff = [30, 120, 300]; // Retry after 30s, 2min, 5min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PayoutRequest $payout
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PaystackService $paystackService, FlutterwaveService $flutterwaveService): void
    {
        try {
            // Verify payout is still in processing status
            if ($this->payout->status !== 'processing') {
                Log::warning('Payout job skipped - status already changed', [
                    'payout_id' => $this->payout->id,
                    'status' => $this->payout->status,
                ]);
                return;
            }

            $result = null;

            // Process based on payment method
            if ($this->payout->payment_method === 'paystack') {
                $result = $this->processPaystack($paystackService);
            } elseif ($this->payout->payment_method === 'flutterwave') {
                $result = $this->processFlutterwave($flutterwaveService);
            } else {
                // Manual bank transfer - just mark as processing (admin will complete manually)
                Log::info('Manual bank transfer payout', ['payout_id' => $this->payout->id]);
                return;
            }

            if ($result['success']) {
                DB::transaction(function () use ($result) {
                    $this->payout->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'gateway_response' => $result['data'],
                    ]);

                    // Set transaction ID
                    $transactionId = $result['data']['reference'] ?? $result['data']['id'] ?? 'N/A';
                    $this->payout->transaction_id = $transactionId;
                    $this->payout->save();

                    // Send success notification
                    $this->payout->affiliate->notify(new PayoutProcessedNotification($this->payout));
                });

                Log::info('Payout processed successfully', [
                    'payout_id' => $this->payout->id,
                    'transaction_id' => $transactionId ?? null,
                ]);
            } else {
                // Mark as failed and return funds
                DB::transaction(function () use ($result) {
                    $this->payout->update([
                        'status' => 'failed',
                        'gateway_response' => ['error' => $result['message']],
                    ]);

                    // Return funds to affiliate balance
                    $this->payout->affiliate->update([
                        'balance' => DB::raw('balance + ' . $this->payout->amount),
                    ]);
                });

                Log::error('Payout processing failed', [
                    'payout_id' => $this->payout->id,
                    'error' => $result['message'],
                ]);

                // If it's a gateway error, don't retry
                if ($this->attempts() >= $this->tries) {
                    // Final failure - notify admin or affiliate
                    Log::critical('Payout failed after all retries', [
                        'payout_id' => $this->payout->id,
                        'attempts' => $this->attempts(),
                    ]);
                } else {
                    // Retry by throwing exception
                    throw new \Exception($result['message']);
                }
            }
        } catch (\Exception $e) {
            Log::error('Payout job exception', [
                'payout_id' => $this->payout->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark as failed on last attempt
            if ($this->attempts() >= $this->tries) {
                DB::transaction(function () use ($e) {
                    $this->payout->update([
                        'status' => 'failed',
                        'gateway_response' => ['error' => $e->getMessage()],
                    ]);

                    // Return funds to affiliate balance
                    $this->payout->affiliate->update([
                        'balance' => DB::raw('balance + ' . $this->payout->amount),
                    ]);
                });
            }

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Process payout via Paystack
     */
    protected function processPaystack(PaystackService $paystackService): array
    {
        $paymentDetails = $this->payout->payment_details;

        if (!isset($paymentDetails['account_number']) || !isset($paymentDetails['bank_code'])) {
            return [
                'success' => false,
                'message' => 'Invalid payment details for Paystack',
            ];
        }

        // Create recipient
        $recipient = $paystackService->createTransferRecipient(
            $paymentDetails['account_number'],
            $paymentDetails['account_name'],
            $paymentDetails['bank_code']
        );

        if (!$recipient['success']) {
            return $recipient;
        }

        // Initiate transfer
        return $paystackService->initiateTransfer(
            $this->payout->amount,
            $recipient['data']['recipient_code'],
            'Affiliate Payout #' . $this->payout->id
        );
    }

    /**
     * Process payout via Flutterwave
     */
    protected function processFlutterwave(FlutterwaveService $flutterwaveService): array
    {
        $paymentDetails = $this->payout->payment_details;

        if (!isset($paymentDetails['account_number']) || !isset($paymentDetails['bank_code'])) {
            return [
                'success' => false,
                'message' => 'Invalid payment details for Flutterwave',
            ];
        }

        return $flutterwaveService->initiateTransfer(
            $this->payout->amount,
            $paymentDetails['account_number'],
            $paymentDetails['bank_code'],
            $paymentDetails['account_name'],
            'PAYOUT_' . $this->payout->id . '_' . time()
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Payout job permanently failed', [
            'payout_id' => $this->payout->id,
            'error' => $exception->getMessage(),
        ]);

        // Ensure payout is marked as failed and funds returned
        try {
            DB::transaction(function () use ($exception) {
                if ($this->payout->status === 'processing') {
                    $this->payout->update([
                        'status' => 'failed',
                        'gateway_response' => ['error' => 'Job failed: ' . $exception->getMessage()],
                    ]);

                    $this->payout->affiliate->update([
                        'balance' => DB::raw('balance + ' . $this->payout->amount),
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::emergency('Failed to handle payout job failure', [
                'payout_id' => $this->payout->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
