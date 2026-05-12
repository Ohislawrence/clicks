<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Roles and Permissions
        $this->call([
            RoleSeeder::class,
            OfferCategorySeeder::class,
            NigeriaLocationSeeder::class,
        ]);

        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'ClcksIntel Admin',
            'email' => 'lord@clicksintel.com',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Test Affiliate
        $affiliate = User::factory()->create([
            'name' => 'Test Affiliate',
            'email' => 'affiliate@test.com',
            'instagram_handle' => '@testaffiliate',
            'follower_count' => 50000,
            'is_verified' => true,
            'is_active' => true,
        ]);
        $affiliate->assignRole('affiliate');

        // Create Test Advertiser
        $advertiser = User::factory()->create([
            'name' => 'Test Advertiser',
            'email' => 'advertiser@test.com',
            'company_name' => 'Test Company',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $advertiser->assignRole('advertiser');

        $this->command->info('Database seeded successfully!');
    }
}
