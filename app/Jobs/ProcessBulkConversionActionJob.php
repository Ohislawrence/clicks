<?php

namespace App\Jobs;

use App\Models\Conversion;
use App\Models\Offer;
use App\Notifications\ConversionApprovedNotification;
use App\Notifications\ConversionRejectedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBulkConversionActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes for bulk operations
    public $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $conversionIds,
        public string $action, // 'approve' or 'reject'
        public ?string $rejectionReason = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $conversions = Conversion::with(['affiliate', 'commission'])
                ->whereIn('id', $this->conversionIds)
                ->where('status', 'pending')
                ->get();

            if ($conversions->isEmpty()) {
                Log::warning('No pending conversions found for bulk action', [
                    'conversion_ids' => $this->conversionIds,
                    'action' => $this->action,
                ]);
                return;
            }

            if ($this->action === 'approve') {
                $this->processBulkApprove($conversions);
            } elseif ($this->action === 'reject') {
                $this->processBulkReject($conversions);
            } else {
                Log::error('Invalid bulk action', ['action' => $this->action]);
                return;
            }

            Log::info('Bulk conversion action completed', [
                'action' => $this->action,
                'count' => $conversions->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk conversion action failed', [
                'action' => $this->action,
                'conversion_ids' => $this->conversionIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Process bulk approval
     */
    protected function processBulkApprove($conversions): void
    {
        DB::transaction(function () use ($conversions) {
            foreach ($conversions as $conversion) {
                // Update conversion status
                $conversion->update(['status' => 'approved']);

                // Update commission status
                $conversion->commission?->update(['status' => 'approved']);

                // Update affiliate balance
                $affiliate = $conversion->affiliate;
                $affiliate->update([
                    'pending_balance' => DB::raw('pending_balance - ' . $conversion->commission_amount),
                    'balance' => DB::raw('balance + ' . $conversion->commission_amount),
                ]);

                // Send notification (already queued via ShouldQueue)
                $affiliate->notify(new ConversionApprovedNotification($conversion));
            }
        });
    }

    /**
     * Process bulk rejection
     */
    protected function processBulkReject($conversions): void
    {
        DB::transaction(function () use ($conversions) {
            foreach ($conversions as $conversion) {
                // Update conversion status
                $conversion->update([
                    'status' => 'rejected',
                    'rejection_reason' => $this->rejectionReason ?? 'Bulk rejection',
                ]);

                // Update commission status
                $conversion->commission?->update(['status' => 'rejected']);

                // Remove from affiliate pending balance
                $affiliate = $conversion->affiliate;
                $affiliate->update([
                    'pending_balance' => DB::raw('pending_balance - ' . $conversion->commission_amount),
                ]);

                // Send notification (already queued via ShouldQueue)
                $affiliate->notify(new ConversionRejectedNotification(
                    $conversion,
                    $this->rejectionReason ?? 'Bulk rejection'
                ));
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Bulk conversion action job permanently failed', [
            'action' => $this->action,
            'conversion_ids' => $this->conversionIds,
            'error' => $exception->getMessage(),
        ]);
    }
}
