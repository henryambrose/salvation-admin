<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Define menu items and their permissions
        $menuItems = [
            'Dashboard',
            'Member',
            'Community',
            'Community Fund',
            'Zone',
            'Blood Group',
            'Family Income Range',
            'SCC Head',
            'PPC Head',
            'Country',
            'State',
            'Town',
            'Users',
        ];

        // Create permissions for each menu item
        foreach ($menuItems as $menuItem) {
            Permission::firstOrCreate(['name' => "view-$menuItem"]);
            Permission::firstOrCreate(['name' => "create-$menuItem"]);
            Permission::firstOrCreate(['name' => "edit-$menuItem"]);
            Permission::firstOrCreate(['name' => "delete-$menuItem"]);
        }
        // Create the permission if it doesn't exist
        $manageRolesPermission = Permission::firstOrCreate(['name' => 'update-role-permissions']);

        // Create the superadmin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);

        // Assign permission to role
        if (!$superAdminRole->hasPermissionTo($manageRolesPermission)) {
            $superAdminRole->givePermissionTo($manageRolesPermission);
        }

        // Create the superadmin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@salvationchurch.in'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superadmin'), // Change password after first login
            ]
        );

        // Assign role to user
        if (!$user->hasRole($superAdminRole)) {
            $user->assignRole($superAdminRole);
        }

        // Assign all permissions to the superadmin role
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);


        $this->command->info('UserSeeder completed successfully!');
        $this->command->info('Super Admin assigned to User ID superadmin@salvationchurch.in with password "superadmin"');
        $this->command->info('You can now login with the superadmin role and manage permissions.');

        // Create SCC Head and PPC Head roles
        $sccHeadRole = Role::firstOrCreate(['name' => 'scc-head']);
        $ppcHeadRole = Role::firstOrCreate(['name' => 'ppc-head']);

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

        $this->command->info('SCC Head and PPC Head roles and users created successfully!');

        // Assign member permissions only to SCC Head and PPC Head roles
        $memberPermissions = Permission::where('name', 'LIKE', '%-Member')->get();

        $sccHeadRole->syncPermissions($memberPermissions);
        $ppcHeadRole->syncPermissions($memberPermissions);

        $this->command->info('Member permissions assigned to SCC Head and PPC Head roles successfully!');


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

        // Assign member and community permissions to the admin role
        $adminPermissions = Permission::whereIn('name', [
            'view-Member', 'create-Member', 'edit-Member', 'delete-Member',
            'view-Community', 'create-Community', 'edit-Community', 'delete-Community',
        ])->get();

        $adminRole->syncPermissions($adminPermissions);

        $this->command->info('Admin role and user created successfully with member and community permissions!');
    }
}
