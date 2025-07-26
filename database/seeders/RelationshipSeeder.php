<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationships = [
            ['name' => 'Head'],
            ['name' => 'Wife'],
            ['name' => 'Husband'],
            ['name' => 'Son'],
            ['name' => 'Daughter'],
            ['name' => 'Grand Daughter'],
            ['name' => 'Daughter-in-Law'],
            ['name' => 'Sister-in-Law'],
            ['name' => 'Grand Son'],
            ['name' => 'Mother'],
            ['name' => 'Mother-in-law'],
            ['name' => 'Sister'],
            ['name' => 'Brother'],
            ['name' => 'Niece'],
            ['name' => 'Nephew'],
            ['name' => 'Grand Daughter-in-Law'],
            ['name' => 'Housemaid'],
            ['name' => 'Son-in-Law'],
            ['name' => 'Father-in-Law'],
            ['name' => 'Cousin'],
            ['name' => 'Brother-in-Law'],
            ['name' => 'Father'],
            ['name' => 'Aunty'],
            ['name' => 'Uncle'],
            ['name' => 'Daughter-in-Law\'s Mother'],
            ['name' => 'Daughter-in-Law\'s Sister'],
            ['name' => 'Grandmother\'s Sister'],
            ['name' => 'Grand Mother'],
            ['name' => 'Grand Mother Son'],
            ['name' => 'Niece\'s Son'],
            ['name' => 'Niece-in-Law'],
            ['name' => 'Grand Niece'],
            ['name' => 'Grand Nephew'],
            ['name' => 'Great Grand Son'],
            ['name' => 'Sister-in-Law\'s Brother'],
            ['name' => 'Maid'],
            ['name' => 'Step Mom'],
            ['name' => 'Spouse'],
            ['name' => 'Grand Sister-in-Law'],
            ['name' => 'Guest'],
            ];

        Relationship::insert($relationships);
    }
}
