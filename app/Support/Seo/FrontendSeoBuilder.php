<?php

namespace App\Support\Seo;

use App\Models\Post;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\Landing;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class FrontendSeoBuilder
{
    private ?string $defaultImage = null;

    public function __construct(
        private readonly WebsiteSettings $website,
        private readonly LanguageCatalog $languages,
    ) {}

    public function default(): array
    {
        return $this->page(
            title: $this->website->seo_title ?: $this->website->site_name,
            description: $this->website->seo_description,
            canonical: $this->homeCanonical(),
            schema: [$this->organizationSchema()],
        );
    }

    public function siteName(): string
    {
        return $this->website->site_name;
    }

    public function home(): array
    {
        $canonical = $this->homeCanonical();

        return $this->page(
            title: $this->website->seo_title ?: $this->website->site_name,
            description: $this->website->seo_description,
            canonical: $canonical,
            schema: [
                $this->organizationSchema(),
                [
                    '@type' => 'WebSite',
                    '@id' => $this->baseUrl().'#website',
                    'name' => $this->website->site_name,
                    'url' => $this->baseUrl(),
                    'inLanguage' => $this->languageTag(),
                ],
                $this->webPageSchema($canonical, $this->website->seo_title ?: $this->website->site_name, $this->website->seo_description),
            ],
        );
    }

    public function listing(string $title, string $description, string $canonical): array
    {
        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            schema: [
                $this->organizationSchema(),
                $this->webPageSchema($canonical, $title, $description),
            ],
        );
    }

    /** @param iterable<PricingPlan> $plans */
    public function pricing(iterable $plans): array
    {
        $canonical = LocalizedUrl::route('pricing.index');
        $title = 'Bảng giá | '.$this->website->site_name;
        $description = 'Các gói dịch vụ và mức đầu tư tham khảo tại '.$this->website->site_name.'.';
        $offers = [];

        foreach ($plans as $plan) {
            $offers[] = [
                '@type' => 'Offer',
                'name' => $plan->name,
                'description' => $this->description($plan->description ?: $plan->name),
                'url' => $canonical.'#bang-gia-'.$plan->id,
            ];
        }

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            schema: [
                $this->organizationSchema(),
                $this->webPageSchema($canonical, $title, $description),
                [
                    '@type' => 'OfferCatalog',
                    '@id' => $canonical.'#offer-catalog',
                    'name' => 'Bảng giá dịch vụ',
                    'itemListElement' => $offers,
                ],
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => __('site.pricing'), 'url' => $canonical],
                ]),
            ],
        );
    }

    public function landing(Landing $landing): array
    {
        $canonical = LocalizedUrl::slug($landing->slug);
        $title = $landing->seo_title ?: $landing->title.' | '.$this->website->site_name;
        $description = $landing->seo_description ?: $landing->excerpt ?: $landing->title;

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $landing->image_url,
            schema: [
                $this->organizationSchema(),
                [
                    '@type' => 'Service',
                    '@id' => $canonical.'#service',
                    'name' => $landing->title,
                    'description' => $this->description($description),
                    'url' => $canonical,
                    ...$this->imageProperty($landing->image_url),
                    'provider' => ['@id' => $this->baseUrl().'#organization'],
                ],
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => __('site.services'), 'url' => LocalizedUrl::route('services.index')],
                    ['name' => $landing->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function project(Project $project): array
    {
        $canonical = LocalizedUrl::project($project);
        $title = $project->seo_title ?: $project->title.' | '.$this->website->site_name;
        $description = $project->seo_description ?: $project->excerpt ?: $project->title;

        $creativeWork = [
            '@type' => 'CreativeWork',
            '@id' => $canonical.'#project',
            'name' => $project->title,
            'description' => $this->description($description),
            'url' => $canonical,
            ...$this->imageProperty($project->image_url),
            'creator' => ['@id' => $this->baseUrl().'#organization'],
        ];

        if ($project->published_at) {
            $creativeWork['datePublished'] = $project->published_at->toDateString();
        }

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $project->image_url,
            schema: [
                $this->organizationSchema(),
                $creativeWork,
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => __('site.projects'), 'url' => LocalizedUrl::route('projects.index')],
                    ['name' => $project->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function post(Post $post): array
    {
        $canonical = LocalizedUrl::post($post);
        $title = $post->seo_title ?: $post->title.' | '.$this->website->site_name;
        $description = $post->seo_description ?: $post->excerpt ?: $post->title;

        $blogPosting = [
            '@type' => 'BlogPosting',
            '@id' => $canonical.'#article',
            'headline' => $post->title,
            'description' => $this->description($description),
            'mainEntityOfPage' => ['@id' => $canonical],
            'url' => $canonical,
            ...$this->imageProperty($post->image_url),
            'author' => ['@id' => $this->baseUrl().'#organization'],
            'publisher' => ['@id' => $this->baseUrl().'#organization'],
            'datePublished' => ($post->published_at ?: $post->created_at)->toAtomString(),
            'dateModified' => $post->updated_at->toAtomString(),
        ];

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $post->image_url,
            type: 'article',
            schema: [
                $this->organizationSchema(),
                $blogPosting,
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => __('site.news'), 'url' => LocalizedUrl::route('posts.index')],
                    ['name' => $post->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    private function page(string $title, ?string $description, string $canonical, ?string $image = null, string $type = 'website', array $schema = []): array
    {
        $description = $this->description($description);
        $image = $image ?: $this->defaultImageUrl();

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->website->seo_keywords,
            'robots' => app()->environment('production') && $this->languages->isIndexable(app()->getLocale())
                ? 'index, follow'
                : 'noindex, nofollow',
            'canonical' => $canonical,
            'type' => $type,
            'locale' => $this->languages->ogLocale(app()->getLocale()),
            'image' => $image,
            'image_alt' => $this->website->site_name,
            'schema_json' => json_encode([
                '@context' => 'https://schema.org',
                '@graph' => array_values(array_filter($schema)),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ];
    }

    private function organizationSchema(): array
    {
        $organization = [
            '@type' => 'Organization',
            '@id' => $this->baseUrl().'#organization',
            'name' => $this->website->company_name ?: $this->website->site_name,
            'url' => $this->baseUrl(),
            ...$this->imageProperty($this->defaultImageUrl(), 'logo'),
        ];

        if (filled($this->website->contact_email)) {
            $organization['email'] = $this->website->contact_email;
        }

        if (filled($this->website->hotline)) {
            $organization['telephone'] = $this->website->hotline;
        }

        if (filled($this->website->address)) {
            $organization['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->website->address,
            ];
        }

        $sameAs = array_values(array_filter([
            $this->website->facebook_url,
            $this->website->zalo_url,
            $this->website->youtube_url,
        ]));

        if ($sameAs !== []) {
            $organization['sameAs'] = $sameAs;
        }

        return $organization;
    }

    private function webPageSchema(string $canonical, string $title, ?string $description): array
    {
        return [
            '@type' => 'WebPage',
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $title,
            'description' => $this->description($description),
            'inLanguage' => $this->languageTag(),
            'isPartOf' => ['@id' => $this->baseUrl().'#website'],
        ];
    }

    private function breadcrumb(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(
                fn (array $item, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ],
                $items,
                array_keys($items),
            ),
        ];
    }

    private function imageProperty(?string $image, string $key = 'image'): array
    {
        return filled($image) ? [$key => $image] : [];
    }

    private function defaultImageUrl(): ?string
    {
        if ($this->defaultImage !== null) {
            return $this->defaultImage;
        }

        $mediaId = $this->website->seo_image_media_id ?: $this->website->logo_media_id;

        return $this->defaultImage = $mediaId ? Media::query()->find($mediaId)?->url : null;
    }

    private function description(?string $description): string
    {
        return Str::limit(Str::squish(strip_tags((string) $description)), 160, '');
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    private function absoluteUrl(string $path): string
    {
        return $path === '/' ? $this->baseUrl().'/' : $this->baseUrl().'/'.ltrim($path, '/');
    }

    private function languageTag(): string
    {
        return $this->languages->languageTag(app()->getLocale());
    }

    private function homeCanonical(): string
    {
        return app()->getLocale() === $this->languages->defaultCode()
            ? $this->absoluteUrl('/')
            : LocalizedUrl::route('home');
    }
}
