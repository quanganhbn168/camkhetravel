<?php

namespace App\Support\Seo;

use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
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
            schema: [$this->organizationSchema()],
        );
    }

    public function siteName(): string
    {
        return $this->website->site_name;
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
                'name' => $this->website->site_name,
                'url' => $this->baseUrl(),
                'inLanguage' => $this->languageTag(),
            ],
            $this->webPageSchema($canonical, (string) $profile['seo_title'], (string) $profile['seo_description']),
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
            useDefaultImage: false,
        );
    }

    public function listing(string $title, string $description, string $canonical, bool $indexable = true, ?string $image = null): array
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
                $this->organizationSchema(),
                $this->webPageSchema($canonical, (string) $profile['seo_title'], (string) $profile['seo_description']),
            ],
            useDefaultImage: false,
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
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => 'Dịch vụ', 'url' => route('services.index')],
                    ['name' => $service->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function project(Project $project): array
    {
        $canonical = route('projects.show', ['slug' => $project->slug]);
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
            image: $project->seoImageUrl($project->image_url),
            schema: [
                $this->organizationSchema(),
                $creativeWork,
                $this->breadcrumb([
                    ['name' => 'Trang chủ', 'url' => route('home')],
                    ['name' => 'Dự án', 'url' => route('projects.index')],
                    ['name' => $project->title, 'url' => $canonical],
                ]),
            ],
        );
    }

    public function post(Post $post): array
    {
        $canonical = route('slug.show', ['slug' => $post->slug]);
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
            image: $post->seoImageUrl($post->image_url),
            type: 'article',
            schema: [
                $this->organizationSchema(),
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
                $this->organizationSchema(),
                [
                    '@type' => 'Product',
                    '@id' => $canonical.'#product',
                    'name' => $product->title,
                    'description' => $this->description($description),
                    'url' => $canonical,
                    ...$this->imageProperty($product->image_url),
                    'brand' => ['@id' => $this->baseUrl().'#organization'],
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
