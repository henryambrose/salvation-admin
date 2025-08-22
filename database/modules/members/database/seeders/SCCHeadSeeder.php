<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\SCCHead;
use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SCCHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sccHeads =  [
           
                ['member_id' => '426', 'community_id' => '1', ],
                ['member_id' => '685', 'community_id' => '2', ],
                ['member_id' => '3725', 'community_id' => '3', ],
                ['member_id' => '685', 'community_id' => '4', ],
                ['member_id' => '3712', 'community_id' => '5', ],
                ['member_id' => '4859', 'community_id' => '6', ],
                ['member_id' => '2187', 'community_id' => '7', ],
                ['member_id' => '4711', 'community_id' => '8', ],
                ['member_id' => '5294', 'community_id' => '9', ],
                ['member_id' => '4630', 'community_id' => '10', ],
                ['member_id' => '501', 'community_id' => '11', ],
                ['member_id' => '49', 'community_id' => '12', ],
                ['member_id' => '685', 'community_id' => '13', ],
                ['member_id' => '685', 'community_id' => '14', ],
                ['member_id' => '336', 'community_id' => '15', ],
                ['member_id' => '1416', 'community_id' => '16', ],
                ['member_id' => '3695', 'community_id' => '17', ],
                ['member_id' => '685', 'community_id' => '18', ],
                ['member_id' => '632', 'community_id' => '19', ],
                ['member_id' => '2882', 'community_id' => '20', ],
                ['member_id' => '4455', 'community_id' => '21', ],
                ['member_id' => '3902', 'community_id' => '22', ],
                ['member_id' => '2331', 'community_id' => '23', ]
               
        ];
        SCCHead::insert($sccHeads); 
    }
}
