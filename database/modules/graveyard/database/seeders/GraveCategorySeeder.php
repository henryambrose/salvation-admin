<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GraveCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            [
                'name' => 'Normal Grave',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tall Grave',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('grave_categories')->insert($categories);
    }
}