<?php

namespace Database\Seeders;

use App\Models\SCCHead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SCCHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sccHeads = [
                ['member_id' => null, 'community_id' => 1],
                ['member_id' => 484, 'community_id' => 2],
                ['member_id' => 805, 'community_id' => 3],
                ['member_id' => null, 'community_id' => 4],
                ['member_id' => 1267, 'community_id' => 5],
                ['member_id' => 1614, 'community_id' => 6],
                ['member_id' => 4015, 'community_id' => 7],
                ['member_id' => 4331, 'community_id' => 8],
                ['member_id' => 4459, 'community_id' => 9],
                ['member_id' => 1726, 'community_id' => 10],
                ['member_id' => 1804, 'community_id' => 11],
                ['member_id' => 1915, 'community_id' => 12],
                ['member_id' => 2507, 'community_id' => 13],
                ['member_id' => 2640, 'community_id' => 14],
                ['member_id' => 2862, 'community_id' => 15],
                ['member_id' => 3045, 'community_id' => 16],
                ['member_id' => 3248, 'community_id' => 17],
                ['member_id' => null, 'community_id' => 18],
                ['member_id' => null, 'community_id' => 19],
                ['member_id' => null, 'community_id' => 20],
                ['member_id' => 4862, 'community_id' => 21],
                ['member_id' => 5471, 'community_id' => 22],
                ['member_id' => 5552, 'community_id' => 23],
        ];

        SCCHead::insert($sccHeads);
    }
}
