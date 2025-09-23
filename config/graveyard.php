<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Annual Maintenance Fee
    |--------------------------------------------------------------------------
    |
    | The annual maintenance fee amount for permanent graves.
    | This amount can be overridden per grave if needed.
    |
    */
    'annual_maintenance_fee' => env('PERMANENT_GRAVE_ANNUAL_MAINTENANCE_FEE', 5000),

    /*
    |--------------------------------------------------------------------------
    | Payment Periods
    |--------------------------------------------------------------------------
    |
    | Configuration for payment periods and partial payments.
    |
    */
    'payment_periods' => [
        'enable_partial_payments' => true,
        'minimum_payment_months' => 1,
        'maximum_advance_years' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Fee Settings
    |--------------------------------------------------------------------------
    |
    | Various settings for maintenance fee management.
    |
    */
    'maintenance_fee' => [
        'enable_reminders' => true,
        'reminder_months_before_due' => 2,
        'grace_period_days' => 30,
        'overdue_penalty_percentage' => 0, // No penalty for now
    ],
];