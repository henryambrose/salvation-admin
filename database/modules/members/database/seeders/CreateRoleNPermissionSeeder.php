<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateRoleNPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles only - NO generic permissions
        $superadmin = Role::firstOrCreate(['name' => 'super admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // Create users and assign roles
        $user = User::firstOrCreate(
            ['email' => 'superadmin@salvationchurch.in'],
            ['name' => 'Super Admin', 'is_superadmin' => true, 'password' => bcrypt('superadmin')]
        );
        $user->assignRole('super admin');

        $user = User::firstOrCreate(
            ['email' => 'admin@salvationchurch.in'],
            ['name' => 'Admin', 'password' => bcrypt('admin')]
        );
        $user->assignRole('admin');

        $user = User::firstOrCreate(
            ['email' => 'viewer@salvationchurch.in'],
            ['name' => 'Viewer', 'password' => bcrypt('viewer')]
        );
        $user->assignRole('viewer');

        $this->command->info('Roles and users created successfully!');
        $this->command->info('Note: Permissions will be assigned by ModuleSeeder');
    }
}
