<?php

namespace Modules\Graveyard\Services;


use Modules\Graveyard\Models\ObituaryBackgroundTheme;
use Illuminate\Support\Collection;

class ObituaryBackgroundService
{
    /**
     * Get all available background themes for a user tier
     */
    public function getAvailableThemes(string $userTier = 'basic'): Collection
    {
        return ObituaryBackgroundTheme::getAvailableForTier($userTier);
    }

    /**
     * Get background themes formatted for frontend
     */
    public function getThemesForFrontend(string $userTier = 'basic'): array
    {
        return $this->getAvailableThemes($userTier)
            ->map(fn(ObituaryBackgroundTheme $theme) => $theme->toFrontendArray())
            ->toArray();
    }

    /**
     * Get background themes formatted as the old config structure
     * For backward compatibility
     */
    public function getThemesAsConfig(string $userTier = 'basic'): array
    {
        $themes = $this->getAvailableThemes($userTier);
        $config = [];

        foreach ($themes as $theme) {
            $config[$theme->key] = [
                'name' => $theme->name,
                'description' => $theme->description,
                'image' => $theme->image_path,
                'style' => $theme->getCompleteStyleAttribute(),
                'tier' => $theme->tier,
            ];
        }

        return $config;
    }

    /**
     * Get a specific theme by key
     */
    public function getThemeByKey(string $key): ?ObituaryBackgroundTheme
    {
        return ObituaryBackgroundTheme::getByKey($key);
    }

    /**
     * Get theme style for a specific key
     */
    public function getThemeStyle(string $key): array
    {
        $theme = $this->getThemeByKey($key);
        return $theme ? $theme->getCompleteStyleAttribute() : [];
    }

    /**
     * Validate if a theme is available for a user tier
     */
    public function isThemeAvailableForTier(string $themeKey, string $userTier = 'basic'): bool
    {
        $theme = $this->getThemeByKey($themeKey);

        if (!$theme) {
            return false;
        }

        $allowedTiers = $userTier === 'premium' ? ['basic', 'premium'] : ['basic'];

        return in_array($theme->tier, $allowedTiers);
    }

    /**
     * Get the default theme
     */
    public function getDefaultTheme(): ObituaryBackgroundTheme
    {
        return ObituaryBackgroundTheme::where('key', 'plain')
            ->active()
            ->first() ?? ObituaryBackgroundTheme::active()->ordered()->first();
    }

    /**
     * Get themes grouped by tier
     */
    public function getThemesGroupedByTier(): array
    {
        $themes = ObituaryBackgroundTheme::active()->ordered()->get();

        return [
            'basic' => $themes->where('tier', 'basic')->map(fn($theme) => $theme->toFrontendArray())->values()->toArray(),
            'premium' => $themes->where('tier', 'premium')->map(fn($theme) => $theme->toFrontendArray())->values()->toArray(),
        ];
    }

    /**
     * Create a new theme (for admin management)
     */
    public function createTheme(array $data): ObituaryBackgroundTheme
    {
        return ObituaryBackgroundTheme::create($data);
    }

    /**
     * Update a theme (for admin management)
     */
    public function updateTheme(string $key, array $data): bool
    {
        $theme = $this->getThemeByKey($key);
        return $theme ? $theme->update($data) : false;
    }

    /**
     * Delete a theme (for admin management)
     */
    public function deleteTheme(string $key): bool
    {
        $theme = $this->getThemeByKey($key);
        return $theme ? $theme->delete() : false;
    }

    /**
     * Toggle theme active status
     */
    public function toggleThemeStatus(string $key): bool
    {
        $theme = ObituaryBackgroundTheme::where('key', $key)->first();

        if ($theme) {
            $theme->is_active = !$theme->is_active;
            return $theme->save();
        }

        return false;
    }
}
