<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the superadmin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Create the superadmin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@salvationchurch.in'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superadmin'), // Change password after first login
                'is_superadmin' => true, // Set superadmin flag
            ]
        );

        // Ensure is_superadmin is set to true (in case user already existed)
        $user->update(['is_superadmin' => true]);

        // Assign role to user
        if (!$user->hasRole($superAdminRole)) {
            $user->assignRole($superAdminRole);
        }


        // Create SCC Head and PPC Head roles
        $sccHeadRole = Role::firstOrCreate(['name' => 'scc-head']); // hyphen
        $ppcHeadRole = Role::firstOrCreate(['name' => 'ppc-head']); // hyphen

        // Create sample users for SCC Head and PPC Head roles
        $sccHeadUser = User::firstOrCreate(
            ['email' => 'scchead@salvationchurch.in'],
            [
            'name' => 'SCC Head User',
            'password' => bcrypt('scchead'), // Change password after first login
            ]
        );

        $ppcHeadUser = User::firstOrCreate(
            ['email' => 'ppchead@salvationchurch.in'],
            [
            'name' => 'PPC Head User',
            'password' => bcrypt('ppchead'), // Change password after first login
            ]
        );

        // Assign roles to the users
        if (!$sccHeadUser->hasRole($sccHeadRole)) {
            $sccHeadUser->assignRole($sccHeadRole);
        }

        if (!$ppcHeadUser->hasRole($ppcHeadRole)) {
            $ppcHeadUser->assignRole($ppcHeadRole);
        }


        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create sample user for the admin role
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@salvationchurch.in'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin'), // Change password after first login
            ]
        );

        // Assign role to the admin user
        if (!$adminUser->hasRole($adminRole)) {
            $adminUser->assignRole($adminRole);
        }


        // Create and assign permissions
        $ppcHeadRole = Role::where('name', 'ppc-head')->first();
        $permissions = [
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member',
            'create-external-member', 'read-external-member', 'update-external-member', 
            'delete-external-member', 'list-external-member'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $ppcHeadRole->syncPermissions($permissions);


        // Clean up PPC Head duplicates
        $ppcUnderscore = Role::where('name', 'ppc_head')->first();
        $ppcHyphen = Role::where('name', 'ppc-head')->first();

        if ($ppcUnderscore && $ppcHyphen) {
            // Move users from underscore to hyphen role
            $usersWithUnderscore = $ppcUnderscore->users;
            foreach ($usersWithUnderscore as $user) {
                $user->removeRole('ppc_head');
                $user->assignRole('ppc-head');
            }
            // Delete the underscore role
            $ppcUnderscore->delete();
        }

        // Clean up SCC Head duplicates
        $sccUnderscore = Role::where('name', 'scc_head')->first();
        $sccHyphen = Role::where('name', 'scc-head')->first();

        if ($sccUnderscore && $sccHyphen) {
            // Move users from underscore to hyphen role
            $usersWithUnderscore = $sccUnderscore->users;
            foreach ($usersWithUnderscore as $user) {
                $user->removeRole('scc_head');
                $user->assignRole('scc-head');
            }
            // Delete the underscore role
            $sccUnderscore->delete();
        }

        // Verify cleanup
        Role::all()->pluck('name');

        // Now assign permissions to the hyphen roles
        $ppcHeadRole = Role::where('name', 'ppc-head')->first();
        $sccHeadRole = Role::where('name', 'scc-head')->first();

        // PPC Head permissions
        $ppcPermissions = [
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member',
            'create-external-member', 'read-external-member', 'update-external-member', 
            'delete-external-member', 'list-external-member'
        ];

        // SCC Head permissions (same as PPC for now)
        $sccPermissions = $ppcPermissions;

        // Create and assign permissions
        foreach ($ppcPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $ppcHeadRole->syncPermissions($ppcPermissions);
        $sccHeadRole->syncPermissions($sccPermissions);

        echo "Cleanup completed!";

        // Create the PPC Head and SCC Head roles with hyphenated names
        $ppcHeadRole = Role::firstOrCreate(['name' => 'ppc-head']);
        $sccHeadRole = Role::firstOrCreate(['name' => 'scc-head']);

        echo "Roles created:\n";
        echo "- ppc-head\n";
        echo "- scc-head\n";

        // Create and assign permissions for PPC Head and SCC Head
        $permissions = [
            'create-member', 'read-member', 'update-member', 'delete-member', 'list-member',
            'create-external-member', 'read-external-member', 'update-external-member', 
            'delete-external-member', 'list-external-member'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to both roles
        $ppcHeadRole->syncPermissions($permissions);
        $sccHeadRole->syncPermissions($permissions);

        echo "Permissions assigned to both roles\n";

        // Verify
        echo "\nCurrent roles:\n";
        Role::all()->pluck('name')->each(function($name) { echo "- $name\n"; });

        echo "\nPPC Head permissions:\n";
        $ppcHeadRole->permissions->pluck('name')->each(function($name) { echo "- $name\n"; });

        echo "Setup completed!\n";
    }
}
