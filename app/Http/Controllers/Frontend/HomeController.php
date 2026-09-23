<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\Service;
use App\Models\Testimonial;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
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
        $heroSlide = HeroSlide::query()
            ->active()
            ->with('curatorMedia')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $services = Service::query()
            ->published()
            ->where('is_home', true)
            ->with('curatorMedia')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Service $service): Service {
                $service->setAttribute('image_url', MediaUrl::versioned($service->curatorMedia) ?: asset('images/no-image.svg'));
                $title = mb_strtolower($service->title);
                $type = str_contains($title, 'cưới')
                    ? 'wedding'
                    : (str_contains($title, 'hợp đồng') ? 'partner' : (str_contains($title, 'ghép') ? 'shared' : 'trip'));
                $service->setAttribute('quote_type', $type);
                $service->setAttribute('quote_icon', match ($type) {
                    'wedding' => 'heart',
                    'partner' => 'briefcase',
                    'shared' => 'pin',
                    default => 'car',
                });

                return $service;
            });

        $testimonials = Testimonial::query()
            ->active()
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

        $fleetTypes = collect($this->homepage->fleet_types)->map(fn (array $item) => [
            ...$item,
            'features' => collect(preg_split('/\r\n|\r|\n/', (string) ($item['features'] ?? '')))
                ->map(fn (string $feature) => trim($feature))
                ->filter()
                ->values(),
        ]);
        $fleetCapacityLabels = $fleetTypes
            ->map(function (array $vehicle): ?string {
                return preg_match('/\d+(?:[–-]\d+)?/', (string) ($vehicle['title'] ?? ''), $matches)
                    ? $matches[0]
                    : null;
            })
            ->filter()
            ->values();

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
            'heroSlide' => $heroSlide,
            'services' => $services,
            'testimonials' => $testimonials,
            'latestPosts' => $latestPosts,
            'hasIllustrativeTestimonials' => $testimonials->contains(fn (Testimonial $item) => $item->is_illustrative),
            'homepage' => $this->homepage,
            'fleetTypes' => $fleetTypes,
            'heroFleetLabel' => $fleetCapacityLabels->isNotEmpty() ? $fleetCapacityLabels->implode(' – ').' chỗ' : '',
            'noImageUrl' => asset('images/no-image.svg'),
            'frontendConfig' => $frontendConfig,
            'seo' => $this->seo->home($page),
        ]);
    }
}
