<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassIntentionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intentionTypes = [
            [
                'id' => 2,
                'name' => 'Anniversary',
                'description' => 'Mass intention for anniversary celebrations',
                'default_amount' => 100.00,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Thanksgiving',
                'description' => 'Mass intention for thanksgiving prayers',
                'default_amount' => 100.00,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Months Mind',
                'description' => 'Mass intention for monthly remembrance',
                'default_amount' => 100.00,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'For the Intention of',
                'description' => 'General mass intention for specific purposes',
                'default_amount' => 100.00,
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Feast Mass in',
                'description' => 'Mass intention for feast day celebrations',
                'default_amount' => 100.00,
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Nuptial Mass of',
                'description' => 'Mass intention for wedding ceremonies',
                'default_amount' => 200.00,
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Silver Wedding',
                'description' => 'Mass intention for 25th wedding anniversary',
                'default_amount' => 150.00,
                'sort_order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Golden Wedding',
                'description' => 'Mass intention for 50th wedding anniversary',
                'default_amount' => 200.00,
                'sort_order' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Soul of',
                'description' => 'Mass intention for departed souls',
                'default_amount' => 100.00,
                'sort_order' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'Special Intention',
                'description' => 'Mass intention for special prayer requests',
                'default_amount' => 100.00,
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 13,
                'name' => 'For Good Health',
                'description' => 'Mass intention for health and wellness',
                'default_amount' => 100.00,
                'sort_order' => 11,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 15,
                'name' => 'Funeral Mass',
                'description' => 'Mass intention for funeral services',
                'default_amount' => 150.00,
                'sort_order' => 12,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($intentionTypes as $intentionType) {
            DB::table('mass_intention_types')->insertOrIgnore($intentionType);
        }
    }
}
