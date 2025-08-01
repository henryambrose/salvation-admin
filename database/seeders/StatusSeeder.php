<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Resident'], 
            ['name' => 'Redevelopment Unsettled'], 
            ['name' => 'Non-Resident'], 
            ['name' => 'Deceased']
            ];

        Status::insert($statuses);
    }
}
