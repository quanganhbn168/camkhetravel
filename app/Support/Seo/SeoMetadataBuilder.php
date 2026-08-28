<?php

namespace App\Support\Seo;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Services\WordPress\WordPressMediaUrlMapper;

class SeoMetadataBuilder
{
    public function __construct(private readonly WordPressMediaUrlMapper $mediaUrlMapper) {}

    public function forContent(
        ContentItem $item,
        ?string $canonicalPath = null,
        bool $useMaterialized = true,
    ): SeoMetadata {
        $effective = $useMaterialized && is_array($item->effective_seo)
            ? $item->effective_seo
            : [];
        $settings = $this->rankMathTitleSettings();
        $siteName = $this->normalizeText($this->setting('blogname') ?: config('app.name'));
        $siteDescription = $this->normalizeText($this->setting('blogdescription') ?: '');
        $separator = (string) ($settings['title_separator'] ?? '-');
        $excerpt = $this->excerpt($item);
        $variables = [
            '%title%' => $this->normalizeText($item->title),
            '%sitename%' => $siteName,
            '%sitedesc%' => $siteDescription,
            '%sep%' => $separator,
            '%excerpt%' => $excerpt,
            '%url%' => $item->legacy_url ?: '',
            '%date%' => $item->published_at?->format('d/m/Y') ?: '',
            '%modified%' => $item->content_modified_at?->format('d/m/Y') ?: '',
            '%page%' => '',
        ];
        $titleTemplate = $item->seo_title
            ?: ($settings['pt_'.$item->type.'_title'] ?? '%title% %sep% %sitename%');
        $title = $this->normalizeText($this->expand((string) $titleTemplate, $variables))
            ?: $this->normalizeText($item->title);
        $title = $this->normalizeSeoTitle($title, $siteName, $separator);
        $description = $this->description($item, $settings, $variables);

        if (filled($effective['title'] ?? null)) {
            $title = $this->normalizeSeoTitle(
                $this->normalizeText((string) $effective['title']),
                $siteName,
                $separator,
            );
        }

        if (filled($effective['description'] ?? null)) {
            $description = $this->nullableText(
                $this->truncateAtWordBoundary((string) $effective['description'], 160),
            );
        }
        $canonicalUrl = $canonicalPath !== null
            ? $this->absoluteUrl($canonicalPath)
            : ($item->seo_canonical_url ?: $this->absoluteUrl($item->canonical_path ?: '/'));
        $robots = $this->robots($item, $settings);
        $featuredImage = $this->featuredImage($item);
        $ogTitle = $this->normalizeText(
            $this->expand((string) ($effective['og_title'] ?? $item->og_title ?: $title), $variables),
        ) ?: $title;
        $ogDescriptionValue = $effective['og_description'] ?? $item->og_description;
        $ogDescription = filled($ogDescriptionValue)
            ? $this->nullableText($this->truncateAtWordBoundary(
                $this->expand((string) $ogDescriptionValue, $variables),
                160,
            ))
            : $description;
        $ogImage = ($effective['og_image'] ?? null)
            ?: $item->og_image_url
            ?: $featuredImage
            ?: ($settings['open_graph_image'] ?? null);
        $ogImage = $this->mediaUrlMapper->absoluteUrl(
            $this->mediaUrlMapper->localizeUrl($ogImage),
        );
        $twitterTitle = $this->normalizeText(
            $this->expand(
                (string) ($effective['twitter_title'] ?? $item->twitter_title ?: $ogTitle),
                $variables,
            ),
        ) ?: $ogTitle;
        $twitterDescriptionValue = $effective['twitter_description'] ?? $item->twitter_description;
        $twitterDescription = filled($twitterDescriptionValue)
            ? $this->nullableText($this->truncateAtWordBoundary(
                $this->expand((string) $twitterDescriptionValue, $variables),
                160,
            ))
            : $ogDescription;

        return new SeoMetadata(
            title: $title,
            description: $description,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            ogTitle: $ogTitle,
            ogDescription: $ogDescription,
            ogImageUrl: $ogImage,
            twitterTitle: $twitterTitle,
            twitterDescription: $twitterDescription,
            twitterImageUrl: $this->mediaUrlMapper->absoluteUrl(
                $this->mediaUrlMapper->localizeUrl(
                    ($effective['twitter_image'] ?? null) ?: $item->twitter_image_url ?: $ogImage,
                ),
            ),
            structuredData: $this->structuredData(
                $item,
                $title,
                $description,
                $canonicalUrl,
                $ogImage,
                $settings,
            ),
        );
    }

    private function setting(string $key): ?string
    {
        return SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', $key)
            ->value('value');
    }

    private function rankMathTitleSettings(): array
    {
        $setting = SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'rank-math-options-titles')
            ->first();

