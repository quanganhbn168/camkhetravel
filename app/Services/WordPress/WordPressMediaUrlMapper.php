<?php

namespace App\Services\WordPress;

use Illuminate\Support\Str;

class WordPressMediaUrlMapper
{
    public function localizeText(?string $value, ?array &$stats = null): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $localized = preg_replace_callback(
            $this->sourceUploadUrlPattern(),
            function (array $matches) use (&$stats): string {
                $relativePath = $this->sanitizeRelativePath(rawurldecode($matches['path']));

                if ($relativePath === null) {
                    return $matches[0];
                }

                $stats['urls_rewritten'] = ($stats['urls_rewritten'] ?? 0) + 1;

                if (! $this->sourceFileExists($relativePath)) {
                    $stats['missing_source_references'] = ($stats['missing_source_references'] ?? 0) + 1;
                    $replacements = (array) config('wordpress.missing_media_replacements', []);
                    $replacement = $replacements[$relativePath] ?? null;
                    $replacement = is_string($replacement)
                        ? $this->sanitizeRelativePath($replacement)
                        : null;

                    if ($replacement && $this->sourceFileExists($replacement)) {
                        $stats['mapped_source_references'] = ($stats['mapped_source_references'] ?? 0) + 1;
                        $relativePath = $replacement;
                    } else {
                        $stats['fallback_source_references'] = ($stats['fallback_source_references'] ?? 0) + 1;
                        $fallback = $this->sanitizeRelativePath(
                            (string) config('wordpress.fallback_media_path'),
                        );

                        if (! $fallback || ! $this->sourceFileExists($fallback)) {
                            $stats['unresolved_source_references'] = ($stats['unresolved_source_references'] ?? 0) + 1;

                            return $matches[0];
                        }

                        $relativePath = $fallback;
                    }
                }

                return $this->publicPath($relativePath).($matches['suffix'] ?? '');
            },
            $value,
        ) ?? $value;

        foreach ((array) config('wordpress.broken_external_media_replacements', []) as $source => $replacement) {
            if (! is_string($source) || ! is_string($replacement)) {
                continue;
            }

            $replacement = $this->sanitizeRelativePath($replacement);

            if (! $replacement || ! $this->sourceFileExists($replacement)) {
                continue;
            }

            $count = 0;
            $localized = str_replace(
                array_values(array_unique([
                    $source,
                    htmlspecialchars($source, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                ])),
                $this->publicPath($replacement),
                $localized,
                $count,
            );
            $stats['external_source_references'] = ($stats['external_source_references'] ?? 0) + $count;
        }

        foreach ((array) config('wordpress.broken_external_media_patterns', []) as $pattern => $replacement) {
            if (! is_string($pattern) || ! is_string($replacement)) {
                continue;
            }

            $replacement = $this->sanitizeRelativePath($replacement);

            if (! $replacement || ! $this->sourceFileExists($replacement)) {
                continue;
            }

            $count = 0;
            $rewritten = preg_replace(
                $pattern,
                $this->publicPath($replacement),
                $localized,
                -1,
                $count,
            );

            if (is_string($rewritten)) {
                $localized = $rewritten;
                $stats['external_source_references'] = ($stats['external_source_references'] ?? 0) + $count;
            }
        }

        return $localized;
    }

    public function localizeValue(mixed $value, ?array &$stats = null): mixed
    {
        if (is_string($value)) {
            return $this->localizeText($value, $stats);
        }

        if (! is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $nested) {
            $value[$key] = $this->localizeValue($nested, $stats);
        }

        return $value;
    }

    public function localizeUrl(?string $url, ?array &$stats = null): ?string
    {
        return $this->localizeText($url, $stats);
    }

    public function absoluteUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (Str::startsWith($url, '/')) {
            return rtrim((string) config('app.url'), '/').'/'.ltrim($url, '/');
        }

        return $url;
    }

    public function absoluteLocalMediaUrls(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $baseUrl = rtrim((string) config('app.url'), '/');

        return preg_replace(
            '~(?<![a-zA-Z0-9_:/.-])/storage/~',
            $baseUrl.'/storage/',
            $value,
        ) ?? $value;
    }

    public function publicPath(string $relativePath): string
    {
        return '/storage/'.trim((string) config('wordpress.media_directory'), '/')
            .'/'.ltrim(str_replace('\\', '/', $relativePath), '/');
    }

    public function storagePath(string $relativePath): string
    {
        return trim((string) config('wordpress.media_directory'), '/')
            .'/'.ltrim(str_replace('\\', '/', $relativePath), '/');
    }

    public function relativePathFromSourcePath(?string $sourcePath): ?string
    {
        if (blank($sourcePath)) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $sourcePath), '/');
        $prefix = 'wp-content/uploads/';

        if (! Str::startsWith($normalized, $prefix)) {
            return null;
        }

        return $this->sanitizeRelativePath(Str::after($normalized, $prefix));
    }

    public function sourceUploadsPath(): string
    {
        $root = (string) config('wordpress.local_path');

        if (! preg_match('~^(?:[a-zA-Z]:[\\\\/]|[\\\\/]{1,2})~', $root)) {
            $root = base_path($root);
        }

        return rtrim($root, '\\/')
            .DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'uploads';
    }

    public function sourceFileExists(string $relativePath): bool
    {
        return $this->resolvedSourceFilePath($relativePath) !== null;
    }

    public function resolvedSourceFilePath(string $relativePath): ?string
    {
        $relativePath = $this->sanitizeRelativePath($relativePath);

        if ($relativePath === null) {
            return null;
        }

        $uploadsPath = realpath($this->sourceUploadsPath());
        $candidatePath = realpath($this->sourceUploadsPath().DIRECTORY_SEPARATOR.str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $relativePath,
        ));

        if (! is_string($uploadsPath) || ! is_string($candidatePath) || ! is_file($candidatePath)) {
            return null;
        }

        $uploadsPrefix = rtrim(str_replace('\\', '/', $uploadsPath), '/').'/';
        $candidate = str_replace('\\', '/', $candidatePath);

        return str_starts_with(strtolower($candidate), strtolower($uploadsPrefix))
            ? $candidatePath
            : null;
    }

    private function sourceUploadUrlPattern(): string
    {
        $sourceHost = preg_quote(
            (string) parse_url(config('wordpress.source_url'), PHP_URL_HOST),
            '~',
        );

        return "~(?:(?:https?:)?//(?:www\\.)?{$sourceHost})/wp-content/uploads/"
            ."(?<path>[^\"'\\s<>)?,#]+)(?<suffix>\\?[^\"'\\s<>)]+|\\#[^\"'\\s<>)]+)?~iu";
    }

    public function sanitizeRelativePath(string $path): ?string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $segments = explode('/', $path);

        if (
            $path === ''
            || str_contains($path, "\0")
            || str_contains($path, ':')
            || in_array('..', $segments, true)
            || in_array('.', $segments, true)
        ) {
            return null;
        }

        return $path;
    }
}
