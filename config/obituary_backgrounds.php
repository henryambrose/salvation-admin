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
            'tier' => 'basic'
        ],
        'gradient' => [
            'name' => 'Gradient',
            'description' => 'Soft gradient background',
            'image' => null,
            'style' => [
                'background' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
            ],
            'tier' => 'premium'
        ],
        'pattern' => [
            'name' => 'Pattern',
            'description' => 'Subtle pattern background',
            'image' => null,
            'style' => [
                'backgroundColor' => '#f8f9fa',
                'backgroundImage' => 'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.5) 10px, rgba(255,255,255,.5) 20px)',
            ],
            'tier' => 'premium'
        ],
        'memorial' => [
            'name' => 'Memorial Sunset',
            'description' => 'Peaceful sunset memorial border',
            'image' => '/images/backgrounds/memorial-sunset.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/memorial-sunset.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f8f9fa',
            ],
            'tier' => 'basic'
        ],
        'floral' => [
            'name' => 'Floral Garden',
            'description' => 'Elegant floral garden border',
            'image' => '/images/backgrounds/memorial-garden.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/memorial-garden.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f8f9fa',
            ],
            'tier' => 'basic'
        ],
        'watercolor_blue' => [
            'name' => 'Watercolor Blue',
            'description' => 'Serene watercolor blue border',
            'image' => '/images/backgrounds/background_1.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/background_1.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f0f8ff',
            ],
            'tier' => 'basic'
        ],
        'rose_corner' => [
            'name' => 'Rose Corners',
            'description' => 'Delicate roses in corners',
            'image' => '/images/backgrounds/background_2.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/background_2.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fefefe',
            ],
            'tier' => 'premium'
        ],
        'botanical_frame' => [
            'name' => 'Botanical Frame',
            'description' => 'Natural botanical frame',
            'image' => '/images/backgrounds/background_3.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/background_3.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#f9f9f9',
            ],
            'tier' => 'premium'
        ],
        'elegant_border_4' => [
            'name' => 'Elegant Border',
            'description' => 'Sophisticated decorative border',
            'image' => '/images/backgrounds/background_4.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/background_4.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fafafa',
            ],
            'tier' => 'premium'
        ],
        'decorative_frame_5' => [
            'name' => 'Decorative Frame',
            'description' => 'Ornate decorative frame',
            'image' => '/images/backgrounds/background_5.png',
            'style' => [
                'backgroundImage' => 'url("/images/backgrounds/background_5.png")',
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
                'backgroundColor' => '#fbfbfb',
            ],
            'tier' => 'premium'
        ],
    ],
];