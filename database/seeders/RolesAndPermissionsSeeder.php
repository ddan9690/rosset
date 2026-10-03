<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define All Permissions based on application routes
        $permissions = [
            // Admin Dashboard & Overview
            'view dashboard',

            // Member Management
            'view members',
            'create members',
            'edit members',
            'show members',
            'onboard members',
            'manage membership requests',

            // Benevolence Categories & Cases Management
            'manage benevolence categories',
            'view benevolence cases',
            'create benevolence cases',
            'edit benevolence cases',
            'show benevolence cases',

            // Transactions & Administrative Reports
            'view transactions',
            'download reports', // Single unified permission for administrative downloads/reports

            // System Settings (Technical / Super Admin level)
            'manage settings',

            // Portal & Member-specific Features
            'access portal',
            'view membership status',
            'pay registration fee',
            'view solidarity fund',
            'update profile',
            'update dependants',
            'contribute benevolence',
            'download member reports', 
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions

        // Super Admin: System developer with full technical access (all permissions)
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Welfare Admin: Day-to-day manager who accesses all operational pages, reports, and records
        $welfareAdminRole = Role::firstOrCreate(['name' => 'welfare admin']);
        $welfareAdminRole->givePermissionTo([
            'view dashboard',
            'view members',
            'create members',
            'edit members',
            'show members',
            'onboard members',
            'manage membership requests',
            'manage benevolence categories',
            'view benevolence cases',
            'create benevolence cases',
            'edit benevolence cases',
            'show benevolence cases',
            'view transactions',
            'download reports',
           
            'access portal',
            'view membership status',
            'view solidarity fund',
            'download member reports',
        ]);

      
        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $memberRole->givePermissionTo([
            'access portal',
            'view membership status',
            'pay registration fee',
            'view solidarity fund',
            'update profile',
            'update dependants',
            'contribute benevolence',
            'download member reports', 
        ]);
    }
}