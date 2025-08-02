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

        $communities = [
          ['name' => '01 - St. Augustine', 'id' => 1, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '02 - St. Anthony', 'id' => 2, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '03 - St. Faustina', 'id' => 3, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '04 - St. Theresa of the Child Jesus', 'id' => 4, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '05 - St. Peter', 'id' => 5, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '06 - St. Christopher', 'id' => 6, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '07 - St. Andrew', 'id' => 7, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '08 - St. Francis Xavier', 'id' => 8, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '09 - St. Blaise', 'id' => 9, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '10 - St. Lawrence', 'id' => 10, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '11 - St. Vincent de Paul', 'id' => 11, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '12 - St. Maria Goretti', 'id' => 12, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '13 - St. Anne', 'id' => 13, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '14 - St. Martin', 'id' => 14, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '15 - St. Jude', 'id' => 15, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '16 - St. Gonsalo Garcia', 'id' => 16, 'zone_id' => 2, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '17 - St. Thomas', 'id' => 17, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '18 - St. Paul', 'id' => 18, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '19 - St. Sebastian', 'id' => 19, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '20 - St. John the Baptist', 'id' => 20, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '21 - St. Domnic Savio', 'id' => 21, 'zone_id' => 1, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '22 - St. Michael', 'id' => 22, 'zone_id' => 4, 'created_at' => $now, 'updated_at' => $now],
          ['name' => '23 - Holy Family', 'id' => 23, 'zone_id' => 3, 'created_at' => $now, 'updated_at' => $now]
        ];
        Community::insert($communities);
    }
}
