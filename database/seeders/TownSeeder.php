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
            ['name' => 'Dadar', 'state_id' => 21, 'pincode' => '400028'],
            ['name' => 'Prabhadevi', 'state_id' => '21', 'pincode' => '400025'],
            ['name' => 'Lower Parel', 'state_id' => '21', 'pincode' => '400013']
        ];

        Town::insert($towns);
    }
}
