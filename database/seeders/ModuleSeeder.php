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
        // Define modules and their actions
        $modules = [
            [
                'name' => 'Member',
                'slug' => 'member',
                'icon' => 'UserCircle',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Community',
                'slug' => 'community',
                'icon' => 'CurrencyIcon',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Community Fund',
                'slug' => 'community-fund',
                'icon' => 'CurrencyIcon',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Zone',
                'slug' => 'zone',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Blood Group',
                'slug' => 'blood-group',
                'icon' => 'Droplet',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Family Income Range',
                'slug' => 'family-income-range',
                'icon' => null,
                // No icon specified
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'SCC Head',
                'slug' => 'scc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'PPC Head',
                'slug' => 'ppc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Country',
                'slug' => 'country',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'State',
                'slug' => 'state',
                'icon' => 'MapPin',
                // No icon specified
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Town',
                'slug' => 'town',
                'icon' => 'Home',
                // No icon specified
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
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


    }
}
