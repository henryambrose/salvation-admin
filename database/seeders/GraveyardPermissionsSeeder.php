<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GraveyardPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all Graveyard module permissions
        $graveyardPermissions = [
            // Dashboard
            'read-graveyard-dashboard',

            // Permanent Graves
            'create-permanent-grave',
            'read-permanent-grave',
            'update-permanent-grave',
            'delete-permanent-grave',
            'list-permanent-grave',
            'restore-permanent-grave',

            // Temporary Graves
            'create-temporary-grave',
            'read-temporary-grave',
            'update-temporary-grave',
            'delete-temporary-grave',
            'list-temporary-grave',
            'restore-temporary-grave',

            // Niches
            'create-niche',
            'read-niche',
            'update-niche',
            'delete-niche',
            'list-niche',
            'restore-niche',

            // Grave Bookings
            'create-grave-booking',
            'read-grave-booking',
            'update-grave-booking',
            'delete-grave-booking',
            'list-grave-booking',
            'restore-grave-booking',
            'confirm-grave-booking',
            'cancel-grave-booking',

            // Service Types
            'create-service-type',
            'read-service-type',
            'update-service-type',
            'delete-service-type',
            'list-service-type',
            'restore-service-type',




            // Maintenance Records
            'create-maintenance',
            'read-maintenance',
            'update-maintenance',
            'delete-maintenance',
            'list-maintenance',
            'restore-maintenance',



            // Reports
            'view-graveyard-reports',
            'export-graveyard-data',

            // Transfer Operations
            'transfer-temporary-to-niche',
            'transfer-temporary-to-permanent',

            // Administrative
            'manage-graveyard-settings',
            'view-graveyard-analytics',
        ];

        // Create permissions
        foreach ($graveyardPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($graveyardPermissions);
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($graveyardPermissions);
        }
    }
}
