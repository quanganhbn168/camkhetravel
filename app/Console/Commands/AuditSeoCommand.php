<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AuditSeoCommand extends Command
{
    protected $signature = 'seo:audit';

    protected $description = 'Kiểm tra SEO hiệu lực, slug và media đã localize';

    public function handle(): int
    {
        $public = ContentItem::query()
            ->where('status', 'published')
            ->whereIn('type', config('wordpress.public_types', []))
            ->whereNotNull('canonical_path')
            ->get();
        $failures = [];
        $warnings = [];
        $localReferences = [];
        $blankImageAlts = 0;

        foreach ($public as $item) {
            $seo = $item->effective_seo ?? [];

            if (blank($seo['title'] ?? null)) {
                $failures[] = "{$item->canonical_path}: thiếu title";
            } elseif (mb_strlen($seo['title']) > 60) {
                $failures[] = "{$item->canonical_path}: title SEO quá 60 ký tự";
            }

            if (blank($seo['description'] ?? null) || mb_strlen($seo['description']) > 160) {
                $failures[] = "{$item->canonical_path}: description thiếu hoặc quá 160 ký tự";
            }

            if (blank($seo['og_image'] ?? null)) {
                $failures[] = "{$item->canonical_path}: thiếu ảnh OG";
            }

            foreach (['og_title', 'og_description', 'twitter_title', 'twitter_description', 'twitter_image'] as $field) {
                if (blank($seo[$field] ?? null)) {
                    $failures[] = "{$item->canonical_path}: thiếu {$field}";
                }
            }

            $encoded = json_encode($seo, JSON_UNESCAPED_SLASHES);

            if (preg_match('~https?://[^/]+~i', (string) $encoded)) {
                $failures[] = "{$item->canonical_path}: SEO cache còn hostname";
            }

            foreach (['og_image', 'twitter_image'] as $field) {
                $path = $seo[$field] ?? null;

                if (is_string($path) && str_starts_with($path, '/storage/')) {
                    $localReferences[ltrim(substr($path, strlen('/storage/')), '/')] = true;
                }
            }
        }

        $duplicates = $public->groupBy('canonical_path')->filter->has(1);

        foreach ($duplicates as $path => $items) {
            $priorities = [
                'landing' => 1,
                'page' => 2,
                'post' => 3,
                'service' => 4,
                'us_portfolio' => 5,
            ];
            $winner = $items->sortBy(
                fn (ContentItem $item): int => $priorities[$item->type] ?? 9,
            )->first();
            $warnings[] = $path.': trùng canonical từ WordPress, route chỉ dùng source ID '
                .$winner->source_id
                .' (các source IDs: '.$items->pluck('source_id')->implode(', ').')';
        }

        $mediaCount = 0;

        foreach (MediaAsset::query()->whereIn('source', ['wordpress', 'external'])->cursor() as $asset) {
            $mediaCount++;

            if (
                ! $asset->disk
                || ! $asset->file_path
                || $asset->localization_status !== 'localized'
                || blank($asset->checksum_sha256)
                || ! Storage::disk($asset->disk)->exists($asset->file_path)
            ) {
                $failures[] = "Media {$asset->source_id}: chưa có file local";
            }
        }

        foreach (ContentItem::query()
            ->where('status', 'published')
            ->whereIn('type', config('wordpress.public_types', []))
            ->cursor(['source', 'source_id', 'body']) as $item) {
            $reference = $item->source.':'.$item->source_id;
            $body = (string) $item->body;

            if (str_contains($body, 'thtmedia.com.vn/wp-content/uploads/')) {
                $failures[] = "Content {$reference}: còn URL uploads WordPress";
            }

            preg_match_all(
                "~/storage/([^\"'\\s<>)?,#]+)~iu",
                $body,
                $storageMatches,
            );

            foreach ($storageMatches[1] ?? [] as $path) {
                $localReferences[rawurldecode($path)] = true;
            }

            preg_match_all('~<(?:img|source)\b[^>]*>~iu', $body, $mediaTags);

            foreach ($mediaTags[0] ?? [] as $tag) {
                if (preg_match('~https?://~i', html_entity_decode($tag, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) {
                    $failures[] = "Content {$reference}: còn hotlink ảnh ngoài";
                }

                if (str_starts_with(strtolower($tag), '<img')) {
                    $hasAlt = preg_match('~\balt\s*=\s*(["\'])(.*?)\1~isu', $tag, $alt) === 1;

                    if (! $hasAlt || trim(html_entity_decode($alt[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) === '') {
                        $blankImageAlts++;
                    }
                }
            }
        }

        foreach (array_keys($localReferences) as $path) {
            if (! Storage::disk('public')->exists($path)) {
                $failures[] = 'Thiếu file được nội dung/SEO tham chiếu: '.$path;
            }
        }

        if ($blankImageAlts > 0) {
            $warnings[] = $blankImageAlts.' thẻ img chưa đủ ngữ cảnh để điền alt tự động.';
        }

        foreach ($warnings as $warning) {
            $this->warn($warning);
        }

        if ($failures !== []) {
            foreach (array_slice($failures, 0, 50) as $failure) {
                $this->error($failure);
            }

            $this->error('Tổng lỗi: '.count($failures));

            return self::FAILURE;
        }

        $this->info(
            "SEO đạt: {$public->count()} nội dung public, {$mediaCount} media local, "
            .count($localReferences).' đường dẫn media đang được nội dung/SEO sử dụng.',
        );

        return self::SUCCESS;
    }
}
