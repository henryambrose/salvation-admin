<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleAction;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define modules and their actions - Ordered to match sidebar navigation
        $modules = [
            // Core Management
            [
                'name' => 'Members',
                'slug' => 'member',
                'icon' => 'UserCircle',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'External Members',
                'slug' => 'external-member',
                'icon' => 'UserX',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Communities',
                'slug' => 'community',
                'icon' => 'UsersRound',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Parishes',
                'slug' => 'parish',
                'icon' => 'Church',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            
            // Organizational Structure
            [
                'name' => 'Zones',
                'slug' => 'zone',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Community Clusters',
                'slug' => 'community-cluster',
                'icon' => 'Network',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Clusters',
                'slug' => 'cluster',
                'icon' => 'Network',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Cells and Associations',
                'slug' => 'cells-and-association',
                'icon' => 'Heart',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Cells and Association Members',
                'slug' => 'cells-and-association-member',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            
            // Leadership
            [
                'name' => 'SCC Heads',
                'slug' => 'scc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'PPC Heads',
                'slug' => 'ppc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            
            // Member Attributes
            [
                'name' => 'Relationships',
                'slug' => 'relationship',
                'icon' => 'HeartHandshake',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Designations',
                'slug' => 'designation',
                'icon' => 'Crown',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Age Groups',
                'slug' => 'age-group',
                'icon' => 'UsersRound',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Blood Groups',
                'slug' => 'blood-group',
                'icon' => 'HeartPulse',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Genders',
                'slug' => 'gender',
                'icon' => 'UserCircle',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Statuses',
                'slug' => 'status',
                'icon' => 'CheckCircle',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Income Ranges',
                'slug' => 'income-range',
                'icon' => 'BadgeIndianRupee',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            
            // Geographic Data
            [
                'name' => 'Countries',
                'slug' => 'country',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'States',
                'slug' => 'state',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Cities',
                'slug' => 'city',
                'icon' => 'Building',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Towns',
                'slug' => 'town',
                'icon' => 'Home',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            
            // System Management
            [
                'name' => 'Users',
                'slug' => 'user',
                'icon' => 'User',
                'actions' => ['create', 'read', 'update', 'delete', 'list', 'restore'],
            ],
            [
                'name' => 'Role Management',
                'slug' => 'role',
                'icon' => 'Shield',
                'actions' => ['read', 'manage'], // Role management has read and manage actions
            ],
            
            // Special Pages
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'LayoutDashboard',
                'actions' => ['read'], // Dashboard only needs read permission
            ],
        ];

        foreach ($modules as $index => $module) {
            $moduleModel = Module::firstOrCreate([
                'name' => $module['name'],
                'slug' => $module['slug'],
                'icon' => $module['icon'],
            ]);
            Permission::firstOrCreate(['name' => $module['slug']]);

            foreach ($module['actions'] as $action) {
                $slug = "{$action}-{$module['slug']}";
                ModuleAction::firstOrCreate([
                    'module_id' => $moduleModel->id,
                    'action' => $action,
                    'name' => ucfirst($action) . " " . $module['name'],
                    'slug' => $slug,
                ]);

                Permission::firstOrCreate(['name' => $slug]);
            }
        }

        // Assign all permissions to superadmin role
        $superadminRole = \Spatie\Permission\Models\Role::where('name', 'super admin')->first();
        if ($superadminRole) {
            $allPermissions = Permission::all();
            $superadminRole->givePermissionTo($allPermissions);
        }
    }
}

