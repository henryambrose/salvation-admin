<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call the PermissionCategorySeeder first
        $this->call([
            PermissionCategorySeeder::class,
        ]);

        // Call the permission seeders
        $this->call([
            FundPermissionsSeeder::class,
            MembersPermissionsSeeder::class,
            CorePermissionsSeeder::class,
        ]);

        // Call the Members module seeders
        $this->call([
            \Modules\Members\Database\Seeders\DatabaseSeeder::class,
        ]);

        // Call the Fund module seeders
        $this->call([
            \Modules\Fund\Database\Seeders\DatabaseSeeder::class,
        ]);
    }
}
