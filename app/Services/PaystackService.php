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

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            Log::error('Paystack Create Recipient Error', $response->json());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create recipient',
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

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            Log::error('Paystack Transfer Error', $response->json());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initiate transfer',
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email'        => $email,
                'amount'       => (int) round($amount * 100), // kobo
                'reference'    => $reference,
                'callback_url' => $callbackUrl,
                'metadata'     => $metadata,
            ]);

            if ($response->successful() && ($response->json()['status'] ?? false)) {
                return [
                    'success'           => true,
                    'authorization_url' => $response->json()['data']['authorization_url'],
                    'reference'         => $response->json()['data']['reference'],
                ];
            }

            Log::error('Paystack Initialize Payment Error', $response->json());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initialize payment',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while initializing payment',
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

            if ($response->successful() && ($response->json()['status'] ?? false)) {
                $data   = $response->json()['data'];
                $status = $data['status'] ?? '';

                return [
                    'success' => true,
                    'paid'    => $status === 'success',
                    'data'    => $data,
                ];
            }

            Log::error('Paystack Verify Payment Error', $response->json());
            return [
                'success' => false,
                'paid'    => false,
                'message' => $response->json()['message'] ?? 'Failed to verify payment',
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

