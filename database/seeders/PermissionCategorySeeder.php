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
                'app' => 'Members',
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
                'app' => 'Members',
                'color' => '#10B981',
                'sort_order' => 2,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'zone', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'cluster', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'community-cluster', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'cells-and-association', 'priority' => 15],

                ]
            ],
            [
                'name' => 'Leadership',
                'slug' => 'leadership',
                'description' => 'PPC Head, SCC Head, and leadership permissions',
                'app' => 'Members',
                'color' => '#8B5CF6',
                'sort_order' => 3,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'scc-head', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'ppc-head', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Member Attributes',
                'slug' => 'member-attributes',
                'description' => 'Member attribute and demographic permissions',
                'app' => 'Members',
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
                ]
            ],
            [
                'name' => 'Geographic Data',
                'slug' => 'geographic-data',
                'description' => 'Country, state, city, and town permissions',
                'app' => 'Members',
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
                'app' => 'Members',
                'color' => '#EF4444',
                'sort_order' => 6,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'user', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'role', 'priority' => 10],
                ]
            ],
            // [
            //     'name' => 'Data Management',
            //     'slug' => 'data-management',
            //     'description' => 'Data verification, import, export permissions',
            //     'app' => 'Members',
            //     'color' => '#8B5A2B',
            //     'sort_order' => 7,
            //     'rules' => [
            //         ['rule_type' => 'contains', 'rule_value' => 'data-verification', 'priority' => 10],
            //         ['rule_type' => 'contains', 'rule_value' => 'import', 'priority' => 10],
            //         ['rule_type' => 'contains', 'rule_value' => 'export', 'priority' => 10],
            //         ['rule_type' => 'contains', 'rule_value' => 'backup', 'priority' => 10],
            //         ['rule_type' => 'contains', 'rule_value' => 'report', 'priority' => 5],
            //     ]
            // ],
            [
                'name' => 'AI Assistance',
                'slug' => 'ai-assistance',
                'description' => 'AI chat and assistance permissions',
                'app' => 'Members',
                'color' => '#FF6B6B',
                'sort_order' => 8,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'chat', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'ai', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'assistant', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Dashboard and overview permissions',
                'app' => 'Members',
                'color' => '#6366F1',
                'sort_order' => 9,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'dashboard', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Fund Categories',
                'slug' => 'fund-categories',
                'description' => 'Fund category management permissions',
                'app' => 'Fund',
                'color' => '#10B981',
                'sort_order' => 10,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'fund-category', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'fund_category', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'fundcategory', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Annual Contributions',
                'slug' => 'annual-contributions',
                'description' => 'Annual contribution management permissions (uses family_contributions table)',
                'app' => 'Fund',
                'color' => '#F59E0B',
                'sort_order' => 11,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'annual-contribution', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'annual_contribution', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Community Contributions',
                'slug' => 'community-contributions',
                'description' => 'Community contribution management permissions ',
                'app' => 'Fund',
                'color' => '#F59E0B',
                'sort_order' => 12,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'community-contribution', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'annual_contribution', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Community Contributions Type',
                'slug' => 'community-contributions-type',
                'description' => 'Community contribution type management permissions ',
                'app' => 'Fund',
                'color' => '#F59E0B',
                'sort_order' => 13,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'community-contribution-type', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'annual_contribution', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Mass Intentions',
                'slug' => 'mass-intentions',
                'description' => 'Mass intention management permissions',
                'app' => 'Fund',
                'color' => '#8B5CF6',
                'sort_order' => 14,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'mass-intention', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'mass_intention', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Mass Types',
                'slug' => 'mass-types',
                'description' => 'Mass type management permissions',
                'app' => 'Fund',
                'color' => '#EC4899',
                'sort_order' => 15,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'mass-type', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'mass_type', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Mass Intention Types',
                'slug' => 'mass-intention-types',
                'description' => 'Mass intention type management permissions',
                'app' => 'Fund',
                'color' => '#06B6D4',
                'sort_order' => 16,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'mass-intention-type', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'mass_intention_type', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Payment Methods',
                'slug' => 'payment-methods',
                'description' => 'Payment method management permissions',
                'app' => 'Fund',
                'color' => '#EF4444',
                'sort_order' => 17,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'payment-method', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'payment_method', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Graveyard Management',
                'slug' => 'graveyard-management',
                'description' => 'Core graveyard management permissions',
                'app' => 'Graveyard',
                'color' => '#374151',
                'sort_order' => 18,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'graveyard-dashboard', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'graveyard-settings', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'graveyard-analytics', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'graveyard-reports', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'graveyard-data', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Permanent Graves',
                'slug' => 'permanent-graves',
                'description' => 'Permanent grave management permissions',
                'app' => 'Graveyard',
                'color' => '#059669',
                'sort_order' => 19,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'permanent-grave', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'permanent_grave', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Temporary Graves',
                'slug' => 'temporary-graves',
                'description' => 'Temporary grave management permissions',
                'app' => 'Graveyard',
                'color' => '#D97706',
                'sort_order' => 20,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'temporary-grave', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'temporary_grave', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Niches',
                'slug' => 'niches',
                'description' => 'Niche management permissions',
                'app' => 'Graveyard',
                'color' => '#7C3AED',
                'sort_order' => 21,
                'rules' => [
                    // ['rule_type' => 'exact', 'rule_value' => 'niche', 'priority' => 15],
                    // ['rule_type' => 'starts_with', 'rule_value' => 'niche-', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'niche-transfer', 'priority' => 20],
                    // ['rule_type' => 'contains', 'rule_value' => 'niche-valid-member', 'priority' => 20],
                ]
            ],
            [
                'name' => 'Grave Bookings',
                'slug' => 'grave-bookings',
                'description' => 'Grave booking management permissions',
                'app' => 'Graveyard',
                'color' => '#DC2626',
                'sort_order' => 22,
                'rules' => [
                    // ['rule_type' => 'contains', 'rule_value' => 'grave-booking', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'permanent-grave-booking', 'priority' => 20],
                    ['rule_type' => 'contains', 'rule_value' => 'temporary-grave-booking', 'priority' => 20],
                ]
            ],
            [
                'name' => 'Obituary Management',
                'slug' => 'obituary-management',
                'description' => 'Obituary page and condolence management',
                'app' => 'Graveyard',
                'color' => '#1F2937',
                'sort_order' => 23,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'obituary-page', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'obituary-condolence', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'obituary-payment', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'obituary-plans', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Graveyard Configuration',
                'slug' => 'graveyard-configuration',
                'description' => 'Graveyard setup and configuration permissions',
                'app' => 'Graveyard',
                'color' => '#0891B2',
                'sort_order' => 24,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'grave-category', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'service-type', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'valid-member', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'permanent-valid-member', 'priority' => 15],
                ]
            ],
            // [
            //     'name' => 'Graveyard Operations',
            //     'slug' => 'graveyard-operations',
            //     'description' => 'Transfer operations and special graveyard processes',
            //     'app' => 'Graveyard',
            //     'color' => '#7C2D12',
            //     'sort_order' => 23,
            //     'rules' => [
            //         ['rule_type' => 'starts_with', 'rule_value' => 'transfer-', 'priority' => 15],
            //         ['rule_type' => 'contains', 'rule_value' => 'graveyard-expiration', 'priority' => 15],
            //         ['rule_type' => 'contains', 'rule_value' => 'bulk-import-graveyard', 'priority' => 15],
            //         ['rule_type' => 'contains', 'rule_value' => 'bulk-export-graveyard', 'priority' => 15],
            //     ]
            // ],
            [
                'name' => 'Graveyard Payments',
                'slug' => 'graveyard-payments',
                'description' => 'Graveyard payment processing permissions',
                'app' => 'Graveyard',
                'color' => '#BE185D',
                'sort_order' => 25,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'graveyard-payment', 'priority' => 15],
                    // ['rule_type' => 'starts_with', 'rule_value' => 'process-', 'priority' => 10],
                    // ['rule_type' => 'starts_with', 'rule_value' => 'refund-', 'priority' => 10],
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
