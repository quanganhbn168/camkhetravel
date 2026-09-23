<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Product;
use App\Models\ProductCategory;
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

    public function test_home_service_image_belongs_to_category_not_service(): void
    {
        $category = \App\Models\ServiceCategory::create([
            'name' => 'Danh mục ảnh QA', 'is_active' => true, 'is_featured' => true, 'is_home' => true,
            'curator_media_id' => \Database\Seeders\MediaSeeder::id('facility'),
        ]);
        \App\Models\Service::create([
            'title' => 'Dịch vụ ảnh QA', 'service_category_id' => $category->id,
            'status' => 'published', 'is_home' => true,
            'curator_media_id' => \Database\Seeders\MediaSeeder::id('equipment'),
        ]);

        $response = $this->get(route('home'))->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $images = $xpath->query('//*[@id="service-pane-'.$category->id.'"]//img');
        $this->assertSame(1, $images->length);
        $this->assertSame($category->image_url, $images->item(0)->getAttribute('src'));
        $this->assertSame($category->name, $images->item(0)->getAttribute('alt'));

        $category->update(['curator_media_id' => null]);
        $response = $this->get(route('home'))->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(0, $xpath->query('//*[@id="service-pane-'.$category->id.'"]//img')->length);
        $response->assertSee('Dịch vụ ảnh QA');
    }

    public function test_home_uses_real_equipment_and_discrete_partner_slides(): void
    {
        $category = ProductCategory::create(['name' => 'Danh mục thiết bị QA', 'is_active' => true]);
        $product = Product::create(['title' => 'Thiết bị công khai QA', 'product_category_id' => $category->id, 'status' => 'published']);
        Product::create(['title' => 'Thiết bị nháp QA', 'product_category_id' => $category->id, 'status' => 'draft']);
        Partner::create(['name' => 'Đối tác công khai QA', 'is_active' => true]);
        Partner::create(['name' => 'Đối tác ẩn QA', 'is_active' => false]);

        $response = $this->get(route('home'))->assertOk()
            ->assertSee('Tại sao chọn chúng tôi')->assertSee('Quy trình triển khai')
            ->assertSee('Danh mục thiết bị')->assertSee('Liên hệ tư vấn miễn phí')
            ->assertSee('Đối tác của chúng tôi')->assertSee('data-partner-swiper', false)
            ->assertSee('Danh mục thiết bị QA')->assertSee('Thiết bị công khai QA')
            ->assertSee(route('products.show', ['slug' => $product->slug]), false)
            ->assertDontSee('Thiết bị nháp QA')->assertDontSee('Đối tác ẩn QA')
            ->assertDontSee('partner-marquee')->assertDontSee('Một hệ thống PCCC tốt không chỉ nằm ở thiết bị');
        $this->assertSame(1, substr_count($response->getContent(), 'Đối tác công khai QA'));
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
