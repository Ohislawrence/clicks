<?php

namespace App\Console\Commands;

use App\Services\CpaleadService;
use Illuminate\Console\Command;

class SyncCpaleadOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpalead:sync {--disable-missing : Deactivate CPAlead offers that are no longer returned by the API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync offers from the CPAlead network into the platform';

    public function handle(CpaleadService $cpalead)
    {
        if (!$cpalead->isConfigured()) {
            $this->error('CPAlead service is not fully configured. Please set CPALEAD_API_KEY, CPALEAD_BASE_URL and CPALEAD_ADVERTISER_ID in your environment.');
            return 1;
        }

        if ($this->option('disable-missing')) {
            config(['services.cpalead.disable_missing_offers' => true]);
        }

        $this->info('Fetching offers from CPAlead...');
        $rawOffers = $cpalead->fetchOffers();

        if (empty($rawOffers)) {
            $this->warn('No offers were returned from the CPAlead API. Check your configuration and network connectivity.');
            return 0;
        }

        $this->info('Importing offers...');
        $results = $cpalead->importOffers($rawOffers);

        $created = collect($results)->where('status', 'created')->count();
        $updated = collect($results)->where('status', 'updated')->count();

        $this->info("Imported {$created} new CPAlead offers.");
        $this->info("Updated {$updated} existing CPAlead offers.");

        if ($this->option('disable-missing')) {
            $this->info('Any CPAlead offers not returned by the API were deactivated.');
        }

        return 0;
    }
}

