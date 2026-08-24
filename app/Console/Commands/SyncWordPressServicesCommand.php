<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\Landing;
use App\Models\LandingCategory;
use App\Models\MediaAsset;
use App\Models\Slug;
use App\Models\Taxonomy;
use App\Models\Term;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SyncWordPressServicesCommand extends Command
{
    protected $signature = 'cms:sync-wordpress-services
        {--refresh-landing-content : Làm mới nội dung đã ghép từ các custom field landing WordPress}';

    protected $description = 'Đồng bộ toàn bộ WordPress landing và service vào Dịch vụ, giữ slug dịch vụ làm URL gốc.';

    public function handle(): int
    {
        $sourceItems = $this->sourceItems();
        $canonicalItems = $sourceItems
            ->sortBy(fn (ContentItem $item): int => $item->type === 'landing' ? 0 : 1)
            ->unique('slug')
            ->values();
        $canonicalIds = $canonicalItems->pluck('id')->all();
        $aliases = $sourceItems
            ->reject(fn (ContentItem $item): bool => in_array($item->id, $canonicalIds, true))
            ->values();

        $categories = $this->syncCategories();
        $servicesBySlug = [];
        $created = 0;
        $updated = 0;
        $released = 0;

        DB::transaction(function () use ($canonicalItems, $aliases, $categories, &$servicesBySlug, &$created, &$updated, &$released): void {
            foreach ($canonicalItems as $item) {
                $service = Landing::query()->firstOrNew(['legacy_content_item_id' => $item->id]);
                $category = $this->categoryFor($item, $categories);
                $attributes = $this->serviceAttributes($item, $category);

                if (! $service->exists) {
                    $service->fill($attributes);
                    $created++;
                } else {
                    $changed = $this->fillMissingImportedAttributes($service, $attributes);

                    if ($item->type === 'landing' && $this->option('refresh-landing-content')) {
                        $body = $attributes['body'];

                        if ((string) $service->body !== (string) $body) {
                            $service->body = $body;
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        $updated++;
                    }
                }

                $this->releaseRootSlug($item->slug, $service, $released);
                $service->slug = $item->slug;
                $service->save();

                $servicesBySlug[$item->slug] = $service;
            }

            foreach ($aliases as $alias) {
                $service = $servicesBySlug[$alias->slug] ?? null;

                if (! $service) {
                    continue;
                }

                $this->components->warn(sprintf(
                    'Giữ %s là URL dịch vụ chính; /service/%s/ sẽ dẫn về đây.',
                    $service->slug,
                    $alias->slug,
                ));
            }
        });

        $this->components->info(sprintf(
            'Đã đồng bộ %d dịch vụ WordPress (%d tạo mới, %d bổ sung media/danh mục, %d slug nội dung khác đã nhường cho dịch vụ).',
            $canonicalItems->count(),
            $created,
            $updated,
            $released,
        ));

        return self::SUCCESS;
    }

    /** @return Collection<int, ContentItem> */
    private function sourceItems(): Collection
    {
        return ContentItem::query()
            ->where('source', 'wordpress')
            ->whereIn('type', ['landing', 'service'])
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->whereNotNull('slug')
            ->with('terms.taxonomy')
            ->orderBy('id')
            ->get();
    }

    /** @return Collection<int, LandingCategory> */
    private function syncCategories(): Collection
    {
        return Taxonomy::query()
            ->where('source', 'wordpress')
            ->whereIn('key', ['danh-muc-dich-vu', 'landing-cate'])
            ->with('terms')
            ->get()
            ->flatMap(fn (Taxonomy $taxonomy) => $taxonomy->terms)
            ->mapWithKeys(function (Term $term): array {
                $category = LandingCategory::query()->firstOrCreate(
                    ['legacy_term_id' => $term->id],
                    [
                        'name' => $term->name,
                        'description' => $term->description,
                        'is_active' => true,
                        'sort_order' => 0,
                    ],
                );

                if (blank($category->slug) && filled($term->slug)) {
                    $category->slug = $term->slug;
                    $category->save();
                }

                return [$term->id => $category];
            });
    }

    private function categoryFor(ContentItem $item, Collection $categories): ?LandingCategory
    {
        $term = $item->terms->first(fn (Term $term): bool => in_array(
            $term->taxonomy?->key,
            ['danh-muc-dich-vu', 'landing-cate'],
            true,
        ));

        return $term ? $categories->get($term->id) : null;
    }

    /** @return array<string, mixed> */
    private function serviceAttributes(ContentItem $item, ?LandingCategory $category): array
    {
        $featuredMediaId = $this->curatorMediaIds([(int) $item->featured_media_source_id])[0] ?? null;

        return [
            'landing_category_id' => $category?->id,
            'legacy_media_asset_id' => MediaAsset::query()
                ->where('source', 'wordpress')
                ->where('source_id', $item->featured_media_source_id)
                ->value('id'),
            'curator_media_id' => $featuredMediaId,
            'pricing_media_id' => $this->metaMediaIds($item, 'lotus_tvc_pricing_table_upload')[0] ?? null,
            'backstage_gallery' => $this->metaMediaIds($item, 'lotus_tvc_sence'),
            'gallery' => $this->metaMediaIds($item, 'lotus_bonus_img_galley'),
            'title' => $item->title,
            'excerpt' => $item->excerpt ?: Str::limit(trim(strip_tags((string) $item->body)), 260),
            'body' => $this->bodyFor($item),
            'status' => $item->status,
            'is_featured' => false,
            'sort_order' => $item->menu_order ?? 0,
            'seo_title' => $item->seo_title,
            'seo_description' => $item->seo_description,
            'published_at' => $item->published_at,
        ];
    }

    /** @param array<string, mixed> $attributes */
    private function fillMissingImportedAttributes(Landing $service, array $attributes): bool
    {
        $changes = [];

        foreach ([
            'landing_category_id',
            'legacy_media_asset_id',
            'curator_media_id',
            'pricing_media_id',
            'backstage_gallery',
            'gallery',
        ] as $attribute) {
            if (blank($service->getAttribute($attribute)) && filled($attributes[$attribute] ?? null)) {
                $changes[$attribute] = $attributes[$attribute];
            }
        }

        if ($changes === []) {
            return false;
        }

        $service->fill($changes);

        return true;
    }

    private function releaseRootSlug(string $slug, Landing $service, int &$released): void
    {
        $record = Slug::query()
            ->where('slug', $slug)
            ->where('locale', app()->getLocale())
            ->with('sluggable')
            ->first();

        if (! $record || ($record->sluggable instanceof Landing && $record->sluggable->is($service))) {
            return;
        }

        if ($record->sluggable instanceof Landing) {
            throw new \RuntimeException(sprintf(
                'Slug dịch vụ "%s" đang thuộc một dịch vụ khác (#%d).',
                $slug,
                $record->sluggable->id,
            ));
        }

        $owner = $record->sluggable;
        $record->delete();
        $released++;

        $this->components->info(sprintf(
            'Đã chuyển %s #%d khỏi root slug /%s/ để dành cho Dịch vụ.',
            class_basename($owner),
            $owner?->getKey() ?? 0,
            $slug,
        ));
    }

    private function bodyFor(ContentItem $item): ?string
    {
        if ($item->type === 'landing') {
            return $this->landingBodyFor($item);
        }

        if (filled($item->body)) {
            return $item->body;
        }

        return $this->contentToHtml($this->metaText($item, 'lotus_tvc_desicription'));
    }

    /**
     * Layout landing WordPress chỉ render `lotus_tvc_desicription` làm phần nội dung.
     * Các field khác thuộc template (media, video, bảng giá và hậu trường) được hiển thị
     * riêng tại trang dịch vụ Laravel, nên không được trộn vào body.
     */
    private function landingBodyFor(ContentItem $item): ?string
    {
        return $this->contentToHtml($this->metaText($item, 'lotus_tvc_desicription'));
    }

    private function metaText(ContentItem $item, string $key): ?string
    {
        $value = collect($item->legacy_meta[$key] ?? [])
            ->flatten()
            ->first(fn (mixed $value): bool => is_string($value) && filled(trim($value)));

        return is_string($value) ? trim($value) : null;
    }

    private function paragraphsToHtml(?string $text): ?string
    {
        if (blank($text)) {
            return null;
        }

        $paragraphs = collect(preg_split('/\R{2,}/u', trim($text)) ?: [])
            ->map(fn (string $paragraph): string => trim($paragraph))
            ->filter();

        $html = $paragraphs->map(function (string $paragraph): string {
            $lines = collect(preg_split('/\R/u', $paragraph) ?: [])
                ->map(fn (string $line): string => trim($line))
                ->filter()
                ->values();

            if ($lines->isNotEmpty() && $lines->every(fn (string $line): bool => Str::startsWith($line, ['-', '–', '•']))) {
                return '<ul>'.$lines
                    ->map(fn (string $line): string => '<li>'.e(trim((string) preg_replace('/^[-–•]\s*/u', '', $line))).'</li>')
                    ->implode('').'</ul>';
            }

            return '<p>'.nl2br(e($paragraph)).'</p>';
        })->implode("\n");

        return filled(strip_tags($html)) ? $html : null;
    }

    private function contentToHtml(?string $content): ?string
    {
        if (blank($content)) {
            return null;
        }

        // Một số landing lưu nguyên đoạn HTML từ WordPress; escape sẽ làm chữ <p>, <h2>
        // hiện ra trên giao diện thay vì render đúng cấu trúc gốc.
        if (preg_match('/<\/?[a-z][^>]*>/iu', $content) === 1) {
            return trim($content);
        }

        return $this->paragraphsToHtml($content);
    }

    /** @return list<int> */
    private function metaMediaIds(ContentItem $item, string $metaKey): array
    {
        return $this->curatorMediaIds(
            collect($item->legacy_meta[$metaKey] ?? [])
                ->flatten()
                ->filter(fn (mixed $id): bool => is_numeric($id))
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values()
                ->all(),
        );
    }

    /** @param list<int> $sourceIds
     *  @return list<int>
     */
    private function curatorMediaIds(array $sourceIds): array
    {
        if ($sourceIds === []) {
            return [];
        }

        $pathsBySourceId = MediaAsset::query()
            ->where('source', 'wordpress')
            ->whereIn('source_id', $sourceIds)
            ->pluck('file_path', 'source_id');
        $mediaIdsByPath = Media::query()
            ->where('disk', 'public')
            ->whereIn('path', $pathsBySourceId->filter()->values())
            ->pluck('id', 'path');

        return collect($sourceIds)
            ->map(fn (int $sourceId): ?int => $mediaIdsByPath->get($pathsBySourceId->get($sourceId)))
            ->filter()
            ->values()
            ->all();
    }
}
