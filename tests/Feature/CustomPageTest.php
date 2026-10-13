<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Partner;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_published_custom_page_is_served_at_its_root_slug(): void
    {
        Page::query()->create([
            'title' => 'Chính sách bảo mật',
            'slug' => 'chinh-sach-bao-mat',
            'status' => 'published',
        ]);

        $this->get('/chinh-sach-bao-mat')
            ->assertOk()
            ->assertSee('Chính sách bảo mật');
    }

    public function test_draft_custom_page_is_not_public(): void
    {
        Page::query()->create([
            'title' => 'Bản nháp',
            'slug' => 'ban-nhap',
            'status' => 'draft',
        ]);

        $this->get('/ban-nhap')->assertNotFound();
    }

    public function test_partner_name_is_optional(): void
    {
        $partner = Partner::query()->create(['is_active' => true, 'sort_order' => 1]);

        $this->assertNull($partner->fresh()->name);
    }
}
