<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\BloodGroup;
use Illuminate\Database\Seeder;

class BloodGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodGroups = array_map(function ($group) {
            return ['name' => $group];
        }, ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+']);

        foreach ($bloodGroups as $bloodGroup) {
            BloodGroup::updateOrCreate(
                ['name' => $bloodGroup['name']],
                $bloodGroup
            );
        }
    }
}
