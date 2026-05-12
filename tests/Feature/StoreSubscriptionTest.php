<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreSubscription;
use App\Models\StoreTheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Store $store;
    private StorePlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->assignRole('advertiser');

        $this->plan = StorePlan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'store_type' => 'multi',
            'product_limit' => 10,
            'monthly_price' => 5000,
            'yearly_price' => 50000,
            'yearly_discount_percent' => 17,
            'features' => ['feature1'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $theme = StoreTheme::create([
            'name' => 'Default',
            'slug' => 'default',
            'description' => 'Default theme',
            'thumbnail' => null,
            'config' => [],
            'store_type' => 'both',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->store = Store::create([
            'user_id' => $this->user->id,
            'name' => 'Test Store',
            'slug' => 'test-store',
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'subscription_start_date' => now()->subMonth()->toDateString(),
            'subscription_end_date' => now()->subDay()->toDateString(), // expired
            'subscription_status' => 'expired',
            'store_theme_id' => $theme->id,
            'payment_method' => 'link',
        ]);
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_advertiser_can_view_subscription_page()
    {
        $this->store->update(['subscription_status' => 'active', 'subscription_end_date' => now()->addMonth()]);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.index', $this->store));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Advertiser/Store/Subscription/Index')
                ->has('store')
                ->has('subscriptionDetails')
                ->has('availablePlans')
                ->has('paymentHistory')
            );
    }

    public function test_guest_cannot_view_subscription_page()
    {
        $response = $this->get(route('advertiser.store.subscription.index', $this->store));
        $response->assertRedirect(route('login'));
    }

    public function test_other_user_cannot_view_subscription_page()
    {
        $other = User::factory()->create();
        $other->assignRole('advertiser');

        $response = $this->actingAs($other)
            ->get(route('advertiser.store.subscription.index', $this->store));

        $response->assertStatus(404); // findOrFail scoped to user's stores
    }

    public function test_subscription_details_shows_correct_active_status()
    {
        $this->store->update([
            'subscription_status' => 'active',
            'subscription_end_date' => now()->addDays(20)->toDateString(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.index', $this->store));

        $response->assertInertia(fn ($page) => $page
            ->where('subscriptionDetails.is_active', true)
            ->where('subscriptionDetails.is_expiring_soon', false)
        );
    }

    public function test_subscription_details_shows_expiring_soon()
    {
        $this->store->update([
            'subscription_status' => 'active',
            'subscription_end_date' => now()->addDays(3)->toDateString(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.index', $this->store));

        $response->assertInertia(fn ($page) => $page
            ->where('subscriptionDetails.is_active', true)
            ->where('subscriptionDetails.is_expiring_soon', true)
        );
    }

    public function test_subscription_details_shows_inactive_when_expired()
    {
        // store already expired in setUp

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.index', $this->store));

        $response->assertInertia(fn ($page) => $page
            ->where('subscriptionDetails.is_active', false)
        );
    }

    // ── Initiate Renewal ───────────────────────────────────────────────────────

    public function test_renewal_initiates_paystack_payment()
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://paystack.com/pay/test123'],
            ], 200),
        ]);

        // Set a fake key so the controller doesn't bail
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        // Store defaults to paystack gateway
        $this->store->update(['subscription_payment_gateway' => 'paystack']);

        $response = $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.renew', $this->store), [
                'billing_cycle' => 'monthly',
            ]);

        // Inertia::location issues an external redirect (302 in test env)
        $response->assertStatus(302);

        $this->assertDatabaseHas('store_subscriptions', [
            'store_id' => $this->store->id,
            'billing_cycle' => 'monthly',
            'payment_gateway' => 'paystack',
            'status' => 'pending',
        ]);
    }

    public function test_renewal_initiates_flutterwave_payment()
    {
        Http::fake([
            'api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'data' => ['link' => 'https://flutterwave.com/pay/test456'],
            ], 200),
        ]);

        config(['services.flutterwave.secret_key' => 'FLWSECK_TEST_fake']);

        // Admin configured this store to use Flutterwave
        $this->store->update(['subscription_payment_gateway' => 'flutterwave']);

        $response = $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.renew', $this->store), [
                'billing_cycle' => 'yearly',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('store_subscriptions', [
            'store_id' => $this->store->id,
            'billing_cycle' => 'yearly',
            'payment_gateway' => 'flutterwave',
            'status' => 'pending',
        ]);
    }

    public function test_renewal_uses_admin_configured_gateway()
    {
        Http::fake([
            'api.paystack.co/*' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://paystack.com/pay/x'],
            ]),
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        // Admin set the gateway to paystack
        $this->store->update(['subscription_payment_gateway' => 'paystack']);

        $response = $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.renew', $this->store), [
                'billing_cycle' => 'monthly',
            ]);

        // Subscription should always use the store's configured gateway, not any user input
        $this->assertDatabaseHas('store_subscriptions', [
            'store_id' => $this->store->id,
            'payment_gateway' => 'paystack',
        ]);
    }

    public function test_renewal_creates_correct_subscription_amount_monthly()
    {
        Http::fake([
            'api.paystack.co/*' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://paystack.com/pay/x'],
            ]),
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.renew', $this->store), [
                'billing_cycle' => 'monthly',
            ]);

        $this->assertDatabaseHas('store_subscriptions', [
            'store_id' => $this->store->id,
            'amount' => $this->plan->monthly_price,
        ]);
    }

    public function test_renewal_creates_correct_subscription_amount_yearly()
    {
        Http::fake([
            'api.paystack.co/*' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://paystack.com/pay/x'],
            ]),
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.renew', $this->store), [
                'billing_cycle' => 'yearly',
            ]);

        $this->assertDatabaseHas('store_subscriptions', [
            'store_id' => $this->store->id,
            'amount' => $this->plan->yearly_price,
        ]);
    }

    // ── Verify Payment ─────────────────────────────────────────────────────────

    public function test_verify_activates_store_when_paystack_confirms_success()
    {
        $ref = 'SUB-TEST-' . uniqid();

        $subscription = StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'pending',
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success'],
            ], 200),
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', [
                'store' => $this->store->id,
                'reference' => $ref,
            ]));

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('store_subscriptions', [
            'id' => $subscription->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'subscription_status' => 'active',
            'is_active' => true,
            'subscription_end_date' => now()->addMonth()->toDateString(),
        ]);
    }

    public function test_verify_activates_store_when_no_api_key_configured()
    {
        // With no key, verifyPaystack returns true (trusts callback)
        config(['services.paystack.secret_key' => null]);

        $ref = 'SUB-NOKEY-' . uniqid();

        StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'pending',
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', [
                'store' => $this->store->id,
                'reference' => $ref,
            ]));

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'subscription_status' => 'active',
            'is_active' => true,
        ]);
    }

    public function test_verify_accepts_trxref_query_param()
    {
        config(['services.paystack.secret_key' => null]);

        $ref = 'SUB-TRXREF-' . uniqid();

        StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'pending',
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        // Paystack sends trxref as well as reference
        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', ['store' => $this->store->id]) . '?trxref=' . $ref);

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHas('success');
    }

    public function test_verify_fails_when_paystack_returns_failure()
    {
        $ref = 'SUB-FAIL-' . uniqid();

        StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'pending',
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'failed'],
            ], 200),
        ]);
        config(['services.paystack.secret_key' => 'sk_test_fake']);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', [
                'store' => $this->store->id,
                'reference' => $ref,
            ]));

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'subscription_status' => 'expired', // unchanged
        ]);
    }

    public function test_verify_redirects_if_already_paid()
    {
        config(['services.paystack.secret_key' => null]);

        $ref = 'SUB-PAID-' . uniqid();

        StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'paid',
            'paid_at' => now(),
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', [
                'store' => $this->store->id,
                'reference' => $ref,
            ]));

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHas('success', 'Subscription already activated!');
    }

    public function test_verify_unauthorized_for_other_users_subscription()
    {
        $other = User::factory()->create();
        $other->assignRole('advertiser');

        $ref = 'SUB-OTHER-' . uniqid();

        StoreSubscription::create([
            'store_id' => $this->store->id,
            'store_plan_id' => $this->plan->id,
            'billing_cycle' => 'monthly',
            'amount' => $this->plan->monthly_price,
            'payment_reference' => $ref,
            'payment_gateway' => 'paystack',
            'status' => 'pending',
            'period_start' => now()->toDateString(),
            'period_end' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->actingAs($other)
            ->get(route('advertiser.store.subscription.verify', [
                'store' => $this->store->id,
                'reference' => $ref,
            ]));

        $response->assertStatus(403);
    }

    public function test_verify_missing_reference_redirects_with_error()
    {
        $response = $this->actingAs($this->user)
            ->get(route('advertiser.store.subscription.verify', ['store' => $this->store->id]));

        $response->assertRedirect(route('advertiser.store.subscription.index', $this->store));
        $response->assertSessionHasErrors('error');
    }

    // ── Change Plan ────────────────────────────────────────────────────────────

    public function test_advertiser_can_change_plan()
    {
        $newPlan = StorePlan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'store_type' => 'multi',
            'product_limit' => 50,
            'monthly_price' => 10000,
            'yearly_price' => 100000,
            'yearly_discount_percent' => 17,
            'features' => [],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.change-plan', $this->store), [
                'store_plan_id' => $newPlan->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'store_plan_id' => $newPlan->id,
        ]);
    }

    public function test_cannot_change_to_plan_with_insufficient_product_limit()
    {
        $limitedPlan = StorePlan::create([
            'name' => 'Tiny Plan',
            'slug' => 'tiny',
            'store_type' => 'multi',
            'product_limit' => 1,
            'monthly_price' => 1000,
            'yearly_price' => 10000,
            'yearly_discount_percent' => 17,
            'features' => [],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Create 3 products in the store
        $this->store->products()->createMany([
            ['name' => 'P1', 'slug' => 'p1', 'price' => 100, 'is_active' => true],
            ['name' => 'P2', 'slug' => 'p2', 'price' => 200, 'is_active' => true],
            ['name' => 'P3', 'slug' => 'p3', 'price' => 300, 'is_active' => true],
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('advertiser.store.subscription.change-plan', $this->store), [
                'store_plan_id' => $limitedPlan->id,
            ]);

        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'store_plan_id' => $this->plan->id, // unchanged
        ]);
    }
}
