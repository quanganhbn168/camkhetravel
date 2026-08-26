<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Landing;
use App\Models\Post;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Testimonial;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Frontend\MediaUrl;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly HomepageSettings $homepage,
        private readonly WebsiteSettings $website,
        private readonly LanguageCatalog $languages,
    ) {}

    public function __invoke(): View
    {
        $heroSlides = HeroSlide::query()
            ->active()
            ->with([
                'curatorMedia',
                'videoMedia',
                'translations' => fn ($query) => $query->where('locale', app()->getLocale()),
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function (HeroSlide $slide, int $index): array {
                $content = $slide->contentFor(app()->getLocale());
                $videoUrl = match ($slide->video_source) {
                    'youtube' => $this->youtubeUrl($slide->video_url),
                    'upload' => $slide->videoMedia?->url,
                    default => null,
                };
                $secondaryFallback = match ($index) {
                    0 => LocalizedUrl::route('projects.index'),
                    1 => LocalizedUrl::route('about'),
                    default => LocalizedUrl::route('posts.index'),
                };

                return [
                    ...$content,
                    'has_primary_cta' => filled($content['primary_label']),
                    'has_secondary_cta' => filled($content['secondary_label']),
                    'has_content' => filled($content['title'])
                        || filled($content['description'])
                        || filled($content['primary_label'])
                        || filled($content['secondary_label']),
                    'primary_url' => $content['primary_url'] ?: LocalizedUrl::route('contact'),
                    'secondary_url' => $content['secondary_url'] ?: $secondaryFallback,
                    'image_url' => $slide->curatorMedia?->url,
                    'video_url' => $videoUrl,
                    'video_source' => $videoUrl ? $slide->video_source : null,
                ];
            });

        $services = Landing::query()
            ->published()
            ->with(['category', 'curatorMedia', 'legacyMedia'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
        $showcaseProjects = Project::query()
            ->published()
            ->with(['category', 'curatorMedia', 'legacyMedia'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();
        $posts = Post::query()
            ->published()
            ->with(['categories', 'curatorMedia', 'legacyMedia'])
            ->latest('published_at')
            ->limit(8)
            ->get();

        $this->attachImages($services);
        $this->attachImages($showcaseProjects);
        $this->attachImages($posts);

        $projectCategories = ProjectCategory::query()
            ->where('is_active', true)
            ->whereIn('id', $showcaseProjects->pluck('project_category_id')->filter())
            ->orderBy('sort_order')
            ->get();
        $projectTabs = $this->projectTabs($showcaseProjects, $projectCategories);
        $companyProfileUrl = $this->website->company_profile_media_id
            ? Media::query()->find($this->website->company_profile_media_id)?->url
            : null;
        $aboutImageUrl = $this->website->about_image_media_id
            ? Media::query()->find($this->website->about_image_media_id)?->url
            : null;
        $googleMapsEmbedUrl = filled($this->website->google_maps_embed_url)
            ? trim($this->website->google_maps_embed_url)
            : (filled($this->website->address)
                ? 'https://www.google.com/maps?q='.rawurlencode($this->website->address).'&output=embed'
                : null);
        $stats = $this->stats($projectCategories);

        return view('frontend.home', compact('heroSlides', 'services', 'posts', 'projectTabs', 'companyProfileUrl', 'aboutImageUrl', 'googleMapsEmbedUrl') + [
            'marqueePartners' => Partner::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->get(),
            'stats' => $stats,
            'about' => [
                'eyebrow' => $this->translated($this->homepage->about_eyebrow),
                'title' => $this->translated($this->homepage->about_title),
                'content' => $this->translated($this->homepage->about_content),
            ],
            'commitments' => $this->lines($this->homepage->commitments),
            'capabilities' => $this->lines($this->homepage->capabilities),
            'testimonials' => Testimonial::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'contactServices' => Landing::query()
                ->published()
                ->orderBy('sort_order')
                ->get(['id', 'title']),
            'seo' => $this->seo->home(),
        ]);
    }

    private function projectTabs(Collection $projects, Collection $categories): Collection
    {
        $createTab = function (string $id, string $label, Collection $items): array {
            $items = $items->take(5)->values();

            return [
                'id' => $id,
                'label' => $label,
                'primary' => $items->first(),
                'secondary' => $items->slice(1)->values(),
            ];
        };

        return collect([
            $createTab('all', 'Tất cả', $projects),
        ])->merge($categories->map(fn (ProjectCategory $category): array => $createTab(
            'category-'.$category->getKey(),
            $category->name,
            $projects->where('project_category_id', $category->getKey()),
        )))->filter(fn (array $tab): bool => $tab['primary'] !== null)->values();
    }

    private function stats(Collection $projectCategories): Collection
    {
        $configuredStats = collect($this->homepage->stats ?? [])
            ->filter(fn (mixed $stat): bool => is_array($stat)
                && filled($stat['value'] ?? null)
                && filled($stat['label'] ?? null))
            ->take(4)
            ->map(fn (array $stat): array => $this->stat($stat));

        if ($configuredStats->isNotEmpty()) {
            return $configuredStats->values();
        }

        return collect([
            ['value' => (string) Project::query()->published()->count(), 'label' => 'dự án đã xuất bản'],
            ['value' => (string) Landing::query()->published()->count(), 'label' => 'hạng mục dịch vụ'],
            ['value' => (string) Post::query()->published()->count(), 'label' => 'bài viết và góc nhìn'],
            ['value' => (string) $projectCategories->count(), 'label' => 'nhóm dự án'],
        ])->map(fn (array $stat): array => $this->stat($stat));
    }

    /** @param array<string, mixed> $stat */
    private function stat(array $stat): array
    {
        $value = trim((string) ($stat['value'] ?? ''));

        return [
            'prefix' => (string) ($stat['prefix'] ?? ''),
            'suffix' => (string) ($stat['suffix'] ?? ''),
            'label' => (string) ($stat['label'] ?? ''),
            'segments' => collect(preg_split('/(\d+)/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [])
                ->map(fn (string $segment): array => [
                    'value' => $segment,
                    'is_number' => ctype_digit($segment),
                ])
                ->all(),
        ];
    }

    private function translated(array $content): string
    {
        $locale = app()->getLocale();
        $defaultLocale = $this->languages->defaultCode();

        return (string) ($content[$locale] ?? $content[$defaultLocale] ?? reset($content) ?: '');
    }

    private function lines(array $content): Collection
    {
        return collect(preg_split('/\R/u', $this->translated($content)) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values();
    }

    private function youtubeUrl(?string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return in_array($host, [
            'youtu.be',
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ], true) ? $url : null;
    }

    private function attachImages(iterable $items): void
    {
        foreach ($items as $item) {
            $item->setAttribute('image_url', MediaUrl::resolve($item->curatorMedia, $item->legacyMedia));
        }
    }
}
