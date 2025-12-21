<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PPCHeadPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create the role if it doesn't exist (using hyphen)
        $ppcHeadRole = Role::firstOrCreate(['name' => 'ppc-head']);
        
        // Full CRUD permissions for Members and External Members
        $permissions = [
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member', 'restore-member',
            'create-external-member', 'read-external-member', 'update-external-member', 'delete-external-member', 'list-external-member', 'restore-external-member',
            // 'read-data-verification', 'update-data-verification',
            'read-ppc-head', 'list-ppc-head',
            'read-community-cluster', 'list-community-cluster', 'read-cluster', 'list-cluster', 
            'read-cells-and-association', 'list-cells-and-association', 'read-cells-and-association-member', 'list-cells-and-association-member',
            'read-scc-head', 'list-scc-head',
            'read-community', 'list-community', 'read-parish', 'list-parish', 'read-zone', 'list-zone', 
            'read-relationship', 'list-relationship', 'read-designation', 'list-designation', 
            'read-age-group', 'list-age-group', 'read-blood-group', 'list-blood-group', 
            'read-gender', 'list-gender', 'read-status', 'list-status', 
            'read-income-range', 'list-income-range', 'read-country', 'list-country', 
            'read-state', 'list-state', 'read-city', 'list-city', 'read-town', 'list-town',
            'read-dashboard'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to PPC Head role
        $ppcHeadRole->syncPermissions($permissions);
    }
}
