<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MembersPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Members module permissions...');

        // Define all Members module permissions
        $membersPermissions = [
            // Core Management
            'create-member',
            'read-member',
            'update-member',
            'delete-member',
            'list-member',
            'restore-member',

            'create-external-member',
            'read-external-member',
            'update-external-member',
            'delete-external-member',
            'list-external-member',
            'restore-external-member',

            'create-community',
            'read-community',
            'update-community',
            'delete-community',
            'list-community',
            'restore-community',

            'create-parish',
            'read-parish',
            'update-parish',
            'delete-parish',
            'list-parish',
            'restore-parish',

            // Organizational Structure
            'create-zone',
            'read-zone',
            'update-zone',
            'delete-zone',
            'list-zone',
            'restore-zone',

            'create-cluster',
            'read-cluster',
            'update-cluster',
            'delete-cluster',
            'list-cluster',
            'restore-cluster',

            'create-community-cluster',
            'read-community-cluster',
            'update-community-cluster',
            'delete-community-cluster',
            'list-community-cluster',
            'restore-community-cluster',

            'create-cells-and-association',
            'read-cells-and-association',
            'update-cells-and-association',
            'delete-cells-and-association',
            'list-cells-and-association',
            'restore-cells-and-association',

            'create-cells-association-member',
            'read-cells-association-member',
            'update-cells-association-member',
            'delete-cells-association-member',
            'list-cells-association-member',
            'restore-cells-association-member',

            // Leadership
            'create-scc-head',
            'read-scc-head',
            'update-scc-head',
            'delete-scc-head',
            'list-scc-head',
            'restore-scc-head',

            'create-ppc-head',
            'read-ppc-head',
            'update-ppc-head',
            'delete-ppc-head',
            'list-ppc-head',
            'restore-ppc-head',

            'create-s-c-c-head',
            'read-s-c-c-head',
            'update-s-c-c-head',
            'delete-s-c-c-head',
            'list-s-c-c-head',
            'restore-s-c-c-head',

            'create-p-p-c-head',
            'read-p-p-c-head',
            'update-p-p-c-head',
            'delete-p-p-c-head',
            'list-p-p-c-head',
            'restore-p-p-c-head',

            // Member Attributes
            'create-relationship',
            'read-relationship',
            'update-relationship',
            'delete-relationship',
            'list-relationship',
            'restore-relationship',

            'create-designation',
            'read-designation',
            'update-designation',
            'delete-designation',
            'list-designation',
            'restore-designation',

            'create-age-group',
            'read-age-group',
            'update-age-group',
            'delete-age-group',
            'list-age-group',
            'restore-age-group',

            'create-blood-group',
            'read-blood-group',
            'update-blood-group',
            'delete-blood-group',
            'list-blood-group',
            'restore-blood-group',

            'create-gender',
            'read-gender',
            'update-gender',
            'delete-gender',
            'list-gender',
            'restore-gender',

            'create-status',
            'read-status',
            'update-status',
            'delete-status',
            'list-status',
            'restore-status',

            'create-income-range',
            'read-income-range',
            'update-income-range',
            'delete-income-range',
            'list-income-range',
            'restore-income-range',

            'create-group',
            'read-group',
            'update-group',
            'delete-group',
            'list-group',
            'restore-group',

            // Geographic Data
            'create-country',
            'read-country',
            'update-country',
            'delete-country',
            'list-country',
            'restore-country',

            'create-state',
            'read-state',
            'update-state',
            'delete-state',
            'list-state',
            'restore-state',

            'create-city',
            'read-city',
            'update-city',
            'delete-city',
            'list-city',
            'restore-city',

            'create-town',
            'read-town',
            'update-town',
            'delete-town',
            'list-town',
            'restore-town',

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
        ];

        // Create permissions
        foreach ($membersPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
            $this->command->info("Created permission: {$permissionName}");
        }

        $this->command->info('Members module permissions created successfully!');
        $this->command->info('Total permissions created: ' . count($membersPermissions));

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($membersPermissions);
            $this->command->info('Permissions assigned to super-admin role');
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($membersPermissions);
            $this->command->info('Permissions assigned to admin role');
        }
    }
}
