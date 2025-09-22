<?php

namespace Modules\Fund\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fund\Models\FundCategory;
use Modules\Fund\Models\PaymentMethod;


class FundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Fund Categories using updateOrCreate to avoid conflicts
        $categories = [
            ['name' => 'Annual Contributions', 'description' => 'Yearly membership contributions', 'sort_order' => 1],
            ['name' => 'Building Fund', 'description' => 'Contributions for church building maintenance and construction', 'sort_order' => 2],
            ['name' => 'Mission Fund', 'description' => 'Support for missionary activities', 'sort_order' => 3],
            ['name' => 'Charity Fund', 'description' => 'Contributions for charitable activities', 'sort_order' => 4],
            ['name' => 'Special Collections', 'description' => 'Special occasion collections', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            FundCategory::updateOrCreate(
                ['name' => $category['name']],  // Search by name
                $category                       // Update/create with this data
            );
        }

        // Seed Payment Methods
        $paymentMethods = [
            ['name' => 'Cash', 'description' => 'Cash payment', 'sort_order' => 1],
            ['name' => 'Check', 'description' => 'Check payment', 'sort_order' => 2],
            ['name' => 'Bank Transfer', 'description' => 'Direct bank transfer', 'sort_order' => 3],
            ['name' => 'Online Payment', 'description' => 'Online payment gateway', 'sort_order' => 4],
            ['name' => 'Mobile Money', 'description' => 'Mobile money transfer', 'sort_order' => 5],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }

        // // Seed Intention Types with default amounts
        // $intentionTypes = [
        //     ['name' => 'Regular Mass', 'description' => 'Standard mass intention', 'default_amount' => 50.00, 'sort_order' => 1],
        //     ['name' => 'Special Intention', 'description' => 'Special prayer requests', 'default_amount' => 30.00, 'sort_order' => 2],
        //     ['name' => 'Anniversary Mass', 'description' => 'Wedding or birthday anniversaries', 'default_amount' => 40.00, 'sort_order' => 3],
        //     ['name' => 'Birthday Mass', 'description' => 'Birthday celebrations', 'default_amount' => 25.00, 'sort_order' => 4],
        //     ['name' => 'Death Anniversary Mass', 'description' => 'Memorial masses', 'default_amount' => 35.00, 'sort_order' => 5],
        //     ['name' => 'Thanksgiving Mass', 'description' => 'Gratitude masses', 'default_amount' => 45.00, 'sort_order' => 6],
        // ];

        // foreach ($intentionTypes as $type) {
        //     MassIntentionType::updateOrCreate(
        //         ['name' => $type['name']],
        //         $type
        //     );
        // }
    }
}
