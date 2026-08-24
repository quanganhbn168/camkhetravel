<?php

namespace App\Services\WordPress;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\Term;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class WordPressMediaLocalizer
{
    public function __construct(private readonly WordPressMediaUrlMapper $mapper) {}

    public function localize(bool $dryRun = false, bool $verify = false): array
    {
        $diskName = (string) config('wordpress.media_disk', 'public');
        $disk = Storage::disk($diskName);
        $stats = [
            'files_discovered' => 0,
            'files_copied' => 0,
            'files_unchanged' => 0,
            'files_missing' => 0,
            'files_verified' => 0,
            'bytes_copied' => 0,
            'media_localized' => 0,
            'media_unchanged' => 0,
            'media_records_updated' => 0,
            'media_failed' => 0,
            'content_updated' => 0,
            'settings_updated' => 0,
            'urls_rewritten' => 0,
            'missing_source_references' => 0,
            'mapped_source_references' => 0,
            'fallback_source_references' => 0,
            'unresolved_source_references' => 0,
            'external_source_references' => 0,
            'external_files_discovered' => 0,
            'external_files_downloaded' => 0,
            'external_files_unchanged' => 0,
            'external_media_failed' => 0,
            'external_urls_rewritten' => 0,
            'image_alts_added' => 0,
            'image_alts_unresolved' => 0,
            'terms_updated' => 0,
        ];
        $files = $this->discoverFiles();
        $stats['files_discovered'] = count($files);
        $externalMappings = $this->localizeExternalMedia($disk, $diskName, $stats, $dryRun, $verify);

        foreach ($files as $relativePath) {
            $sourcePath = $this->mapper->resolvedSourceFilePath($relativePath);

            if ($sourcePath === null) {
                $stats['files_missing']++;

                continue;
            }

            $destinationPath = $this->mapper->storagePath($relativePath);
            $sourceSize = filesize($sourcePath) ?: 0;

            $destinationMatches = $disk->exists($destinationPath)
                && $disk->size($destinationPath) === $sourceSize;

            if ($destinationMatches && $verify) {
                $destinationMatches = hash_file('sha256', $sourcePath)
                    === hash_file('sha256', $disk->path($destinationPath));

                if ($destinationMatches) {
                    $stats['files_verified']++;
                }
            }

            if ($destinationMatches) {
                $stats['files_unchanged']++;

                continue;
            }

            if ($dryRun) {
                $stats['files_copied']++;
                $stats['bytes_copied'] += $sourceSize;

                continue;
            }

            $this->copyFile($disk, $sourcePath, $destinationPath, $verify);

            if ($verify) {
                $stats['files_verified']++;
            }

            $stats['files_copied']++;
            $stats['bytes_copied'] += $sourceSize;
        }

        foreach (MediaAsset::query()->where('source', 'wordpress')->cursor() as $asset) {
            $relativePath = $this->mapper->relativePathFromSourcePath($asset->source_path);
            $sourceExists = $relativePath && $this->mapper->sourceFileExists($relativePath);
            $exists = $relativePath && $disk->exists($this->mapper->storagePath($relativePath));
            $lockedFields = (array) $asset->import_locked_fields;
            $manualFileOverride = in_array('file_path', $lockedFields, true)
                && $asset->disk
                && $asset->file_path
                && Storage::disk($asset->disk)->exists($asset->file_path);
            $localExists = $manualFileOverride || $exists;

            if ($dryRun) {
                ($sourceExists || $localExists) ? $stats['media_localized']++ : $stats['media_failed']++;

                continue;
            }

            if (! $sourceExists) {
                $asset->forceFill([
                    'localization_status' => $localExists ? 'localized' : 'missing',
                    'localization_error' => $localExists
                        ? 'Tệp nguồn không còn; đang giữ bản local gần nhất.'
                        : 'Không tìm thấy tệp nguồn đã đăng ký.',
                ])->save();
                $localExists ? $stats['media_localized']++ : $stats['media_failed']++;

                continue;
            }

            if (! $exists && ! $manualFileOverride) {
                $asset->forceFill([
                    'localization_status' => 'missing',
                    'localization_error' => 'Không tìm thấy tệp nguồn đã đăng ký.',
                ])->save();
                $stats['media_failed']++;

                continue;
            }

            $sourcePath = $this->mapper->resolvedSourceFilePath($relativePath);

            if ($sourcePath === null) {
                $stats['media_failed']++;

                continue;
            }
            $checksum = hash_file('sha256', $sourcePath) ?: null;
            $attributes = [
                'effective_alt_text' => $asset->effective_alt_text ?: $this->effectiveAltText($asset),
                'localization_status' => 'localized',
                'localization_error' => null,
            ];

            if (! $manualFileOverride) {
                $attributes = [
                    ...$attributes,
                    'disk' => $diskName,
                    'file_path' => $this->mapper->storagePath($relativePath),
                    'file_size' => filesize($sourcePath) ?: $asset->file_size,
                    'checksum_sha256' => $checksum,
                ];
            }
            $unchanged = $asset->localized_at !== null
                && collect($attributes)->every(
                    fn (mixed $value, string $field): bool => $asset->{$field} === $value,
                );

            if ($unchanged) {
                $stats['media_unchanged']++;
            } else {
                $asset->forceFill([
                    ...$attributes,
                    'localized_at' => now(),
                ])->save();
                $stats['media_records_updated']++;
            }

            $stats['media_localized']++;
        }

        if (! $dryRun) {
            $this->rewriteDatabaseUrls($stats, $externalMappings);
        }

        return $stats;
    }

