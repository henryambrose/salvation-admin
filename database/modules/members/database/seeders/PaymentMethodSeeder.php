<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fund\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $paymentMethods = [
            [
                'name' => 'Cash',
                'description' => 'Cash payment method',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cheque',
                'description' => 'Cheque payment method',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Online Payment',
                'description' => 'Online payment gateway',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Credit Card',
                'description' => 'Credit card payment',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Debit Card',
                'description' => 'Debit card payment',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'UPI',
                'description' => 'Unified Payment Interface',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Digital Wallet',
                'description' => 'Digital wallet payment',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($paymentMethods as $paymentMethodData) {
            PaymentMethod::firstOrCreate(
                ['name' => $paymentMethodData['name']],
                array_merge($paymentMethodData, [
                    'created_by' => 1, // Assuming user ID 1 exists
                    'updated_by' => 1,
                ])
            );
        }

    }
}
