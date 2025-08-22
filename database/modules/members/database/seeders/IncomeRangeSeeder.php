<?php

namespace Modules\Members\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Members\Models\IncomeRange;

class IncomeRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomeRanges = [
            ['name' => '<10000', 'starting_range' => 0, 'ending_range' => 10000],
            ['name' => '10001-40000', 'starting_range' => 10001, 'ending_range' => 40000],
            ['name' => '40001-70000', 'starting_range' => 40001, 'ending_range' => 70000],
            ['name' => '70001-100000', 'starting_range' => 70001, 'ending_range' => 100000],
            ['name' => '>100000', 'starting_range' => 100001, 'ending_range' => 999999999],
        ];
        // Only insert name field, as only name is fillable
        foreach ($incomeRanges as $incomeRange) {
            IncomeRange::updateOrCreate(
                ['name' => $incomeRange['name']],
                [
                    'name' => $incomeRange['name'], 
                    'starting_range' => $incomeRange['starting_range'], 
                    'ending_range' => $incomeRange['ending_range']
                ]
            );
        }
    }
}
