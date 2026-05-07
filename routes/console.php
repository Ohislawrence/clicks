<?php

use App\Jobs\SendWeeklyPerformanceSummaries;
use App\Services\OfferCapService;
use App\Services\TierService;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

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
