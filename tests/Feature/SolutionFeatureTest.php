<?php

namespace Tests\Feature;

use App\Models\Solution;
use App\Models\SolutionCategory;
use Database\Seeders\MediaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolutionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function seedSolutions(): void
    {
        $category = SolutionCategory::create([
            'name' => 'Nhóm giải pháp QA',
            'is_active' => true,
        ]);
        $mediaId = MediaSeeder::id('facility');
        foreach (range(1, 5) as $number) {
            $category->solutions()->create([
                'title' => 'Giải pháp PCCC nhà xưởng QA '.$number,
                'excerpt' => 'Nội dung phục vụ kiểm thử',
                'curator_media_id' => $mediaId,
                'banner_media_id' => $mediaId,
                'is_active' => true,
                'is_home' => true,
                'sort_order' => $number,
            ]);
        }
    }

    public function test_detail_sanitizes_editor_html_without_losing_formatting(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục HTML QA', 'is_active' => true]);
        $solution = $category->solutions()->create(['title' => 'Nội dung an toàn QA', 'is_active' => true,
            'body' => '<p><strong>Nội dung hợp lệ</strong></p><img src="x" onerror="alert(1)"><script>alert(2)</script>']);
        $this->get(route('solutions.show', ['solution' => $solution->slug]))->assertOk()
            ->assertSee('<strong>Nội dung hợp lệ</strong>', false)
            ->assertDontSee('onerror=', false)->assertDontSee('<script>alert(2)</script>', false);
    }

    public function test_detail_and_sitemap_only_publish_active_solutions(): void
    {
        $this->seedSolutions();
        $solution = Solution::firstOrFail();
        $url = route('solutions.show', ['solution' => $solution->slug]);
        $this->get($url)->assertOk()->assertSee($solution->title)->assertSee('rel="canonical" href="'.$url.'"', false);
        $this->get('/giai-phap')->assertOk()->assertSee($solution->title);
        $this->get('/sitemap.xml')->assertOk()->assertSee($url, false);
        $solution->update(['is_active' => false]);
        $this->get($url)->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee($url, false);
        $this->get('/giai-phap/khong-ton-tai')->assertNotFound();
    }

    public function test_inactive_category_hides_solutions_from_every_public_entry_point(): void
    {
        $this->seedSolutions();
        $solution = Solution::firstOrFail();
        $solution->category->update(['is_active' => false]);
        $url = route('solutions.show', ['solution' => $solution->slug]);
        $this->get($url)->assertNotFound();
        $this->get('/giai-phap')->assertOk()->assertDontSee($solution->title);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee($url, false);
        $this->assertModelExists($solution);
    }
}
