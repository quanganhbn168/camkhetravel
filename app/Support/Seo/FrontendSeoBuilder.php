<?php

namespace App\Support\Seo;

use App\Models\BniInvitation;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class FrontendSeoBuilder
{
    private const INDEX_ROBOTS = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    private const NOINDEX_ROBOTS = 'noindex, nofollow, noarchive';

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

    /** @param iterable<array{question: string, answer: string}> $faqItems */
    public function home(iterable $faqItems = []): array
    {
        $canonical = $this->homeCanonical();
        $faqSchemaItems = [];

        foreach ($faqItems as $item) {
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $faqSchemaItems[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        $schema = [
            $this->organizationSchema(),
            [
                '@type' => 'WebSite',
                '@id' => $this->baseUrl().'#website',
                'name' => $this->website->site_name,
                'url' => $this->baseUrl(),
                'inLanguage' => $this->languageTag(),
            ],
            $this->webPageSchema($canonical, $this->website->seo_title ?: $this->website->site_name, $this->website->seo_description),
        ];

        if ($faqSchemaItems !== []) {
            $schema[] = [
                '@type' => 'FAQPage',
                '@id' => $canonical.'#cau-hoi-thuong-gap',
                'mainEntity' => $faqSchemaItems,
            ];
        }

        return $this->page(
            title: $this->website->seo_title ?: $this->website->site_name,
            description: $this->website->seo_description,
            canonical: $canonical,
            schema: $schema,
        );
    }

    public function listing(string $title, string $description, string $canonical, bool $indexable = true): array
    {
        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            schema: [
                $this->organizationSchema(),
                $this->webPageSchema($canonical, $title, $description),
            ],
            robots: $indexable ? self::INDEX_ROBOTS : self::NOINDEX_ROBOTS,
        );
    }

    /** @param array<string, mixed> $content */
    public function invitation(BniInvitation $invitation, string $guestName, array $content, ?string $image = null): array
    {
        $canonical = LocalizedUrl::route('bni.invitations.show', [
            'invitation' => $invitation,
            'accessToken' => $invitation->access_token,
        ]);
        $event = $invitation->event;
        $eventLabel = trim((string) ($content['event_label'] ?? 'LỄ CHUYỂN GIAO'));
        $eventTitle = $event?->title ?: $eventLabel;
        $title = trim(($content['label'] ?? 'THƯ MỜI').' '.$eventLabel.' – '.$guestName.' | '.$this->website->site_name);
        $description = trim(($content['greeting'] ?? 'Trân trọng kính mời').' '.$guestName.' tham dự '.$eventTitle.'.');

        $eventSchema = [
            '@type' => 'Event',
            '@id' => $canonical.'#event',
            'name' => $eventTitle,
            'description' => $this->description($description),
            'url' => $canonical,
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'organizer' => ['@id' => $this->baseUrl().'#organization'],
            ...$this->imageProperty($image),
        ];

        if ($event?->starts_at) {
            $eventSchema['startDate'] = $event->starts_at->toAtomString();
        }

        if ($event?->ends_at) {
            $eventSchema['endDate'] = $event->ends_at->toAtomString();
        }

        $locationName = collect([$event?->venue, $event?->address])->filter()->implode(', ');
        if ($locationName !== '') {
            $eventSchema['location'] = [
                '@type' => 'Place',
                'name' => $locationName,
                'address' => $event?->address ?: $event?->venue,
            ];
        }

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $image,
            schema: [
                $this->organizationSchema(),
                $this->webPageSchema($canonical, $title, $description),
                $eventSchema,
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => $eventLabel, 'url' => LocalizedUrl::route('bni.handover')],
                    ['name' => $guestName, 'url' => $canonical],
                ]),
            ],
            robots: self::NOINDEX_ROBOTS,
        );
    }

    /** @param iterable<array<string, mixed>> $packages */
    public function pricing(?Service $service, iterable $packages): array
    {
        $canonical = LocalizedUrl::route('pricing.index');
        $title = $service
            ? 'Bảng giá '.$service->title.' | '.$this->website->site_name
            : 'Bảng giá theo dịch vụ | '.$this->website->site_name;
        $description = $service
            ? 'Các gói và mức đầu tư tham khảo cho dịch vụ '.$service->title.' tại '.$this->website->site_name.'.'
            : 'Chọn từng dịch vụ để xem đúng bảng giá và phạm vi công việc tại '.$this->website->site_name.'.';
        $offers = [];

        foreach ($packages as $package) {
            $offers[] = [
                '@type' => 'Offer',
                'name' => $package['name'],
                'description' => $this->description($package['description'] ?: $package['name']),
                'url' => $canonical.'#goi-dich-vu',
            ];
        }

        $schema = [
            $this->organizationSchema(),
            $this->webPageSchema($canonical, $title, $description),
            $this->breadcrumb([
                ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                ['name' => __('site.pricing'), 'url' => $canonical],
            ]),
        ];

        if ($offers !== []) {
            $schema[] = [
                '@type' => 'OfferCatalog',
                '@id' => $canonical.'#offer-catalog',
                'name' => $service ? 'Bảng giá '.$service->title : 'Bảng giá dịch vụ',
                'itemListElement' => $offers,
            ];
        }

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            schema: $schema,
        );
    }

    public function service(Service $service): array
    {
        $canonical = LocalizedUrl::service($service);
        $title = $service->seo_title ?: $service->title.' | '.$this->website->site_name;
        $description = $service->seo_description ?: $service->excerpt ?: $service->title;

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $service->image_url,
            schema: [
                $this->organizationSchema(),
                [
                    '@type' => 'Service',
                    '@id' => $canonical.'#service',
                    'name' => $service->title,
                    'description' => $this->description($description),
                    'url' => $canonical,
                    ...$this->imageProperty($service->image_url),
                    'provider' => ['@id' => $this->baseUrl().'#organization'],
                ],
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => __('site.services'), 'url' => LocalizedUrl::route('services.index')],
                    ['name' => $service->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function landingPage(LandingPage $landingPage): array
    {
        $canonical = LocalizedUrl::landingPage($landingPage);
        $title = $landingPage->seo_title ?: $landingPage->title.' | '.$this->website->site_name;
        $description = $landingPage->seo_description ?: $landingPage->excerpt ?: $landingPage->title;

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $landingPage->image_url,
            schema: [
                $this->organizationSchema(),
                $this->webPageSchema($canonical, $landingPage->title, $description),
                $this->breadcrumb([
                    ['name' => __('site.home'), 'url' => LocalizedUrl::route('home')],
                    ['name' => 'Landing page', 'url' => $canonical],
                    ['name' => $landingPage->title, 'url' => $canonical],
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

    private function page(
        string $title,
        ?string $description,
        string $canonical,
        ?string $image = null,
        string $type = 'website',
        array $schema = [],
        string $robots = self::INDEX_ROBOTS,
    ): array {
        $description = $this->description($description);
        $image = $image ?: $this->defaultImageUrl();

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->website->seo_keywords,
            'robots' => $robots,
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
        return 'vi-VN';
    }

    private function homeCanonical(): string
    {
        return $this->absoluteUrl('/');
    }
}
