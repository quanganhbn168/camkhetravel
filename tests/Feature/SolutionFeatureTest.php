<?php

namespace Tests\Feature;

use App\Models\Solution;
use App\Models\SolutionCategory;
use Database\Seeders\MediaSeeder;
use Database\Seeders\SolutionCategorySeeder;
use Database\Seeders\SolutionSeeder;
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
        $this->seed(SolutionCategorySeeder::class);
        $category = SolutionCategory::where('seed_key', 'pccc')->firstOrFail();
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

    public function test_solution_section_has_its_own_background_and_detail_keeps_menu_active(): void
    {
        $this->seed(\Database\Seeders\MenuSeeder::class);
        $this->seedSolutions();
        $this->get('/')->assertOk()->assertSee('data-solution-background', false);
        $solution = Solution::firstOrFail();
        $response = $this->get(route('solutions.show', ['solution' => $solution->slug]))->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertGreaterThan(0, $xpath->query('//a[@href="'.route('solutions.index').'" and contains(@class,"active")]')->length);
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

    public function test_home_background_is_inside_image_panel_and_link_in_separate_details_panel(): void
    {
        $this->seedSolutions();
        $response = $this->get('/')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(1, $xpath->query('//*[@id="giai-phap"]/div/header[contains(@class,"text-center")]/h2')->length);
        $this->assertSame(1, $xpath->query('//*[@id="giai-phap"]/div/header/p')->length);
        $this->assertSame(5, $xpath->query('//*[@id="giai-phap"]//*[contains(@class,"solution-image")]/img')->length);
        $this->assertSame(5, $xpath->query('//*[@id="giai-phap"]//*[contains(@class,"solution-list")]/a')->length);
    }

    public function test_legacy_seed_command_preserves_editor_changes_without_republishing_demos(): void
    {
        $this->seedSolutions();
        $solution = Solution::firstOrFail();
        $solution->update(['title' => 'Nội dung đã chỉnh']);
        $this->seed(SolutionSeeder::class);
        $this->assertSame(5, Solution::count());
        $this->assertSame('Nội dung đã chỉnh', $solution->fresh()->title);
        $this->assertNotEmpty($solution->fresh()->slug);
    }

    public function test_homepage_uses_each_solutions_image_and_detail_link(): void
    {
        $this->seedSolutions();
        $response = $this->get('/')->assertOk()->assertSee('Giải pháp cho từng loại công trình');
        foreach (Solution::published()->where('is_home', true)->get() as $solution) {
            $response->assertSee(route('solutions.show', ['solution' => $solution->slug]), false)
                ->assertSee($solution->image_url, false);
            $this->assertSame($solution->title, $solution->short_title);
        }
        $response->assertViewHas('solutions', fn ($rows) => $rows->count() === 5);
        $hidden = Solution::firstOrFail()->category->solutions()->create(['title' => 'Giải pháp ẩn QA', 'is_active' => false, 'is_home' => true]);
        $this->get('/')->assertDontSee($hidden->title);
        Solution::query()->update(['is_home' => false]);
        $this->get('/')->assertDontSee('id="giai-phap"', false);
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
        $this->get('/')->assertOk()->assertDontSee('id="giai-phap"', false);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee($url, false);
        $this->assertModelExists($solution);
    }
}
