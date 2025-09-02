<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Call specific seeders for the Graveyard module (guarded if classes not autoloadable)
        $seeders = [];
        if (class_exists(ServiceTypesSeeder::class)) {
            $seeders[] = ServiceTypesSeeder::class;
        }
        // Add more seeders here as needed
        // if (class_exists(CemeterySectionsSeeder::class)) { $seeders[] = CemeterySectionsSeeder::class; }
        // if (class_exists(SampleGravesSeeder::class)) { $seeders[] = SampleGravesSeeder::class; }

        if (!empty($seeders)) {
            $this->call($seeders);
        }

    }
}
