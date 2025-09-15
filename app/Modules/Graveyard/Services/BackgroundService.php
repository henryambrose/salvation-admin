<?php

namespace Modules\Graveyard\Services;


use Modules\Graveyard\Helpers\BackgroundThemeHelper;

class BackgroundService
{
    /**
     * Get available backgrounds for a specific tier
     * Updated to use database-driven system
     */
    public static function getAvailableBackgrounds(string $tier = 'basic'): array
    {
        return BackgroundThemeHelper::getBackgroundThemes($tier);
    }

    /**
     * Get background style for a specific key
     * Updated to use database-driven system
     */
    public static function getBackgroundStyle(string $backgroundKey): array
    {
        $style = BackgroundThemeHelper::getBackgroundStyle($backgroundKey);

        // Fallback to plain style if theme not found
        if (empty($style)) {
            $style = BackgroundThemeHelper::getBackgroundStyle('plain');
        }

        // Final fallback if plain doesn't exist
        if (empty($style)) {
            $style = ['backgroundColor' => '#ffffff'];
        }

        return $style;
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
