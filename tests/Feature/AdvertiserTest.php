<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Conversion;
use App\Models\Offer;
use App\Models\OfferCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdvertiserTest extends TestCase
{
    use RefreshDatabase;

    private User $advertiser;
    private User $affiliate;
    private OfferCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'affiliate', 'guard_name' => 'web']);

        $this->advertiser = User::factory()->create(['advertiser_balance' => 50000]);
        $this->advertiser->assignRole('advertiser');

        $this->affiliate = User::factory()->create();
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
            'name'             => 'Test Offer',
            'slug'             => 'test-offer-' . uniqid(),
            'description'      => 'Test description',
            'commission_model' => 'pps',
            'commission_rate'  => 10.00,
            'cookie_duration'  => 30,
            'access_type'      => 'open',
            'is_active'        => true,
            'approval_status'  => 'pending',
            'preview_url'      => 'https://example.com',
        ], $overrides));
    }

    // ── Access Control ─────────────────────────────────────────────────────────

    public function test_guest_cannot_access_advertiser_dashboard(): void
    {
        $response = $this->get(route('advertiser.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_advertiser_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.dashboard'));

        $response->assertOk();
    }

    public function test_affiliate_cannot_access_advertiser_dashboard(): void
    {
        $response = $this->actingAs($this->affiliate)
            ->get(route('advertiser.dashboard'));

        $response->assertForbidden();
    }

    public function test_advertiser_cannot_access_affiliate_routes(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('affiliate.dashboard'));

        $response->assertForbidden();
    }

    public function test_advertiser_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    // ── Offers ─────────────────────────────────────────────────────────────────

    public function test_advertiser_can_view_offers(): void
    {
        $this->makeOffer();

        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.offers.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Advertiser/Offers/Index'));
    }

    public function test_advertiser_can_create_offer(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->post(route('advertiser.offers.store'), [
                'name'             => 'My New Offer',
                'description'      => 'Offer description',
                'category_id'      => $this->category->id,
                'commission_model' => 'pps',
                'commission_rate'  => 12.5,
                'cookie_duration'  => 30,
                'access_type'      => 'open',
                'preview_url'      => 'https://example.com/offer',
                'is_active'        => true,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'advertiser_id'   => $this->advertiser->id,
            'name'            => 'My New Offer',
            'approval_status' => 'pending',
        ]);
    }

    public function test_advertiser_can_view_offer_detail(): void
    {
        $offer = $this->makeOffer();

        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.offers.show', $offer));

        $response->assertOk();
    }

    public function test_advertiser_can_update_own_offer(): void
    {
        $offer = $this->makeOffer();

        $response = $this->actingAs($this->advertiser)
            ->put(route('advertiser.offers.update', $offer), [
                'name'             => 'Updated Offer Name',
                'description'      => 'Updated description',
                'category_id'      => $this->category->id,
                'commission_model' => 'pps',
                'commission_rate'  => 15.0,
                'cookie_duration'  => 60,
                'access_type'      => 'open',
                'preview_url'      => 'https://example.com/updated',
                'is_active'        => true,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'id'   => $offer->id,
            'name' => 'Updated Offer Name',
        ]);
    }

    public function test_advertiser_cannot_update_another_advertisers_offer(): void
    {
        $other = User::factory()->create();
        $other->assignRole('advertiser');
        $offer = Offer::create([
            'advertiser_id'    => $other->id,
            'category_id'      => $this->category->id,
            'name'             => 'Other Offer',
            'slug'             => 'other-offer',
            'description'      => 'Other description',
            'commission_model' => 'pps',
            'commission_rate'  => 10.00,
            'cookie_duration'  => 30,
            'access_type'      => 'open',
            'is_active'        => true,
            'approval_status'  => 'pending',
            'preview_url'      => 'https://other.com',
        ]);

        $response = $this->actingAs($this->advertiser)
            ->put(route('advertiser.offers.update', $offer), [
                'name'             => 'Hacked Name',
                'description'      => 'x',
                'category_id'      => $this->category->id,
                'commission_model' => 'pps',
                'commission_rate'  => 10,
                'cookie_duration'  => 30,
                'access_type'      => 'open',
                'preview_url'      => 'https://example.com',
                'is_active'        => true,
            ]);

        $response->assertForbidden();
    }

    public function test_advertiser_can_toggle_offer_status(): void
    {
        $offer = $this->makeOffer(['is_active' => true]);

        $response = $this->actingAs($this->advertiser)
            ->patch(route('advertiser.offers.toggle', $offer));

        $response->assertRedirect();
        $this->assertFalse($offer->fresh()->is_active);
    }

    public function test_advertiser_can_delete_own_offer(): void
    {
        $offer = $this->makeOffer();

        $response = $this->actingAs($this->advertiser)
            ->delete(route('advertiser.offers.destroy', $offer));

        $response->assertRedirect();
        $this->assertSoftDeleted('offers', ['id' => $offer->id]);
    }

    public function test_offer_requires_valid_data(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->post(route('advertiser.offers.store'), [
                'name' => '', // required
                'commission_rate' => -5, // min 0
            ]);

        $response->assertSessionHasErrors(['name', 'commission_rate']);
    }

    // ── Conversions ────────────────────────────────────────────────────────────

    public function test_advertiser_can_view_conversions(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.conversions.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Advertiser/Conversions/Index'));
    }

    public function test_advertiser_can_approve_own_conversion(): void
    {
        $offer = $this->makeOffer();

        $link = AffiliateLink::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $offer->id,
        ]);

        $conversion = Conversion::create([
            'affiliate_id'      => $this->affiliate->id,
            'offer_id'          => $offer->id,
            'affiliate_link_id' => $link->id,
            'transaction_id'    => 'TXN-TEST-001',
            'conversion_value'  => 5000,
            'commission_amount' => 500,
            'commission_model'  => 'pps',
            'status'            => 'pending',
        ]);

        // Give affiliate some pending balance to deduct from
        $this->affiliate->update(['pending_balance' => 500]);

        $response = $this->actingAs($this->advertiser)
            ->post(route('advertiser.conversions.approve', $conversion));

        $response->assertRedirect();

        $this->assertEquals('approved', $conversion->fresh()->status);
    }

    public function test_advertiser_cannot_approve_another_advertisers_conversion(): void
    {
        $other = User::factory()->create();
        $other->assignRole('advertiser');
        $otherOffer = Offer::create([
            'advertiser_id'    => $other->id,
            'category_id'      => $this->category->id,
            'name'             => 'Other',
            'slug'             => 'other-' . uniqid(),
            'description'      => 'x',
            'commission_model' => 'pps',
            'commission_rate'  => 10,
            'cookie_duration'  => 30,
            'access_type'      => 'open',
            'is_active'        => true,
            'approval_status'  => 'pending',
            'preview_url'      => 'https://example.com',
        ]);

        $otherLink = AffiliateLink::create([
            'affiliate_id' => $this->affiliate->id,
            'offer_id'     => $otherOffer->id,
        ]);

        $conversion = Conversion::create([
            'affiliate_id'      => $this->affiliate->id,
            'offer_id'          => $otherOffer->id,
            'affiliate_link_id' => $otherLink->id,
            'transaction_id'    => 'TXN-OTHER-001',
            'conversion_value'  => 5000,
            'commission_amount' => 500,
            'commission_model'  => 'pps',
            'status'            => 'pending',
        ]);

        $response = $this->actingAs($this->advertiser)
            ->post(route('advertiser.conversions.approve', $conversion));

        $response->assertForbidden();
    }

    // ── Reports ────────────────────────────────────────────────────────────────

    public function test_advertiser_can_view_reports(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.reports.index'));

        $response->assertOk();
    }

    // ── Documentation ──────────────────────────────────────────────────────────

    public function test_advertiser_can_view_documentation(): void
    {
        $response = $this->actingAs($this->advertiser)
            ->get(route('advertiser.documentation.index'));

        $response->assertOk();
    }
}
