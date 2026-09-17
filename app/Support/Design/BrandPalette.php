<?php

namespace App\Support\Design;

/** Coordinates configurable website colors and Bootstrap component variables. */
final class BrandPalette
{
    /** @return array{primary: string, hover: string, rgb: string, hover_rgb: string, contrast: string, hover_contrast: string} */
    public static function make(?string $primary, ?string $hover): array
    {
        $primary = self::normalize($primary, '#d71920');
        $hover = self::normalize($hover, '#ad1117');

        return [
            'primary' => $primary,
            'hover' => $hover,
            'rgb' => implode(', ', self::channels($primary)),
            'hover_rgb' => implode(', ', self::channels($hover)),
            'contrast' => self::contrast($primary),
            'hover_contrast' => self::contrast($hover),
        ];
    }

    private static function normalize(?string $color, string $fallback): string
    {
        $color = strtolower(trim($color ?? ''));
        if (preg_match('/^#[a-f0-9]{3}$/', $color)) {
            $color = '#'.$color[1].$color[1].$color[2].$color[2].$color[3].$color[3];
        }

        return preg_match('/^#[a-f0-9]{6}$/', $color) ? $color : $fallback;
    }

    /** @return list<int> */
    private static function channels(string $color): array
    {
        return [hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2))];
    }

    private static function contrast(string $color): string
    {
        $linear = array_map(static function (int $channel): float {
            $value = $channel / 255;

            return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, self::channels($color));
        $luminance = 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];

        return 1.05 / ($luminance + 0.05) >= ($luminance + 0.05) / 0.05 ? '#ffffff' : '#000000';
    }
}
