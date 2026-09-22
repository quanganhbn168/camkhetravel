<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Partner;
use App\Models\Post;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Solution;
use App\Models\Testimonial;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly HomepageSettings $homepage,
        private readonly WebsiteSettings $website,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function __invoke(): View
    {
        $page = $this->systemPages->require('home');
        $heroSlides = HeroSlide::query()
            ->active()
            ->with([
                'curatorMedia',
                'videoMedia',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $contactServices = Service::query()
            ->published()
            ->with(['slugs', 'curatorMedia'])
            ->orderBy('sort_order')
            ->get(['id', 'title', 'is_featured', 'is_home', 'sort_order', 'service_category_id', 'curator_media_id']);
        request()->attributes->set(
            'frontend.footer_services',
            $contactServices->sortBy([['is_featured', 'desc'], ['sort_order', 'asc']])->take(4)->values(),
        );
        $homeServicesByCategory = $contactServices
            ->where('is_home', true)
            ->groupBy('service_category_id');

        $featuredServiceCategories = ServiceCategory::query()
            ->where('is_active', true)->where('is_featured', true)->where('is_home', true)
            ->whereHas('services', fn ($query) => $query->published()->where('is_home', true))
            ->with(['slugs', 'curatorMedia'])
            ->orderBy('sort_order')->orderBy('id')->get();
        foreach ($featuredServiceCategories as $category) {
            $services = $homeServicesByCategory
                ->get($category->getKey(), collect())
                ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
                ->values();
            $category->setRelation('services', $services);
            $this->attachImages($services);
        }

        $showcaseProjects = Project::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(30)
            ->get();
        $posts = Post::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs'])
            ->latest('published_at')
            ->limit(8)
            ->get();

        $this->attachImages($showcaseProjects);
        $this->attachImages($posts);

        $projectCategories = $showcaseProjects
            ->pluck('category')
            ->filter(fn (?ProjectCategory $category): bool => $category?->is_active === true)
            ->unique(fn (ProjectCategory $category): int => $category->getKey())
            ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
            ->values();
        $projectTabs = $this->projectTabs($showcaseProjects, $projectCategories);
        $websiteMedia = ViewFacade::shared('websiteMedia');
        $websiteMedia = $websiteMedia instanceof Collection ? $websiteMedia : collect();
        $companyProfileUrl = MediaUrl::versioned($websiteMedia->get($this->website->company_profile_media_id));
        $aboutImageUrl = MediaUrl::versioned($websiteMedia->get($this->website->about_image_media_id));
        $defaultBannerUrl = ViewFacade::shared('defaultBannerUrl');
        $googleMapsEmbedUrl = GoogleMapsUrl::normalizeEmbed($this->website->google_maps_embed_url);

        if ($googleMapsEmbedUrl === null && filled($this->website->address)) {
            $googleMapsEmbedUrl = 'https://www.google.com/maps?q='.rawurlencode($this->website->address).'&output=embed';
        }
        $googleMapsUrl = filled($this->website->google_maps_url)
            ? trim($this->website->google_maps_url)
            : null;
        $stats = $this->stats($projectCategories, $contactServices->count());
        $companyName = trim($this->website->company_name)
            ?: trim($this->website->site_name)
            ?: (string) config('app.name');
        $faqItems = Faq::query()
            ->homepage()
            ->get(['question', 'answer'])
            ->map(fn (Faq $faq): array => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ]);
        $heroSlides->each(function (HeroSlide $slide): void {
            $slide->setAttribute('has_content', $this->heroSlideHasContent($slide));
            $slide->setAttribute('video_url', $slide->resolvedVideoUrl());
        });
        $contactPhones = $this->contactPhones();
        $featuredProjects = $this->featuredProjects($projectTabs);
        $featuredPost = $posts->first();
        $sidePosts = $posts->slice(1, 3)->values();
        $whyImageUrl = $featuredProjects->first()?->image_url
            ?: $aboutImageUrl
            ?: $defaultBannerUrl;

        return view('frontend.home', compact('heroSlides', 'posts', 'projectTabs', 'companyProfileUrl', 'aboutImageUrl', 'googleMapsEmbedUrl', 'googleMapsUrl') + [
            'companyName' => $companyName,
            'contactPhones' => $contactPhones,
            'primaryPhone' => data_get($contactPhones->first(), 'number'),
            'featuredServiceCategories' => $featuredServiceCategories,
            'featuredProjects' => $featuredProjects,
            'featuredPost' => $featuredPost,
            'sidePosts' => $sidePosts,
            'whyImageUrl' => $whyImageUrl,
            'uspItems' => $this->uspItems(),
            'solutions' => Solution::published()->where('is_home', true)->with(['slugs', 'curatorMedia'])->orderBy('sort_order')->orderBy('id')->get(),
            'whyChooseUs' => $this->whyChooseUs(),
            'processSteps' => $this->processSteps(),
            'equipmentCategories' => ProductCategory::active()
                ->doesntHave('children')
                ->with(['slugs', 'products' => fn ($query) => $query->published()->with(['curatorMedia', 'slugs'])->limit(6)])
                ->orderBy('sort_order')->orderBy('id')->get(),
            'certificateItems' => $this->certificateItems(),
            'homePartners' => Partner::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->get(),
            'stats' => $stats,
            'about' => [
                'title' => trim($this->homepage->about_title),
                'content' => trim($this->homepage->about_content),
            ],
            'faqTitle' => trim($this->homepage->faq_title),
            'faqDescription' => trim($this->homepage->faq_description),
            'commitments' => $this->lines($this->homepage->commitments),
            'capabilities' => $this->lines($this->homepage->capabilities),
            'faqItems' => $faqItems,
            'testimonials' => Testimonial::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'contactServices' => $contactServices,
            'page' => $page,
            'pageBannerUrl' => $page['banner_url'],
            'seo' => $this->seo->home($page, $faqItems),
        ]);
    }

    private function heroSlideHasContent(HeroSlide $slide): bool
    {
        return filled($slide->title)
            || filled($slide->description)
            || (filled($slide->primary_label) && filled($slide->primary_url))
            || (filled($slide->secondary_label) && filled($slide->secondary_url));
    }

    /** @return Collection<int, array{number: string}> */
    private function contactPhones(): Collection
    {
        $phones = collect($this->website->phones ?? [])
            ->filter(fn ($phone): bool => is_array($phone) && filled($phone['number'] ?? null))
            ->values();

        if ($phones->isEmpty()) {
            $phones = collect([
                ['number' => $this->website->hotline],
                ['number' => $this->website->contact_phone],
            ])->filter(fn (array $phone): bool => filled($phone['number'] ?? null))->values();
        }

        return $phones;
    }

    private function featuredProjects(Collection $projectTabs): Collection
    {
        $firstProjectTab = $projectTabs->first();

        if (! is_array($firstProjectTab)) {
            return collect();
        }

        return collect([
            $firstProjectTab['primary'] ?? null,
            ...($firstProjectTab['secondary'] ?? []),
        ])->filter()->take(4)->values();
    }

    /** @return list<array{title: string, description: string}> */
    private function uspItems(): array
    {
        return [
            ['title' => 'Khảo sát thực tế', 'description' => 'Đánh giá chính xác yêu cầu'],
            ['title' => 'Thi công đồng bộ', 'description' => 'Đảm bảo chất lượng toàn diện'],
            ['title' => 'Hỗ trợ hồ sơ pháp lý', 'description' => 'Tư vấn đúng quy định'],
            ['title' => 'Bảo trì dài hạn', 'description' => 'Đồng hành sau bàn giao'],
        ];
    }

    /** @return list<array{number: string, title: string, description: string}> */
    private function whyChooseUs(): array
    {
        return [
            ['number' => '01', 'title' => 'Khảo sát kỹ hiện trạng', 'description' => 'Đánh giá chi tiết để đưa ra phương án phù hợp thực tế.'],
            ['number' => '02', 'title' => 'Phương án tối ưu', 'description' => 'Phù hợp công năng, ngân sách và yêu cầu công trình.'],
            ['number' => '03', 'title' => 'Thi công đồng bộ', 'description' => 'Đảm bảo chất lượng, tiến độ và tính thống nhất.'],
            ['number' => '04', 'title' => 'Hỗ trợ sau bàn giao', 'description' => 'Bảo trì định kỳ và đồng hành khi vận hành.'],
        ];
    }

    /** @return list<array{number: string, title: string, description: string}> */
    private function processSteps(): array
    {
        return [
            ['number' => '01', 'title' => 'Khảo sát', 'description' => 'Nắm hiện trạng'],
            ['number' => '02', 'title' => 'Phân tích', 'description' => 'Đề xuất giải pháp'],
            ['number' => '03', 'title' => 'Thiết kế', 'description' => 'Hoàn thiện hồ sơ'],
            ['number' => '04', 'title' => 'Thi công', 'description' => 'Lắp đặt đồng bộ'],
            ['number' => '05', 'title' => 'Kiểm tra', 'description' => 'Nghiệm thu bàn giao'],
            ['number' => '06', 'title' => 'Bảo trì', 'description' => 'Hỗ trợ lâu dài'],
        ];
    }

    /** @return list<string> */
    private function certificateItems(): array
    {
        return ['Hồ sơ năng lực', 'Hồ sơ pháp lý', 'Chứng chỉ 01', 'Chứng chỉ 02', 'Chứng chỉ 03'];
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

    private function stats(Collection $projectCategories, int $serviceCount): Collection
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
            ['value' => (string) $serviceCount, 'label' => 'hạng mục dịch vụ'],
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

    private function lines(string $content): Collection
    {
        return collect(preg_split('/\R/u', $content) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values();
    }

    private function attachImages(iterable $items): void
    {
        foreach ($items as $item) {
            $item->setAttribute('image_url', MediaUrl::resolve($item->curatorMedia));
        }
    }
}
