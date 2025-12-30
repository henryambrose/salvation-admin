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

            // Dashboard
            'read-dashboard',
            'list-dashboard',

            // Certificate Management
            'create-certificate',
            'read-certificate',
            'update-certificate',
            'delete-certificate',
            'list-certificate',
            'restore-certificate',

            'generate-certificate',
            'reprint-certificate',
            'download-certificate',
            'view-certificate-history',

            // Certificate Types
            'create-certificate-type',
            'read-certificate-type',
            'update-certificate-type',
            'delete-certificate-type',
            'list-certificate-type',
            'restore-certificate-type',

            // Certificate Templates
            'create-certificate-template',
            'read-certificate-template',
            'update-certificate-template',
            'delete-certificate-template',
            'list-certificate-template',
            'restore-certificate-template',

            'manage-certificate-templates',
            'set-template-default',
            'preview-certificate-template',

            // Birth Archive Certificates
            'create-birth-archive',
            'read-birth-archive',
            'list-birth-archive',
            'update-birth-archive',
            'delete-birth-archive',
            'restore-birth-archive',
            'download-birth-archive',

            // Marriage Archive Certificates
            'create-marriage-archive',
            'read-marriage-archive',
            'list-marriage-archive',
            'update-marriage-archive',
            'delete-marriage-archive',
            'restore-marriage-archive',
            'download-marriage-archive',

            // Death Archive Certificates
            'create-death-archive',
            'read-death-archive',
            'list-death-archive',
            'update-death-archive',
            'delete-death-archive',
            'restore-death-archive',
            'download-death-archive',
        ];

        // Create permissions
        foreach ($membersPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Optionally assign these permissions to a super admin role if it exists
        $superAdminRole = Role::where('name', 'super admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($membersPermissions);
        }

        // Also assign to admin role if it exists
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($membersPermissions);
        }
    }
}
