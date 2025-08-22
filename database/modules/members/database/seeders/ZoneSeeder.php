<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Zone;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $zones = [
            [
            'id' => 1,
            'name' => 'Matthew',
            'description' => 'Description for Matthew',
            'created_at' => $now,
            'updated_at' => $now,
            ],
            [
            'id' => 2,
            'name' => 'Mark',
            'description' => 'Description for Mark',
            'created_at' => $now,
            'updated_at' => $now,
            ],
            [
            'id' => 3,
            'name' => 'Luke',
            'description' => 'Description for Luke',
            'created_at' => $now,
            'updated_at' => $now,
            ],
            [
            'id' => 4,
            'name' => 'John',
            'description' => 'Description for John',
            'created_at' => $now,
            'updated_at' => $now,
            ],
        ];
        Zone::insert($zones);
    }
}
