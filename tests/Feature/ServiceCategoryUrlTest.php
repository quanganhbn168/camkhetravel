<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_category_uses_morph_slug_and_redirects_old_urls(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Category URL QA', 'slug' => 'category-url-qa', 'is_active' => true]);
        $url = route('services.category', ['category' => $category->slug]);
        $this->assertStringEndsWith('/dich-vu/category-url-qa', $url);
        $this->get('/dich-vu/category-url-qa')->assertOk()->assertSee($url)->assertDontSee('resource-card__badge', false);
        $this->get('/dich-vu/danh-muc/category-url-qa')->assertRedirect($url)->assertStatus(301);
        $this->get('/category-url-qa')->assertRedirect($url)->assertStatus(301);
        $category->update(['is_active' => false]);
        $this->get('/dich-vu/category-url-qa')->assertNotFound();
        $this->get('/dich-vu/danh-muc/category-url-qa')->assertNotFound();
    }
}
