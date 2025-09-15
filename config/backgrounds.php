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
            'image' => '/images/backgrounds/Memorial-01-01.webp',
        ],
        'floral' => [
            'name' => 'Floral Border',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-02-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial 4',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-04-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial 5',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-05-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial 6',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-06-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial 7',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-07-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-09-01.webp',
        ],
        'floral' => [
            'name' => 'Memorial 10',
            'description' => 'Beautiful floral border design',
            'type' => 'image',
            'image' => '/images/backgrounds/Memorial-10-01.webp',
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
