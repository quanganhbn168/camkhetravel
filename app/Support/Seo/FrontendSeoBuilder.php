<?php

namespace App\Support\Seo;

use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class FrontendSeoBuilder
{
    private const INDEX_ROBOTS = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    private const NOINDEX_ROBOTS = 'noindex, nofollow, noarchive';

    private ?string $defaultImage = null;

    public function __construct(private readonly WebsiteSettings $website) {}

    public function default(): array
    {
        return $this->page(
            title: $this->website->seo_title ?: $this->website->site_name,
            description: $this->website->seo_description,
            canonical: $this->homeCanonical(),
        );
    }

    public function siteName(): string
    {
        return $this->website->site_name;
    }

    /** JSON-LD của một trang; view của trang gọi qua <x-json-ld>. Encode ở PHP vì Blade hiểu "@context" là directive. */
    public static function jsonLd(array $seo): ?string
    {
        $graph = $seo['schema'] ?? [];

        return $graph === [] ? null : json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
        );
    }

    private function organizationName(): string
    {
        return $this->website->company_name ?: $this->website->site_name ?: (string) config('app.name');
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  iterable<array{question: string, answer: string}>  $faqItems
     */
    public function home(array $profile, iterable $faqItems = []): array
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
                'name' => $this->website->site_name ?: $this->organizationName(),
                'url' => $this->baseUrl(),
                'inLanguage' => $this->languageTag(),
                'publisher' => ['@id' => $this->baseUrl().'#organization'],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('search').'?q={search_term_string}'],
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            $this->webPageSchema($canonical, (string) $profile['seo_title'], (string) $profile['seo_description'], home: true),
        ];

        if ($faqSchemaItems !== []) {
            $schema[] = [
                '@type' => 'FAQPage',
                '@id' => $canonical.'#cau-hoi-thuong-gap',
                'mainEntity' => $faqSchemaItems,
            ];
        }

        return $this->page(
            title: (string) $profile['seo_title'],
            description: (string) $profile['seo_description'],
            canonical: $canonical,
            schema: $schema,
            image: (string) $profile['og_image_url'],
        );
    }

    /** @param list<array{name: string, url: string}> $trail Breadcrumb sau "Trang chủ"; để trống thì dùng chính trang này. */
    public function listing(string $title, string $description, string $canonical, bool $indexable = true, ?string $image = null, array $trail = [], string $pageType = 'CollectionPage'): array
    {
        $trail = $trail ?: [['name' => $title, 'url' => $canonical]];

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            schema: $indexable ? [
                $this->webPageSchema($canonical, $title, $description, type: $pageType),
                $this->breadcrumb([['name' => 'Trang chủ', 'url' => route('home')], ...$trail]),
            ] : [],
            robots: $indexable ? self::INDEX_ROBOTS : self::NOINDEX_ROBOTS,
            image: $image,
        );
    }

    /** @param array<string, mixed> $profile */
    public function systemPage(array $profile, string $routeName): array
    {
        $canonical = route($routeName);

        return $this->page(
            title: (string) $profile['seo_title'],
            description: (string) $profile['seo_description'],
            canonical: $canonical,
            image: (string) $profile['og_image_url'],
            schema: [
                $this->webPageSchema($canonical, (string) $profile['seo_title'], (string) $profile['seo_description'], type: match ($routeName) {
                    'about' => 'AboutPage',
                    'contact' => 'ContactPage',
                    default => 'CollectionPage',
                }),
                $this->breadcrumb([
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => (string) ($profile['title'] ?? $profile['seo_title']), 'url' => $canonical],
                ]),
            ],
        );
    }

    public function solution(\App\Models\Solution $solution): array
    {
        $canonical = route('solutions.show', ['solution' => $solution->slug]);
        return $this->page(
            title: $solution->seo_title ?: $solution->title,
            description: $solution->seo_description ?: $solution->excerpt,
            canonical: $canonical,
            image: $solution->seoImageUrl(),
            schema: [$this->webPageSchema($canonical, (string) ($solution->seo_title ?: $solution->title), (string) ($solution->seo_description ?: $solution->excerpt)), $this->breadcrumb([
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => 'Giải pháp', 'url' => route('solutions.index')],
                ['name' => $solution->title, 'url' => $canonical],
            ])],
        );
    }

    public function service(Service $service): array
    {
        $canonical = route('slug.show', ['slug' => $service->slug]);
        $title = $service->seo_title ?: $service->title.' | '.$this->website->site_name;
        $description = $service->seo_description ?: $service->excerpt ?: $service->title;

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $service->seoImageUrl($service->image_url),
            schema: [
                [
                    '@type' => 'Service',
                    '@id' => $canonical.'#service',
                    'name' => $service->title,
                    'description' => $this->description($description),
                    'url' => $canonical,
                    ...$this->imageProperty($service->image_url),
                    'provider' => $this->publisherNode(),
                    ...$this->serviceAreaProperty($service),
                    ...$this->serviceRatingProperties($service),
                ],
                $this->serviceFaqSchema($service, $canonical),
                $this->breadcrumb([
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => 'Dịch vụ', 'url' => route('services.index')],
                    ['name' => $service->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    /**
     * Điểm đánh giá chỉ lấy từ đánh giá thật đã được duyệt của chính dịch vụ; chưa có đánh giá thì không khai báo.
     *
     * @return array<string, mixed>
     */
    private function serviceRatingProperties(Service $service): array
    {
        if (! $service->relationLoaded('approvedComments')) {
            return [];
        }

        $rated = $service->approvedComments->filter(fn ($comment): bool => $comment->rating !== null)->values();
        if ($rated->isEmpty()) {
            return [];
        }

        return [
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => round((float) $rated->avg('rating'), 1),
                'ratingCount' => $rated->count(),
                'bestRating' => 5,
                'worstRating' => 1,
            ],
            'review' => $rated->take(5)->map(fn ($comment): array => [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $comment->author_name],
                ...($comment->approved_at ? ['datePublished' => $comment->approved_at->toDateString()] : []),
                'reviewBody' => Str::limit(Str::squish((string) $comment->body), 500, ''),
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => (int) $comment->rating, 'bestRating' => 5, 'worstRating' => 1],
            ])->all(),
        ];
    }

    /** @return array<string, mixed> */
    private function serviceAreaProperty(Service $service): array
    {
        $areas = collect($service->area_items ?? [])
            ->map(fn (mixed $item): string => is_array($item) ? trim((string) ($item['title'] ?? '')) : '')
            ->filter()
            ->values();
        if ($areas->isEmpty() && filled($service->area_title)) {
            $areas = collect([trim((string) $service->area_title)]);
        }

        return $areas->isEmpty() ? [] : ['areaServed' => $areas->map(fn (string $name): array => ['@type' => 'Place', 'name' => $name])->all()];
    }

    /** @return array<string, mixed>|null */
    private function serviceFaqSchema(Service $service, string $canonical): ?array
    {
        $questions = $service->faqs
            ->filter(fn ($faq): bool => filled($faq->question) && filled($faq->answer))
            ->map(fn ($faq): array => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
            ])
            ->values()
            ->all();

        return $questions === [] ? null : ['@type' => 'FAQPage', '@id' => $canonical.'#faq', 'mainEntity' => $questions];
    }

    public function post(Post $post): array
    {
        $canonical = route('slug.show', ['slug' => $post->slug]);
        $title = $post->seo_title ?: $post->title.' | '.$this->website->site_name;
        $description = $post->seo_description ?: $post->excerpt ?: $post->title;

        $blogPosting = [
            '@type' => 'BlogPosting',
            '@id' => $canonical.'#article',
            'headline' => Str::limit($post->title, 110, ''),
            'description' => $this->description($description),
            'mainEntityOfPage' => ['@id' => $canonical],
            'url' => $canonical,
            ...$this->imageProperty($post->image_url),
            ...($post->category ? ['articleSection' => $post->category->name] : []),
            'inLanguage' => $this->languageTag(),
            'author' => $this->publisherNode(logo: false),
            'publisher' => $this->publisherNode(),
            'datePublished' => ($post->published_at ?: $post->created_at)->toAtomString(),
            'dateModified' => $post->updated_at->toAtomString(),
        ];

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $post->seoImageUrl($post->image_url),
            type: 'article',
            extra: [
                'published_time' => ($post->published_at ?: $post->created_at)->toAtomString(),
                'modified_time' => $post->updated_at->toAtomString(),
                'section' => $post->category?->name,
            ],
            schema: [
                $blogPosting,
                $this->breadcrumb([
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => 'Tin tức', 'url' => route('posts.index')],
                    ['name' => $post->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function product(Product $product): array
    {
        $canonical = route('products.show', ['slug' => $product->slug]);
        $title = $product->seo_title ?: $product->title.' | '.$this->website->site_name;
        $description = $product->seo_description ?: $product->excerpt ?: $product->title;

        return $this->page(
            title: $title,
            description: $description,
            canonical: $canonical,
            image: $product->seoImageUrl($product->image_url),
            schema: [
                [
                    '@type' => 'Product',
                    '@id' => $canonical.'#product',
                    'name' => $product->title,
                    'description' => $this->description($description),
                    'url' => $canonical,
                    ...$this->imageProperty($product->image_url),
                    'brand' => $this->publisherNode(logo: false),
                ],
                $this->breadcrumb([
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => 'Sản phẩm', 'url' => route('products.index')],
                    ['name' => $product->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function productCategory(ProductCategory $category): array
    {
        return $this->listing(
            $category->seo_title ?: $category->name.' | Sản phẩm',
            $category->seo_description ?: $category->description ?: 'Sản phẩm thuộc nhóm '.$category->name.'.',
            route('products.category', ['slug' => $category->slug]),
            image: $category->seoImageUrl(),
            trail: [['name' => 'Sản phẩm', 'url' => route('products.index')], ['name' => $category->name, 'url' => route('products.category', ['slug' => $category->slug])]],
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
        bool $useDefaultImage = true,
        array $extra = [],
    ): array {
        $description = $this->description($description);
        $image = $image ?: ($useDefaultImage ? $this->defaultImageUrl() : null);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->website->seo_keywords,
            'robots' => $robots,
            'canonical' => $canonical,
            'type' => $type,
            'locale' => 'vi_VN',
            'image' => $image,
            'image_alt' => $this->website->site_name,
            'article' => array_filter($extra),
            // Schema thuộc về từng trang: view của trang đó tự in qua <x-json-ld>, layout không in schema chung.
            'schema' => array_values(array_filter($schema)),
        ];
    }

    private function organizationSchema(): array
    {
        // Có địa chỉ thì khai báo LocalBusiness (Google yêu cầu address cho LocalBusiness); chưa có thì giữ Organization.
        $organization = [
            '@type' => filled($this->website->address) ? 'LocalBusiness' : 'Organization',
            '@id' => $this->baseUrl().'#organization',
            'name' => $this->organizationName(),
            'url' => $this->baseUrl(),
            ...$this->imageProperty($this->logoUrl(), 'logo'),
            ...$this->imageProperty($this->defaultImageUrl() ?: $this->logoUrl()),
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
                'addressCountry' => 'VN',
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

    private function webPageSchema(string $canonical, string $title, ?string $description, string $type = 'WebPage', bool $home = false): array
    {
        return [
            '@type' => $type,
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $title,
            'description' => $this->description($description),
            'inLanguage' => $this->languageTag(),
            // Chỉ trang chủ có node WebSite đầy đủ; trang khác nhúng thông tin tối thiểu để không tham chiếu node không có mặt.
            'isPartOf' => $home
                ? ['@id' => $this->baseUrl().'#website']
                : ['@type' => 'WebSite', 'name' => $this->website->site_name ?: $this->organizationName(), 'url' => $this->baseUrl()],
        ];
    }

    /** Organization rút gọn nhúng thẳng vào schema trang chi tiết (author/publisher/provider/brand). */
    private function publisherNode(bool $logo = true): array
    {
        return [
            '@type' => 'Organization',
            'name' => $this->organizationName(),
            'url' => $this->baseUrl(),
            ...($logo ? $this->imageProperty($this->logoUrl(), 'logo') : []),
        ];
    }

    private function logoUrl(): ?string
    {
        $mediaId = $this->website->logo_media_id ?: $this->website->seo_image_media_id;

        return $mediaId ? Media::query()->find($mediaId)?->url : null;
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
