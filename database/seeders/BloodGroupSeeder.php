<?php

namespace Database\Seeders;

use App\Models\BloodGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        BloodGroup::insert($bloodGroups);
    }
}
