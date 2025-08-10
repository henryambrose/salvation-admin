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
            // Member permissions
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member', 'restore-member',
            // External Member permissions  
            'create-external-member', 'read-external-member', 'update-external-member', 'delete-external-member', 'list-external-member', 'restore-external-member',
            // Basic read permissions for related data
            'read-community', 'read-parish', 'read-zone', 'read-relationship', 'read-designation', 
            'read-age-group', 'read-blood-group', 'read-gender', 'read-status', 'read-income-range',
            'read-country', 'read-state', 'read-city', 'read-town'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to PPC Head role
        $ppcHeadRole->syncPermissions($permissions);
        
        $this->command->info('PPC Head permissions assigned successfully!');
        $this->command->info('Total permissions assigned: ' . count($permissions));
    }
}
