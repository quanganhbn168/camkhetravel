<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Settings\AboutSettings;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Media\MediaUrl as CuratorMediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly AboutSettings $settings,
        private readonly HomepageSettings $homepage,
        private readonly WebsiteSettings $website,
        private readonly LanguageCatalog $languages,
    ) {}

    public function __invoke(): View
    {
        $managed = [
            'title' => $this->translated($this->settings->page_title),
            'intro' => $this->translated($this->settings->page_intro),
            'story_title' => $this->translated($this->settings->story_title),
            'story' => $this->translated($this->settings->story),
            'history' => $this->translated($this->settings->history),
            'history_title' => $this->translated($this->settings->history_title),
            'history_description' => $this->translated($this->settings->history_description),
            'mission' => $this->translated($this->settings->mission),
            'vision' => $this->translated($this->settings->vision),
            'core_values' => $this->translated($this->settings->core_values),
            'principles_title' => $this->translated($this->settings->principles_title),
            'services_title' => $this->translated($this->settings->services_title),
            'services_link_label' => $this->translated($this->settings->services_link_label),
            'stats_title' => $this->translated($this->settings->stats_title),
            'office_title' => $this->translated($this->settings->office_title),
            'office_description' => $this->translated($this->settings->office_description),
            'cta_title' => $this->translated($this->settings->cta_title),
            'cta_button_label' => $this->translated($this->settings->cta_button_label),
        ];
        $historyTimeline = $this->historyTimeline($this->settings->history_timeline ?? []);
        $hasManagedContent = collect($managed)->filter(fn (string $value): bool => filled($value))->isNotEmpty()
            || $historyTimeline->isNotEmpty();

        abort_if(! $hasManagedContent, 404);

        $officeGalleryIds = collect($this->settings->office_gallery ?? [])
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $mediaIds = collect([
            $this->settings->default_image_media_id,
            $this->settings->story_image_media_id,
            $this->settings->video_poster_media_id,
            $this->settings->core_values_image_media_id,
            $this->settings->office_image_media_id,
        ])->merge($officeGalleryIds)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');
        $fallbackImageUrl = $this->sectionImageUrl($media, $this->settings->default_image_media_id);
        $about = $managed;
        $about['title'] = $managed['title'] ?: $this->website->company_name;
        $about['image_url'] = $fallbackImageUrl;
        $about['story_image_url'] = $this->sectionImageUrl($media, $this->settings->story_image_media_id, $fallbackImageUrl);
        $about['core_values_image_url'] = $this->sectionImageUrl($media, $this->settings->core_values_image_media_id, $fallbackImageUrl);
        $about['office_image_url'] = $this->sectionImageUrl($media, $this->settings->office_image_media_id, $fallbackImageUrl);
        $about['office_gallery'] = $officeGalleryIds
            ->map(function (int $mediaId, int $index) use ($media, $managed): ?array {
                $item = $media->get($mediaId);
                $url = $item && str_starts_with((string) $item->type, 'image/')
                    ? CuratorMediaUrl::versioned($item)
                    : null;

                return $url ? [
                    'url' => $url,
                    'alt' => trim((string) ($item->alt ?? $item->title ?? '')) ?: ($managed['office_title'] ?: 'Văn phòng '.$this->website->site_name).' — ảnh '.($index + 1),
                ] : null;
            })
            ->filter()
            ->values();
        if ($about['office_gallery']->isEmpty() && $about['office_image_url']) {
            $about['office_gallery'] = collect([[
                'url' => $about['office_image_url'],
                'alt' => $managed['office_title'] ?: 'Văn phòng '.$this->website->site_name,
            ]]);
        }
        $videoPosterUrl = $this->sectionImageUrl($media, $this->settings->video_poster_media_id, $fallbackImageUrl);
        $about['video'] = $this->introVideo($videoPosterUrl);
        $services = Service::query()
            ->published()
            ->with(['curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
        foreach ($services as $service) {
            $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia));
        }
        $description = $about['intro'] ?: trim(strip_tags($about['story'])) ?: $about['title'];

        return view('frontend.about', compact('about') + [
            'historyTimeline' => $historyTimeline,
            'services' => $services,
            'stats' => $this->stats(),
            'seo' => $this->seo->listing(
                $about['title'].' | '.$this->seo->siteName(),
                $description,
                LocalizedUrl::route('about'),
            ),
        ]);
    }

    /** @param array<int, mixed> $items */
    private function historyTimeline(array $items): Collection
    {
        $items = collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['year'] ?? null))
            ->values();
        $mediaIds = $items
            ->map(fn (array $item): mixed => $item['media_id'] ?? null)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');

        return $items
            ->map(function (array $item, int $index) use ($media): array {
                $title = $this->localizedValue($item['title'] ?? null);
                $description = $this->localizedValue($item['description'] ?? null);
                $mediaId = is_numeric($item['media_id'] ?? null) ? (int) $item['media_id'] : null;

                return [
                    'id' => 'history-'.$index,
                    'year' => trim((string) $item['year']),
                    'title' => $title,
                    'description' => $description,
                    'image_url' => CuratorMediaUrl::versioned($media->get($mediaId)),
                ];
            })
            ->filter(fn (array $item): bool => filled($item['title']) || filled($item['description']))
            ->values();
    }

    private function localizedValue(mixed $value): string
    {
        return is_array($value) ? $this->translated($value) : trim((string) $value);
    }

    private function stats(): Collection
    {
        $configured = collect($this->settings->page_stats ?? [])
            ->filter(fn (mixed $stat): bool => is_array($stat) && filled($stat['value'] ?? null) && filled($stat['label'] ?? null))
            ->take(4)
            ->map(fn (array $stat): array => [
                'value' => trim((string) ($stat['prefix'] ?? '').(string) ($stat['value'] ?? '').(string) ($stat['suffix'] ?? '')),
                'label' => (string) $stat['label'],
            ]);

        if ($configured->isNotEmpty()) {
            return $configured->values();
        }

        return collect([
            ['value' => (string) Project::query()->published()->count(), 'label' => 'dự án đã thực hiện'],
            ['value' => (string) Service::query()->published()->count(), 'label' => 'dịch vụ đang cung cấp'],
            ['value' => (string) Post::query()->published()->count(), 'label' => 'bài viết chuyên môn'],
        ]);
    }

    /** @param Collection<int, Media> $media */
    private function sectionImageUrl(Collection $media, mixed $mediaId, ?string $fallback = null): ?string
    {
        $item = is_numeric($mediaId) ? $media->get((int) $mediaId) : null;

        return $item && str_starts_with((string) $item->type, 'image/')
            ? CuratorMediaUrl::versioned($item)
            : $fallback;
    }

    /** @return array{source: string, url: string, poster_url: ?string}|null */
    private function introVideo(?string $posterUrl): ?array
    {
        if ($this->settings->video_source === 'youtube') {
            $embedUrl = $this->youtubeEmbedUrl($this->settings->video_youtube_url);

            return $embedUrl ? [
                'source' => 'youtube',
                'url' => $embedUrl,
                'poster_url' => $posterUrl,
            ] : null;
        }

        if ($this->settings->video_source !== 'upload' || ! $this->settings->video_media_id) {
            return null;
        }

        $media = Media::query()->find($this->settings->video_media_id);
        $videoUrl = $media && str_starts_with((string) $media->type, 'video/')
            ? CuratorMediaUrl::versioned($media)
            : null;

        return $videoUrl ? [
            'source' => 'upload',
            'url' => $videoUrl,
            'poster_url' => $posterUrl,
        ] : null;
    }

    private function youtubeEmbedUrl(?string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $segments = explode('/', $path);
            $videoId = $path === 'watch'
                ? ($query['v'] ?? null)
                : (in_array($segments[0] ?? null, ['embed', 'shorts', 'live'], true) ? ($segments[1] ?? null) : null);
        }

        if (! is_string($videoId) || preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) !== 1) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?rel=0';
    }

    private function translated(array $content): string
    {
        $locale = app()->getLocale();
        $defaultLocale = $this->languages->defaultCode();
        $fallback = reset($content);

        return (string) ($content[$locale] ?? $content[$defaultLocale] ?? $fallback ?? '');
    }
}
