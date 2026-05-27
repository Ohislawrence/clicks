<?php

use App\Models\Offer;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$offers = Offer::all();

echo "Total Offers: " . $offers->count() . "\n";

foreach ($offers as $offer) {
    $owner = User::find($offer->advertiser_id);
    $ownerName = $owner ? $owner->name : "Unknown (" . $offer->advertiser_id . ")";
    $roles = $owner ? $owner->roles->pluck('name')->implode(', ') : "N/A";

    echo "ID: {$offer->id} | Name: {$offer->name} | Owner: {$ownerName} | Roles: {$roles}\n";
}
