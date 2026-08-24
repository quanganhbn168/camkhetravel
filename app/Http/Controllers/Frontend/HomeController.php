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
                'translations' => fn ($query) => $query->where('locale', app()->getLocale()),
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function (HeroSlide $slide, int $index): array {
                $content = $slide->contentFor(app()->getLocale());
                $secondaryFallback = match ($index) {
                    0 => LocalizedUrl::route('projects.index'),
                    1 => LocalizedUrl::route('about'),
                    default => LocalizedUrl::route('posts.index'),
                };

                return [
                    ...$content,
                    'primary_url' => $content['primary_url'] ?: LocalizedUrl::route('contact'),
                    'secondary_url' => $content['secondary_url'] ?: $secondaryFallback,
                    'image_url' => $slide->curatorMedia?->url,
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
        $contactImageUrl = $this->website->contact_image_media_id
            ? Media::query()->find($this->website->contact_image_media_id)?->url
            : null;

        return view('frontend.home', compact('heroSlides', 'services', 'posts', 'projectTabs', 'companyProfileUrl', 'aboutImageUrl', 'contactImageUrl') + [
            'marqueePartners' => Partner::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->get(),
            'stats' => [
                ['value' => Project::query()->published()->count(), 'label' => 'dự án đã xuất bản'],
                ['value' => Landing::query()->published()->count(), 'label' => 'hạng mục dịch vụ'],
                ['value' => Post::query()->published()->count(), 'label' => 'bài viết và góc nhìn'],
                ['value' => $projectCategories->count(), 'label' => 'nhóm dự án'],
            ],
            'about' => [
                'eyebrow' => $this->translated($this->homepage->about_eyebrow),
                'title' => $this->translated($this->homepage->about_title),
                'content' => $this->translated($this->homepage->about_content),
            ],
            'commitments' => $this->lines($this->homepage->commitments),
            'capabilities' => $this->lines($this->homepage->capabilities),
            'consultation' => [
                'title' => $this->translated($this->homepage->consultation_title),
                'content' => $this->translated($this->homepage->consultation_content),
            ],
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

    private function attachImages(iterable $items): void
    {
        foreach ($items as $item) {
            $item->setAttribute('image_url', MediaUrl::resolve($item->curatorMedia, $item->legacyMedia));
        }
    }
}
