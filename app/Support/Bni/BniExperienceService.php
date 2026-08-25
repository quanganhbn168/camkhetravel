<?php

namespace App\Support\Bni;

use App\Models\BniArticle;
use App\Models\BniEvent;
use App\Support\Media\MediaUrl;
use Illuminate\Support\Collection;

class BniExperienceService
{
    /** @return array<string, mixed> */
    public function handover(): array
    {
        $event = $this->event('handover');
        $chapters = $event?->chapters->where('is_active', true)->values() ?? collect();
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
            'heroImageUrl' => MediaUrl::versioned($event?->heroMedia),
            'chapters' => $chapters->map(fn ($chapter): array => [
                'name' => $chapter->name,
                'short_name' => $chapter->short_name ?: $chapter->name,
                'description' => $chapter->description,
                'logo_url' => MediaUrl::versioned($chapter->logoMedia),
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
                'group' => $item->group,
                'title' => $item->title,
                'caption' => $item->caption,
                'image_url' => MediaUrl::versioned($item->media),
            ])->values() ?? collect(),
        ];
    }

    /** @return array<string, mixed> */
    public function pickleball(): array
    {
        $event = $this->event('pickleball');
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
        ];
    }

    private function event(string $type): ?BniEvent
    {
        return BniEvent::query()
            ->published()
            ->where('type', $type)
            ->with([
                'heroMedia',
                'chapters.logoMedia',
                'purposes',
                'scheduleItems',
                'activities.media',
                'galleries.media',
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
