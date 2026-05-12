<?php

namespace App\Jobs;

use App\Models\Store;
use App\Notifications\StoreSubscriptionExpiredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeactivateExpiredStoreJob implements ShouldQueue
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
            Log::warning('Store not found for deactivation', ['store_id' => $this->store->id]);
            return;
        }

        // Check if store is already inactive
        if (!$store->is_active) {
            Log::info('Store already inactive', ['store_id' => $store->id]);
            return;
        }

        // Check if subscription has actually expired
        if ($store->isSubscriptionActive()) {
            Log::info('Store subscription is still active', [
                'store_id' => $store->id,
                'subscription_end_date' => $store->subscription_end_date,
            ]);
            return;
        }

        // Deactivate store
        try {
            $store->update([
                'is_active' => false,
                'status' => 'inactive',
            ]);

            // Send notification to store owner
            $store->user->notify(new StoreSubscriptionExpiredNotification($store));

            Log::info('Store deactivated due to expired subscription', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'user_id' => $store->user_id,
                'subscription_end_date' => $store->subscription_end_date,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to deactivate expired store', [
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
        Log::error('DeactivateExpiredStoreJob failed', [
            'store_id' => $this->store->id,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
