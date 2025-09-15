<?php

namespace App\Services;

class BackgroundService
{
    public static function getAvailableBackgrounds(string $tier = 'basic'): array
    {
        $backgrounds = config('obituary_backgrounds.backgrounds');

        if ($tier === 'basic') {
            return array_filter($backgrounds, function ($background) {
                return $background['tier'] === 'basic';
            });
        } elseif ($tier === 'premium') {
            return $backgrounds; // Premium users get all backgrounds
        }

        return $backgrounds;
    }

    public static function getBackgroundStyle(string $backgroundKey): array
    {
        $backgrounds = config('obituary_backgrounds.backgrounds');

        if (!isset($backgrounds[$backgroundKey])) {
            return $backgrounds['plain']['style']; // Fallback to plain
        }

        return $backgrounds[$backgroundKey]['style'];
    }

    public static function getBackgroundOptions(string $tier = 'basic'): array
    {
        $backgrounds = self::getAvailableBackgrounds($tier);
        $options = [];

        foreach ($backgrounds as $key => $background) {
            $options[] = [
                'value' => $key,
                'label' => $background['name'],
                'description' => $background['description'],
                'image' => $background['image'],
                'tier' => $background['tier']
            ];
        }

        return $options;
    }

    public static function isValidBackground(string $backgroundKey, string $tier = 'basic'): bool
    {
        $backgrounds = self::getAvailableBackgrounds($tier);
        return isset($backgrounds[$backgroundKey]);
    }
}