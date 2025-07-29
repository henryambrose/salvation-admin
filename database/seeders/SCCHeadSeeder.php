<?php

namespace Database\Seeders;

use App\Models\SCCHead;
use App\Models\Member;
use App\Models\Community;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SCCHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all communities and members
        $communities = Community::all();
        $members = Member::all();
        
        // Clear existing SCC heads
        SCCHead::truncate();
        
        // Create SCC heads for each community
        foreach ($communities as $community) {
            // Randomly assign a member as SCC head (80% chance)
            $memberId = null;
            if ($members->count() > 0 && rand(1, 100) <= 80) {
                $memberId = $members->random()->id;
            }
            
            SCCHead::create([
                'community_id' => $community->id,
                'member_id' => $memberId,
            ]);
        }
        
        $this->command->info('SCC Heads created successfully!');
    }
}
