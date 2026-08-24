<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Models\Landing;
use App\Models\LandingCategory;
use App\Models\Term;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;

class SyncLegacyServiceLandingCommand extends Command
{
    protected $signature = 'cms:sync-legacy-landing
        {source-id=4767 : WordPress source ID of the landing page to publish natively}
        {--landing= : Existing Landing ID to replace with this landing data}';

    protected $description = 'Đưa một landing WordPress sang Landing page, giữ nguyên media đã localize và không ghi đè dữ liệu đã quản trị.';

    public function handle(): int
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('source_id', $this->argument('source-id'))
            ->where('type', 'landing')
            ->with('terms.taxonomy')
            ->first();

        if (! $item) {
            $this->components->error('Không tìm thấy landing WordPress tương ứng.');

            return self::FAILURE;
        }

        $categoryTerm = $item->terms
            ->first(fn (Term $term): bool => $term->taxonomy?->key === 'landing-cate');
        $category = $categoryTerm ? $this->syncCategory($categoryTerm) : null;
        $pricingMediaId = $this->curatorMediaIds($item, 'lotus_tvc_pricing_table_upload')[0] ?? null;
        $backstageGallery = $this->curatorMediaIds($item, 'lotus_tvc_sence');
        $referenceGallery = $this->curatorMediaIds($item, 'lotus_bonus_img_galley');
        $featuredMediaAssetId = MediaAsset::query()
            ->where('source', 'wordpress')
            ->where('source_id', $item->featured_media_source_id)
            ->value('id');
        $featuredMediaId = $this->curatorMediaIdsForSourceIds([(int) $item->featured_media_source_id])[0] ?? null;

        $targetLandingId = $this->option('landing');
        $landing = filled($targetLandingId)
            ? Landing::query()->find($targetLandingId)
            : Landing::query()->firstOrNew(['legacy_content_item_id' => $item->id]);

        if (! $landing) {
            $this->components->error('Không tìm thấy Landing đích để thay bằng dữ liệu này.');

            return self::FAILURE;
        }

        if (! $landing->exists || filled($targetLandingId)) {
            $landing->fill([
                'landing_category_id' => $category?->id,
                'legacy_content_item_id' => $item->id,
                'legacy_media_asset_id' => $featuredMediaAssetId,
                'curator_media_id' => $featuredMediaId,
                'pricing_media_id' => $pricingMediaId,
                'backstage_gallery' => $backstageGallery,
                'gallery' => $referenceGallery,
                'title' => $item->title,
                'excerpt' => $item->excerpt,
                'body' => $this->bodyFor($item),
                'status' => $item->status,
                'is_featured' => false,
                'sort_order' => $item->menu_order ?? 0,
                'seo_title' => $item->seo_title,
                'seo_description' => $item->seo_description,
                'published_at' => $item->published_at,
            ]);
            if (! $landing->exists) {
                $landing->slug = $item->slug;
            }
            $landing->save();
        } else {
            $updates = array_filter([
                'landing_category_id' => $landing->landing_category_id ?: $category?->id,
                'legacy_media_asset_id' => $landing->legacy_media_asset_id ?: $featuredMediaAssetId,
                'curator_media_id' => $landing->curator_media_id ?: $featuredMediaId,
                'pricing_media_id' => $landing->pricing_media_id ?: $pricingMediaId,
                'backstage_gallery' => blank($landing->backstage_gallery) ? $backstageGallery : null,
                'gallery' => blank($landing->gallery) ? $referenceGallery : null,
            ], fn (mixed $value): bool => $value !== null);

            if ($updates !== []) {
                $landing->fill($updates)->save();
            }

            if (blank($landing->slug) && filled($item->slug)) {
                $landing->slug = $item->slug;
                $landing->save();
            }
        }

        $this->components->info(sprintf(
            'Đã đồng bộ Landing #%d: %d ảnh bảng giá, %d ảnh hậu trường, %d ảnh tư liệu.',
            $landing->id,
            $pricingMediaId ? 1 : 0,
            count($backstageGallery),
            count($referenceGallery),
        ));

        return self::SUCCESS;
    }

    private function syncCategory(Term $term): LandingCategory
    {
        $category = LandingCategory::query()->firstOrCreate(
            ['legacy_term_id' => $term->id],
            [
                'name' => $term->name,
                'description' => $term->description,
                'is_active' => true,
                'sort_order' => $term->term_order ?? 0,
            ],
        );

        if (filled($term->slug)) {
            $category->slug = $term->slug;
            $category->save();
        }

        return $category;
    }

    private function bodyFor(ContentItem $item): ?string
    {
        if (filled($item->body)) {
            return $item->body;
        }

        $description = collect($item->legacy_meta['lotus_tvc_desicription'] ?? [])
            ->flatten()
            ->filter(fn (mixed $value): bool => is_string($value) && filled(trim($value)))
            ->map(fn (string $value): string => trim($value))
            ->implode("\n\n");

        if (blank($description)) {
            return null;
        }

        return collect(preg_split('/\R{2,}/u', $description) ?: [])
            ->map(fn (string $paragraph): string => '<p>'.e(trim($paragraph)).'</p>')
            ->filter(fn (string $paragraph): bool => $paragraph !== '<p></p>')
            ->implode("\n");
    }

    /** @return list<int> */
    private function curatorMediaIds(ContentItem $item, string $metaKey): array
    {
        $sourceIds = collect($item->legacy_meta[$metaKey] ?? [])
            ->flatten()
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        return $this->curatorMediaIdsForSourceIds($sourceIds);
    }

    /** @param list<int> $sourceIds
     *  @return list<int>
     */
    private function curatorMediaIdsForSourceIds(array $sourceIds): array
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
