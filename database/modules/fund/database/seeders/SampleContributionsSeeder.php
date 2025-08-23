<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fund\Models\FamilyContribution;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\PaymentMethod;

class SampleContributionsSeeder extends Seeder
{
    public function run(): void
    {
        $category = FundCategory::first();
        $method = PaymentMethod::first();

        if (!$category || !$method) {
            $this->command->error('Fund categories or payment methods not found. Please run the main FundSeeder first.');
            return;
        }

        $contributions = [
            [
                'family_no' => 'F001',
                'year' => 2024,
                'amount' => 5000.00,
                'fund_category_id' => $category->id,
                'payment_method_id' => $method->id,
                'payment_date' => '2024-01-15',
                'status' => 'paid',
                'paid_by_name' => 'John Doe',
                'notes' => 'Building fund contribution',
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'family_no' => 'F002',
                'year' => 2024,
                'amount' => 3000.00,
                'fund_category_id' => $category->id,
                'payment_method_id' => $method->id,
                'payment_date' => '2024-02-20',
                'status' => 'partial',
                'paid_by_name' => 'Jane Smith',
                'notes' => 'Partial payment for building fund',
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'family_no' => 'F003',
                'year' => 2024,
                'amount' => 7500.00,
                'fund_category_id' => $category->id,
                'payment_method_id' => $method->id,
                'payment_date' => '2024-03-10',
                'status' => 'pending',
                'paid_by_name' => 'Bob Johnson',
                'notes' => 'Mission fund contribution',
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'family_no' => 'F004',
                'year' => 2023,
                'amount' => 4500.00,
                'fund_category_id' => $category->id,
                'payment_method_id' => $method->id,
                'payment_date' => '2023-12-15',
                'status' => 'paid',
                'paid_by_name' => 'Alice Brown',
                'notes' => 'Previous year contribution',
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'family_no' => 'F005',
                'year' => 2024,
                'amount' => 6000.00,
                'fund_category_id' => $category->id,
                'payment_method_id' => $method->id,
                'payment_date' => '2024-01-30',
                'status' => 'paid',
                'paid_by_name' => 'Charlie Wilson',
                'notes' => 'Charity fund contribution',
                'created_by' => 1,
                'updated_by' => 1
            ]
        ];

        foreach ($contributions as $contribution) {
            FamilyContribution::updateOrCreate(
                ['family_no' => $contribution['family_no'], 'year' => $contribution['year']],
                $contribution
            );
        }

        $this->command->info('Sample contributions created successfully!');
    }
}
