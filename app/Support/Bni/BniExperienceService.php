<?php

namespace App\Support\Bni;

use App\Models\BniArticle;
use App\Models\BniArticleCategory;
use App\Models\BniChapter;
use App\Models\BniContact;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BniExperienceService
{
    /** @return array<string, mixed> */
    public function handover(): array
    {
        $event = $this->event('handover');
        $video = $event?->video;
        $chapters = $event?->chapters->where('is_active', true)->values() ?? collect();
        $heroImageUrl = $event?->bniMediaUrl('hero');
        $heroSlides = $event?->slides
            ->where('is_active', true)
            ->map(function ($slide) use ($event): array {
                $slideMedia = $slide->bniFirstMedia('image');
                $externalVideoUrl = trim((string) $slide->video_url);

                if ($externalVideoUrl !== '' && ! Str::startsWith($externalVideoUrl, ['http://', 'https://'])) {
                    $externalVideoUrl = '';
                }

                return [
                    'image_url' => $slide->bniMediaUrl('image'),
                    'alt_text' => $slide->alt_text ?: $slideMedia?->getCustomProperty('alt') ?: $slideMedia?->name ?: $event?->title,
                    'video_media_url' => $slide->bniMediaUrl('video', false),
                    'video_external_url' => $externalVideoUrl ?: null,
                ];
            })
            ->values() ?? collect();

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([[
                'image_url' => $heroImageUrl,
                'alt_text' => $event?->bniFirstMedia('hero')?->getCustomProperty('alt') ?: $event?->bniFirstMedia('hero')?->name ?: $event?->title ?: 'Key visual Lễ chuyển giao BNI',
                'video_media_url' => null,
                'video_external_url' => null,
            ]]);
        }

        $videoPosterUrl = $video?->bniMediaUrl('poster') ?: $heroImageUrl;
        $registrationUrl = trim((string) $video?->registration_url);

        if ($registrationUrl === '' || $registrationUrl === '#dang-ky') {
            $registrationUrl = LocalizedUrl::route('bni.registrations.create');
        }
        $newsCategories = $this->newsCategories($event);
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
            'image_url' => $item->bniMediaUrl('image'),
            'source' => $item->publicSourceLabel(),
        ])->values();
        $galleryGroups = $galleryCards
            ->map(fn (array $gallery): array => [
                'key' => $gallery['group_key'],
                'label' => $gallery['group_label'],
            ])
            ->unique('key')
            ->values();
        $generalContacts = $event?->contacts->where('is_active', true)->values() ?? collect();

        if ($generalContacts->isEmpty()) {
            $generalContacts = BniContact::query()
                ->general()
                ->active()
                ->whereNull('bni_event_id')
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->get();
        }

        return [
            'event' => $event,
            'heroImageUrl' => $heroImageUrl,
            'heroSlides' => $heroSlides,
            'eventVideo' => [
                'media_url' => $video?->bniMediaUrl('video', false),
                'external_url' => $video?->external_url,
                'poster_url' => $videoPosterUrl,
            ],
            'registration' => [
                'label' => $video?->registration_label ?: 'Đăng ký ngay',
                'url' => $registrationUrl,
            ],
            'chapters' => $chapters->map(fn ($chapter): array => [
                'name' => $chapter->name,
                'slug' => $chapter->slug,
                'detail_url' => LocalizedUrl::route('bni.chapters.show', ['chapter' => $chapter->slug]),
                'short_name' => $chapter->short_name ?: $chapter->name,
                'description' => $chapter->description,
                'logo_url' => $chapter->bniMediaUrl('logo'),
                'cover_url' => $chapter->bniMediaUrl('cover') ?: $videoPosterUrl,
                'video_media_url' => $chapter->bniMediaUrl('video', false),
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
                'image_url' => $activity->bniMediaUrl('image'),
                'link_url' => $activity->link_url,
            ])->values() ?? collect(),
            'newsCategories' => $newsCategories,
            'newsInitialCategory' => $newsCategories->first()['key'] ?? null,
            'galleryGroups' => $galleryGroups,
            'galleryInitialGroup' => $galleryGroups->first()['key'] ?? null,
            'galleries' => $galleryCards,
            'generalContacts' => $this->contactCards($generalContacts),
        ];
    }

    /** @return array<string, mixed> */
    public function chapter(BniChapter $chapter): array
    {
        $chapter->loadMissing(['event', 'media', 'contacts']);
        $event = $chapter->event;
        $coverUrl = $chapter->bniMediaUrl('cover');
        $externalVideoUrl = trim((string) $chapter->video_url);

        if ($externalVideoUrl !== '' && ! Str::startsWith($externalVideoUrl, ['http://', 'https://'])) {
            $externalVideoUrl = '';
        }

        $articles = BniArticle::query()
            ->published()
            ->with('media')
            ->where('bni_chapter_id', $chapter->id)
            ->latest('published_at')
            ->latest('id')
            ->limit(6)
            ->get();

        $siblings = $event
            ? $event->chapters()
                ->where('is_active', true)
                ->whereKeyNot($chapter->getKey())
                ->with('media')
                ->get()
            : collect();

        $chapterContacts = $this->contactCards($chapter->contacts->where('is_active', true)->values());

        return [
            'chapter' => [
                'name' => $chapter->name,
                'short_name' => $chapter->short_name ?: $chapter->name,
                'description' => $chapter->description,
                'logo_url' => $chapter->bniMediaUrl('logo'),
                'cover_url' => $coverUrl,
            ],
            'chapterVideo' => [
                'media_url' => $chapter->bniMediaUrl('video', false),
                'external_url' => $externalVideoUrl ?: null,
                'poster_url' => $coverUrl,
            ],
            'chapterContacts' => $chapterContacts,
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
                'logo_url' => $sibling->bniMediaUrl('logo'),
                'cover_url' => $sibling->bniMediaUrl('cover'),
                'url' => LocalizedUrl::route('bni.chapters.show', ['chapter' => $sibling->slug]),
            ])->values(),
        ];
    }

    /** @return array<string, mixed> */
    public function pickleball(): array
    {
        $event = $this->event('pickleball');
        $landing = $event?->landing;
        $articles = BniArticle::query()
            ->published()
            ->with(['chapter', 'media'])
            ->where('type', 'pickleball')
            ->when($event, fn ($query) => $query->where('bni_event_id', $event->id))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(6)
            ->get();

        return [
            'event' => $event,
            'heroImageUrl' => $event?->bniMediaUrl('hero'),
            'scheduleDays' => $this->scheduleDays($event),
            'articles' => $articles->map(fn (BniArticle $article): array => $this->articleCard($article)),
            'chapters' => BniChapter::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'short_name']),
            'pickleballContent' => [
                'countdown_label' => $landing?->countdown_label ?: 'Đếm ngược đến giải đấu',
                'prizes_title' => $landing?->prizes_title ?: 'Cơ cấu giải thưởng',
                'prizes_description' => $landing?->prizes_description,
                'prizes' => $landing?->prizes->map(fn ($prize): array => [
                    'title' => $prize->title,
                    'value' => $prize->value,
                    'description' => $prize->description,
                    'highlight' => $prize->highlight,
                ])->values() ?? collect(),
                'rules_title' => $landing?->rules_title ?: 'Thể lệ giải đấu',
                'rules' => $landing?->rules,
                'registration_title' => $landing?->registration_title ?: 'Đăng ký tham gia',
                'registration_description' => $landing?->registration_description ?: 'Đăng ký để Ban tổ chức sắp xếp bảng đấu, thông tin check-in và hỗ trợ phù hợp.',
            ],
        ];
    }

    private function event(string $type): ?BniEvent
    {
        return BniEvent::query()
            ->published()
            ->where('type', $type)
            ->with([
                'media',
                'video.media',
                'landing.prizes',
                'slides.media',
                'chapters.media',
                'purposes',
                'scheduleDays.items',
                'activities.media',
                'contacts',
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->first();
    }

    /**
     * @return Collection<int, array{
     *     key: string,
     *     label: string,
     *     articles: Collection<int, array<string, mixed>>
     * }>
     */
    private function newsCategories(?BniEvent $event): Collection
    {
        return BniArticleCategory::query()
            ->where('is_active', true)
            ->whereHas('articles', fn ($query) => $query
                ->published()
                ->whereIn('type', ['event', 'chapter'])
                ->when($event, fn ($query) => $query->where(fn ($query) => $query
                    ->where('bni_event_id', $event->id)
                    ->orWhereNull('bni_event_id'))))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (BniArticleCategory $category) use ($event): array {
                $articles = $category->articles()
                    ->published()
                    ->with(['chapter', 'media'])
                    ->whereIn('type', ['event', 'chapter'])
                    ->when($event, fn ($query) => $query->where(fn ($query) => $query
                        ->where('bni_event_id', $event->id)
                        ->orWhereNull('bni_event_id')))
                    ->orderByDesc('is_featured')
                    ->latest('published_at')
                    ->latest('bni_articles.id')
                    ->limit(4)
                    ->get();

                return [
                    'key' => 'bni-article-category-'.$category->getKey(),
                    'label' => $category->name,
                    'articles' => $articles->map(fn (BniArticle $article): array => $this->articleCard($article) + [
                        'excerpt' => Str::limit(trim(strip_tags((string) $article->excerpt)), 180),
                        'url' => LocalizedUrl::route('bni.articles.show', ['article' => $article->slug]),
                    ])->values(),
                ];
            })
            ->values();
    }

    /** @return Collection<int, array{number: int, label: string, items: Collection<int, array<string, mixed>>}> */
    public function scheduleDays(?BniEvent $event): Collection
    {
        if (! $event) {
            return collect();
        }

        return $event->scheduleDays
            ->where('is_active', true)
            ->values()
            ->map(fn ($day, int $index): array => [
                'number' => $index + 1,
                'label' => collect([
                    $day->title,
                    $day->event_date?->format('d/m/Y'),
                ])->filter()->implode(' · ') ?: 'Ngày '.($index + 1),
                'description' => $day->description,
                'items' => $day->items->map(fn ($item): array => [
                    'time' => collect([$item->starts_at ? substr((string) $item->starts_at, 0, 5) : null, $item->ends_at ? substr((string) $item->ends_at, 0, 5) : null])->filter()->implode(' – '),
                    'title' => $item->title,
                    'description' => $item->description,
                ]),
            ])
            ->values();
    }

    /** @param Collection<int, BniContact> $contacts
     * @return Collection<int, array<string, mixed>>
     */
    private function contactCards(Collection $contacts): Collection
    {
        return $contacts->map(function (BniContact $contact): array {
            $phone = trim((string) $contact->phone);
            $phoneTarget = preg_replace('/[^0-9+]/', '', $phone) ?: null;
            $email = trim((string) $contact->email);

            return [
                'name' => $contact->name,
                'position' => $contact->position,
                'phone' => $phone ?: null,
                'phone_url' => $phoneTarget ? 'tel:'.$phoneTarget : null,
                'email' => $email ?: null,
                'email_url' => $email ? 'mailto:'.$email : null,
                'zalo_url' => $contact->zalo_url,
                'note' => $contact->note,
                'is_primary' => $contact->is_primary,
            ];
        })->values();
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
            'image_url' => $article->bniMediaUrl('cover'),
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
