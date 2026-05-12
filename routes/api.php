<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Store Payment Webhooks (no CSRF or auth required - verified via signature)
Route::post('webhooks/paystack/store-order', [WebhookController::class, 'paystackStoreOrder'])
    ->name('webhooks.paystack.store-order');
Route::post('webhooks/flutterwave/store-order', [WebhookController::class, 'flutterwaveStoreOrder'])
    ->name('webhooks.flutterwave.store-order');

// Store Subscription Webhooks
Route::post('webhooks/paystack/store-subscription', [WebhookController::class, 'paystackStoreSubscription'])
    ->name('webhooks.paystack.store-subscription');
Route::post('webhooks/flutterwave/store-subscription', [WebhookController::class, 'flutterwaveStoreSubscription'])
    ->name('webhooks.flutterwave.store-subscription');