    public function discoverFiles(): array
    {
        $files = [];

        foreach (MediaAsset::query()->where('source', 'wordpress')->cursor() as $asset) {
            $relativePath = $this->mapper->relativePathFromSourcePath($asset->source_path);

            if ($relativePath) {
                $files[$relativePath] = true;
            }

            $attachment = $asset->metadata['attachment'] ?? [];
            $baseDirectory = $relativePath ? trim(dirname($relativePath), './') : '';

            foreach ((array) ($attachment['sizes'] ?? []) as $size) {
                if (! is_array($size) || blank($size['file'] ?? null)) {
                    continue;
                }

                $variant = ($baseDirectory !== '' ? $baseDirectory.'/' : '').$size['file'];
                $variant = $this->mapper->sanitizeRelativePath($variant);

                if ($variant) {
                    $files[$variant] = true;
                }
            }

            if (filled($attachment['original_image'] ?? null)) {
                $original = ($baseDirectory !== '' ? $baseDirectory.'/' : '').$attachment['original_image'];
                $original = $this->mapper->sanitizeRelativePath($original);

                if ($original) {
                    $files[$original] = true;
                }
            }
        }

        foreach (ContentItem::query()->where('source', 'wordpress')->cursor(['body']) as $item) {
            foreach ($this->referencedPaths((string) $item->body) as $relativePath) {
                if ($this->mapper->sourceFileExists($relativePath)) {
                    $files[$relativePath] = true;
                }
            }
        }

        $fallback = (string) config('wordpress.fallback_media_path');
        $fallback = $this->mapper->sanitizeRelativePath($fallback);

        if ($fallback) {
            $files[$fallback] = true;
        }

        foreach ((array) config('wordpress.missing_media_replacements', []) as $replacement) {
            $replacement = is_string($replacement)
                ? $this->mapper->sanitizeRelativePath($replacement)
                : null;

            if ($replacement) {
                $files[$replacement] = true;
            }
        }
        ksort($files);

        return array_keys($files);
    }

