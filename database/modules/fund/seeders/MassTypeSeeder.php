<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $massTypes = [
            [
                'id' => 1,
                'name' => '1st Mass',
                'description' => 'Early morning mass',
                'default_time' => '08:00:00',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => '2nd Mass',
                'description' => 'Morning mass',
                'default_time' => '09:30:00',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => '3rd Mass',
                'description' => 'Mid-morning mass',
                'default_time' => '10:30:00',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Evening Mass',
                'description' => 'Evening mass',
                'default_time' => '19:00:00',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($massTypes as $massType) {
            DB::table('mass_types')->insertOrIgnore($massType);
        }
    }
}
