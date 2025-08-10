<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Young 0-15
         * Youth 16-25
         * Adult 26-59
         * Senior 60+
         */
        $ageGroups = [
            ['name' => 'Young 0-15', 'description' => 'Ages 0-15', 'min_age' => 0, 'max_age' => 15],
            ['name' => 'Youth 16-25', 'description' => 'Ages 16-25', 'min_age' => 16, 'max_age' => 25],
            ['name' => 'Adult 26-59', 'description' => 'Ages 26-59', 'min_age' => 26, 'max_age' => 59],
            ['name' => 'Senior 60+', 'description' => 'Ages 60 and above', 'min_age' => 60, 'max_age' => 120], // Assuming max age is capped at 120
        ];
        AgeGroup::insert($ageGroups);
    }
}
