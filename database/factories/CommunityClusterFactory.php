<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Community;
use Modules\Members\Models\Cluster;

class CommunityClusterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = CommunityCluster::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => Community::factory(),
            'cluster_id' => Cluster::factory(),
            'member_id' => null, // Optional cluster head
        ];
    }
}
