<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Offer Management
            'view offers',
            'create offers',
            'edit offers',
            'delete offers',
            
            // Affiliate Links
            'create affiliate links',
            'view affiliate links',
            
            // Conversions
            'view conversions',
            'approve conversions',
            'reject conversions',
            
            // Payouts
            'request payouts',
            'approve payouts',
            'process payouts',
            
            // Users
            'view users',
            'manage users',
            
            // Reports
            'view reports',
            'view all reports',
            
            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Admin Role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Affiliate Role
        $affiliate = Role::firstOrCreate(['name' => 'affiliate']);
        $affiliate->givePermissionTo([
            'view offers',
            'create affiliate links',
            'view affiliate links',
            'view conversions',
            'request payouts',
            'view reports',
        ]);

        // Advertiser Role
        $advertiser = Role::firstOrCreate(['name' => 'advertiser']);
        $advertiser->givePermissionTo([
            'view offers',
            'create offers',
            'edit offers',
            'delete offers',
            'view conversions',
            'approve conversions',
            'reject conversions',
            'view reports',
            'view all reports',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
