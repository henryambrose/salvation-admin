<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Cluster;
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

    }
}
