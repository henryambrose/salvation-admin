<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObituaryPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obituaryPlans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Basic obituary page with essential features for 30 days',
                'duration_in_days' => 30,
                'cost' => 500.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard Plan',
                'description' => 'Standard obituary page with enhanced features for 90 days',
                'duration_in_days' => 90,
                'cost' => 1200.00,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'Premium obituary page with all features for 180 days',
                'duration_in_days' => 180,
                'cost' => 2000.00,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Annual Plan',
                'description' => 'Full-featured obituary page for one year',
                'duration_in_days' => 365,
                'cost' => 3500.00,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Lifetime Plan',
                'description' => 'Permanent obituary page with lifetime access',
                'duration_in_days' => null,
                'cost' => 10000.00,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($obituaryPlans as $plan) {
            // Use updateOrInsert to avoid duplicate entries
            DB::table('obituary_plans')->updateOrInsert(
                ['name' => $plan['name']], // Search criteria
                array_merge($plan, [       // Data to insert/update
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}