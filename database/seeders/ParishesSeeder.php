<?php

namespace Database\Seeders;

use App\Models\Parish;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParishesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parishes = [
            ['deanery' => 'Deanery 1', 'name' => 'St. Mary\'s Parish', 'code' => 'SM001', 'address' => '123 Main St, City, Country'],
            ['deanery' => 'Deanery 2', 'name' => 'St. Joseph\'s Parish', 'code' => 'SJ002', 'address' => '456 Elm St, City, Country'],
            ['deanery' => 'Deanery 3', 'name' => 'St. Peter\'s Parish', 'code' => 'SP003', 'address' => '789 Oak St, City, Country'],
            ['deanery' => 'Deanery 4', 'name' => 'St. Paul\'s Parish', 'code' => 'SP004', 'address' => '321 Pine St, City, Country'],
            ['deanery' => 'Deanery 5', 'name' => 'St. Andrew\'s Parish', 'code' => 'SA005', 'address' => '654 Maple St, City, Country'],
        ];

        Parish::insert($parishes);
        // Optionally, you can use the factory to create more parishes
    }
}
