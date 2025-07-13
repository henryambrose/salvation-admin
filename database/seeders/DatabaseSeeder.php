<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
         $this->call([
            UserSeeder::class,
            // CreateRoleNPermissionSeeder::class,
            AgeGroupSeeder::class,
            BloodGroupSeeder::class,
            ZoneSeeder::class,
            CommunitySeeder::class,
            CellsAndAssociationMemberSeeder::class,
            CellsAndAssociationSeeder::class,
            CommunityClusterHeadSeeder::class,
            CommunityClusterSeeder::class,
            FamilyIncomeRangeSeeder::class,
            ParishSeeder::class,
            DesignationSeeder::class,
            GenderSeeder::class,
            StatusSeeder::class,
            RelationshipSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            TownSeeder::class,
            MemberSeeder::class,
            PPCHeadSeeder::class,
            SCCHeadSeeder::class,

         ]);
    }
}
