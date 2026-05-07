<?php

namespace App\Jobs;

use App\Models\Click;
use App\Models\Conversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 5; // Retry up to 5 times
    public $backoff = [10, 30, 60, 120, 300]; // Backoff delays in seconds

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $postbackUrl,
        public Conversion $conversion,
        public Click $click
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Replace placeholders in postback URL
            $url = $this->buildPostbackUrl();

            Log::info('Sending postback to advertiser', [
                'conversion_id' => $this->conversion->id,
                'url' => $url,
            ]);

            // Send HTTP request with timeout
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'DealsIntel-Tracking/1.0',
                    'X-Conversion-ID' => $this->conversion->id,
                ])
                ->get($url);

            // Check response
            if ($response->successful()) {
                // Update conversion with postback response
                $this->conversion->update([
                    'postback_sent_at' => now(),
                    'postback_response' => json_encode([
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'sent_at' => now()->toDateTimeString(),
                    ]),
                ]);

                Log::info('Postback sent successfully', [
                    'conversion_id' => $this->conversion->id,
                    'status' => $response->status(),
                ]);
            } else {
                Log::warning('Postback failed with non-200 response', [
                    'conversion_id' => $this->conversion->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Retry if not successful
                throw new \Exception("Postback failed with status: {$response->status()}");
            }

        } catch (\Exception $e) {
            Log::error('Postback send error', [
                'conversion_id' => $this->conversion->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // If we've exhausted all retries, mark as failed
            if ($this->attempts() >= $this->tries) {
                $this->conversion->update([
                    'postback_sent_at' => now(),
                    'postback_response' => json_encode([
                        'error' => $e->getMessage(),
                        'failed_at' => now()->toDateTimeString(),
                        'attempts' => $this->attempts(),
                    ]),
                ]);
            }

            throw $e;
        }
    }

    /**
     * Build postback URL with replaced macros
     */
    protected function buildPostbackUrl(): string
    {
        $url = $this->postbackUrl;

        // Common macro replacements
        $macros = [
            '{transaction_id}' => $this->conversion->transaction_id,
            '{conversion_id}' => $this->conversion->id,
            '{click_id}' => $this->click->id,
            '{affiliate_id}' => $this->conversion->affiliate_id,
            '{offer_id}' => $this->conversion->offer_id,
            '{conversion_value}' => $this->conversion->conversion_value,
            '{commission_amount}' => $this->conversion->commission_amount,
            '{status}' => $this->conversion->status,
            '{ip_address}' => $this->click->ip_address,
            '{country}' => $this->click->country_code ?? '',
            '{device}' => $this->click->device_type ?? '',
            '{timestamp}' => now()->timestamp,
        ];

        foreach ($macros as $macro => $value) {
            $url = str_replace($macro, $value, $url);
        }

        return $url;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Postback job failed permanently', [
            'conversion_id' => $this->conversion->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark conversion as having failed postback
        $this->conversion->update([
            'postback_sent_at' => now(),
            'postback_response' => json_encode([
                'error' => 'Failed after ' . $this->tries . ' attempts',
                'last_error' => $exception->getMessage(),
                'failed_at' => now()->toDateTimeString(),
            ]),
        ]);
    }
}
