<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();
$advertiser = User::whereHas('roles', fn($q) => $q->where('name', 'advertiser'))->first();

if (!$admin || !$advertiser) {
    echo "Need an admin and an advertiser for this test.\n";
    exit;
}

echo "Admin ID: " . $admin->id . "\n";
echo "Advertiser ID: " . $advertiser->id . "\n";

// Simulate login
Auth::login($advertiser);
echo "Auth ID (after login as advertiser): " . Auth::id() . "\n";

// Simulate impersonation session
session(['impersonate' => $admin->id]);
echo "Session 'impersonate' value: " . session('impersonate') . "\n";
echo "Auth ID type: " . gettype(Auth::id()) . "\n";
$latestOffer = App\Models\Offer::latest()->first();
if ($latestOffer) {
    echo "Offer Advertiser ID: " . $latestOffer->advertiser_id . "\n";
    echo "Offer Advertiser ID type: " . gettype($latestOffer->advertiser_id) . "\n";
    echo "Match? " . ($latestOffer->advertiser_id == Auth::id() ? 'Yes' : 'No') . "\n";
    echo "Strict Match? " . ($latestOffer->advertiser_id === Auth::id() ? 'Yes' : 'No') . "\n";
}
