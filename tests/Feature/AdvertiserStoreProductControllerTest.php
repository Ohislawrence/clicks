<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\StorePlan;
use App\Models\StoreTheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdvertiserStoreProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertiser_can_create_product_with_image_upload()
    {
        $role = Role::firstOrCreate(['name' => 'advertiser']);
        $user = User::factory()->create();
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
            'description' => 'Default store theme',
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
            'description' => 'Test store description',
            'store_plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
            'subscription_start_date' => now()->toDateString(),
            'subscription_end_date' => now()->addMonth()->toDateString(),
            'subscription_status' => 'active',
            'store_theme_id' => $theme->id,
            'payment_method' => 'link',
            'payment_provider' => 'paystack',
        ]);

        $response = $this->actingAs($user)->post(route('advertiser.store.products.store', ['store' => $store->id]), [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'price' => 1000,
            'compare_at_price' => 1200,
            'stock_quantity' => 5,
            'sku' => 'TESTSKU',
            'images' => [UploadedFile::fake()->image('product.jpg')],
            'is_active' => true,
            'is_featured' => false,
            'product_type' => 'tangible',
        ]);

        $response->assertRedirect(route('advertiser.store.products.index', ['store' => $store->id]));

        $this->assertDatabaseHas('store_products', [
            'store_id' => $store->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
        ]);

        $product = Store::find($store->id)->products()->first();
        $this->assertNotEmpty($product->images);
        $this->assertStringContainsString('store-products', $product->images[0]);
    }
}
