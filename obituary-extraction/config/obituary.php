<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Obituary Duration Settings
    |--------------------------------------------------------------------------
    |
    | These values determine how long obituary pages remain active for
    | each service type. Values are in days.
    |
    */

    'duration' => [
        'basic' => (int) env('OBITUARY_BASIC_DURATION_DAYS', 90),      // 3 months
        'premium' => (int) env('OBITUARY_PREMIUM_DURATION_DAYS', 365), // 1 year
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiration Warning Settings
    |--------------------------------------------------------------------------
    |
    | Number of days before expiration to show warning badges
    |
    */

    'warning_days' => (int) env('OBITUARY_WARNING_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Auto-processing Settings
    |--------------------------------------------------------------------------
    |
    | Whether to automatically process expired obituaries
    |
    */

    'auto_process_expired' => env('OBITUARY_AUTO_PROCESS_EXPIRED', true),

    /*
    |--------------------------------------------------------------------------
    | Grace Period Settings
    |--------------------------------------------------------------------------
    |
    | Days after expiration before obituary becomes completely inaccessible
    |
    */

    'grace_period_days' => (int) env('OBITUARY_GRACE_PERIOD_DAYS', 30),
];