<?php

namespace Database\Seeders;

use App\Models\Zone;
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
        /**
         * ['name' => 'Matthew'],
            ['name' => 'Mark'],
            ['name' => 'Luke'],
            ['name' => 'John'],
         */
        Zone::insert([
            [
                'name' => 'Matthew',
                'description' => 'Description for Matthew',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mark',
                'description' => 'Description for Mark',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Luke',
                'description' => 'Description for Luke',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'John',
                'description' => 'Description for John',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
