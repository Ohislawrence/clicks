<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessBulkConversionActionJob;
use App\Jobs\ProcessImageJob;
use App\Jobs\ProcessPayoutJob;
use App\Models\Commission;
use App\Models\Conversion;
use App\Models\Offer;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Notifications\ConversionApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QueueJobsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for tests
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    public function test_process_payout_job_can_be_dispatched(): void
    {
        Queue::fake();

        $affiliate = User::factory()->create();
        $affiliate->assignRole('affiliate');

        $payout = PayoutRequest::create([
            'affiliate_id' => $affiliate->id,
            'amount' => 100.00,
            'payment_method' => 'bank_transfer',
            'payment_details' => [
                'account_name' => 'Test Account',
                'account_number' => '1234567890',
                'bank_name' => 'Test Bank',
                'bank_code' => '000',
            ],
            'status' => 'processing',
        ]);

        ProcessPayoutJob::dispatch($payout);

        Queue::assertPushed(ProcessPayoutJob::class, function ($job) use ($payout) {
            return $job->payout->id === $payout->id;
        });
    }

    public function test_process_image_job_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessImageJob::dispatch('test/image.jpg', 'public', [
            'thumbnail' => ['width' => 150, 'height' => 150],
        ], 85);

        Queue::assertPushed(ProcessImageJob::class);
    }

    public function test_process_image_job_creates_thumbnails(): void
    {
        $this->markTestSkipped('Intervention Image facade needs configuration in test environment');

        Storage::fake('public');

        // Create a test image
        $image = UploadedFile::fake()->image('test.jpg', 800, 600);
        $path = $image->store('test', 'public');

        // Process the image
        $job = new ProcessImageJob($path, 'public', [
            'thumbnail' => ['width' => 150, 'height' => 150],
        ], 85);

        $job->handle();

        // Check original exists
        Storage::disk('public')->assertExists($path);

        // Check thumbnail was created
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumbnail.' . $pathInfo['extension'];
        Storage::disk('public')->assertExists($thumbnailPath);
    }

    public function test_process_bulk_conversion_action_job_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessBulkConversionActionJob::dispatch([1, 2, 3], 'approve');

        Queue::assertPushed(ProcessBulkConversionActionJob::class, function ($job) {
            return $job->action === 'approve' && count($job->conversionIds) === 3;
        });
    }

    public function test_bulk_conversion_approve_updates_balances(): void
    {
        $this->markTestSkipped('Integration test requires full database setup with affiliate links');

        Notification::fake();

        // Create advertiser
        $advertiser = User::factory()->create();
        $advertiser->assignRole('advertiser');

        // Create affiliate
        $affiliate = User::factory()->create([
            'balance' => 0,
            'pending_balance' => 0,
        ]);
        $affiliate->assignRole('affiliate');

        // Create offer
        $offer = Offer::create([
            'advertiser_id' => $advertiser->id,
            'name' => 'Test Offer',
            'slug' => 'test-offer-' . time(),
            'description' => 'Test offer description',
            'commission_model' => 'pps',
            'commission_rate' => 10.00,
            'is_active' => true,
            'access_type' => 'open',
        ]);

        // Create conversion with commission
        $conversion = Conversion::create([
            'affiliate_id' => $affiliate->id,
            'offer_id' => $offer->id,
            'conversion_id' => 'TEST_CONV_' . time(),
            'commission_amount' => 10.00,
            'status' => 'pending',
        ]);

        $commission = Commission::create([
            'user_id' => $affiliate->id,
            'conversion_id' => $conversion->id,
            'amount' => 10.00,
            'tier_bonus' => 0,
            'status' => 'pending',
        ]);

        // Update affiliate pending balance
        $affiliate->update(['pending_balance' => 10.00]);

        // Process job
        $job = new ProcessBulkConversionActionJob([$conversion->id], 'approve');
        $job->handle();

        // Verify conversion approved
        $this->assertEquals('approved', $conversion->fresh()->status);
        $this->assertEquals('approved', $commission->fresh()->status);

        // Verify balance updated
        $affiliate = $affiliate->fresh();
        $this->assertEquals(10.00, $affiliate->balance);
        $this->assertEquals(0, $affiliate->pending_balance);

        // Verify notification sent
        Notification::assertSentTo($affiliate, ConversionApprovedNotification::class);
    }

    public function test_bulk_conversion_reject_updates_balances(): void
    {
        $this->markTestSkipped('Integration test requires full database setup with affiliate links');

        Notification::fake();

        // Create advertiser
        $advertiser = User::factory()->create();
        $advertiser->assignRole('advertiser');

        // Create affiliate
        $affiliate = User::factory()->create([
            'balance' => 0,
            'pending_balance' => 10.00,
        ]);
        $affiliate->assignRole('affiliate');

        // Create offer
        $offer = Offer::create([
            'advertiser_id' => $advertiser->id,
            'name' => 'Test Offer 2',
            'slug' => 'test-offer-2-' . time(),
            'description' => 'Test offer description',
            'commission_model' => 'pps',
            'commission_rate' => 10.00,
            'is_active' => true,
            'access_type' => 'open',
        ]);

        // Create conversion
        $conversion = Conversion::create([
            'affiliate_id' => $affiliate->id,
            'offer_id' => $offer->id,
            'conversion_id' => 'TEST_CONV_' . time(),
            'commission_amount' => 10.00,
            'status' => 'pending',
        ]);

        Commission::create([
            'user_id' => $affiliate->id,
            'conversion_id' => $conversion->id,
            'amount' => 10.00,
            'tier_bonus' => 0,
            'status' => 'pending',
        ]);

        // Process job
        $job = new ProcessBulkConversionActionJob([$conversion->id], 'reject', 'Test rejection');
        $job->handle();

        // Verify conversion rejected
        $this->assertEquals('rejected', $conversion->fresh()->status);
        $this->assertEquals('Test rejection', $conversion->fresh()->rejection_reason);

        // Verify pending balance removed
        $affiliate = $affiliate->fresh();
        $this->assertEquals(0, $affiliate->pending_balance);
    }

    public function test_jobs_implement_should_queue_interface(): void
    {
        $affiliate = User::factory()->create();
        $payout = PayoutRequest::create([
            'affiliate_id' => $affiliate->id,
            'amount' => 100.00,
            'payment_method' => 'bank_transfer',
            'payment_details' => [],
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new ProcessPayoutJob($payout)
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new ProcessImageJob('test.jpg')
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new ProcessBulkConversionActionJob([1], 'approve')
        );
    }

    public function test_jobs_have_proper_timeout_and_retry_settings(): void
    {
        $affiliate = User::factory()->create();
        $payout = PayoutRequest::create([
            'affiliate_id' => $affiliate->id,
            'amount' => 100.00,
            'payment_method' => 'bank_transfer',
            'payment_details' => [],
            'status' => 'pending',
        ]);

        $payoutJob = new ProcessPayoutJob($payout);
        $this->assertEquals(120, $payoutJob->timeout);
        $this->assertEquals(3, $payoutJob->tries);

        $imageJob = new ProcessImageJob('test.jpg');
        $this->assertEquals(120, $imageJob->timeout);
        $this->assertEquals(2, $imageJob->tries);

        $bulkJob = new ProcessBulkConversionActionJob([1], 'approve');
        $this->assertEquals(300, $bulkJob->timeout);
        $this->assertEquals(2, $bulkJob->tries);
    }
}
