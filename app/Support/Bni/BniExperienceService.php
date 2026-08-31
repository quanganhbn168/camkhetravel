<?php

namespace App\Support\Bni;

use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Support\Media\MediaUrl;
use Illuminate\Support\Collection;

class BniExperienceService
{
    /** @return array<string, mixed> */
    public function handover(): array
    {
        $event = $this->event('handover');
        $chapters = $event?->chapters->where('is_active', true)->values() ?? collect();
        $heroImageUrl = MediaUrl::versioned($event?->heroMedia);
        $videoPosterUrl = MediaUrl::versioned($event?->videoPosterMedia) ?: $heroImageUrl;
        $articles = BniArticle::query()
            ->published()
            ->with(['chapter', 'coverMedia'])
            ->whereIn('type', ['event', 'chapter'])
            ->when($event, fn ($query) => $query->where(fn ($query) => $query->where('bni_event_id', $event->id)->orWhereNull('bni_event_id')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get();

        return [
            'event' => $event,
            'heroImageUrl' => $heroImageUrl,
            'eventVideo' => [
                'media_url' => MediaUrl::versioned($event?->videoMedia),
                'external_url' => $event?->video_url,
                'poster_url' => $videoPosterUrl,
            ],
            'registration' => [
                'label' => $event?->registration_label ?: 'Đăng ký ngay',
                'url' => $event?->registration_url ?: '#dang-ky',
            ],
            'chapters' => $chapters->map(fn ($chapter): array => [
                'name' => $chapter->name,
                'slug' => $chapter->slug,
                'short_name' => $chapter->short_name ?: $chapter->name,
                'description' => $chapter->description,
                'logo_url' => MediaUrl::versioned($chapter->logoMedia),
                'cover_url' => MediaUrl::versioned($chapter->coverMedia) ?: $videoPosterUrl,
                'video_media_url' => MediaUrl::versioned($chapter->videoMedia),
                'video_external_url' => $chapter->video_url,
            ]),
            'purposes' => $event?->purposes->map(fn ($purpose): array => [
                'title' => $purpose->title,
                'description' => $purpose->description,
                'icon' => $purpose->icon,
            ]) ?? collect(),
            'scheduleDays' => $this->scheduleDays($event),
            'activities' => $event?->activities->where('is_active', true)->map(fn ($activity): array => [
                'type' => $activity->type,
                'title' => $activity->title,
                'description' => $activity->description,
                'image_url' => MediaUrl::versioned($activity->media),
                'link_url' => $activity->link_url,
            ])->values() ?? collect(),
            'articles' => $articles->map(fn (BniArticle $article): array => $this->articleCard($article)),
            'galleries' => $event?->galleries->where('is_active', true)->map(fn ($item): array => [
                'id' => $item->id,
                'group' => $item->group,
                'title' => $item->title,
                'caption' => $item->caption,
                'image_url' => MediaUrl::versioned($item->media),
                'chapter' => $item->chapter?->short_name ?: $item->chapter?->name,
                'source' => BniGalleryItem::sourceOptions()[$item->source] ?? $item->source,
            ])->values() ?? collect(),
        ];
    }

    /** @return array<string, mixed> */
    public function pickleball(): array
    {
        $event = $this->event('pickleball');
        $settings = $event?->settings ?? [];
        $articles = BniArticle::query()
            ->published()
            ->with(['chapter', 'coverMedia'])
            ->where('type', 'pickleball')
            ->when($event, fn ($query) => $query->where('bni_event_id', $event->id))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(6)
            ->get();

        return [
            'event' => $event,
            'heroImageUrl' => MediaUrl::versioned($event?->heroMedia),
            'scheduleDays' => $this->scheduleDays($event),
            'articles' => $articles->map(fn (BniArticle $article): array => $this->articleCard($article)),
            'chapters' => BniChapter::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'short_name']),
            'pickleballContent' => [
                'countdown_label' => $settings['countdown_label'] ?? 'Đếm ngược đến giải đấu',
                'prizes_title' => $settings['prizes_title'] ?? 'Cơ cấu giải thưởng',
                'prizes_description' => $settings['prizes_description'] ?? null,
                'prizes' => collect($settings['prizes'] ?? [])->filter(fn ($prize): bool => is_array($prize) && filled($prize['title'] ?? null))->values(),
                'rules_title' => $settings['rules_title'] ?? 'Thể lệ giải đấu',
                'rules' => $settings['rules'] ?? null,
                'registration_title' => $settings['registration_title'] ?? 'Đăng ký tham gia',
                'registration_description' => $settings['registration_description'] ?? 'Đăng ký để Ban tổ chức sắp xếp bảng đấu, thông tin check-in và hỗ trợ phù hợp.',
            ],
        ];
    }

    private function event(string $type): ?BniEvent
    {
        return BniEvent::query()
            ->published()
            ->where('type', $type)
            ->with([
                'heroMedia',
                'videoMedia',
                'videoPosterMedia',
                'chapters.logoMedia',
                'chapters.coverMedia',
                'chapters.videoMedia',
                'purposes',
                'scheduleItems',
                'activities.media',
                'galleries' => fn ($query) => $query
                    ->published()
                    ->with(['media', 'chapter'])
                    ->orderBy('sort_order'),
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->first();
    }

    /** @return Collection<int, array{number: int, label: string, items: Collection<int, array<string, mixed>>}> */
    private function scheduleDays(?BniEvent $event): Collection
    {
        if (! $event) {
            return collect();
        }

        return $event->scheduleItems
            ->groupBy('day_number')
            ->map(fn (Collection $items, int|string $day): array => [
                'number' => (int) $day,
                'label' => 'Ngày '.(int) $day,
                'items' => $items->map(fn ($item): array => [
                    'time' => collect([$item->starts_at ? substr((string) $item->starts_at, 0, 5) : null, $item->ends_at ? substr((string) $item->ends_at, 0, 5) : null])->filter()->implode(' – '),
                    'title' => $item->title,
                    'stage' => $item->stage,
                    'description' => $item->description,
                    'result' => $item->result,
                    'location' => $item->location,
                ]),
            ])
            ->values();
    }

    /** @return array<string, mixed> */
    private function articleCard(BniArticle $article): array
    {
        return [
            'title' => $article->title,
            'slug' => $article->slug,
            'excerpt' => $article->excerpt,
            'type' => $article->type,
            'chapter' => $article->chapter?->short_name ?: $article->chapter?->name,
            'published_at' => $article->published_at,
            'image_url' => MediaUrl::versioned($article->coverMedia),
            'featured' => $article->is_featured,
        ];
    }
}
