<?php

namespace App\Support\Landing;

/** Small, framework-native presentation helpers used by Landing Blade views. */
final class LandingView
{
    public static function zaloUrl(array $contact = []): string
    {
        return trim((string) ($contact['zalo_url'] ?? '')) ?: 'https://zalo.me/0973494999';
    }

    public static function safeHtml(mixed $value, array $allowedHtml = []): string
    {
        $allowedTags = $allowedHtml !== []
            ? array_keys($allowedHtml)
            : ['br', 'strong', 'em', 'span', 'a', 'p'];
        $allowed = implode('', array_map(static fn (string $tag): string => '<'.$tag.'>', $allowedTags));
        $html = strip_tags((string) $value, $allowed);
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu', '', $html) ?? $html;
        $html = preg_replace('/\s+style\s*=\s*(["\']).*?\1/iu', '', $html) ?? $html;
        $html = preg_replace('/(href\s*=\s*["\'])\s*javascript:[^"\']*(["\'])/iu', '$1#$2', $html) ?? $html;

        return $html;
    }

    public static function fileName(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', $name) ?: '';
    }

    public static function className(string $class): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', $class) ?: 'tht-landing-theme-enterprise';
    }
}
