<?php

use App\Models\Offer;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$latestOffer = Offer::withTrashed()->latest()->first();

if (!$latestOffer) {
    echo "No offers found in DB.\n";
    exit;
}

echo "Latest Offer ID: " . $latestOffer->id . "\n";
echo "Offer Name: " . $latestOffer->name . "\n";
echo "Advertiser ID: " . $latestOffer->advertiser_id . "\n";
echo "Is Deleted: " . ($latestOffer->deleted_at ? 'Yes' : 'No') . "\n";
echo "Approval Status: " . ($latestOffer->approval_status ?? 'N/A') . "\n";
echo "Slug: " . $latestOffer->slug . "\n";
echo "Created At: " . $latestOffer->created_at . "\n";

$advertiser = User::find($latestOffer->advertiser_id);
if ($advertiser) {
    echo "Advertiser Name: " . $advertiser->name . "\n";
    echo "Advertiser Roles: " . $advertiser->roles->pluck('name')->implode(', ') . "\n";
    echo "Is Verified: " . ($advertiser->email_verified_at ? 'Yes' : 'No') . "\n";
} else {
    echo "Advertiser not found!\n";
}
