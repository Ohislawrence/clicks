<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepseekService
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        // DeepSeek base url typically defaults to https://api.deepseek.com
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com') ?? 'https://api.deepseek.com';
        $this->defaultModel = config('services.deepseek.default_model', 'deepseek-v4-flash') ?? 'deepseek-v4-flash';
    }

    /**
     * Centralized request handler pointing to the true Chat Completions endpoint.
     */
    protected function request(array $messages, bool $forceJson = false): array
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/chat/completions';

            $payload = [
                'model' => $this->defaultModel,
                'messages' => $messages,
                'temperature' => 0.3, // Low temp for predictable business logic
            ];

            // If the method expects a clean JSON array/object back, tell DeepSeek
            if ($forceJson) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'content' => $response->json('choices.0.message.content'),
                ];
            }

            Log::error('Deepseek API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $response->json('error.message') ?? 'Deepseek API request failed',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Deepseek API exception', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Deepseek API exception: ' . $e->getMessage(),
            ];
        }
    }

    public function generateLesson(string $courseTitle, string $lessonTitle, string $audience, string $level, string $outline = ''): array
    {
        $audienceLabel = match($audience) {
            'affiliate'  => 'Nigerian affiliate marketers',
            'advertiser' => 'Nigerian advertisers and e-commerce business owners',
            default      => 'Nigerian affiliates and advertisers',
        };

        $outlineSection = $outline ? "\n\nThe instructor has provided this outline / key points to cover:\n{$outline}" : '';

        $systemPrompt = <<<PROMPT
You are an expert course content writer for a Nigerian affiliate marketing platform called DealsintelNG.
Write practical, engaging, and action-oriented lesson content for online learners.

Guidelines:
- Write in clear, conversational Nigerian English (no slang, just friendly and direct)
- Use real Nigerian context (naira amounts, WhatsApp, local platforms, etc.) where relevant
- Structure content with clear H2/H3 headings, bullet points, numbered steps, and tables where helpful
- Include actionable tips the learner can apply immediately
- Keep the tone encouraging and professional
- Output ONLY well-formed HTML (no markdown, no code fences, no explanation outside the HTML)
- Use semantic HTML: <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <em>, <table>, <thead>, <tbody>, <tr>, <th>, <td>, <blockquote>
- Do NOT include <html>, <head>, <body> tags — just the inner content HTML
PROMPT;

        $userPrompt = "Course: \"{$courseTitle}\" ({$level} level, for {$audienceLabel})\nLesson: \"{$lessonTitle}\"{$outlineSection}\n\nWrite the full lesson content as HTML.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ];

        return $this->request($messages);
    }

    public function generateCampaignCopy(array $context): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are an expert copywriter for a performance ad network. Generate high-converting marketing copy based on the user context.'],
            ['role' => 'user', 'content' => json_encode($context)]
        ];

        return $this->request($messages);
    }

    public function scoreLead(array $lead): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'Analyze the provided user registration data and return a JSON object containing a quality fraud score between 0 and 100 under the key "score", and an array of brief reasons under "reasons". Your output must be valid raw JSON only.'],
            ['role' => 'user', 'content' => json_encode($lead)]
        ];

        return $this->request($messages, forceJson: true);
    }

    public function recommendOffer(array $lead, array $offers): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'Compare the lead data against the available array of offers. Return a JSON object explaining the highest matching offer ID and reasons.'],
            ['role' => 'user', 'content' => json_encode(['lead' => $lead, 'offers' => $offers])]
        ];

        return $this->request($messages, forceJson: true);
    }

    public function generateLeadProfile(array $lead): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'Generate a scannable user behavioral profile summary text from this metadata.'],
            ['role' => 'user', 'content' => json_encode($lead)]
        ];

        return $this->request($messages);
    }

    public function buildFollowUpMessage(array $lead, array $offer): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'Draft a compelling, direct WhatsApp or email follow-up notification asking the user to complete the specified offer.'],
            ['role' => 'user', 'content' => json_encode(['lead' => $lead, 'offer' => $offer])]
        ];

        return $this->request($messages);
    }

    public function summarizePerformance(array $metrics): array
    {
        $messages = [
            ['role' => 'system', 'content' => 'Analyze these raw performance logs and traffic data points, highlighting anomalies or major drops.'],
            ['role' => 'user', 'content' => json_encode($metrics)]
        ];

        return $this->request($messages);
    }

    public function chat(array $messages): array
    {
        return $this->request($messages);
    }
}

