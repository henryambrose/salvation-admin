<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CellsAndAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cellsAndAssociations = [
            ['name' => 'Legion of Mary', 'description' => 'A Catholic lay organization that serves the Church and the community.'],
            ['name' => 'Womens Cell', 'description' => 'A group focused on women\'s issues and empowerment.'],
            ['name' => 'Saint Vincent dePaul', 'description' => 'A charitable organization that provides assistance to those in need.'],
            ['name' => 'English Charismatic Prayer Group', 'description' => 'A prayer group focused on charismatic worship in English.'],
            ['name' => 'Konkani Charismatic Prayer Group', 'description' => 'A prayer group focused on charismatic worship in Konkani.'],
            ['name' => 'Green Cell', 'description' => 'An environmental awareness and action group.'],
            ['name' => 'Media Cell', 'description' => 'A group focused on media and communication within the church.'],
        ];
        \App\Models\CellsAndAssociation::insert($cellsAndAssociations);
    }
}
