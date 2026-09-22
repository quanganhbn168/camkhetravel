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
