<?php

namespace App\Support\Seo;

use Illuminate\Support\Str;

final class ContentSeoFallbacks
{
    public static function slug(mixed $value): ?string
    {
        $slug = Str::slug((string) $value);

        return $slug !== '' ? $slug : null;
    }

    public static function title(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            $text = self::plainText($value);

            if ($text !== null) {
                return $text;
            }
        }

        return null;
    }

    public static function description(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            $text = self::plainText($value);

            if ($text !== null) {
                return Str::limit($text, 160, '');
            }
        }

        return null;
    }

    private static function plainText(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $text = Str::squish(strip_tags(html_entity_decode((string) $value)));

        return $text !== '' ? $text : null;
    }
}
