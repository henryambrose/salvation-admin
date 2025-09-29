<?php

namespace Database\Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Graveyard\Models\AnnualMaintenanceFee;

class AnnualMaintenanceFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create historical maintenance fees for demonstration
        $maintenanceFees = [
            [
                'year' => 2020,
                'applies_to' => 'both',
                'permanent_grave_amount' => 3000.00,
                'niche_amount' => 2000.00,
                'effective_from' => '2020-01-01',
                'effective_until' => '2020-12-31',
                'is_active' => true,
                'notes' => 'Initial fee structure for both permanent graves and niches.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2021,
                'applies_to' => 'both',
                'permanent_grave_amount' => 3000.00,
                'niche_amount' => 2000.00,
                'effective_from' => '2021-01-01',
                'effective_until' => '2021-12-31',
                'is_active' => true,
                'notes' => 'Maintained same rates as 2020.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2022,
                'applies_to' => 'both',
                'permanent_grave_amount' => 3500.00,
                'niche_amount' => 2500.00,
                'effective_from' => '2022-01-01',
                'effective_until' => '2022-12-31',
                'is_active' => true,
                'notes' => 'Increased rates due to inflation and maintenance costs.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2023,
                'applies_to' => 'both',
                'permanent_grave_amount' => 5000.00,
                'niche_amount' => 3500.00,
                'effective_from' => '2023-01-01',
                'effective_until' => '2023-12-31',
                'is_active' => true,
                'notes' => 'Significant increase to cover enhanced maintenance and facility upgrades.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2024,
                'applies_to' => 'both',
                'permanent_grave_amount' => 5000.00,
                'niche_amount' => 3500.00,
                'effective_from' => '2024-01-01',
                'effective_until' => '2024-12-31',
                'is_active' => true,
                'notes' => 'Maintained 2023 rates with no increase.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2025,
                'applies_to' => 'both',
                'permanent_grave_amount' => 5500.00,
                'niche_amount' => 4000.00,
                'effective_from' => '2025-01-01',
                'effective_until' => null,
                'is_active' => true,
                'notes' => 'Current year rates with modest increase for ongoing facility improvements.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($maintenanceFees as $fee) {
            AnnualMaintenanceFee::updateOrCreate(
                ['year' => $fee['year']],
                $fee
            );
        }

        $this->command->info('Annual maintenance fees seeded successfully.');
    }
}