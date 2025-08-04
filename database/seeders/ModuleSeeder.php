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
                'name' => 'Members',
                'slug' => 'member',
                'icon' => 'UserCircle',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Communities',
                'slug' => 'community',
                'icon' => 'UsersRound',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            // [
            //     'name' => 'Community Fund',
            //     'slug' => 'community-fund',
            //     'icon' => 'CurrencyIcon',
            //     'actions' => ['create', 'read', 'update', 'delete', 'list'],
            // ],
            [
                'name' => 'Zones',
                'slug' => 'zone',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Blood Groups',
                'slug' => 'blood-group',
                'icon' => 'HeartPulse',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Income Ranges',
                'slug' => 'income-range',
                'icon' => 'BadgeIndianRupee',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'SCC Heads',
                'slug' => 'scc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'PPC Heads',
                'slug' => 'ppc-head',
                'icon' => 'UserCheck',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Countries',
                'slug' => 'country',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'States',
                'slug' => 'state',
                'icon' => 'MapPin',
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
                'name' => 'Towns',
                'slug' => 'town',
                'icon' => 'Home',
                // No icon specified
                'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
              'name' => 'Age Groups',
              'slug' => 'age-group',
              'icon' => 'UsersRound',
              'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
              'name' => 'Parishes',
              'slug' => 'parish',
              'icon' => 'Church',
              'actions' => ['create', 'read', 'update', 'delete', 'list'],
            ],
            [
              'name' => 'Relationships',
              'slug' => 'relationship',
              'icon' => 'HeartHandshake',
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
