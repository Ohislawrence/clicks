<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDeepseekLeadJob;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeepseekController extends Controller
{
    public function testWorkflow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deepseek_ai_enabled' => 'boolean',
            'deepseek_auto_offer_recommendation' => 'boolean',
            'deepseek_lead_score_threshold' => 'required|numeric|min:0|max:100',
        ]);

        Cache::forever('deepseek_ai_enabled', $validated['deepseek_ai_enabled']);
        Cache::forever('deepseek_auto_offer_recommendation', $validated['deepseek_auto_offer_recommendation']);
        Cache::forever('deepseek_lead_score_threshold', $validated['deepseek_lead_score_threshold']);
        $offers = Offer::where('is_active', true)
            ->take(6)
            ->get([
                'id',
                'name',
                'description',
                'commission_model',
                'commission_rate',
                'affiliate_payout',
                'advertiser_payout',
                'platform_spread_percentage',
                'cookie_duration',
            ])
            ->toArray();

        if (empty($offers)) {
            return back()->with('warning', 'No active offers are available to test the Deepseek workflow.');
        }

        $lead = [
            'name' => 'Admin Test Lead',
            'email' => 'test+deepseek@clicksintel.local',
            'company' => 'ClicksIntel',
            'industry' => 'affiliate marketing',
            'intent' => 'Evaluate customer acquisition and offer recommendation workflows',
            'budget' => 'medium',
        ];

        ProcessDeepseekLeadJob::dispatch(
            $lead,
            $offers,
            [
                'campaign_type' => 'Admin workflow test',
                'goal' => 'Verify Deepseek lead scoring and offer matching',
            ]
        );

        return back()->with('success', 'Deepseek workflow test has been queued. Review the last workflow result on the settings page after the job runs.');
    }
}

