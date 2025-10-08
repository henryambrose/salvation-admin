<?php

return [
    'backgrounds' => [
        'plain' => [
            'name' => 'Plain',
            'description' => 'Simple white background',
            'image' => null,
            'style' => [
                'backgroundColor' => '#ffffff',
            ],

        ],
        'gradient' => [
            'name' => 'Gradient',
            'description' => 'Soft gradient background',
            'image' => null,
            'style' => [
                'background' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
            ],

        ],
        'pattern' => [
            'name' => 'Pattern',
            'description' => 'Subtle pattern background',
            'image' => null,
            'style' => [
                'backgroundColor' => '#f8f9fa',
                'backgroundImage' => 'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.5) 10px, rgba(255,255,255,.5) 20px)',
            ],

        ],
        'memorial' => [
            'name' => 'Memorial Sunset',
            'description' => 'Peaceful sunset memorial border',
            'image' => '/storage/backgrounds/memorial-sunset.png',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/memorial-sunset.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f8f9fa',
            ],

        ],
        'floral' => [
            'name' => 'Floral Garden',
            'description' => 'Elegant floral garden border',
            'image' => '/storage/backgrounds/memorial-garden.png',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/memorial-garden.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f8f9fa',
            ],

        ],
        'watercolor_blue' => [
            'name' => 'Watercolor Blue',
            'description' => 'Serene watercolor blue border',
            'image' => '/storage/backgrounds/Memorial-01-01.webp',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/Memorial-01-01.webp")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f0f8ff',
            ],

        ],
        'rose_corner' => [
            'name' => 'Rose Corners',
            'description' => 'Delicate roses in corners',
            'image' => '/storage/backgrounds/Memorial-02-01.webp',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/Memorial-02-01.webp")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fefefe',
            ],
        ],
        'botanical_frame' => [
            'name' => 'Botanical Frame',
            'description' => 'Natural botanical frame',
            'image' => '/storage/backgrounds/Memorial-04-01.webp',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/Memorial-04-01.webp")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f9f9f9',
            ],
        ],
        'elegant_border_4' => [
            'name' => 'Elegant Border',
            'description' => 'Sophisticated decorative border',
            'image' => '/storage/backgrounds/Memorial-05-01.webp',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/Memorial-05-01.webp")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fafafa',
            ],
        ],
        'decorative_frame_5' => [
            'name' => 'Decorative Frame',
            'description' => 'Ornate decorative frame',
            'image' => '/storage/backgrounds/Memorial-06-01.webp',
            'style' => [
                'backgroundImage' => 'url("/storage/backgrounds/Memorial-06-01.webp")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fbfbfb',
            ],
        ],
    ],
];
