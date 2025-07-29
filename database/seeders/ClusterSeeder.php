<?php

namespace Database\Seeders;

use App\Models\Cluster;
use Illuminate\Database\Seeder;

class ClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clusters = [
            ['name' => 'A'],
            ['name' => 'B'],
            ['name' => 'C'],
            ['name' => 'D'],
            ['name' => 'E'],
        ];

        foreach ($clusters as $cluster) {
            Cluster::create($cluster);
        }

        $this->command->info('5 clusters seeded: A, B, C, D, E');
    }
}
