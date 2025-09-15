<?php

namespace App\Helpers;

class BackgroundHelper
{
    /**
     * Get CSS styles for obituary background
     */
    public static function getBackgroundStyles(string $backgroundStyle, string $themeColor = '#ffffff'): array
    {
        $styles = [
            'backgroundColor' => $themeColor,
        ];

        switch ($backgroundStyle) {
            case 'gradient':
                $styles['backgroundImage'] = "linear-gradient(135deg, {$themeColor}, #f8f9fa)";
                break;

            case 'pattern':
                $styles['backgroundImage'] = "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0f0f0' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")";
                break;

            case 'floral':
            case 'memorial':
                $styles['backgroundImage'] = "url('/images/backgrounds/memorial-sunset.png')";
                $styles['backgroundSize'] = 'cover';
                $styles['backgroundPosition'] = 'center';
                $styles['backgroundRepeat'] = 'no-repeat';
                $styles['backgroundColor'] = '#f8f9fa'; // Fallback
                break;

            case 'plain':
            default:
                // Keep only background color
                break;
        }

        return $styles;
    }

    /**
     * Get available background options
     */
    public static function getBackgroundOptions(): array
    {
        return config('backgrounds.styles', []);
    }

    /**
     * Convert CSS array to inline style string
     */
    public static function arrayToStyleString(array $styles): string
    {
        $styleString = '';
        foreach ($styles as $property => $value) {
            $cssProperty = self::camelToKebab($property);
            $styleString .= "{$cssProperty}: {$value}; ";
        }
        return trim($styleString);
    }

    /**
     * Convert camelCase to kebab-case
     */
    private static function camelToKebab(string $string): string
    {
        return strtolower(preg_replace('/([A-Z])/', '-$1', $string));
    }
}