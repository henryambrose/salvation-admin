<?php

namespace Database\Seeders;

use App\Models\Town;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $towns =[
            ['name' => 'Other', 'pincode' => '000000', 'city_id' => '2' ],
            ['name' => 'Dadar', 'pincode' => '400028', 'city_id' => '2' ],
            ['name' => 'Prabhadevi', 'pincode' => '400025', 'city_id' => '2' ],
            ['name' => 'Lower Parel', 'pincode' => '400013', 'city_id' => '3' ]
        ];

        Town::insert($towns);
    }
}
