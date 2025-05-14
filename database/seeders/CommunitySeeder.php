<?php

namespace Database\Seeders;

use App\Models\Community;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        /**
         * Communities
            Name	commNo	zoneId
            St. Augustine	1	1
            St. Anthony	2	1
            St. Faustina	3	1
            St. Theresa of the Child Jesus	4	3
            St. Peter	5	2
            St. Christopher	6	3
            St. Andrew	7	2
            St. Francis Xavier	8	2
            St. Blaise	9	2
            St. Lawrence	10	2
            St. Vincent de Paul	11	4
            St. Maria Goretti	12	4
            St. Anne	13	2
            St. Martin	14	1
            St. Jude	15	4
            St. Gonsalo Garcia	16	2
            St. Thomas	17	3
            St. Paul	18	1
            St. Sebastian	19	4
            St. John the Baptist	20	3
            St. Domnic Savio	21	1
            St. Michael	22	4
            Holy Family	23	3

         */
        $communities = [
            ['name' => 'St. Augustine', 'id' => 1, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Anthony', 'id' => 2, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Faustina', 'id' => 3, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Theresa of the Child Jesus', 'id' => 4, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Peter', 'id' => 5, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Christopher', 'id' => 6, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Andrew', 'id' => 7, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Francis Xavier', 'id' => 8, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Blaise', 'id' => 9, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Lawrence', 'id' => 10, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Vincent de Paul', 'id' => 11, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Maria Goretti', 'id' => 12, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Anne', 'id' => 13, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Martin', 'id' => 14, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Jude', 'id' => 15, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Gonsalo Garcia', 'id' => 16, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Thomas', 'id' => 17, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Paul', 'id' => 18, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Sebastian', 'id' => 19, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. John the Baptist', 'id' => 20, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Domnic Savio', 'id' => 21, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'St. Michael', 'id' => 22, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Holy Family', 'id' => 23, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ];
        Community::insert($communities);
    }
}
