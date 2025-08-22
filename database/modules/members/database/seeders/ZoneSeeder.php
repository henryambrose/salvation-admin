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
            'name' => 'Matthew',
            'description' => 'Description for Matthew',
            ],
            [
            'name' => 'Mark',
            'description' => 'Description for Mark',
            ],
            [
            'name' => 'Luke',
            'description' => 'Description for Luke',
            ],
            [
            'name' => 'John',
            'description' => 'Description for John',
            ],
        ];
        
        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
