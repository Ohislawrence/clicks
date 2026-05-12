<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Offer;
use App\Models\OfferAccessRequest;
use App\Models\OfferCategory;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use RefreshDatabase;

    private User $affiliate;
    private User $advertiser;
    private OfferCategory $category;
    private Offer $openOffer;
    private Offer $privateOffer;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'affiliate', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);

        $this->affiliate = User::factory()->create(['balance' => 10000, 'pending_balance' => 0, 'tier' => 'bronze']);
        $this->affiliate->assignRole('affiliate');

        $this->advertiser = User::factory()->create();
        $this->advertiser->assignRole('advertiser');

        $this->category = OfferCategory::create([
            'name' => 'E-Commerce',
            'slug' => 'e-commerce',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->openOffer = Offer::create([
            'advertiser_id' => $this->advertiser->id,
            'category_id'   => $this->category->id,
            'name'          => 'Open Test Offer',
            'slug'          => 'open-test-offer',
            'description'   => 'An open offer for testing',
            'commission_model' => 'pps',
            'commission_rate'  => 10.00,
            'cookie_duration'  => 30,
            'access_type'      => 'open',
            'is_active'        => true,
            'approval_status'  => 'approved',
            'preview_url'      => 'https://example.com',
        ]);

        $this->privateOffer = Offer::create([
            'advertiser_id' => $this->advertiser->id,
            'category_id'   => $this->category->id,
            'name'          => 'Private Test Offer',
            'slug'          => 'private-test-offer',
            'description'   => 'A private offer for testing',
            'commission_model' => 'pps',
            'commission_rate'  => 15.00,
            'cookie_duration'  => 30,
            'access_type'      => 'request',
            'is_active'        => true,
            'approval_status'  => 'approved',
            'preview_url'      => 'https://example.com/private',
        ]);
    }

    // ── Access Control ─────────────────────────────────────────────────────────

    public function test_guest_cannot_access_affiliate_dashboard(): void
    {
        $response = $this->get(route('affiliate.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_affiliate_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.dashboard'));

        $response->assertOk();
    }

    public function test_advertiser_cannot_access_affiliate_dashboard(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('affiliate.dashboard'));

        $response->assertForbidden();
    }

    public function test_affiliate_cannot_access_advertiser_routes(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('advertiser.dashboard'));

        $response->assertForbidden();
    }

    public function test_affiliate_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    // ── Offers ─────────────────────────────────────────────────────────────────

    public function test_affiliate_can_view_offers_list(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.offers.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Affiliate/Offers/Index')
                ->has('offers')
            );
    }

    public function test_affiliate_can_view_offer_detail(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.offers.show', $this->openOffer));

        $response->assertOk();
    }

    public function test_affiliate_can_request_access_to_private_offer(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.offers.request-access', $this->privateOffer), [
                'message' => 'I would like to promote this offer.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('offer_access_requests', [
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->privateOffer->id,
        ]);
    }

    public function test_affiliate_cannot_request_access_twice(): void
    {
        OfferAccessRequest::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->privateOffer->id,
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.offers.request-access', $this->privateOffer), [
                'message' => 'I would like to promote this offer.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(
            1,
            OfferAccessRequest::where('affiliate_id', $this->affiliate->id)
                ->where('offer_id', $this->privateOffer->id)
                ->count()
        );
    }

    // ── Affiliate Links ────────────────────────────────────────────────────────

    public function test_affiliate_can_view_links(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.links.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Affiliate/Links/Index'));
    }

    public function test_affiliate_can_create_link_for_open_offer(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.links.store'), [
                'offer_id' => $this->openOffer->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('affiliate_links', [
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->openOffer->id,
        ]);
    }

    public function test_affiliate_cannot_create_duplicate_link(): void
    {
        AffiliateLink::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->openOffer->id,
        ]);

        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.links.store'), [
                'offer_id' => $this->openOffer->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(
            1,
            AffiliateLink::where('affiliate_id', $this->affiliate->id)
                ->where('offer_id', $this->openOffer->id)
                ->count()
        );
    }

    public function test_affiliate_cannot_create_link_for_inactive_offer(): void
    {
        $this->openOffer->update(['is_active' => false]);

        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.links.store'), [
                'offer_id' => $this->openOffer->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_affiliate_can_toggle_own_link(): void
    {
        $link = AffiliateLink::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->openOffer->id,
            'is_active'    => true,
        ]);

        $response = $this->actingAs($this->affiliate)
            ->patch(route('affiliate.links.toggle', $link));

        $response->assertRedirect();
        $this->assertFalse($link->fresh()->is_active);
    }

    public function test_affiliate_cannot_toggle_another_affiliates_link(): void
    {
        $other = User::factory()->create();
        $other->assignRole('affiliate');

        $link = AffiliateLink::create([
            'affiliate_id' => $other->id,
            'offer_id'     => $this->openOffer->id,
        ]);

        $response = $this->actingAs($this->affiliate)
            ->patch(route('affiliate.links.toggle', $link));

        $response->assertForbidden();
    }

    public function test_affiliate_can_delete_own_link(): void
    {
        $link = AffiliateLink::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $this->openOffer->id,
        ]);

        $response = $this->actingAs($this->affiliate)
            ->delete(route('affiliate.links.destroy', $link));

        $response->assertRedirect();
        $this->assertDatabaseMissing('affiliate_links', ['id' => $link->id]);
    }

    public function test_affiliate_cannot_delete_another_affiliates_link(): void
    {
        $other = User::factory()->create();
        $other->assignRole('affiliate');

        $link = AffiliateLink::create([
            'affiliate_id' => $other->id,
            'offer_id'     => $this->openOffer->id,
        ]);

        $response = $this->actingAs($this->affiliate)
            ->delete(route('affiliate.links.destroy', $link));

        $response->assertForbidden();
        $this->assertDatabaseHas('affiliate_links', ['id' => $link->id]);
    }

    // ── Payouts ────────────────────────────────────────────────────────────────

    public function test_affiliate_can_view_payout_list(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.payouts.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Affiliate/Payouts/Index')
                ->has('balance')
            );
    }

    public function test_affiliate_can_request_payout(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.payouts.store'), [
                'amount'          => 6000,
                'payment_method'  => 'bank_transfer',
                'payment_details' => [
                    'account_name'   => 'Test User',
                    'account_number' => '0123456789',
                    'bank_name'      => 'Test Bank',
                    'bank_code'      => '044',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payout_requests', [
            'affiliate_id' => $this->affiliate->id,
            'amount'       => 6000,
            'status'       => 'pending',
        ]);
    }

    public function test_affiliate_cannot_request_payout_below_minimum(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.payouts.store'), [
                'amount'          => 2000,
                'payment_method'  => 'bank_transfer',
                'payment_details' => [
                    'account_name'   => 'Test User',
                    'account_number' => '0123456789',
                    'bank_name'      => 'Test Bank',
                ],
            ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_affiliate_cannot_request_payout_exceeding_balance(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.payouts.store'), [
                'amount'          => 20000,
                'payment_method'  => 'bank_transfer',
                'payment_details' => [
                    'account_name'   => 'Test User',
                    'account_number' => '0123456789',
                    'bank_name'      => 'Test Bank',
                ],
            ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_affiliate_cannot_have_two_pending_payouts(): void
    {
        PayoutRequest::create([
            'affiliate_id'    => $this->affiliate->id,
            'amount'          => 6000,
            'payment_method'  => 'bank_transfer',
            'payment_details' => ['account_name' => 'Test', 'account_number' => '0123456789', 'bank_name' => 'Bank'],
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->affiliate)
            ->post(route('affiliate.payouts.store'), [
                'amount'          => 6000,
                'payment_method'  => 'bank_transfer',
                'payment_details' => [
                    'account_name'   => 'Test User',
                    'account_number' => '0123456789',
                    'bank_name'      => 'Test Bank',
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(1, PayoutRequest::where('affiliate_id', $this->affiliate->id)->count());
    }

    public function test_affiliate_can_cancel_pending_payout(): void
    {
        $payout = PayoutRequest::create([
            'affiliate_id'    => $this->affiliate->id,
            'amount'          => 6000,
            'payment_method'  => 'bank_transfer',
            'payment_details' => ['account_name' => 'Test', 'account_number' => '0123456789', 'bank_name' => 'Bank'],
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->affiliate)
            ->delete(route('affiliate.payouts.cancel', $payout));

        $response->assertRedirect();
        $this->assertEquals('cancelled', $payout->fresh()->status);
    }

    // ── Reports ────────────────────────────────────────────────────────────────

    public function test_affiliate_can_view_reports(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('affiliate.reports.index'));

        $response->assertOk();
    }
}
