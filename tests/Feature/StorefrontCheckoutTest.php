<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\State;
use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreTheme;
use App\Models\StoreProduct;
use App\Models\User;
use App\Notifications\StoreOrderReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StorefrontCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order_and_store_owner_receives_notification_with_delivery_fee_applied()
    {
        NotificationFacade::fake();
        Http::fake(function ($request) {
            $url = $request->url();

            if ($url === 'https://api.paystack.co/transaction/initialize') {
                $payload = json_decode($request->body(), true) ?: [];
                return Http::response([
                    'status' => true,
                    'message' => 'Authorization URL created',
                    'data' => [
                        'authorization_url' => 'https://paystack.com/authorize/test',
                        'reference' => $payload['reference'] ?? 'TEST_REF_123',
                    ],
                ], 200);
            }

            if (strpos($url, 'https://api.paystack.co/transaction/verify/') === 0) {
                $reference = basename(parse_url($url, PHP_URL_PATH));
                return Http::response([
                    'status' => true,
                    'message' => 'Verification successful',
                    'data' => [
                        'status' => 'success',
                        'amount' => 550000,
                        'reference' => $reference,
                        'paid_at' => now()->toISOString(),
                    ],
                ], 200);
            }

            return Http::response(null, 404);
        });

        config(['services.paystack.secret_key' => 'test_secret_key']);

        $role = Role::firstOrCreate(['name' => 'advertiser']);
        $user = User::factory()->create(['email' => 'owner@example.com']);
        $user->assignRole($role->name);

        $plan = StorePlan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'store_type' => 'multi',
            'product_limit' => 10,
            'monthly_price' => 15000,
            'yearly_price' => 150000,
            'yearly_discount_percent' => 17,
            'features' => [],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $theme = StoreTheme::create([
            'name' => 'Default Theme',
            'slug' => 'default',
            'description' => 'Default theme',
            'thumbnail' => null,
            'config' => ['layout' => 'modern'],
            'store_type' => 'both',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Test Store',
            'slug' => 'test-store',
            'description' => 'A simple test store',
            'store_plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'subscription_start_date' => now()->toDateString(),
            'subscription_end_date' => now()->addMonth()->toDateString(),
            'subscription_status' => 'active',
            'store_theme_id' => $theme->id,
            'payment_method' => 'link',
            'payment_mode' => 'platform',
            'payment_provider' => 'paystack',
            'email' => 'store@example.com',
        ]);

        $country = \App\Models\Country::create([
            'name' => 'Testland',
            'code' => 'TL',
            'is_active' => true,
        ]);

        $state = State::create([
            'country_id' => $country->id,
            'name' => 'Test State',
            'code' => 'TS',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = StoreProduct::create([
            'store_id' => $store->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'A product for testing checkout',
            'price' => 2500,
            'compare_at_price' => 3000,
            'sku' => 'TP-001',
            'stock_quantity' => 10,
            'images' => ['https://example.com/image.jpg'],
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 1,
            'product_type' => 'tangible',
            'delivery_fees' => [
                ['state_id' => $state->id, 'fee' => 500],
            ],
        ]);

        $response = $this->postJson(route('storefront.checkout', ['slug' => $store->slug]), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
            'customer_name' => 'Jane Customer',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+2348012345678',
            'shipping_address' => '123 Test Street, Test City',
            'state_id' => $state->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'payment_url' => 'https://paystack.com/authorize/test',
        ]);

        $this->assertDatabaseHas('store_orders', [
            'store_id' => $store->id,
            'customer_email' => 'jane@example.com',
            'shipping_fee' => 500.00,
            'subtotal' => 5000.00,
            'total' => 5500.00,
            'payment_status' => 'pending',
        ]);

        $verifyResponse = $this->get(route('storefront.checkout.verify', [
            'slug' => $store->slug,
            'reference' => $response->json('order_number'),
        ]));

        $verifyResponse->assertRedirect(route('storefront.thank-you', [
            'slug' => $store->slug,
            'orderNumber' => $response->json('order_number'),
        ]));

        $this->assertDatabaseHas('store_orders', [
            'store_id' => $store->id,
            'customer_email' => 'jane@example.com',
            'payment_status' => 'paid',
        ]);

        NotificationFacade::assertSentTo($user, StoreOrderReceivedNotification::class);
    }
}