    private function rewriteDatabaseUrls(array &$stats, array $externalMappings): void
    {
        $mediaAltsById = MediaAsset::query()
            ->whereNotNull('source_id')
            ->whereNotNull('effective_alt_text')
            ->pluck('effective_alt_text', 'source_id')
            ->all();
        $mediaAltsByPath = MediaAsset::query()
            ->whereNotNull('file_path')
            ->whereNotNull('effective_alt_text')
            ->pluck('effective_alt_text', 'file_path')
            ->all();

        DB::transaction(function () use (&$stats, $mediaAltsById, $mediaAltsByPath, $externalMappings): void {
            ContentItem::query()
                ->where('source', 'wordpress')
                ->chunkById(100, function ($items) use (&$stats, $mediaAltsById, $mediaAltsByPath, $externalMappings): void {
                    foreach ($items as $item) {
                        $updates = [];

                        foreach ([
                            'body',
                            'excerpt',
                            'seo_title',
                            'seo_description',
                            'og_title',
                            'og_description',
                            'og_image_url',
                            'twitter_title',
                            'twitter_description',
                            'twitter_image_url',
                        ] as $field) {
                            $localized = $this->mapper->localizeText($item->{$field}, $stats);
                            $localized = $this->replaceExternalUrls(
                                $localized,
                                $externalMappings,
                                $stats,
                            );

                            if ($field === 'body' && is_string($localized)) {
                                $localized = $this->rewriteImageAlts(
                                    $localized,
                                    $mediaAltsById,
                                    $mediaAltsByPath,
                                    (string) $item->title,
                                    $stats,
                                );
                            }

                            if ($localized !== $item->{$field}) {
                                $updates[$field] = $localized;
                            }
                        }

                        foreach (['content_data', 'structured_data'] as $field) {
                            $localized = $this->mapper->localizeValue($item->{$field}, $stats);
                            $localized = $this->replaceExternalUrlsInValue(
                                $localized,
                                $externalMappings,
                                $stats,
                            );

                            if ($localized !== $item->{$field}) {
                                $updates[$field] = $localized;
                            }
                        }

                        if ($updates !== []) {
                            $item->forceFill($updates)->save();
                            $stats['content_updated']++;
                        }
                    }
                });

            SiteSetting::query()->chunkById(100, function ($settings) use (&$stats, $externalMappings): void {
                foreach ($settings as $setting) {
                    $value = $setting->decodedValue();
                    $localized = $this->mapper->localizeValue($value, $stats);
                    $localized = $this->replaceExternalUrlsInValue(
                        $localized,
                        $externalMappings,
                        $stats,
                    );

                    if ($localized === $value) {
                        continue;
                    }

                    $setting->forceFill([
                        'value' => $setting->type === 'json'
                            ? json_encode($localized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            : $localized,
                    ])->save();
                    $stats['settings_updated']++;
                }
            });

            Term::query()->chunkById(100, function ($terms) use (&$stats, $externalMappings): void {
                foreach ($terms as $term) {
                    $description = $this->mapper->localizeText($term->description, $stats);
                    $description = $this->replaceExternalUrls(
                        $description,
                        $externalMappings,
                        $stats,
                    );

                    if ($description === $term->description) {
                        continue;
                    }

                    $term->forceFill(['description' => $description])->save();
                    $stats['terms_updated']++;
                }
            });
        });
    }

    private function localizeExternalMedia(
        FilesystemAdapter $disk,
        string $diskName,
        array &$stats,
        bool $dryRun,
        bool $verify,
    ): array {
        $urls = $this->discoverExternalImageUrls();
        $stats['external_files_discovered'] = count($urls);
        $mappings = [];

        foreach ($urls as $url) {
            $asset = MediaAsset::query()
                ->where('source', 'external')
                ->where('source_url', $url)
                ->first();
            $existingPath = $asset?->disk === $diskName ? $asset->file_path : null;
            $exists = is_string($existingPath) && $disk->exists($existingPath);

            if ($exists && $verify && filled($asset->checksum_sha256)) {
                $exists = hash_file('sha256', $disk->path($existingPath))
                    === $asset->checksum_sha256;
            }

            if ($exists) {
                $mappings[$url] = '/storage/'.ltrim($existingPath, '/');
                $stats['external_files_unchanged']++;

                continue;
            }

            if ($dryRun) {
                continue;
            }

            try {
                $response = $this->fetchExternalImage($url);
                $contentLength = (int) $response->header('Content-Length');

                if ($contentLength > 52_428_800) {
                    throw new RuntimeException('Dung lượng ảnh nguồn vượt 50 MB.');
                }

                $declaredMimeType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0]));
                $contents = $response->body();

                if (! $response->successful() || ! str_starts_with($declaredMimeType, 'image/')) {
                    throw new RuntimeException("Nguồn trả HTTP {$response->status()} ({$declaredMimeType}).");
                }

                if ($contents === '' || strlen($contents) > 52_428_800) {
                    throw new RuntimeException('Dung lượng ảnh nguồn không hợp lệ hoặc vượt 50 MB.');
                }

                $dimensions = @getimagesizefromstring($contents);

                if (! is_array($dimensions) || blank($dimensions['mime'] ?? null)) {
                    throw new RuntimeException('Nội dung media ngoài không phải ảnh raster hợp lệ.');
                }

                $mimeType = strtolower((string) $dimensions['mime']);
                $extension = $this->extensionForMimeType($mimeType);
                $host = Str::slug((string) parse_url($url, PHP_URL_HOST), '-');
                $hash = hash('sha256', $url);
                $destinationPath = 'media/external/'.($host ?: 'unknown')
                    .'/'.$hash.'.'.$extension;
                $temporaryPath = $destinationPath.'.part-'.Str::uuid();

                $this->putAndPromoteContents(
                    $disk,
                    $contents,
                    $temporaryPath,
                    $destinationPath,
                );
                $checksum = hash_file('sha256', $disk->path($destinationPath));

                if (! is_string($checksum)) {
                    throw new RuntimeException('Không thể tính checksum media ngoài.');
                }

                $sourceId = $this->externalSourceId($url);
                $title = pathinfo(rawurldecode((string) parse_url($url, PHP_URL_PATH)), PATHINFO_FILENAME);
                $title = trim(str_replace(['-', '_'], ' ', $title));
                $asset = MediaAsset::updateOrCreate(
                    ['source' => 'external', 'source_id' => $sourceId],
                    [
                        'slug' => Str::slug($title ?: 'external-'.$sourceId),
                        'title' => $title ?: 'External media '.$sourceId,
                        'mime_type' => $mimeType,
                        'source_url' => $url,
                        'disk' => $diskName,
                        'file_path' => $destinationPath,
                        'localization_status' => 'localized',
                        'checksum_sha256' => $checksum,
                        'localized_at' => now(),
                        'localization_error' => null,
                        'width' => is_array($dimensions) ? $dimensions[0] : null,
                        'height' => is_array($dimensions) ? $dimensions[1] : null,
                        'file_size' => strlen($contents),
                        'metadata' => ['localized_from_content' => true],
                        'published_at' => now(),
                    ],
                );
                $mappings[$url] = '/storage/'.ltrim((string) $asset->file_path, '/');
                $stats['external_files_downloaded']++;
            } catch (Throwable $exception) {
                $stats['external_media_failed']++;

                if ($asset) {
                    $asset->forceFill([
                        'localization_status' => 'failed',
                        'localization_error' => $exception->getMessage(),
                    ])->save();
                }
            }
        }

        return $mappings;
    }

    private function discoverExternalImageUrls(): array
    {
        $urls = [];

        foreach (ContentItem::query()->where('source', 'wordpress')->cursor(['body']) as $item) {
            preg_match_all('~<(?:img|source)\b[^>]*>~isu', (string) $item->body, $mediaTags);

            foreach ($mediaTags[0] ?? [] as $mediaTag) {
                preg_match_all(
                    '~\b(src|srcset|data-src|data-lazy-src|data-original)\s*=\s*(["\'])(.*?)\2~isu',
                    $mediaTag,
                    $attributes,
                    PREG_SET_ORDER,
                );

                foreach ($attributes as $attribute) {
                    $value = html_entity_decode($attribute[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $candidates = strtolower($attribute[1]) === 'srcset'
                        ? preg_split('~,\s+(?=(?:https?:)?//|/)~i', $value)
                        : [$value];

                    foreach ($candidates ?: [] as $candidate) {
                        $url = preg_replace(
                            '~\s+\d+(?:\.\d+)?[wx]\s*$~i',
                            '',
                            trim($candidate),
                        );

                        if (is_string($url) && $this->isExternalImageCandidate($url)) {
                            $urls[$url] = true;
                        }
                    }
                }
            }

            preg_match_all(
                '~url\(\s*(["\']?)(https?://[^)"\']+)\1\s*\)~iu',
                (string) $item->body,
                $cssUrls,
            );

            foreach ($cssUrls[2] ?? [] as $url) {
                $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if ($this->isExternalImageCandidate($url)) {
                    $urls[$url] = true;
                }
            }
        }

        foreach (MediaAsset::query()
            ->where('source', 'external')
            ->whereNotNull('source_url')
            ->pluck('source_url') as $url) {
            if (is_string($url) && preg_match('~^https?://~i', $url)) {
                $urls[$url] = true;
            }
        }

        ksort($urls);

        return array_keys($urls);
    }

    private function isExternalImageCandidate(string $url): bool
    {
        if (! preg_match('~^https?://~i', $url)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $appHost = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));
        $sourceHost = strtolower((string) parse_url(config('wordpress.source_url'), PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($host === $appHost || ($host === $sourceHost && str_starts_with($path, '/wp-content/uploads/'))) {
            return false;
        }

        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
            return false;
        }

        if (array_key_exists($url, (array) config('wordpress.broken_external_media_replacements', []))) {
            return false;
        }

        foreach (array_keys((array) config('wordpress.broken_external_media_patterns', [])) as $pattern) {
            if (is_string($pattern) && @preg_match($pattern, $url) === 1) {
                return false;
            }
        }

        return true;
    }

    private function replaceExternalUrls(?string $value, array $mappings, array &$stats): ?string
    {
        if ($value === null || $value === '' || $mappings === []) {
            return $value;
        }

        foreach ($mappings as $source => $destination) {
            $count = 0;
            $value = str_replace(
                array_values(array_unique([
                    $source,
                    htmlspecialchars($source, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                ])),
                $destination,
                $value,
                $count,
            );
            $stats['external_urls_rewritten'] += $count;
        }

        return $value;
    }

    private function replaceExternalUrlsInValue(mixed $value, array $mappings, array &$stats): mixed
    {
        if (is_string($value)) {
            return $this->replaceExternalUrls($value, $mappings, $stats);
        }

        if (! is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $nested) {
            $value[$key] = $this->replaceExternalUrlsInValue($nested, $mappings, $stats);
        }

        return $value;
    }

    private function extensionForMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            default => throw new RuntimeException('Định dạng ảnh ngoài chưa hỗ trợ: '.$mimeType),
        };
    }

    private function externalSourceId(string $url): int
    {
        $sourceId = (int) hexdec(substr(hash('sha256', $url), 0, 15));

        while (MediaAsset::query()
            ->where('source', 'external')
            ->where('source_id', $sourceId)
            ->where('source_url', '!=', $url)
            ->exists()) {
            $sourceId++;
        }

        return $sourceId;
    }

    private function effectiveAltText(MediaAsset $asset): ?string
    {
        $value = $asset->alt_text ?: $asset->title;
        $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?: $value);

        return $value !== '' ? $value : null;
    }

    private function rewriteImageAlts(
        string $html,
        array $mediaAltsById,
        array $mediaAltsByPath,
        string $contentTitle,
        array &$stats,
    ): string {
        return preg_replace_callback(
            '~<img\b[^>]*>~iu',
            function (array $matches) use (
                $mediaAltsById,
                $mediaAltsByPath,
                $contentTitle,
                &$stats,
            ): string {
                $tag = $matches[0];
                $hasAlt = preg_match('~\balt\s*=\s*(["\'])(.*?)\1~isu', $tag, $altMatch) === 1;

                if ($hasAlt && trim(html_entity_decode($altMatch[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) !== '') {
                    return $tag;
                }

                preg_match('~\bclass\s*=\s*(["\'])(.*?)\1~isu', $tag, $classMatch);
                preg_match('~(?:^|\s)wp-image-(\d+)(?:\s|$)~u', $classMatch[2] ?? '', $idMatch);
                preg_match('~\bsrc\s*=\s*(["\'])(.*?)\1~isu', $tag, $srcMatch);
                $srcPath = isset($srcMatch[2])
                    ? (string) parse_url(html_entity_decode($srcMatch[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH)
                    : '';
                $filePath = str_starts_with($srcPath, '/storage/')
                    ? ltrim(substr($srcPath, strlen('/storage/')), '/')
                    : null;
                $alt = isset($idMatch[1]) ? ($mediaAltsById[(int) $idMatch[1]] ?? null) : null;
                $alt ??= $filePath ? ($mediaAltsByPath[$filePath] ?? null) : null;
                $alt ??= trim(strip_tags($contentTitle)) ?: null;

                if (blank($alt)) {
                    $stats['image_alts_unresolved']++;

                    return $tag;
                }

                $escaped = htmlspecialchars((string) $alt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $stats['image_alts_added']++;

                if ($hasAlt) {
                    return preg_replace(
                        '~\balt\s*=\s*(["\'])(.*?)\1~isu',
                        'alt="'.$escaped.'"',
                        $tag,
                        1,
                    ) ?? $tag;
                }

                $selfClosing = str_ends_with(rtrim($tag), '/>');
                $withoutClosing = preg_replace('~\s*/?>$~u', '', $tag) ?? $tag;

                return $withoutClosing.' alt="'.$escaped.'"'.($selfClosing ? ' />' : '>');
            },
            $html,
        ) ?? $html;
    }

    private function referencedPaths(string $value): array
    {
        $host = preg_quote((string) parse_url(config('wordpress.source_url'), PHP_URL_HOST), '~');
        preg_match_all(
            "~(?:(?:https?:)?//(?:www\\.)?{$host})/wp-content/uploads/([^\"'\\s<>)?,#]+)~iu",
            $value,
            $sourceMatches,
        );
        $mediaDirectory = preg_quote(trim((string) config('wordpress.media_directory'), '/'), '~');
        preg_match_all(
            "~/storage/{$mediaDirectory}/([^\"'\\s<>)?,#]+)~iu",
            $value,
            $localMatches,
        );

        return collect([
            ...($sourceMatches[1] ?? []),
            ...($localMatches[1] ?? []),
        ])
            ->map(fn (string $path): string => rawurldecode($path))
            ->unique()
            ->values()
            ->all();
    }

    private function sourcePath(string $relativePath): string
    {
        $relativePath = $this->mapper->sanitizeRelativePath($relativePath);

        if ($relativePath === null) {
            throw new RuntimeException('Đường dẫn media nguồn không an toàn.');
        }

        return $this->mapper->sourceUploadsPath().DIRECTORY_SEPARATOR.str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $relativePath,
        );
    }

    private function fetchExternalImage(string $url): Response
    {
        $currentUrl = $url;

        for ($redirects = 0; $redirects <= 5; $redirects++) {
            $resolve = $this->publicResolveOption($currentUrl);
            $response = Http::withHeaders([
                'Referer' => (string) config('wordpress.source_url').'/',
                'User-Agent' => 'Mozilla/5.0 THTMediaMigration/1.0',
            ])
                ->withOptions([
                    'curl' => [CURLOPT_RESOLVE => [$resolve]],
                    'on_headers' => function ($response): void {
                        $contentLength = (int) $response->getHeaderLine('Content-Length');

                        if ($contentLength > 52_428_800) {
                            throw new RuntimeException('Dung lượng ảnh nguồn vượt 50 MB.');
                        }
                    },
                    'progress' => function (
                        int $downloadTotal,
                        int $downloadedBytes,
                        int $uploadTotal,
                        int $uploadedBytes,
                    ): void {
                        if ($downloadedBytes > 52_428_800) {
                            throw new RuntimeException('Dung lượng ảnh nguồn vượt 50 MB.');
                        }
                    },
                ])
                ->withoutRedirecting()
                ->connectTimeout(10)
                ->timeout(45)
                ->get($currentUrl);

            if (! $response->redirect()) {
                return $response;
            }

            $location = (string) $response->header('Location');

            if ($location === '') {
                throw new RuntimeException('Media ngoài chuyển hướng nhưng thiếu Location.');
            }

            $currentUrl = (string) UriResolver::resolve(new Uri($currentUrl), new Uri($location));
        }

        throw new RuntimeException('Media ngoài chuyển hướng quá 5 lần.');
    }

    private function publicResolveOption(string $url): string
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = (string) parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT) ?: ($scheme === 'https' ? 443 : 80);
        $user = parse_url($url, PHP_URL_USER);
        $password = parse_url($url, PHP_URL_PASS);

        if (
            ! in_array($scheme, ['http', 'https'], true)
            || $host === ''
            || $user !== null
            || $password !== null
            || ! in_array($port, [80, 443], true)
        ) {
            throw new RuntimeException('Media ngoài phải dùng URL HTTP(S) hợp lệ.');
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : array_values(array_unique(array_filter([
                ...((array) @gethostbynamel($host)),
                ...$this->ipv6Addresses($host),
            ])));

        if ($addresses === []) {
            throw new RuntimeException('Không phân giải được hostname media ngoài.');
        }

        foreach ($addresses as $address) {
            if (! filter_var(
                $address,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
            )) {
                throw new RuntimeException('Media ngoài phân giải tới địa chỉ mạng nội bộ hoặc bị hạn chế.');
            }
        }

        $selectedAddress = $addresses[0];

        if (str_contains($selectedAddress, ':')) {
            $selectedAddress = '['.$selectedAddress.']';
        }

        return $host.':'.$port.':'.$selectedAddress;
    }

    private function ipv6Addresses(string $host): array
    {
        if (! function_exists('dns_get_record')) {
            return [];
        }

        return collect(@dns_get_record($host, DNS_AAAA) ?: [])
            ->pluck('ipv6')
            ->filter()
            ->values()
            ->all();
    }

    private function copyFile(
        FilesystemAdapter $disk,
        string $sourcePath,
        string $destinationPath,
        bool $verify,
    ): void {
        $temporaryPath = $destinationPath.'.part-'.Str::uuid();
        $stream = fopen($sourcePath, 'rb');

        if ($stream === false) {
            throw new RuntimeException('Không thể mở tệp nguồn: '.$sourcePath);
        }

        try {
            if (! $disk->put($temporaryPath, $stream)) {
                throw new RuntimeException('Không thể ghi tệp tạm: '.$temporaryPath);
            }

            if ($verify) {
                $sourceHash = hash_file('sha256', $sourcePath);
                $temporaryHash = hash_file('sha256', $disk->path($temporaryPath));

                if ($sourceHash !== $temporaryHash) {
                    throw new RuntimeException('Checksum tệp tạm không khớp nguồn: '.$destinationPath);
                }
            }

            $this->promoteTemporaryFile($disk, $temporaryPath, $destinationPath);
        } catch (Throwable $exception) {
            $disk->delete($temporaryPath);

            throw $exception;
        } finally {
            fclose($stream);
        }
    }

    private function putAndPromoteContents(
        FilesystemAdapter $disk,
        string $contents,
        string $temporaryPath,
        string $destinationPath,
    ): void {
        if (! $disk->put($temporaryPath, $contents)) {
            throw new RuntimeException('Không thể ghi tệp media ngoài vào storage.');
        }

        try {
            $this->promoteTemporaryFile($disk, $temporaryPath, $destinationPath);
        } catch (Throwable $exception) {
            $disk->delete($temporaryPath);

            throw $exception;
        }
    }

    private function promoteTemporaryFile(
        FilesystemAdapter $disk,
        string $temporaryPath,
        string $destinationPath,
    ): void {
        $backupPath = null;

        if ($disk->exists($destinationPath)) {
            $backupPath = $destinationPath.'.backup-'.Str::uuid();

            if (! $disk->move($destinationPath, $backupPath)) {
                throw new RuntimeException('Không thể tạo bản dự phòng tệp đích: '.$destinationPath);
            }
        }

        if (! $disk->move($temporaryPath, $destinationPath)) {
            if ($backupPath && $disk->exists($backupPath)) {
                $disk->move($backupPath, $destinationPath);
            }

            throw new RuntimeException('Không thể hoàn tất tệp đích: '.$destinationPath);
        }

        if ($backupPath) {
            $disk->delete($backupPath);
        }
    }
}
