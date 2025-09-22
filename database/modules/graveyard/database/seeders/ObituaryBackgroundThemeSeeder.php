<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Graveyard\Models\ObituaryBackgroundTheme;

class ObituaryBackgroundThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing themes
        ObituaryBackgroundTheme::truncate();

        // Migrate from obituary_backgrounds.php config
        $obituaryBackgrounds = config('obituary_backgrounds.backgrounds', []);

        $sortOrder = 0;
        foreach ($obituaryBackgrounds as $key => $background) {
            ObituaryBackgroundTheme::create([
                'key' => $key,
                'name' => $background['name'],
                'description' => $background['description'],
                'type' => $this->determineType($background),
                'tier' => $background['tier'] ?? 'basic',
                'image_path' => $background['image'] ?? null,
                'style_properties' => $background['style'] ?? null,
                'background_color' => $this->extractBackgroundColor($background),
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);
        }

        // Also migrate from backgrounds.php config if it exists and is different
        $generalBackgrounds = config('backgrounds.styles', []);

        foreach ($generalBackgrounds as $key => $background) {
            // Skip if already exists from obituary_backgrounds
            if (ObituaryBackgroundTheme::where('key', $key)->exists()) {
                continue;
            }

            ObituaryBackgroundTheme::create([
                'key' => $key . '_alt', // Add suffix to avoid conflicts
                'name' => $background['name'],
                'description' => $background['description'],
                'type' => $background['type'] ?? 'color',
                'tier' => 'basic', // Default tier for general backgrounds
                'image_path' => $background['image'] ?? null,
                'style_properties' => $this->generateStyleFromBackgroundConfig($background),
                'background_color' => null,
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    /**
     * Determine the type based on background configuration
     */
    private function determineType(array $background): string
    {
        if (!empty($background['image'])) {
            return 'image';
        }

        $style = $background['style'] ?? [];

        if (isset($style['background']) && str_contains($style['background'], 'gradient')) {
            return 'gradient';
        }

        if (isset($style['backgroundImage']) && str_contains($style['backgroundImage'], 'repeating-linear-gradient')) {
            return 'pattern';
        }

        return 'color';
    }

    /**
     * Extract background color from style configuration
     */
    private function extractBackgroundColor(array $background): ?string
    {
        $style = $background['style'] ?? [];
        return $style['backgroundColor'] ?? null;
    }

    /**
     * Generate style properties from background config
     */
    private function generateStyleFromBackgroundConfig(array $background): ?array
    {
        $style = [];

        if ($background['type'] === 'image' && !empty($background['image'])) {
            $style = [
                'backgroundImage' => "url('{$background['image']}')",
                'backgroundSize' => 'cover',
                'backgroundPosition' => 'center',
                'backgroundRepeat' => 'no-repeat',
            ];
        } elseif ($background['type'] === 'gradient') {
            $style = [
                'background' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
            ];
        } elseif ($background['type'] === 'pattern') {
            $style = [
                'backgroundColor' => '#f8f9fa',
                'backgroundImage' => 'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.5) 10px, rgba(255,255,255,.5) 20px)',
            ];
        } else {
            $style = [
                'backgroundColor' => '#ffffff',
            ];
        }

        return empty($style) ? null : $style;
    }
}
