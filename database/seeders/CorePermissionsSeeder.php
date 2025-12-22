<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CorePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Define all Core module permissions
        $corePermissions = [
            // System Management
            'create-user',
            'read-user',
            'update-user',
            'delete-user',
            'list-user',
            'restore-user',

            'create-role',
            'read-role',
            'update-role',
            'delete-role',
            'list-role',
            'restore-role',

            'create-audit',
            'read-audit',
            'update-audit',
            'delete-audit',
            'list-audit',
            'restore-audit',

            'create-permission',
            'read-permission',
            'update-permission',
            'delete-permission',
            'list-permission',
            'restore-permission',

            'create-setting',
            'read-setting',
            'update-setting',
            'delete-setting',
            'list-setting',
            'restore-setting',

            // Dashboard
            'create-dashboard',
            'read-dashboard',
            'update-dashboard',
            'delete-dashboard',
            'list-dashboard',
            'restore-dashboard',
        ];

        // Create permissions
        foreach ($corePermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($corePermissions);
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($corePermissions);
        }
    }
}
