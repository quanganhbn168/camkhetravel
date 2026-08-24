<?php

namespace Tests\Feature;

use App\Http\Controllers\LegacyContentController;
use App\Models\ContentItem;
use App\Models\Redirect;
use App\Support\Seo\SeoMetadataBuilder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tests\TestCase;

class LegacySeoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_imported_homepage_is_available_and_staging_is_noindex(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertSee('<link rel="canonical" href="'.$baseUrl.'/">', false);

        $this->assertStringNotContainsString(
            '<link rel="canonical" href="'.$baseUrl.'/tht-media/">',
            $response->getContent(),
        );
    }

    public function test_imported_post_preserves_title_description_and_canonical_path(): void
    {
        $path = '/lam-phim-doanh-nghiep-yeu-to-khong-the-thieu-trong-marketing/';

        $html = $this->renderLegacyPath($path);

        preg_match('/<title>(.*?)<\/title>/u', $html, $title);
        $this->assertArrayHasKey(1, $title);
        $this->assertLessThanOrEqual(
            60,
            mb_strlen(html_entity_decode($title[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')),
        );
        $this->assertStringContainsString('<meta name="description"', $html);
        $this->assertStringContainsString(
            '<link rel="canonical" href="'.rtrim(config('app.url'), '/').$path.'">',
            $html,
        );
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function test_service_and_portfolio_prefixes_match_wordpress(): void
    {
        $service = '/service/tvc-doanh-nghiep/';
        $portfolio = '/du-an/giai-the-thao-chao-mung-thanh-lap-thi-xa-que-vo/';

        $this->assertStringContainsString('<article>', $this->renderLegacyPath($service));
        $this->assertStringContainsString('<article>', $this->renderLegacyPath($portfolio));
    }

    public function test_contact_uses_the_root_slug_without_a_trailing_slash(): void
    {
        $this->get('/lien-he')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/lien-he">', false);
    }

    public function test_legacy_front_page_slug_redirects_to_root(): void
    {
        $this->get('/tht-media/?utm_source=legacy')
            ->assertStatus(301)
            ->assertRedirect('/?utm_source=legacy');
    }

    public function test_imported_entities_are_not_double_escaped_in_meta_description(): void
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->where('canonical_path', '<>', '/tht-media/')
            ->where('seo_description', 'like', '%&amp;%')
            ->firstOrFail();

        $html = $this->renderLegacyPath($item->canonical_path);

        $this->assertStringNotContainsString('&amp;amp;', $html);
    }

    public function test_generated_rank_math_description_is_limited_to_160_characters(): void
    {
        $html = $this->renderLegacyPath(
            '/lam-phim-doanh-nghiep-yeu-to-khong-the-thieu-trong-marketing/',
        );

        preg_match('/<meta name="description" content="([^"]*)">/', $html, $matches);

        $this->assertArrayHasKey(1, $matches);
        $description = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->assertLessThanOrEqual(160, mb_strlen($description));
    }

    public function test_explicit_imported_description_is_also_limited_to_160_characters(): void
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->whereRaw('CHAR_LENGTH(seo_description) > 200')
            ->firstOrFail();

        $metadata = app(SeoMetadataBuilder::class)->forContent($item);

        $this->assertLessThanOrEqual(160, mb_strlen((string) $metadata->description));
    }

    public function test_published_landing_without_rank_math_template_gets_safe_description(): void
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->where('type', 'landing')
            ->whereNotNull('canonical_path')
            ->whereNull('seo_description')
            ->firstOrFail();

        $metadata = app(SeoMetadataBuilder::class)->forContent($item);

        $this->assertNotNull($metadata->description);
        $this->assertLessThanOrEqual(160, mb_strlen($metadata->description));
    }

    public function test_content_without_featured_media_uses_rank_math_default_social_image(): void
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->whereNull('featured_media_source_id')
            ->firstOrFail();

        $metadata = app(SeoMetadataBuilder::class)->forContent($item);

        $this->assertSame(
            rtrim(config('app.url'), '/')
                .'/storage/media/wordpress/2023/01/1672729584.webp',
            $metadata->ogImageUrl,
        );
        $this->assertSame($metadata->ogImageUrl, $metadata->twitterImageUrl);
    }

    public function test_fallback_schema_contains_organization_website_and_page_graph(): void
    {
        $item = ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->whereNull('structured_data')
            ->firstOrFail();

        $metadata = app(SeoMetadataBuilder::class)->forContent($item);
        $types = collect($metadata->structuredData[0]['@graph'])->pluck('@type')->all();

        $this->assertContains('Organization', $types);
        $this->assertContains('WebSite', $types);
        $this->assertContains($item->type === 'post' ? 'Article' : 'WebPage', $types);
    }

    public function test_unknown_legacy_url_returns_not_found(): void
    {
        $this->get('/duong-dan-chac-chan-khong-ton-tai/')->assertNotFound();
    }

    public function test_imported_404_page_returns_real_404_and_is_not_indexable(): void
    {
        $response = $this->get('/404-not-found/')
            ->assertNotFound()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->assertStringNotContainsString('<link rel="canonical"', $response->getContent());
        $this->assertStringNotContainsString('application/ld+json', $response->getContent());
    }

    public function test_internal_utility_pages_are_noindex_and_not_in_sitemap(): void
    {
        foreach (['/search/', '/under-construction/', '/test/'] as $path) {
            $response = $this->get($path)
                ->assertOk()
                ->assertSee('<meta name="robots" content="noindex, follow">', false);

            $this->assertStringNotContainsString('<link rel="canonical"', $response->getContent());
            $this->assertStringNotContainsString('application/ld+json', $response->getContent());
        }

        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();

        foreach (['/404-not-found/', '/search/', '/under-construction/', '/test/'] as $path) {
            $this->assertStringNotContainsString('<loc>'.rtrim(config('app.url'), '/').$path.'</loc>', $sitemap);
        }
    }

    public function test_managed_legacy_redirect_is_applied_and_counted(): void
    {
        $redirect = Redirect::query()->create([
            'source' => 'test',
            'from_path' => '/duong-dan-seo-cu/',
            'to_url' => '/lien-he/',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $this->get('/duong-dan-seo-cu/')
            ->assertStatus(301)
            ->assertRedirect('/lien-he/');

        $this->assertSame(1, (int) $redirect->fresh()->hits);
        $this->assertNotNull($redirect->fresh()->last_hit_at);
    }

    public function test_imported_structured_data_objects_are_preserved(): void
    {
        $item = new ContentItem([
            'type' => 'page',
            'title' => 'Câu hỏi thường gặp',
            'canonical_path' => '/cau-hoi-thuong-gap/',
            'structured_data' => [[
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ]],
        ]);

        $metadata = app(SeoMetadataBuilder::class)->forContent($item);

        $this->assertSame('FAQPage', $metadata->structuredData[0]['@type']);
    }

    public function test_sitemap_contains_each_canonical_path_once(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $baseUrl = rtrim(config('app.url'), '/');
        $serviceUrl = $baseUrl.'/chay-quang-cao-facebook';
        $postUrl = $baseUrl.'/tin-tuc/chay-quang-cao-facebook';

        $this->assertSame(1, substr_count($response->getContent(), '<loc>'.$serviceUrl.'</loc>'));
        $this->assertSame(1, substr_count($response->getContent(), '<loc>'.$postUrl.'</loc>'));
        $this->assertSame(1, substr_count($response->getContent(), '<loc>'.$baseUrl.'/</loc>'));
        $this->assertStringNotContainsString('<loc>'.$baseUrl.'/tht-media/</loc>', $response->getContent());
        $this->assertStringContainsString('<loc>'.$baseUrl.'/tin-tuc</loc>', $response->getContent());
        $this->assertStringNotContainsString('<loc>'.$baseUrl.'/blog/</loc>', $response->getContent());
        $this->assertStringContainsString(
            '<loc>'.$baseUrl.'/landing-cate/dich-vu-media/</loc>',
            $response->getContent(),
        );
        $this->assertStringNotContainsString(
            '<loc>'.$baseUrl.'/danh-muc-du-an/video-highlight/</loc>',
            $response->getContent(),
        );
    }

    public function test_wordpress_blog_archive_redirects_to_the_native_news_listing(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $this->get('/blog/')
            ->assertMovedPermanently()
            ->assertRedirect($baseUrl.'/tin-tuc');

        $this->get('/blog/page/2/')
            ->assertMovedPermanently()
            ->assertRedirect($baseUrl.'/tin-tuc?page=2');

        foreach ([
            '/danh-muc-dich-vu/dich-vu-va-bang-gia/',
            '/danh-muc-du-an/anh/',
            '/danh-muc-du-an/video/',
            '/landing-cate/dao-tao/',
            '/landing-cate/dich-vu-media/',
            '/landing-cate/truyen-thong-quang-cao/',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_native_news_pagination_uses_a_self_referencing_canonical(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $this->get('/tin-tuc?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$baseUrl.'/tin-tuc?page=2">', false);
    }

    public function test_native_news_sorting_is_noindex_and_canonicalizes_to_the_default_listing(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $this->get('/tin-tuc?sort=oldest')
            ->assertOk()
            ->assertSee('Sắp xếp')
            ->assertSee('value="oldest" selected', false)
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="'.$baseUrl.'/tin-tuc">', false);

        $this->get('/tin-tuc?sort=oldest&page=2')
            ->assertOk()
            ->assertSee('sort=oldest&amp;page=3', false)
            ->assertSee('<link rel="canonical" href="'.$baseUrl.'/tin-tuc?page=2">', false);
    }

    public function test_empty_archive_is_noindex_without_canonical(): void
    {
        $response = $this->get('/danh-muc-du-an/video-highlight/')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $this->assertStringNotContainsString('<link rel="canonical"', $response->getContent());
        $this->assertStringNotContainsString('application/ld+json', $response->getContent());
    }

    public function test_admin_login_is_available(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('THT Media CMS');
    }

    private function renderLegacyPath(string $path): string
    {
        $view = app(LegacyContentController::class)->show(
            Request::create($path),
            trim($path, '/'),
            app(SeoMetadataBuilder::class),
        );

        $this->assertInstanceOf(View::class, $view);

        return $view->render();
    }
}
