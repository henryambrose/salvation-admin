<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SCCHeadPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create the role if it doesn't exist (using hyphen)
        $sccHeadRole = Role::firstOrCreate(['name' => 'scc-head']);
        
        // IDENTICAL permissions as PPC Head
        $permissions = [
            // Member permissions
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member', 'restore-member',
            // External Member permissions  
            'create-external-member', 'read-external-member', 'update-external-member', 'delete-external-member', 'list-external-member', 'restore-external-member',
            // Basic read AND list permissions for related data (same as PPC Head)
            'read-community', 'list-community', 'read-parish', 'list-parish', 'read-zone', 'list-zone', 
            'read-relationship', 'list-relationship', 'read-designation', 'list-designation', 
            'read-age-group', 'list-age-group', 'read-blood-group', 'list-blood-group', 
            'read-gender', 'list-gender', 'read-status', 'list-status', 
            'read-income-range', 'list-income-range', 'read-country', 'list-country', 
            'read-state', 'list-state', 'read-city', 'list-city', 'read-town', 'list-town'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to SCC Head role
        $sccHeadRole->syncPermissions($permissions);
        
        $this->command->info('SCC Head permissions assigned successfully!');
        $this->command->info('Total permissions assigned: ' . count($permissions));
        $this->command->info('SCC Head now has IDENTICAL permissions as PPC Head');
    }
}
