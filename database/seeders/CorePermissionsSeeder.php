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
        $this->command->info('Creating Core module permissions...');

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

            // Data Management
            'create-data-verification',
            'read-data-verification',
            'update-data-verification',
            'delete-data-verification',
            'list-data-verification',
            'restore-data-verification',

            'create-import',
            'read-import',
            'update-import',
            'delete-import',
            'list-import',
            'restore-import',

            'create-export',
            'read-export',
            'update-export',
            'delete-export',
            'list-export',
            'restore-export',

            'create-backup',
            'read-backup',
            'update-backup',
            'delete-backup',
            'list-backup',
            'restore-backup',

            'create-report',
            'read-report',
            'update-report',
            'delete-report',
            'list-report',
            'restore-report',

            // AI Assistance
            'create-chat',
            'read-chat',
            'update-chat',
            'delete-chat',
            'list-chat',
            'restore-chat',

            'create-ai',
            'read-ai',
            'update-ai',
            'delete-ai',
            'list-ai',
            'restore-ai',

            'create-assistant',
            'read-assistant',
            'update-assistant',
            'delete-assistant',
            'list-assistant',
            'restore-assistant',

            // Dashboard
            'create-dashboard',
            'read-dashboard',
            'update-dashboard',
            'delete-dashboard',
            'list-dashboard',
            'restore-dashboard',

            // Additional Core permissions
            'create-log',
            'read-log',
            'update-log',
            'delete-log',
            'list-log',
            'restore-log',

            'create-config',
            'read-config',
            'update-config',
            'delete-config',
            'list-config',
            'restore-config',

            'create-cache',
            'read-cache',
            'update-cache',
            'delete-cache',
            'list-cache',
            'restore-cache',

            'create-queue',
            'read-queue',
            'update-queue',
            'delete-queue',
            'list-queue',
            'restore-queue',
        ];

        // Create permissions
        foreach ($corePermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->command->info("Created permission: {$permissionName}");
        }

        $this->command->info('Core module permissions created successfully!');
        $this->command->info('Total permissions created: ' . count($corePermissions));

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($corePermissions);
            $this->command->info('Permissions assigned to super-admin role');
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($corePermissions);
            $this->command->info('Permissions assigned to admin role');
        }
    }
}