        return is_array($setting?->decodedValue()) ? $setting->decodedValue() : [];
    }

    private function robots(ContentItem $item, array $settings): array
    {
        return ['index', 'follow'];
    }

    private function featuredImage(ContentItem $item): ?string
    {
        if (! $item->featured_media_source_id) {
            return null;
        }

        $asset = MediaAsset::query()
            ->where('source_id', $item->featured_media_source_id)
            ->orderByRaw("CASE source WHEN 'native' THEN 1 WHEN 'wordpress' THEN 2 WHEN 'external' THEN 3 ELSE 9 END")
            ->first();

        return $asset ? $this->mediaUrlMapper->absoluteUrl($asset->public_url) : null;
    }

    private function excerpt(ContentItem $item): string
    {
        $source = $item->excerpt ?: $item->body ?: '';
        $withoutShortcodes = preg_replace('/\[[^\]]+\]/u', ' ', $source) ?: $source;

        return $this->normalizeText(strip_tags($withoutShortcodes));
    }

    private function description(ContentItem $item, array $settings, array $variables): ?string
    {
        if (filled($item->seo_description)) {
            return $this->nullableText(
                $this->truncateAtWordBoundary(
                    $this->expand((string) $item->seo_description, $variables),
                    160,
                ),
            );
        }

        if (filled($item->excerpt)) {
            return $this->nullableText(
                $this->truncateAtWordBoundary($this->excerpt($item), 160),
            );
        }

        $key = 'pt_'.$item->type.'_description';

        $template = array_key_exists($key, $settings) && filled($settings[$key])
            ? (string) $settings[$key]
            : '%title% %sep% %sitedesc%';
        $expanded = $this->expand($template, $variables);

        if (blank($expanded)) {
            $expanded = $this->expand('%title% %sep% %sitedesc%', $variables);
        }

        return $this->nullableText(
            $this->truncateAtWordBoundary(
                $expanded,
                160,
            ),
        );
    }

    private function expand(string $template, array $variables): string
    {
        $expanded = strtr($template, $variables);
        $expanded = preg_replace('/%[a-zA-Z0-9_\-]+%/', '', $expanded) ?: $expanded;

        return trim(preg_replace('/\s+/u', ' ', $expanded) ?: $expanded);
    }

    private function normalizeText(?string $value): string
    {
        $decoded = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $decoded) ?: $decoded);
    }

    private function nullableText(?string $value): ?string
    {
        $normalized = $this->normalizeText($value);

        return $normalized !== '' ? $normalized : null;
    }

    private function truncateAtWordBoundary(string $value, int $length): string
    {
        $value = $this->normalizeText(strip_tags($value));

        if (mb_strlen($value) <= $length) {
            return $value;
        }

        $truncated = mb_substr($value, 0, $length);
        $lastSpace = mb_strrpos($truncated, ' ');

        return rtrim($lastSpace === false ? $truncated : mb_substr($truncated, 0, $lastSpace));
    }

    private function normalizeSeoTitle(
        string $title,
        string $siteName,
        string $separator,
        int $maxLength = 60,
    ): string {
        $title = $this->normalizeText($title);

        if (mb_strlen($title) <= $maxLength) {
            return $title;
        }

        $suffix = trim($separator).' '.$siteName;
        $suffixWithSpacing = ' '.$suffix;

        if (
            $siteName !== ''
            && mb_strlen($suffixWithSpacing) < $maxLength
            && str_ends_with(mb_strtolower($title), mb_strtolower($suffixWithSpacing))
        ) {
            $base = rtrim(mb_substr($title, 0, -mb_strlen($suffixWithSpacing)));

            return $this->truncateAtWordBoundary(
                $base,
                $maxLength - mb_strlen($suffixWithSpacing),
            ).$suffixWithSpacing;
        }

        return $this->truncateAtWordBoundary($title, $maxLength);
    }

    private function structuredData(
        ContentItem $item,
        string $title,
        ?string $description,
        string $canonicalUrl,
        ?string $image,
        array $settings,
    ): array {
        $structuredData = $item->structured_data ?? [];
        $schemas = isset($structuredData['@type']) || isset($structuredData['@context'])
            ? [$structuredData]
            : $structuredData;
        $imported = collect($schemas)
            ->filter(fn ($schema) => is_array($schema) && (isset($schema['@type']) || isset($schema['@context'])))
            ->values()
            ->all();

        if ($imported !== []) {
            return $imported;
        }

        $baseUrl = rtrim((string) config('app.url'), '/').'/';
        $organizationId = $baseUrl.'#organization';
        $websiteId = $baseUrl.'#website';
        $pageId = $canonicalUrl.'#webpage';
        $siteName = $this->normalizeText($this->setting('blogname') ?: config('app.name'));
        $siteDescription = $this->nullableText($this->setting('blogdescription'));
        $organization = array_filter([
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => $this->normalizeText((string) ($settings['knowledgegraph_name'] ?? $siteName)),
            'url' => $baseUrl,
            'logo' => filled($settings['knowledgegraph_logo'] ?? null) ? [
                '@type' => 'ImageObject',
                'url' => $this->mediaUrlMapper->absoluteUrl(
                    $this->mediaUrlMapper->localizeUrl($settings['knowledgegraph_logo']),
                ),
            ] : null,
        ], fn ($value) => filled($value));
        $website = array_filter([
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $baseUrl,
            'name' => $siteName,
            'description' => $siteDescription,
            'publisher' => ['@id' => $organizationId],
        ], fn ($value) => filled($value));
        $page = array_filter([
            '@type' => $item->type === 'post' ? 'Article' : 'WebPage',
            '@id' => $pageId,
            'headline' => $title,
            'description' => $description,
            'url' => $canonicalUrl,
            'mainEntityOfPage' => ['@id' => $pageId],
            'isPartOf' => ['@id' => $websiteId],
            'publisher' => ['@id' => $organizationId],
            'image' => $image,
            'datePublished' => $item->published_at?->toIso8601String(),
            'dateModified' => $item->content_modified_at?->toIso8601String(),
        ], fn ($value) => filled($value));

        return [[
            '@context' => 'https://schema.org',
            '@graph' => [$organization, $website, $page],
        ]];
    }

    private function absoluteUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
