<?php

namespace Modules\Graveyard\Helpers;

use Modules\Graveyard\Services\ObituaryBackgroundService;

class BackgroundThemeHelper
{
    /**
     * Get background themes for backward compatibility
     * This helper function replaces config('obituary_backgrounds.backgrounds')
     */
    public static function getBackgroundThemes(string $userTier = 'basic'): array
    {
        $service = app(ObituaryBackgroundService::class);
        return $service->getThemesAsConfig($userTier);
    }

    /**
     * Get a specific background theme
     */
    public static function getBackgroundTheme(string $key): ?array
    {
        $service = app(ObituaryBackgroundService::class);
        $theme = $service->getThemeByKey($key);

        if (!$theme) {
            return null;
        }

        return [
            'name' => $theme->name,
            'description' => $theme->description,
            'image' => $theme->image_path,
            'style' => $theme->getCompleteStyleAttribute(),
            'tier' => $theme->tier,
        ];
    }

    /**
     * Get theme style properties for CSS
     */
    public static function getBackgroundStyle(string $key): array
    {
        $service = app(ObituaryBackgroundService::class);
        return $service->getThemeStyle($key);
    }

    /**
     * Check if theme is available for user tier
     */
    public static function isThemeAvailable(string $key, string $userTier = 'basic'): bool
    {
        $service = app(ObituaryBackgroundService::class);
        return $service->isThemeAvailableForTier($key, $userTier);
    }
}
