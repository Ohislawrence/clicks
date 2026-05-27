<?php
// Quick Paystack test using Laravel Http facade - run with: php test_paystack.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$key = config('services.paystack.secret_key');
echo "Key length: " . strlen($key) . "\n";
echo "Key prefix: " . substr($key, 0, 7) . "...\n\n";

// Test 1: Using Laravel Http facade (same as PaystackService)
echo "=== Test 1: Laravel Http facade ===\n";
try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $key,
        'Content-Type'  => 'application/json',
    ])->post('https://api.paystack.co/transaction/initialize', [
        'email'        => 'test@example.com',
        'amount'       => (int) round(5000 * 100),
        'reference'    => 'HTTP-TEST-' . time(),
        'callback_url' => 'http://localhost/advertiser/wallet/verify',
        'metadata'     => ['user_id' => 1, 'type' => 'wallet_deposit'],
    ]);
    $data = $response->json();
    echo "HTTP Status: " . $response->status() . "\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    echo "Successful: " . ($response->successful() ? 'YES' : 'NO') . "\n";
    echo "Status flag: " . (($data['status'] ?? false) ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

