<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionCategory;
use App\Models\PermissionCategoryRule;

class PermissionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Core Management',
                'slug' => 'core-management',
                'description' => 'Core member and community management permissions',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'member', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'external-member', 'priority' => 10],
                    ['rule_type' => 'starts_with', 'rule_value' => 'community', 'priority' => 5],
                    ['rule_type' => 'contains', 'rule_value' => 'parish', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Organizational Structure',
                'slug' => 'organizational-structure',
                'description' => 'Zone, cluster, and organizational structure permissions',
                'color' => '#10B981',
                'sort_order' => 2,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'zone', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'cluster', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'community-cluster', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'cells-and-association', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'cells-association-member', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Leadership',
                'slug' => 'leadership',
                'description' => 'PPC Head, SCC Head, and leadership permissions',
                'color' => '#8B5CF6',
                'sort_order' => 3,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'scc-head', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'ppc-head', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 's-c-c-head', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'p-p-c-head', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Member Attributes',
                'slug' => 'member-attributes',
                'description' => 'Member attribute and demographic permissions',
                'color' => '#F59E0B',
                'sort_order' => 4,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'relationship', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'designation', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'age-group', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'blood-group', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'gender', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'status', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'income-range', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'group', 'priority' => 5],
                ]
            ],
            [
                'name' => 'Geographic Data',
                'slug' => 'geographic-data',
                'description' => 'Country, state, city, and town permissions',
                'color' => '#06B6D4',
                'sort_order' => 5,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'country', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'state', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'city', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'town', 'priority' => 10],
                ]
            ],
            [
                'name' => 'System Management',
                'slug' => 'system-management',
                'description' => 'User, role, and system management permissions',
                'color' => '#EF4444',
                'sort_order' => 6,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'user', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'role', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'audit', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'permission', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'setting', 'priority' => 5],
                ]
            ],
            [
                'name' => 'Fund App Management',
                'slug' => 'fund-app-management',
                'description' => 'Fund, contributions, and payment permissions',
                'color' => '#EC4899',
                'sort_order' => 7,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'fund', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'annual-contribution', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'mass-intention', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'payment-method', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'fund-category', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Data Management',
                'slug' => 'data-management',
                'description' => 'Data verification, import, export permissions',
                'color' => '#8B5A2B',
                'sort_order' => 8,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'data-verification', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'import', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'export', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'backup', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'report', 'priority' => 5],
                ]
            ],
            [
                'name' => 'AI Assistance',
                'slug' => 'ai-assistance',
                'description' => 'AI chat and assistance permissions',
                'color' => '#FF6B6B',
                'sort_order' => 9,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'chat', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'ai', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'assistant', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Dashboard and overview permissions',
                'color' => '#6366F1',
                'sort_order' => 10,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'dashboard', 'priority' => 10],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $rules = $categoryData['rules'];
            unset($categoryData['rules']);
            
            $category = PermissionCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Create rules for this category
            foreach ($rules as $ruleData) {
                PermissionCategoryRule::updateOrCreate(
                    [
                        'permission_category_id' => $category->id,
                        'rule_type' => $ruleData['rule_type'],
                        'rule_value' => $ruleData['rule_value'],
                    ],
                    $ruleData
                );
            }
        }
    }
}
