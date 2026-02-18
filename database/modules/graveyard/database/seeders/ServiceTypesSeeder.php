<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Temporary Grave',
                'description' => 'Temporary burial plot allocation',
                'cost' => 2000.00,
                'type' => 'normal',
                'category' => 'grave',
                'applicable_to' => 'temporary',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Permanent Grave',
                'description' => 'Permanent burial plot allocation',
                'cost' => 50000.00,
                'type' => 'normal',
                'category' => 'grave',
                'applicable_to' => 'permanent',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Niche',
                'description' => 'Niche allocation for remains',
                'cost' => 15000.00,
                'type' => 'normal',
                'category' => 'grave',
                'applicable_to' => 'remains-transfer',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Funeral Mass',
                'description' => 'Religious service for the deceased',
                'cost' => 100.00,
                'type' => 'normal',
                'category' => 'funeral',
                'applicable_to' => 'all',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Video Graph',
                'description' => 'Video recording of the service',
                'cost' => 1500.00,
                'type' => 'normal',
                'category' => 'additional',
                'applicable_to' => 'all',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Online Streaming',
                'description' => 'Live streaming of the service',
                'cost' => 5000.00,
                'type' => 'normal',
                'category' => 'additional',
                'applicable_to' => 'all',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Concession',
                'description' => 'Discount applied to total amount',
                'cost' => 0.00,
                'type' => 'concession',
                'category' => 'additional',
                'applicable_to' => 'all',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Free Service',
                'description' => 'Complimentary service (no charge)',
                'cost' => 0.00,
                'type' => 'free',
                'category' => 'additional',
                'applicable_to' => 'all',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($serviceTypes as $serviceType) {
            // Use updateOrInsert to avoid duplicate entries
            DB::table('service_types')->updateOrInsert(
                ['name' => $serviceType['name']], // Search criteria
                array_merge($serviceType, [       // Data to insert/update
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
