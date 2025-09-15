<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Background Styles
    |--------------------------------------------------------------------------
    |
    | Available background styles for obituary pages
    |
    */

    'styles' => [
        'plain' => [
            'name' => 'Plain',
            'description' => 'Simple solid color background',
            'type' => 'color',
        ],
        'gradient' => [
            'name' => 'Gradient',
            'description' => 'Subtle gradient effect',
            'type' => 'gradient',
        ],
        'pattern' => [
            'name' => 'Pattern',
            'description' => 'Subtle geometric pattern',
            'type' => 'pattern',
        ],
        'memorial' => [
            'name' => 'Memorial',
            'description' => 'Peaceful memorial background',
            'type' => 'image',
            'image' => '/images/backgrounds/memorial-sunset.png',
        ],
        'floral' => [
            'name' => 'Floral Border',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/memorial-sunset.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Background Settings
    |--------------------------------------------------------------------------
    */

    'default' => [
        'style' => 'plain',
        'color' => '#ffffff',
    ],
];