<?php

namespace App\Support\Seo;

use App\Models\ContentItem;
use App\Services\WordPress\WordPressMediaUrlMapper;

class SeoMaterializer
{
    public function __construct(
        private readonly SeoMetadataBuilder $builder,
        private readonly WordPressMediaUrlMapper $mediaUrlMapper,
    ) {}

    public function materialize(bool $dryRun = false): array
    {
        $stats = ['targeted' => 0, 'changed' => 0, 'unchanged' => 0];

        ContentItem::query()
            ->where('status', 'published')
            ->whereIn('type', config('wordpress.public_types', []))
            ->whereNotNull('canonical_path')
            ->chunkById(100, function ($items) use (&$stats, $dryRun): void {
                foreach ($items as $item) {
                    $stats['targeted']++;
                    $this->materializeItem($item, $dryRun)
                        ? $stats['changed']++
                        : $stats['unchanged']++;
                }
            });

        return $stats;
    }

    public function materializeItem(ContentItem $item, bool $dryRun = false): bool
    {
        $metadata = $this->builder->forContent($item, useMaterialized: false);
        $effective = [
            'title' => $metadata->title,
            'description' => $metadata->description,
            'og_title' => $metadata->ogTitle,
            'og_description' => $metadata->ogDescription,
            'og_image' => $this->domainNeutralUrl($metadata->ogImageUrl),
            'twitter_title' => $metadata->twitterTitle,
            'twitter_description' => $metadata->twitterDescription,
            'twitter_image' => $this->domainNeutralUrl($metadata->twitterImageUrl),
        ];
        $hash = hash('sha256', json_encode(
            $effective,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ));

        if ($item->effective_seo_hash === $hash) {
            return false;
        }

        if (! $dryRun) {
            $item->forceFill([
                'effective_seo' => $effective,
                'effective_seo_hash' => $hash,
                'seo_materialized_at' => now(),
                'needs_seo_review' => blank($effective['title'])
                    || blank($effective['description'])
                    || blank($effective['og_image']),
            ])->save();
        }

        return true;
    }

    private function domainNeutralUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        if (str_starts_with($url, $appUrl.'/')) {
            return '/'.ltrim(substr($url, strlen($appUrl)), '/');
        }

        return $this->mediaUrlMapper->localizeUrl($url);
    }
}
