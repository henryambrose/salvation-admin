<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the Fund module's database.
     */
    public function run(): void
    {
        $this->call([
            FundSeeder::class,
            // SampleContributionsSeeder::class,
            MassIntentionTypeSeeder::class,
            MassTypeSeeder::class,
            CommunityContributionTypeSeeder::class,
        ]);
    }
}
