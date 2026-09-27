<?php

namespace App\Support\Homepage;

use App\Settings\HomepageSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class HomepageContent
{
    public const TYPES = ['trip' => 'Chuyến đi riêng', 'partner' => 'Hợp tác đối tác', 'wedding' => 'Xe cưới'];

    public function prepare(HomepageSettings $settings, Collection $services): array
    {
        $ids = collect($settings->audience_groups)->pluck('media_id')
            ->merge(collect($settings->fleet_types)->pluck('media_id'))
            ->push($settings->consultation_media_id)->filter()->unique();
        $media = Media::whereIn('id', $ids)->get()->keyBy('id');
        $groups = collect($settings->audience_groups)->filter(fn ($item) => is_array($item) && isset(self::TYPES[$item['key'] ?? '']) && filled($item['title'] ?? null))
            ->unique('key')->map(function ($item) use ($services, $media) {
                $selected = collect($item['service_ids'] ?? [])->map(fn ($id) => $services->firstWhere('id', (int) $id))->filter()->values();
                $selected->each(fn ($service) => $service->setAttribute('quote_type', $item['key']));
                return [...$item, 'image' => $this->image($media->get($item['media_id'] ?? null)),
                    'services' => $selected, 'highlights' => $this->lines($item['highlights'] ?? ''),
                    'anchor' => ['trip' => 'chuyen-di-rieng', 'partner' => 'doi-tac', 'wedding' => 'xe-cuoi'][$item['key']],
                    'icon' => ['trip' => 'car', 'partner' => 'handshake', 'wedding' => 'heart'][$item['key']],
                ];
            })->values();
        $fleet = collect($settings->fleet_types)->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))->map(fn ($item) => [
            ...$item, 'code' => $item['code'] ?? '', 'description' => $item['description'] ?? '',
            'image' => $this->image($media->get($item['media_id'] ?? null)),
            'features' => $this->lines($item['features'] ?? ''),
            'detail_text' => trim(($item['description'] ?? '').' '.$this->lines($item['features'] ?? '')->implode('. ').' '.collect(['Sức chứa' => $item['capacity'] ?? '', 'Hành lý' => $item['luggage'] ?? '', 'Tiện ích' => $item['amenities'] ?? ''])->filter()->map(fn ($value, $label) => $label.': '.$value)->implode('. ')),
            'details' => collect(['Sức chứa' => $item['capacity'] ?? '', 'Hành lý' => $item['luggage'] ?? '', 'Tiện ích' => $item['amenities'] ?? ''])->filter(),
        ])->values();
        $routes = collect($settings->route_items)->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))->map(function ($item) use ($services) {
            $service = $services->firstWhere('id', (int) ($item['service_id'] ?? 0));
            return [...$item, 'service' => $service, 'url' => $service ? route('slug.show', ['slug' => $service->slug]) : null];
        });
        return ['audienceGroups' => $groups, 'fleetTypes' => $fleet, 'routeItems' => $routes,
            'sectionContent' => $settings->section_content,
            'consultationImage' => $this->image($media->get($settings->consultation_media_id)),
        ];
    }

    public function image(?Media $media): array
    {
        $isImage = $media && str_starts_with((string) $media->type, 'image/');
        $url = $isImage ? MediaUrl::versioned($media) : null;
        $small = null;
        if ($isImage && $media->disk === 'public' && $media->directory === 'media/homepage/v1') {
            $path = preg_replace('/\.webp$/', '-768.webp', $media->path);
            if ($path !== $media->path && Storage::disk('public')->exists($path)) {
                $small = Storage::disk('public')->url($path).'?v='.($media->updated_at?->timestamp ?? $media->id);
            }
        }
        return ['url' => $url ?: asset('images/no-image.svg'), 'small_url' => $small, 'alt' => $isImage ? $media->alt : '', 'illustrative' => $isImage && $media->directory === 'media/homepage/v1'];
    }

    private function lines(string $text): Collection
    {
        return collect(preg_split('/\R/u', $text))->map(fn ($line) => trim($line))->filter()->values();
    }
}
