<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call the permission seeders
        $this->call([
            FundPermissionsSeeder::class,
            MembersPermissionsSeeder::class,
            CorePermissionsSeeder::class,
            GraveyardPermissionsSeeder::class,
            PermissionCategorySeeder::class,
            PermissionGroupSeeder::class,
            PPCHeadPermissionSeeder::class,
            SCCHeadPermissionSeeder::class,
            
        ]);

        // Call the Members module seeders
        // $this->call([
        //     \Modules\Members\Database\Seeders\DatabaseSeeder::class,
        // ]);

        // // Call the Fund module seeders
        // $this->call([
        //     \Modules\Fund\Database\Seeders\DatabaseSeeder::class,
        // ]);

        // // Call the Graveyard module seeders
        // $this->call([
        //     \Modules\Graveyard\Database\Seeders\DatabaseSeeder::class,
        // ]);
    }
}
