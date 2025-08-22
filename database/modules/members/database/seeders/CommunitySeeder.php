<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Community;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        $communities = [
            ['name' => '01 - St. Augustine', 'zone_id' => 1],
            ['name' => '02 - St. Anthony', 'zone_id' => 1],
            ['name' => '03 - St. Faustina', 'zone_id' => 1],
            ['name' => '04 - St. Theresa of the Child Jesus', 'zone_id' => 3],
            ['name' => '05 - St. Peter', 'zone_id' => 2],
            ['name' => '06 - St. Christopher', 'zone_id' => 3],
            ['name' => '07 - St. Andrew', 'zone_id' => 2],
            ['name' => '08 - St. Francis Xavier', 'zone_id' => 2],
            ['name' => '09 - St. Blaise', 'zone_id' => 2],
            ['name' => '10 - St. Lawrence', 'zone_id' => 2],
            ['name' => '11 - St. Vincent de Paul', 'zone_id' => 4],
            ['name' => '12 - St. Maria Goretti', 'zone_id' => 4],
            ['name' => '13 - St. Anne', 'zone_id' => 2],
            ['name' => '14 - St. Martin', 'zone_id' => 1],
            ['name' => '15 - St. Jude', 'zone_id' => 4],
            ['name' => '16 - St. Gonsalo Garcia', 'zone_id' => 2],
            ['name' => '17 - St. Thomas', 'zone_id' => 3],
            ['name' => '18 - St. Paul', 'zone_id' => 1],
            ['name' => '19 - St. Sebastian', 'zone_id' => 4],
            ['name' => '20 - St. John the Baptist', 'zone_id' => 3],
            ['name' => '21 - St. Domnic Savio', 'zone_id' => 1],
            ['name' => '22 - St. Michael', 'zone_id' => 4],
            ['name' => '23 - Holy Family', 'zone_id' => 3],
        ];
        
        foreach ($communities as $community) {
            Community::updateOrCreate(
                ['name' => $community['name']],
                $community
            );
        }
    }
}
