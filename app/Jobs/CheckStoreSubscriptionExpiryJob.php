<?php

namespace App\Jobs;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckStoreSubscriptionExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting store subscription expiry check');

        // Find stores expiring in 7 days (that haven't received reminder yet)
        $expiringStores = Store::where('is_active', true)
            ->where('expiry_reminder_sent', false)
            ->whereNotNull('subscription_end_date')
            ->whereDate('subscription_end_date', '<=', Carbon::now()->addDays(7))
            ->whereDate('subscription_end_date', '>', Carbon::now())
            ->get();

        Log::info('Found stores expiring soon', ['count' => $expiringStores->count()]);

        // Dispatch reminder jobs
        foreach ($expiringStores as $store) {
            dispatch(new SendStoreExpiryReminderJob($store));

            Log::info('Dispatched expiry reminder job', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'expiry_date' => $store->subscription_end_date,
            ]);
        }

        // Find stores that have expired
        $expiredStores = Store::where('is_active', true)
            ->whereNotNull('subscription_end_date')
            ->whereDate('subscription_end_date', '<', Carbon::now())
            ->get();

        Log::info('Found expired stores', ['count' => $expiredStores->count()]);

        // Dispatch deactivation jobs
        foreach ($expiredStores as $store) {
            dispatch(new DeactivateExpiredStoreJob($store));

            Log::info('Dispatched deactivation job', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'expiry_date' => $store->subscription_end_date,
            ]);
        }

        Log::info('Store subscription expiry check completed', [
            'expiring_stores' => $expiringStores->count(),
            'expired_stores' => $expiredStores->count(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckStoreSubscriptionExpiryJob failed', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
