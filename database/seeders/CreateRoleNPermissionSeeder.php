<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRoleNPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::create(['name' => 'super admin']); // create, update, delete, read
        $admin = Role::create(['name' => 'admin']); // create, update, read
        $viewer = Role::create(['name' => 'viewer']); // read only

        Permission::create(['name' => 'create all']);
        Permission::create(['name' => 'update all']);
        Permission::create(['name' => 'delete all']);
        Permission::create(['name' => 'read all']);

        $superadmin->givePermissionTo(['create all', 'update all', 'delete all', 'read all']);
        $admin->givePermissionTo(['create all', 'update all', 'read all']);
        $viewer->givePermissionTo(['read all']);

        // $user = User::find(1);
        $user = User::create(
            ['email' => 'superadmin@salvationchurch.in', 'name' => 'Super Admin', 'password' => bcrypt('superadmin')]
        );
        $user->assignRole('super admin');

        $user = User::create(
            ['email' => 'admin@salvationchurch.in', 'name' => 'Admin', 'password' => bcrypt('admin')]
        );
        $user->assignRole('admin');

        $user = User::create(
            ['email' => 'viewer@salvationchurch.in', 'name' => 'Viewer', 'password' => bcrypt('viewer')]
        );
        $user->assignRole('viewer');
        // $user->givePermissionTo(['create all', 'read all']);
        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Super Admin assigned to User ID superadmin@salvationchurch.in with password "superadmin"');
        $this->command->info('Super Admin assigned to User ID admin@salvationchurch.in with password "admin"');
        $this->command->info('Super Admin assigned to User ID viewer@salvationchurch.in with password "viewer"');
        $this->command->info('You can now login with these roles and permissions.');
        $this->command->info('Run `php artisan permission:cache-reset` to clear the cache.');
        $this->command->info('Run `php artisan permission:list` to see the list of roles and permissions.');
        $this->command->info('Run `php artisan permission:role-permission` to see the role and permission mapping.');
        $this->command->info('Run `php artisan permission:role-user` to see the role and user mapping.');
        $this->command->info('Run `php artisan permission:user-permission` to see the user and permission mapping.');
    }
}
