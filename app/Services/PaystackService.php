<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $secretKey;
    protected $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Initialize a transfer recipient
     */
    public function createTransferRecipient($accountNumber, $accountName, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transferrecipient', [
                'type' => 'nuban',
                'name' => $accountName,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'currency' => 'NGN',
            ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $data['data'] ?? [],
                ];
            }

            Log::error('Paystack Create Recipient Error', $data ?? []);
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to create recipient',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while processing your request',
            ];
        }
    }

    /**
     * Initiate a transfer
     */
    public function initiateTransfer($amount, $recipientCode, $reason = 'Payout')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfer', [
                'source' => 'balance',
                'amount' => $amount * 100, // Convert to kobo
                'recipient' => $recipientCode,
                'reason' => $reason,
            ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $data['data'] ?? [],
                ];
            }

            Log::error('Paystack Transfer Error', $data ?? []);
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initiate transfer',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while processing your request',
            ];
        }
    }

    /**
     * Verify transfer status
     */
    public function verifyTransfer($transferCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transfer/' . $transferCode);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to verify transfer',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while verifying transfer',
            ];
        }
    }

    /**
     * Get list of banks
     */
    public function getBanks()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank?country=nigeria');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch banks',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while fetching banks',
            ];
        }
    }

    /**
     * Initialize a payment transaction (for collecting money from a customer).
     *
     * @param  string  $email     Customer email
     * @param  float   $amount    Amount in Naira (will be converted to kobo)
     * @param  string  $reference Unique transaction reference
     * @param  string  $callbackUrl URL Paystack redirects to after payment
     * @param  array   $metadata  Optional extra data to attach to the transaction
     */
    public function initializePayment(string $email, float $amount, string $reference, string $callbackUrl, array $metadata = []): array
    {
        try {
            $amountKobo = (int) round($amount * 100);

            Log::info('Paystack Initialize Payment: request', [
                'email'        => $email,
                'amount_naira' => $amount,
                'amount_kobo'  => $amountKobo,
                'reference'    => $reference,
                'callback_url' => $callbackUrl,
                'key_prefix'   => substr($this->secretKey ?? '', 0, 7),
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/transaction/initialize', [
                'email'        => $email,
                'amount'       => $amountKobo,
                'reference'    => $reference,
                'callback_url' => $callbackUrl,
                'metadata'     => $metadata,
            ]);

            $data = $response->json();

            Log::info('Paystack Initialize Payment: response', [
                'http_status' => $response->status(),
                'successful'  => $response->successful(),
                'data'        => $data,
            ]);

            if ($response->successful() && ($data['status'] ?? false)) {
                return [
                    'success'           => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'reference'         => $data['data']['reference'],
                ];
            }

            $errorMessage = $data['message'] ?? 'Payment provider rejected the request';
            Log::error('Paystack Initialize Payment Error', [
                'http_status'  => $response->status(),
                'message'      => $errorMessage,
                'response'     => $data,
            ]);
            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        } catch (\Throwable $e) {
            Log::error('Paystack Exception in initializePayment', [
                'class'   => get_class($e),
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment transaction by reference.
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false)) {
                $status = $data['data']['status'] ?? '';

                return [
                    'success' => true,
                    'paid'    => $status === 'success',
                    'data'    => $data['data'],
                ];
            }

            Log::error('Paystack Verify Payment Error', $data ?? []);
            return [
                'success' => false,
                'paid'    => false,
                'message' => $data['message'] ?? 'Failed to verify payment',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'paid'    => false,
                'message' => 'An error occurred while verifying payment',
            ];
        }
    }

    /**
     * Resolve account number
     */
    public function resolveAccount($accountNumber, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to resolve account',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while resolving account',
            ];
        }
    }
}

