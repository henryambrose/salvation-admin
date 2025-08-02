<?php

namespace Database\Seeders;

use App\Models\PPCHead;
use App\Models\Member;
use App\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PPCHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ppcHeads =  [
            ['member_id' => '939', 'community_id' => '1', ],
            ['member_id' => '685', 'community_id' => '2', ],
            ['member_id' => '685', 'community_id' => '3', ],
            ['member_id' => '5226', 'community_id' => '4', ],
            ['member_id' => '1981', 'community_id' => '5', ],
            ['member_id' => '4172', 'community_id' => '6', ],
            ['member_id' => '181', 'community_id' => '7', ],
            ['member_id' => '5292', 'community_id' => '8', ],
            ['member_id' => '4733', 'community_id' => '9', ],
            ['member_id' => '687', 'community_id' => '10', ],
            ['member_id' => '5156', 'community_id' => '11', ],
            ['member_id' => '685', 'community_id' => '12', ],
            ['member_id' => '5317', 'community_id' => '13', ],
            ['member_id' => '2678', 'community_id' => '14', ],
            ['member_id' => '4561', 'community_id' => '15', ],
            ['member_id' => '5568', 'community_id' => '16', ],
            ['member_id' => '3295', 'community_id' => '17', ],
            ['member_id' => '1146', 'community_id' => '18', ],
            ['member_id' => '685', 'community_id' => '19', ],
            ['member_id' => '685', 'community_id' => '20', ],
            ['member_id' => '685', 'community_id' => '21', ],
            ['member_id' => '2167', 'community_id' => '22', ],
            ['member_id' => '3791', 'community_id' => '23', ]
        ];
        PPCHead::insert($ppcHeads);


    }
}
