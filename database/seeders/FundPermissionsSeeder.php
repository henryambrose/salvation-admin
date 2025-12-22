<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FundPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Define all Fund module permissions
        $fundPermissions = [
            // Fund Categories
            'create-fund-category',
            'read-fund-category',
            'update-fund-category',
            'delete-fund-category',
            'list-fund-category',
            'restore-fund-category',

            // Annual Contributions (uses family_contributions table)
            'create-annual-contribution',
            'read-annual-contribution',
            'update-annual-contribution',
            'delete-annual-contribution',
            'list-annual-contribution',
            'restore-annual-contribution',

            // Community Contributions
            'create-community-contribution',
            'read-community-contribution',
            'update-community-contribution',
            'delete-community-contribution',
            'list-community-contribution',
            'restore-community-contribution',

            // Community Contributions Type 
            'create-community-contribution-type',
            'read-community-contribution-type',
            'update-community-contribution-type',
            'delete-community-contribution-type',
            'list-community-contribution-type',
            'restore-community-contribution-type',

            // Mass Intentions
            'create-mass-intention',
            'read-mass-intention',
            'update-mass-intention',
            'delete-mass-intention',
            'list-mass-intention',
            'restore-mass-intention',

            // Mass Types
            'create-mass-type',
            'read-mass-type',
            'update-mass-type',
            'delete-mass-type',
            'list-mass-type',
            'restore-mass-type',

            // Mass Intention Types
            'create-mass-intention-type',
            'read-mass-intention-type',
            'update-mass-intention-type',
            'delete-mass-intention-type',
            'list-mass-intention-type',
            'restore-mass-intention-type',

            // Payment Methods
            'create-payment-method',
            'read-payment-method',
            'update-payment-method',
            'delete-payment-method',
            'list-payment-method',
            'restore-payment-method',

            // Mass Schedules
            'create-mass-schedule',
            'read-mass-schedule',
            'update-mass-schedule',
            'delete-mass-schedule',
            'list-mass-schedule',
            'restore-mass-schedule',
        ];

        // Create permissions
        foreach ($fundPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }


        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($fundPermissions);
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($fundPermissions);
        }
    }
}
