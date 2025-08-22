<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call the Members module seeders
        $this->call([
            \Modules\Members\Database\Seeders\DatabaseSeeder::class,
        ]);
    }
}
