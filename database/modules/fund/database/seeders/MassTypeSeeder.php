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
                'default_time' => '06:30:00',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => '2nd Mass',
                'description' => 'morning mass',
                'default_time' => '07:30:00',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Evening Mass',
                'description' => 'Evening mass',
                'default_time' => '19:00:00',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Sunday 1st Mass',
                'description' => 'Sunday morning mass',
                'default_time' => '6:30:00',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Sunday 2nd Mass',
                'description' => 'Sunday morning mass',
                'default_time' => '8:00:00',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Sunday 3rd Mass',
                'description' => 'Sunday morning mass',
                'default_time' => '9:30:00',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Sunday 1st Evening Mass',
                'description' => 'Sunday Evening mass',
                'default_time' => '17:30:00',
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Sunday 2nd Evening Mass',
                'description' => 'Sunday Evening mass',
                'default_time' => '19:00:00',
                'sort_order' => 8,
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
