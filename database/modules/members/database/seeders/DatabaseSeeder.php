<?php

namespace Modules\Members\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CreateRoleNPermissionSeeder::class,
            ModuleSeeder::class,
            AgeGroupSeeder::class,
            BloodGroupSeeder::class,
            ZoneSeeder::class,
            CommunitySeeder::class,
            ClusterSeeder::class,
            CellsAndAssociationMemberSeeder::class,
            CellsAndAssociationSeeder::class,
            CommunityClusterSeeder::class,
            IncomeRangeSeeder::class,
            ParishSeeder::class,
            DesignationSeeder::class,
            GenderSeeder::class,
            StatusSeeder::class,
            RelationshipSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            TownSeeder::class,
            MemberSeeder::class,
            PPCHeadSeeder::class,
            SCCHeadSeeder::class,
            PermissionGroupSeeder::class,
            PPCHeadPermissionSeeder::class,
            SCCHeadPermissionSeeder::class,
            CertificateTypeSeeder::class,
            CertificateTemplateSeeder::class,

        ]);
    }
}
