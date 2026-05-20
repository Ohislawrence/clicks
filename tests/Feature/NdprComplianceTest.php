<?php

namespace Tests\Feature;

use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * NDPR Compliance Tests
 *
 * Covers the Nigeria Data Protection Regulation (NDPR) 2019 requirements:
 * - Payment data encryption at rest
 * - Terms & Privacy Policy consent at registration
 * - Data subject rights (export + erasure request)
 * - Privacy policy and terms page accessibility
 * - Cookie consent banner presence
 */
class NdprComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'affiliate', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    // ─── Payment Data Encryption ──────────────────────────────────────────────

    /** Payment details must be stored encrypted (NDPR Art. 2.1 - data security). */
    public function test_payment_details_are_encrypted_in_database(): void
    {
        $user = User::factory()->create();

        $plain = [
            'account_name'   => 'John Doe',
            'account_number' => '0123456789',
            'bank_name'      => 'First Bank',
            'bank_code'      => '011',
        ];

        $user->update(['payment_details' => $plain]);

        // Raw value from DB must NOT be readable plain JSON
        $raw = \DB::table('users')->where('id', $user->id)->value('payment_details');

        $this->assertNotEquals(json_encode($plain), $raw, 'payment_details must not be stored as plain JSON');
        $this->assertStringNotContainsString('0123456789', $raw, 'Account number must not appear in raw DB value');

        // But through the model it must decrypt correctly
        $user->refresh();
        $this->assertEquals($plain['account_number'], $user->payment_details['account_number']);
    }

    /** PayoutRequest payment_details must also be encrypted. */
    public function test_payout_request_payment_details_are_encrypted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('affiliate');

        $plain = [
            'account_name'   => 'Jane Doe',
            'account_number' => '9876543210',
            'bank_name'      => 'Zenith Bank',
        ];

        $payout = PayoutRequest::create([
            'affiliate_id'    => $user->id,
            'amount'          => 5000.00,
            'payment_method'  => 'bank_transfer',
            'payment_details' => $plain,
            'status'          => 'pending',
        ]);

        $raw = \DB::table('payout_requests')->where('id', $payout->id)->value('payment_details');

        $this->assertStringNotContainsString('9876543210', $raw, 'Payout account number must not appear in raw DB value');

        $payout->refresh();
        $this->assertEquals('9876543210', $payout->payment_details['account_number']);
    }

    // ─── Terms & Privacy Consent at Registration ──────────────────────────────

    /** Terms & Privacy Policy feature must be enabled in Jetstream config. */
    public function test_jetstream_terms_and_privacy_feature_is_enabled(): void
    {
        $this->assertTrue(
            Jetstream::hasTermsAndPrivacyPolicyFeature(),
            'Terms & Privacy Policy feature must be enabled (NDPR consent requirement)'
        );
    }

    /** Affiliate registration without accepting terms must fail. */
    public function test_affiliate_registration_requires_terms_acceptance(): void
    {
        $response = $this->post('/register/affiliate', [
            'name'                  => 'Test Affiliate',
            'email'                 => 'affiliate@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country'               => 'Nigeria',
            'traffic_sources'       => [
                ['type' => 'instagram', 'name' => 'My IG', 'url' => 'https://instagram.com/test', 'followers' => 1000],
            ],
            // 'terms' deliberately omitted
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertDatabaseMissing('users', ['email' => 'affiliate@example.com']);
    }

    /** Advertiser registration without accepting terms must fail. */
    public function test_advertiser_registration_requires_terms_acceptance(): void
    {
        $response = $this->post('/register/advertiser', [
            'name'                  => 'Test Advertiser',
            'email'                 => 'advertiser@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name'          => 'Test Co',
            'country'               => 'Nigeria',
            // 'terms' deliberately omitted
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertDatabaseMissing('users', ['email' => 'advertiser@example.com']);
    }

    // ─── Public Policy Pages ──────────────────────────────────────────────────

    /** Privacy policy page must be publicly accessible. */
    public function test_privacy_policy_page_is_accessible(): void
    {
        $response = $this->get('/privacy');
        $response->assertStatus(200);
    }

    /** Privacy policy must reference NDPR. */
    public function test_privacy_policy_contains_ndpr_reference(): void
    {
        $response = $this->get('/privacy');
        $response->assertStatus(200);
        $response->assertSee('NDPR');
    }

    /** Privacy policy must mention the NDPC/NITDA regulator. */
    public function test_privacy_policy_mentions_ndpc_regulator(): void
    {
        $response = $this->get('/privacy');
        $response->assertSeeInOrder(['NDPC', 'ndpc.gov.ng']);
    }

    /** Privacy policy must mention data retention periods. */
    public function test_privacy_policy_has_retention_periods(): void
    {
        $response = $this->get('/privacy');
        $response->assertSee('7 years');
        $response->assertSee('90 days');
    }

    /** Terms of service must be publicly accessible. */
    public function test_terms_page_is_accessible(): void
    {
        $response = $this->get('/terms');
        $response->assertStatus(200);
    }

    /** Terms must state Nigeria as governing law, not Delaware. */
    public function test_terms_governing_law_is_nigeria(): void
    {
        $response = $this->get('/terms');
        $response->assertSeeText('Federal Republic of Nigeria');
        $response->assertDontSeeText('State of Delaware');
    }

    /** Terms must reference the NDPR. */
    public function test_terms_reference_ndpr(): void
    {
        $response = $this->get('/terms');
        $response->assertSee('Nigeria Data Protection Regulation');
    }

    /** Cookie consent banner must be present on public pages. */
    public function test_cookie_consent_banner_present_on_homepage(): void
    {
        $response = $this->get('/');
        $response->assertSee('cookie-banner', false);
        $response->assertSee('ndpr_cookie_consent', false);
    }

    // ─── Data Subject Rights (NDPR Art. 3.1(6)) ──────────────────────────────

    /** Unauthenticated users must be redirected away from the data export endpoint. */
    public function test_data_export_requires_authentication(): void
    {
        $response = $this->get('/user/data-export');
        $response->assertRedirect('/login');
    }

    /** Authenticated users can export their personal data as JSON. */
    public function test_authenticated_user_can_export_personal_data(): void
    {
        $user = User::factory()->create([
            'name'    => 'Export Test',
            'email'   => 'export@example.com',
            'country' => 'Nigeria',
        ]);

        $response = $this->actingAs($user)->get('/user/data-export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure(['exported_at', 'regulation', 'profile', 'financial_summary', 'affiliate_stats']);
        $response->assertJsonPath('regulation', 'Nigeria Data Protection Regulation (NDPR) 2019');
        $response->assertJsonPath('profile.email', 'export@example.com');
    }

    /** Unauthenticated users must be redirected away from the erasure request endpoint. */
    public function test_erasure_request_requires_authentication(): void
    {
        $response = $this->post('/user/erasure-request');
        $response->assertRedirect('/login');
    }

    /** Erasure request with wrong password must fail. */
    public function test_erasure_request_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $response = $this->actingAs($user)->post('/user/erasure-request', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** Erasure request with correct password must be accepted and logged. */
    public function test_erasure_request_succeeds_with_correct_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) use ($user) {
                return $message === 'NDPR erasure request submitted'
                    && $context['user_id'] === $user->id;
            });

        $response = $this->actingAs($user)->post('/user/erasure-request', [
            'password' => 'correct-password',
            'reason'   => 'I want my data deleted',
        ]);

        $response->assertSessionHas('status');
        $this->assertStringContainsString('NDPR', session('status'));
    }

    // ─── DPO Contact Published ────────────────────────────────────────────────

    /** Privacy policy must publish DPO contact information. */
    public function test_privacy_policy_publishes_dpo_contact(): void
    {
        $response = $this->get('/privacy');
        $response->assertSee('dpo@clicksintel.com');
        $response->assertSee('Data Protection Officer');
    }
}
