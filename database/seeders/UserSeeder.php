<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
            ]
        );

        // Assign role to user
        if (!$user->hasRole($superAdminRole)) {
            $user->assignRole($superAdminRole);
        }

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
    }
}
