<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $secretKey;
    protected $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key');
    }

    /**
     * Initiate a transfer
     */
    public function initiateTransfer($amount, $accountNumber, $accountBank, $accountName, $reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfers', [
                'account_bank' => $accountBank,
                'account_number' => $accountNumber,
                'amount' => $amount,
                'narration' => 'Affiliate Payout',
                'currency' => 'NGN',
                'reference' => $reference,
                'callback_url' => config('app.url') . '/api/flutterwave/callback',
                'debit_currency' => 'NGN',
                'beneficiary_name' => $accountName,
            ]);

            if ($response->successful() && $response->json()['status'] === 'success') {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            Log::error('Flutterwave Transfer Error', $response->json());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initiate transfer',
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while processing your request',
            ];
        }
    }

    /**
     * Verify transfer status
     */
    public function verifyTransfer($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transfers/' . $transactionId);

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
            Log::error('Flutterwave Exception', ['message' => $e->getMessage()]);
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
            ])->get($this->baseUrl . '/banks/NG');

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
            Log::error('Flutterwave Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while fetching banks',
            ];
        }
    }

    /**
     * Verify account number
     */
    public function verifyAccount($accountNumber, $accountBank)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/accounts/resolve', [
                'account_number' => $accountNumber,
                'account_bank' => $accountBank,
            ]);

            if ($response->successful() && $response->json()['status'] === 'success') {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to verify account',
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while verifying account',
            ];
        }
    }

    /**
     * Get transfer fee
     */
    public function getTransferFee($amount)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transfers/fee', [
                'amount' => $amount,
                'currency' => 'NGN',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get transfer fee',
            ];
        } catch (\Exception $e) {
            Log::error('Flutterwave Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred while fetching transfer fee',
            ];
        }
    }
}
