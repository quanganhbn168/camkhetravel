<?php

namespace App\Support\Projects;

use App\Models\Project;
use Awcodes\Curator\Models\Media;

final class ProjectDetailContent
{
    public const ICONS = [
        'fa-industry' => 'Nhà máy', 'fa-clipboard-check' => 'Kiểm tra',
        'fa-stopwatch' => 'Tiến độ', 'fa-gears' => 'Hệ thống',
        'fa-fire-flame-curved' => 'Ngọn lửa', 'fa-shower' => 'Sprinkler',
        'fa-life-ring' => 'Cuộn vòi', 'fa-faucet-drip' => 'Bơm / cấp nước',
        'fa-fan' => 'Quạt hút', 'fa-sliders' => 'Điều khiển',
        'fa-shield-halved' => 'An toàn', 'fa-gear' => 'Vận hành',
        'fa-users' => 'Con người', 'fa-chart-column' => 'Hiệu quả',
        'fa-bolt' => 'Điện', 'fa-building' => 'Tòa nhà',
    ];

    public static function make(Project $project): array
    {
        $details = $project->details ?? [];
        $ids = [];
        array_walk_recursive($details, function ($value, $key) use (&$ids): void {
            if (in_array($key, ['media_id', 'banner_media_id', 'avatar_media_id', 'background_media_id'], true) && is_numeric($value)) {
                $ids[] = (int) $value;
            }
        });
        $galleryIds = self::mediaIds($project->gallery ?? []);
        $constructionIds = self::mediaIds(data_get($details, 'construction.images', []));
        $media = Media::whereKey(array_unique([...$ids, ...$galleryIds, ...$constructionIds]))->get()->keyBy('id');
        $url = fn ($id) => is_numeric($id) ? $media->get((int) $id)?->url : null;
        $images = fn (array $ids) => collect($ids)->map(fn ($id) => $media->get($id))
            ->filter()->map(fn ($image) => ['url' => $image->url, 'alt' => $image->alt ?: $project->title])->values()->all();
        $details['hero'] = [...($details['hero'] ?? []), 'image_url' => $url(data_get($details, 'hero.banner_media_id'))];
        foreach (['challenges', 'scope', 'results'] as $section) {
            $details[$section] ??= [];
            $details[$section]['items'] = collect(data_get($details, $section.'.items', []))
                ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))
                ->map(fn ($item) => [...$item,
                    'icon' => array_key_exists($item['icon'] ?? '', self::ICONS) ? $item['icon'] : null,
                    'image_url' => $url($item['media_id'] ?? null),
                ])->values()->all();
        }
        $details['solution'] ??= [];
        $details['solution']['image_url'] = $url(data_get($details, 'solution.media_id'));
        $details['solution']['items'] = collect(data_get($details, 'solution.items', []))->filter(fn ($item) => filled($item['text'] ?? null))->values()->all();
        $details['testimonial'] ??= [];
        $details['testimonial']['avatar_url'] = $url(data_get($details, 'testimonial.avatar_media_id'));
        $details['testimonial']['background_url'] = $url(data_get($details, 'testimonial.background_media_id'));
        $details['gallery'] = $images($galleryIds);
        $details['construction'] ??= [];
        $details['construction']['images'] = $images($constructionIds);
        $details['overview'] ??= [];
        $details['overview']['body_html'] = (string) str((string) $project->body)->sanitizeHtml();
        $details['overview']['facts'] = collect([
            ['label' => 'Chủ đầu tư', 'value' => $project->client_name],
            ['label' => 'Địa điểm', 'value' => data_get($details, 'hero.location')],
            ['label' => 'Loại công trình', 'value' => $project->industry],
            ['label' => 'Quy mô', 'value' => data_get($details, 'hero.area')],
            ['label' => 'Thời gian thực hiện', 'value' => data_get($details, 'overview.period')],
            ...(data_get($details, 'overview.facts') ?? []),
        ])->filter(fn ($item) => filled($item['label'] ?? null) && filled($item['value'] ?? null))->values()->all();

        return $details;
    }

    private static function mediaIds(?array $items): array
    {
        return collect($items ?? [])->map(fn ($item) => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
