<?php

namespace App\Support\Maps;

use Illuminate\Contracts\Validation\ValidationRule;

final class GoogleMapsUrl
{
    public static function normalizeEmbed(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($value === '') {
            return null;
        }

        if (preg_match('/<iframe\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1/is', $value, $matches) === 1) {
            $value = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } elseif (str_contains(strtolower($value), '<iframe')) {
            return null;
        }

        $value = trim($value);

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($value);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = rtrim((string) ($parts['path'] ?? ''), '/');

        if (($parts['scheme'] ?? '') !== 'https') {
            return null;
        }

        if (! in_array($host, ['google.com', 'www.google.com', 'maps.google.com'], true)) {
            return null;
        }

        if ($path === '/maps/embed') {
            return $value;
        }

        parse_str((string) ($parts['query'] ?? ''), $query);

        return $path === '/maps' && strtolower((string) ($query['output'] ?? '')) === 'embed'
            ? $value
            : null;
    }

    public static function normalizeShare(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($value);

        if (($parts['scheme'] ?? '') !== 'https') {
            return null;
        }

        return self::isGoogleMapsHost(strtolower((string) ($parts['host'] ?? '')))
            ? $value
            : null;
    }

    public static function isGoogleMapsHost(string $host): bool
    {
        return in_array($host, [
            'google.com',
            'www.google.com',
            'maps.google.com',
            'maps.app.goo.gl',
        ], true);
    }

    public static function embedFromCoordinates(string $latitude, string $longitude): string
    {
        return 'https://www.google.com/maps?q='.rawurlencode($latitude.','.$longitude).'&output=embed';
    }

    public static function embedValidationRule(): ValidationRule
    {
        return new GoogleMapsEmbedRule();
    }
}
