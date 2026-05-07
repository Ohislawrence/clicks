<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateAffiliateCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliates:generate-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique affiliate codes for existing affiliates without codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating affiliate codes for existing affiliates...');

        // Get all affiliates without codes
        $affiliates = User::role('affiliate')
            ->whereNull('affiliate_code')
            ->get();

        if ($affiliates->isEmpty()) {
            $this->info('All affiliates already have codes!');
            return 0;
        }

        $bar = $this->output->createProgressBar($affiliates->count());
        $bar->start();

        $generated = 0;

        foreach ($affiliates as $affiliate) {
            $affiliate->affiliate_code = User::generateUniqueAffiliateCode();
            $affiliate->saveQuietly();
            $generated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Generated {$generated} affiliate codes successfully!");

        return 0;
    }
}
