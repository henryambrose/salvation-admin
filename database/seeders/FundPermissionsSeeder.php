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
        $this->command->info('Creating Fund module permissions...');

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
        ];

        // Create permissions
        foreach ($fundPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->command->info("Created permission: {$permissionName}");
        }

        $this->command->info('Fund module permissions created successfully!');
        $this->command->info('Total permissions created: ' . count($fundPermissions));

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($fundPermissions);
            $this->command->info('Permissions assigned to super-admin role');
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($fundPermissions);
            $this->command->info('Permissions assigned to admin role');
        }
    }
}
