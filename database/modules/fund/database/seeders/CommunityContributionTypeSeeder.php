<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunityContributionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contributionTypes = [
            [
                'name' => 'Mass Collection',
                'description' => 'Sunday and daily mass collection from offering boxes',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Event Collection',
                'description' => 'Collections during special church events and celebrations',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Charity Box',
                'description' => 'Donations from permanent charity collection boxes',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Special Appeals',
                'description' => 'Collections for special causes and emergency appeals',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Festival Collection',
                'description' => 'Collections during Christmas, Easter, and other religious festivals',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Building Fund',
                'description' => 'Donations for church construction and maintenance',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Poor Box',
                'description' => 'Collections specifically for helping the poor and needy',
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($contributionTypes as $type) {
            DB::table('community_contribution_types')->insertOrIgnore($type);
        }
    }
}
