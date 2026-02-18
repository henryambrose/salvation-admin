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
                'name' => 'Certificate Management',
                'slug' => 'certificate-management',
                'description' => 'Certificate generation and management permissions',
                'app' => 'Members',
                'color' => '#EC4899',
                'sort_order' => 26,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'certificate', 'priority' => 10],
                    // ['rule_type' => 'contains', 'rule_value' => 'view-certificate-history', 'priority' => 15],
                    // ['rule_type' => 'contains', 'rule_value' => 'manage-certificate-templates', 'priority' => 15],
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
                'description' => 'User, role, permission, and system management permissions',
                'app' => 'Members',
                'color' => '#EF4444',
                'sort_order' => 6,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'user', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'role', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'permission', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Dashboard & Settings',
                'slug' => 'dashboard',
                'description' => 'Dashboard, overview and application settings permissions',
                'app' => 'Members',
                'color' => '#6366F1',
                'sort_order' => 7,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'dashboard', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'setting', 'priority' => 10],
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
                'sort_order' => 16,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'payment-method', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Mass Schedules',
                'slug' => 'mass-schedules',
                'description' => 'Mass schedule management permissions',
                'app' => 'Fund',
                'color' => '#A78BFA',
                'sort_order' => 18,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'mass-schedule', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Graveyard Management',
                'slug' => 'graveyard-management',
                'description' => 'Core graveyard management permissions',
                'app' => 'Graveyard',
                'color' => '#374151',
                'sort_order' => 19,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'graveyard-dashboard', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'view-graveyard-reports', 'priority' => 15],
                ]
            ],
            [
                'name' => 'Permanent Graves',
                'slug' => 'permanent-graves',
                'description' => 'Permanent grave management permissions',
                'app' => 'Graveyard',
                'color' => '#059669',
                'sort_order' => 20,
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
                'sort_order' => 21,
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
                'sort_order' => 22,
                'rules' => [
                    ['rule_type' => 'starts_with', 'rule_value' => 'niche', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'remains-transfer', 'priority' => 20],
                ]
            ],
            [
                'name' => 'Grave Bookings',
                'slug' => 'grave-bookings',
                'description' => 'Grave booking management permissions',
                'app' => 'Graveyard',
                'color' => '#DC2626',
                'sort_order' => 23,
                'rules' => [
                    // ['rule_type' => 'contains', 'rule_value' => 'grave-booking', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'permanent-grave-booking', 'priority' => 20],
                    ['rule_type' => 'contains', 'rule_value' => 'temporary-grave-booking', 'priority' => 20],
                ]
            ],
            [
                'name' => 'Graveyard Configuration',
                'slug' => 'graveyard-configuration',
                'description' => 'Graveyard setup and configuration permissions',
                'app' => 'Graveyard',
                'color' => '#0891B2',
                'sort_order' => 25,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'grave-category', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'service-type', 'priority' => 15],
                    ['rule_type' => 'contains', 'rule_value' => 'valid-member', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Graveyard Payments',
                'slug' => 'graveyard-payments',
                'description' => 'Graveyard payment processing permissions',
                'app' => 'Graveyard',
                'color' => '#BE185D',
                'sort_order' => 26,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'graveyard-payment', 'priority' => 15],
                    // ['rule_type' => 'starts_with', 'rule_value' => 'process-', 'priority' => 10],
                    // ['rule_type' => 'starts_with', 'rule_value' => 'refund-', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Annual Maintenance Fees',
                'slug' => 'annual-maintenance-fees',
                'description' => 'Annual maintenance fee management permissions',
                'app' => 'Graveyard',
                'color' => '#16A34A',
                'sort_order' => 27,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'annual-maintenance-fee', 'priority' => 15],
                ]
            ],
            [
                'name' => 'System Audit & Logs',
                'slug' => 'system-audit',
                'description' => 'Audit logging and system administration permissions',
                'app' => 'Core',
                'color' => '#8B5CF6',
                'sort_order' => 29,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'audit', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'log', 'priority' => 10],
                ]
            ],
            [
                'name' => 'System Configuration',
                'slug' => 'system-configuration',
                'description' => 'Configuration, cache, queue, and system settings permissions',
                'app' => 'Core',
                'color' => '#06B6D4',
                'sort_order' => 31,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'config', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'cache', 'priority' => 10],
                    ['rule_type' => 'contains', 'rule_value' => 'queue', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Reports & Analytics',
                'slug' => 'reports-analytics',
                'description' => 'Report generation and analytics permissions',
                'app' => 'Core',
                'color' => '#EC4899',
                'sort_order' => 32,
                'rules' => [
                    ['rule_type' => 'contains', 'rule_value' => 'report', 'priority' => 10],
                ]
            ],
            [
                'name' => 'Graveyard Special Operations',
                'slug' => 'graveyard-special-operations',
                'description' => 'Special graveyard operations like transfers',
                'app' => 'Graveyard',
                'color' => '#7C2D12',
                'sort_order' => 28,
                'rules' => [
                    ['rule_type' => 'starts_with', 'rule_value' => 'transfer-', 'priority' => 15],
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
