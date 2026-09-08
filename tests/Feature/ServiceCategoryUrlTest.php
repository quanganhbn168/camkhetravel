<?php

namespace Tests\Feature;

use App\Models\ServiceCategory;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceCategoryUrlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_uses_morph_slug_and_redirects_old_urls(): void
    {
        $category = ServiceCategory::query()->create(['name' => 'Category URL QA', 'slug' => 'category-url-qa', 'is_active' => true]);
        $url = LocalizedUrl::serviceCategory($category);
        $this->assertStringEndsWith('/dich-vu/category-url-qa', $url);
        $this->get('/dich-vu/category-url-qa')->assertOk()->assertSee($url)->assertDontSee('resource-card__badge', false);
        $this->get('/dich-vu/danh-muc/category-url-qa')->assertRedirect($url)->assertStatus(301);
        $this->get('/category-url-qa')->assertRedirect($url)->assertStatus(301);
        $category->update(['is_active' => false]);
        $this->get('/dich-vu/category-url-qa')->assertNotFound();
        $this->get('/dich-vu/danh-muc/category-url-qa')->assertNotFound();
    }
}
