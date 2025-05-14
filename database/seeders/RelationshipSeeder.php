<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationships = [
            ['id' => 1, 'name' => 'Head'],
            ['id' => 2, 'name' => 'Father'],
            ['id' => 3, 'name' => 'Mother'],
            ['id' => 4, 'name' => 'Sister'],
            ['id' => 5, 'name' => 'Brother'],
            ['id' => 6, 'name' => 'Husband'],
            ['id' => 7, 'name' => 'Wife'],
        ];

        \App\Models\Relationship::insert($relationships);
    }
}
