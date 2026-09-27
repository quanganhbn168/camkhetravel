<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use App\Models\Testimonial;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use App\Support\Homepage\HomepageContent;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly HomepageContent $content,
        private readonly HomepageSettings $homepage,
        private readonly WebsiteSettings $website,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function __invoke(): View
    {
        $page = $this->systemPages->require('home');
        $heroSlides = HeroSlide::query()
            ->active()
            ->with(['curatorMedia', 'mobileMedia'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HeroSlide $slide): array => [
                'title' => trim((string) $slide->title),
                'description' => trim((string) $slide->description),
                'image_url' => MediaUrl::versioned($slide->curatorMedia) ?: asset('images/no-image.svg'),
                'uses_page_banner' => false,
                'mobile_image_url' => $slide->mobileMedia ? MediaUrl::versioned($slide->mobileMedia) : ($this->content->image($slide->curatorMedia)['small_url'] ?? null),
                'primary_label' => trim((string) $slide->primary_label),
                'primary_url' => trim((string) $slide->primary_url),
                'primary_is_quote' => in_array($slide->primary_url, ['#bao-gia', '/#bao-gia'], true),
                'secondary_label' => trim((string) $slide->secondary_label),
                'secondary_url' => trim((string) $slide->secondary_url),
                'secondary_is_quote' => in_array($slide->secondary_url, ['#bao-gia', '/#bao-gia'], true),
                'has_copy' => filled($slide->title) || filled($slide->description)
                    || (filled($slide->primary_label) && filled($slide->primary_url))
                    || (filled($slide->secondary_label) && filled($slide->secondary_url)),
            ]);

        $showPageBanner = $heroSlides->isNotEmpty() && filled($page['banner_url']);

        if ($heroSlides->isEmpty()) {
            $heroSlides->push([
                'title' => $this->website->site_name ?: $page['title'],
                'description' => $this->website->tagline,
                'image_url' => $page['banner_url'] ?: asset('images/no-image.svg'),
                'uses_page_banner' => filled($page['banner_url']),
                'mobile_image_url' => null,
                'primary_label' => '', 'primary_url' => '', 'primary_is_quote' => false,
                'secondary_label' => '', 'secondary_url' => '', 'secondary_is_quote' => false,
                'has_copy' => true,
            ]);
        }

        $aboutImage = $this->website->about_image_media_id
            ? Media::query()->find($this->website->about_image_media_id)
            : null;
        $about = [
            'title' => trim($this->homepage->about_title),
            'content' => trim($this->homepage->about_content),
            'image_url' => $aboutImage && str_starts_with((string) $aboutImage->type, 'image/')
                ? MediaUrl::versioned($aboutImage)
                : null,
        ];
        $stats = collect($this->homepage->stats)
            ->filter(fn (mixed $item): bool => is_array($item) && ($item['verified'] ?? false) && filled($item['value'] ?? null) && filled($item['label'] ?? null))
            ->take(4)
            ->map(fn (array $item): array => [
                'value' => trim(($item['prefix'] ?? '').$item['value'].($item['suffix'] ?? '')),
                'label' => $item['label'],
            ])->values();
        $commitments = $this->contentLines($this->homepage->commitments);
        $commitmentCards = collect($this->homepage->commitment_items)
            ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))
            ->merge($commitments->map(fn ($title) => ['title' => $title, 'description' => '']))
            ->unique('title')->values();
        $capabilities = $this->contentLines($this->homepage->capabilities);
        $faqs = Faq::query()->homepage()->get(['id', 'question', 'answer']);

        $services = Service::query()
            ->published()
            ->where('is_home', true)
            ->with('curatorMedia')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Service $service): Service {
                $service->setAttribute('image_url', MediaUrl::versioned($service->curatorMedia) ?: asset('images/no-image.svg'));
                $service->setAttribute('quote_type', 'trip');
                $service->setAttribute('quote_icon', 'car');

                return $service;
            });

        $testimonials = Testimonial::query()
            ->active()
            ->where('is_illustrative', false)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $latestPosts = Post::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->each(fn (Post $post): mixed => $post->setAttribute('image_url', MediaUrl::versioned($post->curatorMedia) ?: asset('images/no-image.svg')));

        $presentation = $this->content->prepare($this->homepage, $services);

        $configuredPhones = collect($this->website->phones)
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['number'] ?? null));
        $primaryPhone = $configuredPhones->first(fn (array $item): bool => (bool) ($item['is_primary'] ?? false))
            ?? $configuredPhones->first();
        $frontendConfig = [
            'phone' => ($primaryPhone['number'] ?? null) ?: ($this->website->hotline ?: $this->website->contact_phone),
            'zaloUrl' => $this->website->zalo_url,
            'leadEndpoint' => route('contact.store'),
        ];

        return view('frontend.home', [
            'page' => $page,
            'heroSlides' => $heroSlides,
            'showPageBanner' => $showPageBanner,
            'about' => $about,
            'hasAbout' => filled($about['title']) || filled($about['content']) || filled($about['image_url']),
            'stats' => $stats,
            'commitments' => $commitments,
            'commitmentCards' => $commitmentCards,
            'capabilities' => $capabilities,
            'faqs' => $faqs,
            'services' => $services,
            'testimonials' => $testimonials,
            'latestPosts' => $latestPosts,
            'homepage' => $this->homepage,
            ...$presentation,
            'hideFooterCta' => true,
            'noImageUrl' => asset('images/no-image.svg'),
            'frontendConfig' => $frontendConfig,
            'seo' => $this->seo->home($page, $faqs->toArray()),
        ]);
    }

    /** @return Collection<int, string> */
    private function contentLines(string $content): Collection
    {
        return collect(preg_split('/\r\n|\r|\n/', $content))
            ->map(fn (string $line): string => trim($line))
            ->filter(fn (string $line): bool => $line !== '')
            ->values();
    }
}
