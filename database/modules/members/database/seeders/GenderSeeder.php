<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Gender;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            ['name' => 'Male'],
            ['name' => 'Female'],
            ['name' => 'Other'],
        ];

        foreach ($genders as $gender) {
            Gender::updateOrCreate(
                ['name' => $gender['name']],
                $gender
            );
        }
    }
}
