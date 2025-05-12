<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FamilyIncomeRangeSeeder extends Seeder
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
            ['name' => '>100000', 'starting_range' => 100001, 'ending_range' => PHP_INT_MAX],
        ];
        \App\Models\FamilyIncomeRange::insert($incomeRanges);
    }
}
