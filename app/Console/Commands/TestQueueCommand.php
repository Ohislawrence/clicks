<?php

namespace App\Console\Commands;

use App\Jobs\ProcessClickJob;
use App\Models\AffiliateLink;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('queue:test')]
#[Description('Test queue system by dispatching a sample job')]
class TestQueueCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Queue System...');
        $this->newLine();

        // Check if we have affiliate links
        $link = AffiliateLink::first();
        
        if (!$link) {
            $this->error('❌ No affiliate links found. Please create an affiliate link first.');
            return 1;
        }

        $this->info("✓ Found affiliate link: {$link->tracking_code}");

        // Dispatch test job
        ProcessClickJob::dispatch(
            $link->tracking_code,
            '127.0.0.1',
            'Mozilla/5.0 (Test Browser)',
            'http://test.com',
            ['country_code' => 'NG', 'country_name' => 'Nigeria', 'city' => 'Lagos']
        );

        $this->info('✓ Job dispatched to queue');
        $this->newLine();

        // Check jobs table
        $pendingJobs = DB::table('jobs')->count();
        $this->info("📋 Pending jobs in queue: {$pendingJobs}");

        if ($pendingJobs > 0) {
            $this->newLine();
            $this->comment('💡 To process the job, run:');
            $this->line('   php artisan queue:work');
            $this->newLine();
            $this->comment('💡 Or start all services with:');
            $this->line('   composer dev');
        }

        return 0;
    }
}
