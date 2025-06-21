<?php

namespace Database\Seeders;

use App\Models\PPCHead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PPCHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ppc= [
            ['member_id' => 104, 'community_id' => 1],
            ['member_id' => 685, 'community_id' => 2],
            ['member_id' => 803, 'community_id' => 3],
            ['member_id' => null, 'community_id' => 4],
            ['member_id' => null, 'community_id' => 5],
            ['member_id' => 1388, 'community_id' => 6],
            ['member_id' => 4104, 'community_id' => 7],
            ['member_id' => 4201, 'community_id' => 8],
            ['member_id' => 4396, 'community_id' => 9],
            ['member_id' => 1719, 'community_id' => 10],
            ['member_id' => 1858, 'community_id' => 11],
            ['member_id' => 2239, 'community_id' => 12],
            ['member_id' => 2373, 'community_id' => 13],
            ['member_id' => null, 'community_id' => 14],
            ['member_id' => 2907, 'community_id' => 15],
            ['member_id' => 2956, 'community_id' => 16],
            ['member_id' => 3171, 'community_id' => 17],
            ['member_id' => 3390, 'community_id' => 18],
            ['member_id' => 3772, 'community_id' => 19],
            ['member_id' => 3935, 'community_id' => 20],
            ['member_id' => 4665, 'community_id' => 21],
            ['member_id' => null, 'community_id' => 22],
            ['member_id' => 5578, 'community_id' => 23],
                  
        ];
        PPCHead::insert($ppc);
    }
}
