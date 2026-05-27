<?php

use App\Models\Offer;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$offer = Offer::latest()->first();
if ($offer) {
    echo "Offer ID: " . $offer->id . "\n";
    echo "Advertiser Route: " . route('advertiser.offers.show', $offer) . "\n";
    echo "Admin Route:      " . route('admin.offers.show', $offer) . "\n";
}
