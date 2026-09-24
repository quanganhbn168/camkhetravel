<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeContentSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_displays_address_without_branch_name(): void
    {
        \Illuminate\Support\Facades\View::share('footerContactBranches', collect([
            ['name' => 'Trụ sở chính', 'address' => 'Địa chỉ kiểm tra Bắc Ninh'],
        ]));

        $this->get(route('posts.index'))->assertOk()
            ->assertSee('Địa chỉ kiểm tra Bắc Ninh')
            ->assertDontSee('Trụ sở chính');
    }

    public function test_home_service_uses_its_own_image_and_falls_back_cleanly(): void
    {
        $category = ServiceCategory::create([
            'name' => 'Danh mục ảnh QA', 'is_active' => true,
        ]);
        $media = Media::create([
            'disk' => 'public',
            'directory' => 'qa',
            'visibility' => 'public',
            'name' => 'service-home-image',
            'title' => 'Service home image',
            'path' => 'qa/service-home-image.jpg',
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'size' => 10,
        ]);
        $service = Service::create([
            'title' => 'Dịch vụ ảnh QA', 'service_category_id' => $category->id,
            'status' => 'published', 'is_home' => true,
            'curator_media_id' => $media->id,
        ]);

        $response = $this->get(route('home'))->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $images = $xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " service-card ")][.//button[@data-service-id="'.$service->id.'"]]//img');
        $this->assertSame(1, $images->length);
        $this->assertSame(MediaUrl::versioned($media), $images->item(0)->getAttribute('src'));
        $this->assertSame($service->title, $images->item(0)->getAttribute('alt'));

        $service->update(['curator_media_id' => null]);
        $response = $this->get(route('home'))->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $images = $xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " service-card ")][.//button[@data-service-id="'.$service->id.'"]]//img');
        $this->assertSame(1, $images->length);
        $this->assertSame(asset('images/no-image.svg'), $images->item(0)->getAttribute('src'));
    }

    public function test_blog_archive_has_category_bar_without_sidebar(): void
    {
        $this->get(route('posts.index'))->assertOk()
            ->assertSee('Tin tức &amp; kiến thức', false)
            ->assertSee('blog-categories', false)
            ->assertDontSee('news-sidebar', false)
            ->assertSee('name="sort"', false);
    }
}
