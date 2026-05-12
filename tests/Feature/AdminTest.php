<?php

namespace Tests\Feature;

use App\Models\Conversion;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $advertiser;
    private User $affiliate;
    private OfferCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'affiliate', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->advertiser = User::factory()->create();
        $this->advertiser->assignRole('advertiser');

        $this->affiliate = User::factory()->create(['balance' => 8000, 'pending_balance' => 0, 'is_active' => true]);
        $this->affiliate->assignRole('affiliate');

        $this->category = OfferCategory::create([
            'name' => 'E-Commerce',
            'slug' => 'e-commerce',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function makeOffer(array $overrides = []): Offer
    {
        return Offer::create(array_merge([
            'advertiser_id'    => $this->advertiser->id,
            'category_id'      => $this->category->id,
            'name'             => 'Test Offer ' . uniqid(),
            'slug'             => 'test-offer-' . uniqid(),
            'description'      => 'Test description',
            'commission_model' => 'pps',
            'commission_rate'  => 10.00,
            'affiliate_payout' => 9.00,
            'advertiser_payout' => 11.00,
            'platform_spread_percentage' => 10.00,
            'cookie_duration'  => 30,
            'access_type'      => 'open',
            'is_active'        => true,
            'approval_status'  => 'pending',
            'preview_url'      => 'https://example.com',
        ], $overrides));
    }

    private function makePayout(array $overrides = []): PayoutRequest
    {
        return PayoutRequest::create(array_merge([
            'affiliate_id'    => $this->affiliate->id,
            'amount'          => 6000,
            'payment_method'  => 'bank_transfer',
            'payment_details' => [
                'account_name'   => 'Test Affiliate',
                'account_number' => '0123456789',
                'bank_name'      => 'Test Bank',
            ],
            'status' => 'pending',
        ], $overrides));
    }

    // ── Access Control ─────────────────────────────────────────────────────────

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_affiliate_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_advertiser_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_cannot_access_affiliate_routes(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('affiliate.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_cannot_access_advertiser_routes(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('advertiser.dashboard'));

        $response->assertForbidden();
    }

    // ── User Management ────────────────────────────────────────────────────────

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Users/Index'));
    }

    public function test_admin_can_view_user_detail(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->affiliate));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Show')
                ->has('user')
                ->has('stats')
            );
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $this->assertTrue($this->affiliate->is_active);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.toggle-status', $this->affiliate));

        $response->assertRedirect();
        $this->assertFalse($this->affiliate->fresh()->is_active);
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['role' => 'affiliate']));

        $response->assertOk();
    }

    public function test_admin_can_search_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => $this->affiliate->email]));

        $response->assertOk();
    }

    // ── Offer Management ───────────────────────────────────────────────────────

    public function test_admin_can_view_offers_list(): void
    {
        $this->makeOffer();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.offers.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Offers/Index'));
    }

    public function test_admin_can_view_offer_detail(): void
    {
        $offer = $this->makeOffer();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.offers.show', $offer));

        $response->assertOk();
    }

    public function test_admin_can_approve_pending_offer(): void
    {
        $offer = $this->makeOffer(['approval_status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.offers.approve', $offer), [
                'platform_spread_percentage' => 10,
            ]);

        $response->assertRedirect();

        $this->assertEquals('approved', $offer->fresh()->approval_status);
        $this->assertNotNull($offer->fresh()->reviewed_by);
        $this->assertEquals($this->admin->id, $offer->fresh()->reviewed_by);
    }

    public function test_admin_can_reject_pending_offer(): void
    {
        $offer = $this->makeOffer(['approval_status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.offers.reject', $offer), [
                'rejection_reason' => 'Does not meet our quality standards.',
            ]);

        $response->assertRedirect();

        $this->assertEquals('rejected', $offer->fresh()->approval_status);
        $this->assertFalse($offer->fresh()->is_active);
        $this->assertEquals('Does not meet our quality standards.', $offer->fresh()->rejection_reason);
    }

    public function test_admin_cannot_approve_already_approved_offer(): void
    {
        $offer = $this->makeOffer(['approval_status' => 'approved']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.offers.approve', $offer), [
                'platform_spread_percentage' => 10,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('info');
    }

    public function test_admin_can_toggle_offer_active_status(): void
    {
        $offer = $this->makeOffer(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.offers.toggle', $offer));

        $response->assertRedirect();
        $this->assertFalse($offer->fresh()->is_active);
    }

    public function test_admin_can_delete_offer(): void
    {
        $offer = $this->makeOffer();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.offers.destroy', $offer));

        $response->assertRedirect();
        $this->assertSoftDeleted('offers', ['id' => $offer->id]);
    }

    public function test_admin_can_filter_offers_by_approval_status(): void
    {
        $this->makeOffer(['approval_status' => 'pending']);
        $this->makeOffer(['approval_status' => 'approved']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.offers.index', ['approval_status' => 'pending']));

        $response->assertOk();
    }

    // ── Conversion Management ──────────────────────────────────────────────────

    public function test_admin_can_view_conversions(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.conversions.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Conversions/Index'));
    }

    public function test_admin_can_filter_conversions_by_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.conversions.index', ['status' => 'pending']));

        $response->assertOk();
    }

    // ── Payout Management ──────────────────────────────────────────────────────

    public function test_admin_can_view_payouts(): void
    {
        $this->makePayout();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payouts.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Payouts/Index'));
    }

    public function test_admin_can_process_pending_payout(): void
    {
        $payout = $this->makePayout();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payouts.process', $payout));

        $response->assertRedirect();
        $this->assertEquals('processing', $payout->fresh()->status);
        $this->assertEquals($this->admin->id, $payout->fresh()->processed_by);
    }

    public function test_admin_cannot_process_already_processed_payout(): void
    {
        $payout = $this->makePayout(['status' => 'processing']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payouts.process', $payout));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_reject_pending_payout(): void
    {
        $payout = $this->makePayout();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payouts.reject', $payout), [
                'rejection_reason' => 'Invalid bank account details.',
            ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $payout->fresh()->status);
        $this->assertEquals('Invalid bank account details.', $payout->fresh()->rejection_reason);
    }

    public function test_admin_cannot_reject_without_reason(): void
    {
        $payout = $this->makePayout();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payouts.reject', $payout), [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors(['rejection_reason']);
    }

    public function test_admin_can_filter_payouts_by_status(): void
    {
        $this->makePayout(['status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payouts.index', ['status' => 'pending']));

        $response->assertOk();
    }

    // ── Settings ───────────────────────────────────────────────────────────────

    public function test_admin_can_view_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index'));

        $response->assertOk();
    }

    // ── Reports ────────────────────────────────────────────────────────────────

    public function test_admin_can_view_reports(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
    }
}
