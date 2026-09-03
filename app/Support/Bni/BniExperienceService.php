<?php

namespace App\Support\Bni;

use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BniExperienceService
{
    /** @return array<string, mixed> */
    public function handover(): array
    {
        $event = $this->event('handover');
        $chapters = $event?->chapters->where('is_active', true)->values() ?? collect();
        $heroImageUrl = MediaUrl::versioned($event?->heroMedia);
        $heroSlides = $event?->slides
            ->where('is_active', true)
            ->filter(fn ($slide): bool => filled(MediaUrl::versioned($slide->media)))
            ->map(function ($slide) use ($event): array {
                $buttonUrl = trim((string) $slide->button_url);

                if ($buttonUrl !== '' && ! Str::startsWith($buttonUrl, ['http://', 'https://', '/', '#'])) {
                    $buttonUrl = '';
                }

                return [
                    'image_url' => MediaUrl::versioned($slide->media),
                    'title' => $slide->title,
                    'description' => $slide->description,
                    'button_label' => $slide->button_label,
                    'button_url' => $buttonUrl ?: null,
                    'alt_text' => $slide->alt_text ?: $slide->media?->alt ?: $slide->media?->title ?: $event?->title,
                    'has_content' => filled($slide->title) || filled($slide->description) || (filled($slide->button_label) && filled($buttonUrl)),
                ];
            })
            ->values() ?? collect();

        if ($heroSlides->isEmpty() && $heroImageUrl) {
            $heroSlides = collect([[
                'image_url' => $heroImageUrl,
                'title' => null,
                'description' => null,
                'button_label' => null,
                'button_url' => null,
                'alt_text' => $event?->heroMedia?->alt ?: $event?->heroMedia?->title ?: $event?->title,
                'has_content' => false,
            ]]);
        }

        $videoPosterUrl = MediaUrl::versioned($event?->videoPosterMedia) ?: $heroImageUrl;
        $registrationUrl = trim((string) $event?->registration_url);

        if ($registrationUrl === '' || $registrationUrl === '#dang-ky') {
            $registrationUrl = LocalizedUrl::route('bni.registrations.create');
        }
        $articles = BniArticle::query()
            ->published()
            ->with(['chapter', 'coverMedia'])
            ->whereIn('type', ['event', 'chapter'])
            ->when($event, fn ($query) => $query->where(fn ($query) => $query->where('bni_event_id', $event->id)->orWhereNull('bni_event_id')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get();
        $galleries = BniGalleryItem::query()
            ->published()
            ->with(['activity', 'event', 'media'])
            ->where(fn ($query) => $query
                ->whereNull('bni_event_id')
                ->orWhereHas('event', fn ($query) => $query->published()->whereIn('type', ['handover', 'pickleball'])))
            ->orderByDesc('approved_at')
            ->orderBy('sort_order')
            ->limit(12)
            ->get();
        $galleryCards = $galleries->map(fn (BniGalleryItem $item): array => [
            'id' => $item->id,
            'group_key' => $item->galleryGroupKey(),
            'group_label' => $item->galleryGroupLabel(),
            'title' => $item->title,
            'caption' => $item->caption,
            'image_url' => MediaUrl::versioned($item->media),
            'source' => $item->publicSourceLabel(),
        ])->values();
        $galleryGroups = $galleryCards
            ->map(fn (array $gallery): array => [
                'key' => $gallery['group_key'],
                'label' => $gallery['group_label'],
            ])
            ->unique('key')
            ->values();

        return [
            'event' => $event,
            'heroImageUrl' => $heroImageUrl,
            'heroSlides' => $heroSlides,
            'eventVideo' => [
                'media_url' => MediaUrl::versioned($event?->videoMedia),
                'external_url' => $event?->video_url,
                'poster_url' => $videoPosterUrl,
            ],
            'registration' => [
                'label' => $event?->registration_label ?: 'Đăng ký ngay',
                'url' => $registrationUrl,
            ],
            'chapters' => $chapters->map(fn ($chapter): array => [
                'name' => $chapter->name,
                'slug' => $chapter->slug,
                'detail_url' => LocalizedUrl::route('bni.chapters.show', ['chapter' => $chapter->slug]),
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
            'galleryGroups' => $galleryGroups,
            'galleryInitialGroup' => $galleryGroups->first()['key'] ?? null,
            'galleries' => $galleryCards,
        ];
    }

    /** @return array<string, mixed> */
    public function chapter(BniChapter $chapter): array
    {
        $chapter->loadMissing(['event', 'logoMedia', 'coverMedia', 'videoMedia']);
        $event = $chapter->event;
        $coverUrl = MediaUrl::versioned($chapter->coverMedia);
        $externalVideoUrl = trim((string) $chapter->video_url);

        if ($externalVideoUrl !== '' && ! Str::startsWith($externalVideoUrl, ['http://', 'https://'])) {
            $externalVideoUrl = '';
        }

        $articles = BniArticle::query()
            ->published()
            ->with('coverMedia')
            ->where('bni_chapter_id', $chapter->id)
            ->latest('published_at')
            ->latest('id')
            ->limit(6)
            ->get();

        $siblings = $event
            ? $event->chapters()
                ->where('is_active', true)
                ->whereKeyNot($chapter->getKey())
                ->with(['logoMedia', 'coverMedia'])
                ->get()
            : collect();

        $phone = trim((string) $chapter->contact_phone);
        $email = trim((string) $chapter->contact_email);
        $phoneTarget = preg_replace('/[^0-9+]/', '', $phone) ?: null;

        return [
            'chapter' => [
                'name' => $chapter->name,
                'short_name' => $chapter->short_name ?: $chapter->name,
                'description' => $chapter->description,
                'logo_url' => MediaUrl::versioned($chapter->logoMedia),
                'cover_url' => $coverUrl,
            ],
            'chapterVideo' => [
                'media_url' => MediaUrl::versioned($chapter->videoMedia),
                'external_url' => $externalVideoUrl ?: null,
                'poster_url' => $coverUrl,
            ],
            'chapterContact' => [
                'name' => $chapter->contact_name,
                'phone' => $phone ?: null,
                'phone_url' => $phoneTarget ? 'tel:'.$phoneTarget : null,
                'email' => $email ?: null,
                'email_url' => $email ? 'mailto:'.$email : null,
                'has_details' => filled($chapter->contact_name) || $phone !== '' || $email !== '',
            ],
            'eventContext' => $event ? [
                'title' => $event->title,
                'date' => $this->eventDate($event),
                'location' => collect([$event->venue, $event->address])->filter()->unique()->implode(', '),
                'url' => LocalizedUrl::route('bni.handover'),
            ] : null,
            'chapterArticles' => $articles->map(fn (BniArticle $article): array => $this->articleCard($article) + [
                'url' => LocalizedUrl::route('bni.articles.show', ['article' => $article->slug]),
            ]),
            'siblingChapters' => $siblings->map(fn (BniChapter $sibling): array => [
                'name' => $sibling->name,
                'short_name' => $sibling->short_name ?: $sibling->name,
                'description' => $sibling->description,
                'logo_url' => MediaUrl::versioned($sibling->logoMedia),
                'cover_url' => MediaUrl::versioned($sibling->coverMedia),
                'url' => LocalizedUrl::route('bni.chapters.show', ['chapter' => $sibling->slug]),
            ])->values(),
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
                'slides.media',
                'chapters.logoMedia',
                'chapters.coverMedia',
                'chapters.videoMedia',
                'purposes',
                'scheduleItems',
                'activities.media',
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

    private function eventDate(BniEvent $event): ?string
    {
        if (! $event->starts_at) {
            return null;
        }

        if (! $event->ends_at || $event->starts_at->isSameDay($event->ends_at)) {
            return $event->starts_at->translatedFormat('d/m/Y');
        }

        return $event->starts_at->translatedFormat('d/m/Y').' – '.$event->ends_at->translatedFormat('d/m/Y');
    }
}
