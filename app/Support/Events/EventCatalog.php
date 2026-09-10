<?php

namespace App\Support\Events;

use App\Models\BniEvent;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

class EventCatalog
{
    private ?Collection $nativeEvents = null;

    public function url(BniEvent $event): string
    {
        if ($landingUrl = $this->landingUrl($event)) {
            return $landingUrl;
        }

        $this->nativeEvents ??= BniEvent::query()->published()
            ->whereIn('type', ['handover', 'pickleball'])
            ->orderByDesc('is_featured')->orderByDesc('starts_at')
            ->get(['id', 'type'])->unique('type')->keyBy('type');

        if ($this->nativeEvents->get($event->type)?->id === $event->id) {
            return LocalizedUrl::route($event->type === 'handover' ? 'bni.handover' : 'bni.pickleball');
        }

        return LocalizedUrl::route('bni.events.show', ['event' => $event->slug]);
    }

    public function landingUrl(BniEvent $event): ?string
    {
        $url = trim((string) $event->landing_url);

        if ($url === '') {
            return null;
        }

        if (Str::startsWith($url, '/') && ! Str::startsWith($url, '//')) {
            return $url;
        }

        $scheme = Str::lower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL)
            ? $url
            : null;
    }

    public function present(BniEvent $event): array
    {
        $image = $event->bniMediaUrl('hero');
        if (! $image) {
            $image = $event->slides->where('is_active', true)
                ->map(fn ($slide) => $slide->bniMediaUrl('image'))->first(fn ($url) => filled($url));
        }
        if (! $image && $event->type === 'handover') {
            $image = Vite::asset('resources/images/bni/bni-kv-milk-red.webp');
        }

        $end = $event->ends_at ?? $event->starts_at;
        $status = ! $event->starts_at ? 'Đang cập nhật' : ($end->isPast() ? 'Đã diễn ra' : ($event->starts_at->isPast() ? 'Đang diễn ra' : 'Sắp diễn ra'));

        return [
            'title' => $event->title,
            'url' => $this->url($event),
            'landing_url' => $this->landingUrl($event),
            'image' => $image,
            'category' => match ($event->type) {
                'handover' => 'Kết nối doanh nghiệp',
                'pickleball' => 'Thể thao & giao lưu',
                default => 'Sự kiện',
            },
            'summary' => Str::limit(trim(strip_tags($event->summary ?: $event->content ?: '')), 200),
            'status' => $status,
            'is_past' => $end?->isPast() ?? false,
            'starts_at' => $event->starts_at,
            'date' => $event->starts_at?->format('d.m.Y') ?? 'Thời gian sẽ được cập nhật',
            'venue' => $event->venue ?: $event->address ?: 'Địa điểm sẽ được cập nhật',
        ];
    }
}
