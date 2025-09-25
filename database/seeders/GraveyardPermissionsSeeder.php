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

            // Grave Categories
            'create-grave-category',
            'read-grave-category',
            'update-grave-category',
            'delete-grave-category',
            'list-grave-category',
            'restore-grave-category',

            // Permanent Grave Bookings
            'create-permanent-grave-booking',
            'read-permanent-grave-booking',
            'update-permanent-grave-booking',
            'delete-permanent-grave-booking',
            'list-permanent-grave-booking',
            'restore-permanent-grave-booking',
            // 'confirm-permanent-grave-booking',
            // 'cancel-permanent-grave-booking',

            // Temporary Grave Bookings
            'create-temporary-grave-booking',
            'read-temporary-grave-booking',
            'update-temporary-grave-booking',
            'delete-temporary-grave-booking',
            'list-temporary-grave-booking',
            'restore-temporary-grave-booking',
            // 'confirm-temporary-grave-booking',
            // 'cancel-temporary-grave-booking',

            // Service Types
            'create-service-type',
            'read-service-type',
            'update-service-type',
            'delete-service-type',
            'list-service-type',
            'restore-service-type',

            // Obituary Pages
            'create-obituary-page',
            'read-obituary-page',
            'update-obituary-page',
            'delete-obituary-page',
            'list-obituary-page',
            'restore-obituary-page',
            'publish-obituary-page',
            'unpublish-obituary-page',

            // Obituary Condolences
            'create-obituary-condolence',
            'read-obituary-condolence',
            'update-obituary-condolence',
            'delete-obituary-condolence',
            'list-obituary-condolence',
            'approve-obituary-condolence',
            'reject-obituary-condolence',

            // Payments
            'create-graveyard-payment',
            'read-graveyard-payment',
            'update-graveyard-payment',
            'delete-graveyard-payment',
            'list-graveyard-payment',
            // 'process-graveyard-payment',
            // 'refund-graveyard-payment',

            // Obituary Payments
            'create-obituary-payment',
            'read-obituary-payment',
            'update-obituary-payment',
            'delete-obituary-payment',
            'list-obituary-payment',
            // 'process-obituary-payment',
            // 'refund-obituary-payment',

            // Obituary Plans
            'create-obituary-plans',
            'read-obituary-plans',
            'update-obituary-plans',
            'delete-obituary-plans',
            'list-obituary-plans',
            'restore-obituary-plans',

            // Niche Transfers
            'create-niche-transfer',
            'read-niche-transfer',
            'update-niche-transfer',
            'delete-niche-transfer',
            'list-niche-transfer',
            // 'approve-niche-transfer',
            // 'reject-niche-transfer',

            // Valid Members Management
            'create-valid-member',
            'read-valid-member',
            'update-valid-member',
            'delete-valid-member',
            'list-valid-member',
            'restore-valid-member',

            // Niche Valid Members
            // 'create-niche-valid-member',
            // 'read-niche-valid-member',
            // 'update-niche-valid-member',
            // 'delete-niche-valid-member',
            // 'list-niche-valid-member',

            // Permanent Valid Members
            // 'create-permanent-valid-member',
            // 'read-permanent-valid-member',
            // 'update-permanent-valid-member',
            // 'delete-permanent-valid-member',
            // 'list-permanent-valid-member',

            // Reports
            'view-graveyard-reports',
            // 'export-graveyard-data',
            // 'view-graveyard-analytics',
            // 'generate-graveyard-certificates',

            // Transfer Operations
            'transfer-temporary-to-niche',
            'transfer-temporary-to-permanent',
            // 'transfer-niche-to-permanent',

            // Administrative
            // 'manage-graveyard-settings',
            // 'manage-graveyard-expiration',
            // 'bulk-import-graveyard-data',
            // 'bulk-export-graveyard-data',

            // Legacy compatibility - keep existing generic grave booking permissions
            // 'create-grave-booking',
            // 'read-grave-booking',
            // 'update-grave-booking',
            // 'delete-grave-booking',
            // 'list-grave-booking',
            // 'restore-grave-booking',
            // 'confirm-grave-booking',
            // 'cancel-grave-booking',
        ];

        // Create permissions
        foreach ($graveyardPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign all permissions to super admin role
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($graveyardPermissions);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($graveyardPermissions);
        }

        // Create a specific Graveyard Manager role with all graveyard permissions
        $graveyardManagerRole = Role::firstOrCreate(['name' => 'graveyard-manager']);
        $graveyardManagerRole->givePermissionTo($graveyardPermissions);

        // Create a Graveyard Staff role with limited permissions
        $graveyardStaffRole = Role::firstOrCreate(['name' => 'graveyard-staff']);
        $graveyardStaffPermissions = [
            'read-graveyard-dashboard',
            'read-permanent-grave',
            'read-temporary-grave',
            'read-niche',
            'read-grave-category',
            'create-temporary-grave-booking',
            'read-temporary-grave-booking',
            'update-temporary-grave-booking',
            'create-permanent-grave-booking',
            'read-permanent-grave-booking',
            'update-permanent-grave-booking',
            'read-obituary-page',
            'create-obituary-condolence',
            'read-obituary-condolence',
            'view-graveyard-reports',
        ];
        $graveyardStaffRole->givePermissionTo($graveyardStaffPermissions);

        // Create an Obituary Manager role for managing obituaries
        $obituaryManagerRole = Role::firstOrCreate(['name' => 'obituary-manager']);
        $obituaryManagerPermissions = [
            'read-graveyard-dashboard',
            'create-obituary-page',
            'read-obituary-page',
            'update-obituary-page',
            'delete-obituary-page',
            'list-obituary-page',
            'publish-obituary-page',
            'unpublish-obituary-page',
            'create-obituary-condolence',
            'read-obituary-condolence',
            'update-obituary-condolence',
            'delete-obituary-condolence',
            'approve-obituary-condolence',
            'reject-obituary-condolence',
            'create-obituary-payment',
            'read-obituary-payment',
            // 'process-obituary-payment',
        ];
        $obituaryManagerRole->givePermissionTo($obituaryManagerPermissions);
    }
}
