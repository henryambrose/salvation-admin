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
