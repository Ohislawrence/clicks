<?php

namespace App\Jobs;

use App\Services\DeepseekMarketingAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessDeepseekLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $lead;
    public array $offers;
    public array $campaignContext;

    public function __construct(array $lead, array $offers, array $campaignContext = [])
    {
        $this->lead = $lead;
        $this->offers = $offers;
        $this->campaignContext = $campaignContext;
    }

    public function handle(DeepseekMarketingAgent $agent): void
    {
        $result = $agent->processLeadAcquisitionWorkflow(
            $this->lead,
            $this->offers,
            $this->campaignContext
        );

        Cache::put('deepseek.last_lead_workflow', $result, now()->addMinutes(60));

        Log::info('Deepseek lead workflow completed', [
            'lead' => $this->lead['email'] ?? null,
            'result' => $result,
        ]);
    }

    public function tags(): array
    {
        return ['deepseek', 'ai-workflow'];
    }
}

