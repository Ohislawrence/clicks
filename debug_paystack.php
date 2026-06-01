<?php
/**
 * Paystack diagnostic - tests the EXACT same code path as the web wallet deposit.
 * Access via browser: http://dealsintel.test/debug_paystack.php
 * OR run from CLI:    php debug_paystack.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$logFile = __DIR__ . '/storage/logs/paystack_debug.log';
$log = function ($msg) use ($logFile) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
    echo $line;
};

$log('=== Paystack Diagnostic Start ===');
$log('PHP Version: ' . PHP_VERSION);
$log('SAPI: ' . PHP_SAPI);

$secretKey = config('services.paystack.secret_key');
$log('Key length: ' . strlen((string) $secretKey));
$log('Key prefix: ' . substr((string) $secretKey, 0, 7) . '...');
$log('Key null? ' . ($secretKey === null ? 'YES - KEY IS MISSING' : 'no'));

if (!$secretKey) {
    $log('FATAL: No Paystack secret key configured!');
    exit(1);
}

$email    = 'test@example.com';
$amount   = 5000.00;
$ref      = 'DBG-' . time();
$callback = route('advertiser.wallet.verify');
$log('Callback URL: ' . $callback);

$log('Sending request to Paystack...');

try {
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . $secretKey,
    ])->asJson()->timeout(30)->post('https://api.paystack.co/transaction/initialize', [
        'email'        => $email,
        'amount'       => (int) round($amount * 100),
        'reference'    => $ref,
        'callback_url' => $callback,
        'metadata'     => ['type' => 'debug_test'],
    ]);

    $data = $response->json();
    $log('HTTP Status: ' . $response->status());
    $log('successful(): ' . ($response->successful() ? 'true' : 'false'));
    $log('Response: ' . json_encode($data, JSON_PRETTY_PRINT));

    if ($response->successful() && ($data['status'] ?? false)) {
        $log('SUCCESS: ' . ($data['data']['authorization_url'] ?? 'no URL'));
    } else {
        $log('FAILED - message: ' . ($data['message'] ?? 'NO MESSAGE IN RESPONSE'));
        $log('Raw body: ' . $response->body());
    }
} catch (\Throwable $e) {
    $log('EXCEPTION: ' . get_class($e) . ': ' . $e->getMessage());
}

$log('=== Diagnostic End ===');
