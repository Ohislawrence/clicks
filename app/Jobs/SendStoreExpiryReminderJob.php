<?php

namespace App\Jobs;

use App\Models\Store;
use App\Notifications\StoreSubscriptionExpiringNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendStoreExpiryReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected $store;

    /**
     * Create a new job instance.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Reload store to get latest data
        $store = Store::with(['user', 'plan'])->find($this->store->id);

        if (!$store) {
            Log::warning('Store not found for expiry reminder', ['store_id' => $this->store->id]);
            return;
        }

        // Check if store still needs reminder
        if ($store->expiry_reminder_sent) {
            Log::info('Expiry reminder already sent for store', ['store_id' => $store->id]);
            return;
        }

        // Check if subscription is still expiring soon
        if (!$store->isSubscriptionExpiringSoon()) {
            Log::info('Store subscription is not expiring soon', ['store_id' => $store->id]);
            return;
        }

        // Calculate days remaining
        $daysRemaining = now()->diffInDays($store->subscription_end_date, false);

        if ($daysRemaining < 0) {
            Log::info('Store subscription already expired', ['store_id' => $store->id]);
            return;
        }

        // Send notification to store owner
        try {
            $store->user->notify(new StoreSubscriptionExpiringNotification($store, (int)$daysRemaining));

            // Mark reminder as sent
            $store->update(['expiry_reminder_sent' => true]);

            Log::info('Expiry reminder sent successfully', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'user_id' => $store->user_id,
                'days_remaining' => $daysRemaining,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send expiry reminder', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendStoreExpiryReminderJob failed', [
            'store_id' => $this->store->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
