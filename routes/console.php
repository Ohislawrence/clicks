<?php

use App\Jobs\SendWeeklyPerformanceSummaries;
use App\Jobs\CheckStoreSubscriptionExpiryJob;
// use App\Services\CpaleadService;
use App\Services\OfferCapService;
use App\Services\TierService;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Artisan::command('cpalead:sync {--disable-missing}', function () {
//     $service = app(CpaleadService::class);
//
//     if (! $service->isConfigured()) {
//         $this->error('CPAlead service is not fully configured. Please set CPALEAD_API_KEY, CPALEAD_BASE_URL and CPALEAD_ADVERTISER_ID in your environment.');
//         return 1;
//     }
//
//     $this->info('Fetching offers from CPAlead...');
//     $rawOffers = $service->fetchOffers();
//
//     if (empty($rawOffers)) {
//         $this->warn('No offers were returned from the CPAlead API. Check your configuration and network connectivity.');
//         return 0;
//     }
//
//     if ($this->option('disable-missing')) {
//         config(['services.cpalead.disable_missing_offers' => true]);
//     }
//
//     $this->info('Importing offers...');
//     $results = $service->importOffers($rawOffers);
//
//     $created = collect($results)->where('status', 'created')->count();
//     $updated = collect($results)->where('status', 'updated')->count();
//
//     $this->info("Imported {$created} new CPAlead offers.");
//     $this->info("Updated {$updated} existing CPAlead offers.");
//
//     if ($this->option('disable-missing')) {
//         $this->info('Any CPAlead offers not returned by the API were deactivated.');
//     }
//
//     return 0;
// })->purpose('Sync CPAlead offers into the platform');
//
// Schedule::command('cpalead:sync')
//     ->hourly()
//     ->description('Sync CPAlead offers into the platform');

// Reset daily offer caps at midnight
Schedule::call(function () {
    app(OfferCapService::class)->resetDailyCaps();
})->daily()->description('Reset daily offer conversion caps');

// Reset monthly offer caps on the 1st of each month
Schedule::call(function () {
    app(OfferCapService::class)->resetMonthlyCaps();
})->monthlyOn(1, '00:00')->description('Reset monthly offer conversion caps');

// Update affiliate tiers weekly (every Monday at 1 AM)
Schedule::call(function () {
    $tierService = app(TierService::class);
    User::role('affiliate')->each(function ($affiliate) use ($tierService) {
        $tierService->updateAffiliateTier($affiliate);
    });
})->weekly()->mondays()->at('01:00')->description('Update affiliate tier levels');

// Send weekly performance summaries (every Monday at 9 AM)
Schedule::job(new SendWeeklyPerformanceSummaries())
    ->weekly()
    ->mondays()
    ->at('09:00')
    ->description('Send weekly performance summaries to users');

// Prune Telescope entries older than 7 days (daily at 2 AM)
Schedule::command('telescope:prune --hours=168')
    ->daily()
    ->at('02:00')
    ->description('Delete Telescope entries older than 7 days');

// Check store subscription expiry and send reminders (daily at 8:00 AM)
Schedule::job(new CheckStoreSubscriptionExpiryJob())
    ->daily()
    ->at('08:00')
    ->description('Check store subscriptions, send expiry reminders, and deactivate expired stores');
