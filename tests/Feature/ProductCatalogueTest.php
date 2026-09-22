<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Tag;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductCatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_products_have_their_own_category_and_public_routes(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Thiết bị kiểm thử',
            'description' => 'Danh mục thiết bị PCCC kiểm thử.',
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'product_category_id' => $category->getKey(),
            'title' => 'Tủ báo cháy kiểm thử',
            'excerpt' => 'Thiết bị báo cháy cho công trình.',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee($product->title);
        $this->get(route('products.category', ['slug' => $category->slug]))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee($product->title);
        $this->get(route('products.show', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSee($product->title)
            ->assertSee('<meta name="description"', false);
    }

    public function test_product_tags_and_faqs_are_polymorphic(): void
    {
        $product = Product::query()->create([
            'title' => 'Thiết bị morphable kiểm thử',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $tag = Tag::query()->create(['name' => 'Tag kiểm thử']);
        $product->tags()->sync([$tag->getKey() => ['sort_order' => 10]]);
        $faq = $product->faqs()->create([
            'question' => 'FAQ sản phẩm kiểm thử?',
            'answer' => 'Câu trả lời kiểm thử.',
            'group' => 'default',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $this->assertSame('product', DB::table('taggables')->where('tag_id', $tag->getKey())->value('taggable_type'));
        $this->assertSame('product', $faq->faqable_type);
        $this->assertTrue($product->fresh()->tags->contains($tag));
        $this->assertSame($faq->getKey(), $product->fresh()->faqs->first()->getKey());
    }
}
