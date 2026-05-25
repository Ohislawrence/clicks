<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DeepseekMarketingAgent
{
    protected DeepseekService $deepseek;

    public function __construct(DeepseekService $deepseek)
    {
        $this->deepseek = $deepseek;
    }

    public function processLeadAcquisitionWorkflow(array $lead, array $offers, array $campaignContext = []): array
    {
        if (!Cache::get('deepseek_ai_enabled', false)) {
            return [
                'success' => false,
                'reason' => 'deepseek_disabled',
                'message' => 'Deepseek AI workflow is disabled in admin settings.',
            ];
        }

        $workflow = $this->createAcquisitionWorkflow($lead, $offers, $campaignContext);
        $threshold = (float) Cache::get('deepseek_lead_score_threshold', 50);
        $score = $this->extractNumericScore($workflow['lead_score']['data'] ?? null);

        $workflow['deepseek_lead_score_threshold'] = $threshold;
        $workflow['deepseek_lead_score'] = $score;
        $workflow['deepseek_lead_qualified'] = $threshold <= 0 || ($score !== null && $score >= $threshold);

        if (!$workflow['deepseek_lead_qualified']) {
            $workflow['success'] = true;
            $workflow['reason'] = 'lead_score_below_threshold';
            return $workflow;
        }

        if (!Cache::get('deepseek_auto_offer_recommendation', true)) {
            $workflow['offer_recommendation'] = [
                'success' => false,
                'reason' => 'auto_recommendation_disabled',
            ];
        }

        return $workflow;
    }

    protected function extractNumericScore($value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_array($value)) {
            if (isset($value['score']) && is_numeric($value['score'])) {
                return (float) $value['score'];
            }
            if (isset($value['value']) && is_numeric($value['value'])) {
                return (float) $value['value'];
            }
        }

        if (is_string($value) && preg_match('/([0-9]+(?:\.[0-9]+)?)/', $value, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    public function createAcquisitionWorkflow(array $lead, array $offers, array $campaignContext = []): array
    {
        $leadProfile = $this->generateLeadProfile($lead);
        $leadScore = $this->deepseek->scoreLead($lead);
        $offerRecommendation = $this->deepseek->recommendOffer($lead, $offers);
        $campaignCopy = $this->deepseek->generateCampaignCopy(array_merge($campaignContext, [
            'lead_profile' => $leadProfile,
            'lead_score' => $leadScore['data'] ?? null,
            'recommended_offer' => $offerRecommendation['data'] ?? null,
        ]));

        return [
            'lead_profile' => $leadProfile,
            'lead_score' => $leadScore,
            'offer_recommendation' => $offerRecommendation,
            'campaign_copy' => $campaignCopy,
        ];
    }

    protected function generateLeadProfile(array $lead): array
    {
        return $this->deepseek->generateLeadProfile($lead);
    }

    public function buildFollowUpMessage(array $lead, array $offer): array
    {
        return $this->deepseek->buildFollowUpMessage($lead, $offer);
    }
}
