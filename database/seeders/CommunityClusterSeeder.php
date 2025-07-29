<?php

namespace Database\Seeders;

use App\Models\CommunityCluster;
use App\Models\Member;
use Illuminate\Database\Seeder;

class CommunityClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        CommunityCluster::query()->delete();

        $clusters = [
            ['community_id' => '1', 'cluster_id' => 1],
            ['community_id' => '1', 'cluster_id' => 2],
            ['community_id' => '1', 'cluster_id' => 3],
            ['community_id' => '1', 'cluster_id' => 4],
            ['community_id' => '1', 'cluster_id' => 5],
            ['community_id' => '2', 'cluster_id' => 1],
            ['community_id' => '2', 'cluster_id' => 2],
            ['community_id' => '2', 'cluster_id' => 3],
            ['community_id' => '2', 'cluster_id' => 4],
            ['community_id' => '2', 'cluster_id' => 5],
            ['community_id' => '3', 'cluster_id' => 1],
            ['community_id' => '3', 'cluster_id' => 2],
            ['community_id' => '3', 'cluster_id' => 3],
            ['community_id' => '3', 'cluster_id' => 4],
            ['community_id' => '3', 'cluster_id' => 5],
            ['community_id' => '4', 'cluster_id' => 1],
            ['community_id' => '4', 'cluster_id' => 2],
            ['community_id' => '4', 'cluster_id' => 3],
            ['community_id' => '4', 'cluster_id' => 4],
            ['community_id' => '4', 'cluster_id' => 5],
            ['community_id' => '5', 'cluster_id' => 1],
            ['community_id' => '5', 'cluster_id' => 2],
            ['community_id' => '5', 'cluster_id' => 3],
            ['community_id' => '5', 'cluster_id' => 4],
            ['community_id' => '5', 'cluster_id' => 5],
            ['community_id' => '6', 'cluster_id' => 1],
            ['community_id' => '6', 'cluster_id' => 2],
            ['community_id' => '6', 'cluster_id' => 3],
            ['community_id' => '6', 'cluster_id' => 4],
            ['community_id' => '6', 'cluster_id' => 5],
            ['community_id' => '7', 'cluster_id' => 1],
            ['community_id' => '7', 'cluster_id' => 2],
            ['community_id' => '7', 'cluster_id' => 3],
            ['community_id' => '7', 'cluster_id' => 4],
            ['community_id' => '7', 'cluster_id' => 5],
            ['community_id' => '8', 'cluster_id' => 1],
            ['community_id' => '8', 'cluster_id' => 2],
            ['community_id' => '8', 'cluster_id' => 3],
            ['community_id' => '8', 'cluster_id' => 4],
            ['community_id' => '8', 'cluster_id' => 5],
            ['community_id' => '9', 'cluster_id' => 1],
            ['community_id' => '9', 'cluster_id' => 2],
            ['community_id' => '9', 'cluster_id' => 3],
            ['community_id' => '9', 'cluster_id' => 4],
            ['community_id' => '9', 'cluster_id' => 5],
            ['community_id' => '10', 'cluster_id' => 1],
            ['community_id' => '10', 'cluster_id' => 2],
            ['community_id' => '10', 'cluster_id' => 3],
            ['community_id' => '10', 'cluster_id' => 4],
            ['community_id' => '10', 'cluster_id' => 5],
            ['community_id' => '11', 'cluster_id' => 1],
            ['community_id' => '11', 'cluster_id' => 2],
            ['community_id' => '11', 'cluster_id' => 3],
            ['community_id' => '11', 'cluster_id' => 4],
            ['community_id' => '11', 'cluster_id' => 5],
            ['community_id' => '12', 'cluster_id' => 1],
            ['community_id' => '12', 'cluster_id' => 2],
            ['community_id' => '12', 'cluster_id' => 3],
            ['community_id' => '12', 'cluster_id' => 4],
            ['community_id' => '12', 'cluster_id' => 5],
            ['community_id' => '13', 'cluster_id' => 1],
            ['community_id' => '13', 'cluster_id' => 2],
            ['community_id' => '13', 'cluster_id' => 3],
            ['community_id' => '13', 'cluster_id' => 4],
            ['community_id' => '13', 'cluster_id' => 5],
            ['community_id' => '14', 'cluster_id' => 1],
            ['community_id' => '14', 'cluster_id' => 2],
            ['community_id' => '14', 'cluster_id' => 3],
            ['community_id' => '14', 'cluster_id' => 4],
            ['community_id' => '14', 'cluster_id' => 5],
            ['community_id' => '15', 'cluster_id' => 1],
            ['community_id' => '15', 'cluster_id' => 2],
            ['community_id' => '15', 'cluster_id' => 3],
            ['community_id' => '15', 'cluster_id' => 4],
            ['community_id' => '15', 'cluster_id' => 5],
            ['community_id' => '16', 'cluster_id' => 1],
            ['community_id' => '16', 'cluster_id' => 2],
            ['community_id' => '16', 'cluster_id' => 3],
            ['community_id' => '16', 'cluster_id' => 4],
            ['community_id' => '16', 'cluster_id' => 5],
            ['community_id' => '17', 'cluster_id' => 1],
            ['community_id' => '17', 'cluster_id' => 2],
            ['community_id' => '17', 'cluster_id' => 3],
            ['community_id' => '17', 'cluster_id' => 4],
            ['community_id' => '17', 'cluster_id' => 5],
            ['community_id' => '18', 'cluster_id' => 1],
            ['community_id' => '18', 'cluster_id' => 2],
            ['community_id' => '18', 'cluster_id' => 3],
            ['community_id' => '18', 'cluster_id' => 4],
            ['community_id' => '18', 'cluster_id' => 5],
            ['community_id' => '19', 'cluster_id' => 1],
            ['community_id' => '19', 'cluster_id' => 2],
            ['community_id' => '19', 'cluster_id' => 3],
            ['community_id' => '19', 'cluster_id' => 4],
            ['community_id' => '19', 'cluster_id' => 5],
            ['community_id' => '20', 'cluster_id' => 1],
            ['community_id' => '20', 'cluster_id' => 2],
            ['community_id' => '20', 'cluster_id' => 3],
            ['community_id' => '20', 'cluster_id' => 4],
            ['community_id' => '20', 'cluster_id' => 5],
            ['community_id' => '21', 'cluster_id' => 1],
            ['community_id' => '21', 'cluster_id' => 2],
            ['community_id' => '21', 'cluster_id' => 3],
            ['community_id' => '21', 'cluster_id' => 4],
            ['community_id' => '21', 'cluster_id' => 5],
            ['community_id' => '22', 'cluster_id' => 1],
            ['community_id' => '22', 'cluster_id' => 2],
            ['community_id' => '22', 'cluster_id' => 3],
            ['community_id' => '22', 'cluster_id' => 4],
            ['community_id' => '22', 'cluster_id' => 5],
            ['community_id' => '23', 'cluster_id' => 1],
            ['community_id' => '23', 'cluster_id' => 2],
            ['community_id' => '23', 'cluster_id' => 3],
            ['community_id' => '23', 'cluster_id' => 4],
            ['community_id' => '23', 'cluster_id' => 5],
        ];

        // Add random member_id for each community cluster
        foreach ($clusters as &$cluster) {
            $communityId = $cluster['community_id'];
            $randomMember = Member::where('community_id', $communityId)->inRandomOrder()->first();
            $cluster['member_id'] = $randomMember ? $randomMember->id : null;
        }

        CommunityCluster::insert($clusters);
    }
}
