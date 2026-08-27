<?php

namespace App\Console\Commands;

use App\Models\ContentItem;
use App\Models\MediaAsset;
use App\Models\Partner;
use App\Models\Testimonial;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncWordPressHomepageContentCommand extends Command
{
    protected $signature = 'cms:sync-wordpress-homepage-content
        {--refresh : Làm mới nội dung đã đồng bộ từ WordPress, giữ nguyên trạng thái hiển thị quản trị}';

    protected $description = 'Đồng bộ logo đối tác và phản hồi khách hàng WordPress vào nội dung trang chủ';

    public function handle(): int
    {
        $items = $this->sourceItems();

        if ($items->isEmpty()) {
            $this->components->error('Chưa có dữ liệu đối tác hoặc phản hồi WordPress đã import để đồng bộ.');

            return self::FAILURE;
        }

        $curatorMediaIds = $this->curatorMediaIds($this->mediaSourceIds($items));
        $created = ['partners' => 0, 'testimonials' => 0];
        $updated = ['partners' => 0, 'testimonials' => 0];
        $missingMedia = [];

        DB::transaction(function () use ($items, $curatorMediaIds, &$created, &$updated, &$missingMedia): void {
            $partnerPosition = 1;
            $testimonialPosition = 1;

            foreach ($items as $item) {
                if ($item->type === 'partner') {
                    $mediaSourceId = $this->metaId($item, 'lotus_partner_upload');
                    $this->syncPartner(
                        $item,
                        $curatorMediaIds[$mediaSourceId] ?? null,
                        $partnerPosition++,
                        $created,
                        $updated,
                    );

                    if ($mediaSourceId && ! isset($curatorMediaIds[$mediaSourceId])) {
                        $missingMedia[] = "Đối tác {$item->title} (media WP #{$mediaSourceId})";
                    }

                    continue;
                }

                $mediaSourceId = (int) ($item->featured_media_source_id ?: $this->metaId($item, '_thumbnail_id'));
                $this->syncTestimonial(
                    $item,
                    $curatorMediaIds[$mediaSourceId] ?? null,
                    $testimonialPosition++,
                    $created,
                    $updated,
                );

                if ($mediaSourceId && ! isset($curatorMediaIds[$mediaSourceId])) {
                    $missingMedia[] = "Phản hồi {$item->title} (media WP #{$mediaSourceId})";
                }
            }
        });

        $this->components->info(sprintf(
            'Đối tác: %d tạo mới, %d cập nhật; phản hồi: %d tạo mới, %d cập nhật.',
            $created['partners'],
            $updated['partners'],
            $created['testimonials'],
            $updated['testimonials'],
        ));

        foreach ($missingMedia as $item) {
            $this->components->warn('Chưa nối được Curator: '.$item.'. Nội dung vẫn đã được đồng bộ.');
        }

        return self::SUCCESS;
    }

    /** @return Collection<int, ContentItem> */
    private function sourceItems(): Collection
    {
        return ContentItem::query()
            ->where('source', 'wordpress')
            ->whereIn('type', ['partner', 'us_testimonial'])
            ->where('status', 'published')
            ->orderBy('type')
            ->orderBy('id')
            ->get();
    }

    /** @param Collection<int, ContentItem> $items
     *  @return list<int>
     */
    private function mediaSourceIds(Collection $items): array
    {
        return $items
            ->map(fn (ContentItem $item): ?int => $item->type === 'partner'
                ? $this->metaId($item, 'lotus_partner_upload')
                : (int) ($item->featured_media_source_id ?: $this->metaId($item, '_thumbnail_id')))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @param list<int> $sourceIds
     *  @return array<int, int>
     */
    private function curatorMediaIds(array $sourceIds): array
    {
        if ($sourceIds === []) {
            return [];
        }

        $pathsBySourceId = MediaAsset::query()
            ->where('source', 'wordpress')
            ->whereIn('source_id', $sourceIds)
            ->where('disk', 'public')
            ->whereNotNull('file_path')
            ->pluck('file_path', 'source_id');

        $mediaIdsByPath = Media::query()
            ->where('disk', 'public')
            ->whereIn('path', $pathsBySourceId->values())
            ->pluck('id', 'path');

        return collect($sourceIds)
            ->mapWithKeys(function (int $sourceId) use ($pathsBySourceId, $mediaIdsByPath): array {
                $path = $pathsBySourceId->get($sourceId);
                $mediaId = $path ? $mediaIdsByPath->get($path) : null;

                return $mediaId ? [$sourceId => (int) $mediaId] : [];
            })
            ->all();
    }

    /** @param array<string, int> $created
     *  @param array<string, int> $updated
     */
    private function syncPartner(
        ContentItem $item,
        ?int $curatorMediaId,
        int $sortOrder,
        array &$created,
        array &$updated,
    ): void {
        $partner = Partner::query()->firstOrNew(['legacy_content_item_id' => $item->id]);
        $attributes = [
            'legacy_content_item_id' => $item->id,
            'curator_media_id' => $curatorMediaId,
            'name' => trim((string) $item->title),
            'website_url' => null,
            'is_active' => true,
            'sort_order' => $sortOrder,
        ];

        if (! $partner->exists) {
            $partner->fill($attributes)->save();
            $created['partners']++;

            return;
        }

        if (! $this->option('refresh')) {
            return;
        }

        $partner->fill([
            'name' => $attributes['name'],
            'sort_order' => $sortOrder,
            ...($curatorMediaId ? ['curator_media_id' => $curatorMediaId] : []),
        ])->save();
        $updated['partners']++;
    }

    /** @param array<string, int> $created
     *  @param array<string, int> $updated
     */
    private function syncTestimonial(
        ContentItem $item,
        ?int $curatorMediaId,
        int $sortOrder,
        array &$created,
        array &$updated,
    ): void {
        $testimonial = Testimonial::query()->firstOrNew(['legacy_content_item_id' => $item->id]);
        $attributes = [
            'legacy_content_item_id' => $item->id,
            'curator_media_id' => $curatorMediaId,
            'client_name' => $this->metaText($item, 'us_testimonial_author') ?: trim((string) $item->title),
            'client_role' => $this->metaText($item, 'us_testimonial_role'),
            'company_name' => $this->metaText($item, 'us_testimonial_company'),
            'quote' => $this->plainText($item->body) ?: 'Phản hồi từ khách hàng THT Media.',
            'rating' => $this->rating($item),
            'is_active' => true,
            'sort_order' => $sortOrder,
        ];

        if (! $testimonial->exists) {
            $testimonial->fill($attributes)->save();
            $created['testimonials']++;

            return;
        }

        if (! $this->option('refresh')) {
            return;
        }

        $testimonial->fill([
            'client_name' => $attributes['client_name'],
            'client_role' => $attributes['client_role'],
            'company_name' => $attributes['company_name'],
            'quote' => $attributes['quote'],
            'rating' => $attributes['rating'],
            'sort_order' => $sortOrder,
            ...($curatorMediaId ? ['curator_media_id' => $curatorMediaId] : []),
        ])->save();
        $updated['testimonials']++;
    }

    private function metaId(ContentItem $item, string $key): ?int
    {
        $value = $this->metaText($item, $key);

        return is_numeric($value) ? (int) $value : null;
    }

    private function rating(ContentItem $item): ?int
    {
        $rating = $this->metaId($item, 'us_testimonial_rating');

        return $rating === null ? null : min(5, max(1, $rating));
    }

    private function metaText(ContentItem $item, string $key): ?string
    {
        $value = collect($item->legacy_meta[$key] ?? [])
            ->flatten()
            ->first(fn (mixed $value): bool => is_string($value) && filled(trim($value)));

        return is_string($value) ? trim($value) : null;
    }

    private function plainText(?string $content): ?string
    {
        if (blank($content)) {
            return null;
        }

        $content = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = Str::squish($content);

        return filled($content) ? $content : null;
    }
}
