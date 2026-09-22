<?php

namespace Tests\Feature;

use App\Models\Solution;
use Database\Seeders\SolutionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolutionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_solution_section_has_its_own_background_and_detail_keeps_menu_active(): void
    {
        $this->seed([\Database\Seeders\MenuSeeder::class, SolutionSeeder::class]);
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
        $solution = Solution::create(['title' => 'Nội dung an toàn QA', 'is_active' => true,
            'body' => '<p><strong>Nội dung hợp lệ</strong></p><img src="x" onerror="alert(1)"><script>alert(2)</script>']);
        $this->get(route('solutions.show', ['solution' => $solution->slug]))->assertOk()
            ->assertSee('<strong>Nội dung hợp lệ</strong>', false)
            ->assertDontSee('onerror=', false)->assertDontSee('<script>alert(2)</script>', false);
    }

    public function test_home_background_is_inside_image_panel_and_link_in_separate_details_panel(): void
    {
        $this->seed(SolutionSeeder::class);
        $response = $this->get('/')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(1, $xpath->query('//*[@id="giai-phap"]/div/header[contains(@class,"text-center")]/h2')->length);
        $this->assertSame(1, $xpath->query('//*[@id="giai-phap"]/div/header/p')->length);
        $this->assertSame(5, $xpath->query('//*[@id="giai-phap"]//*[contains(@class,"solution-image")]/img')->length);
        $this->assertSame(5, $xpath->query('//*[@id="giai-phap"]//*[contains(@class,"solution-list")]/a')->length);
    }

    public function test_seed_is_repeatable_and_preserves_editor_changes(): void
    {
        $this->seed(SolutionSeeder::class);
        $this->assertSame(5, Solution::count());
        $solution = Solution::firstOrFail();
        $solution->update(['title' => 'Nội dung đã chỉnh']);
        $this->seed(SolutionSeeder::class);
        $this->assertSame(5, Solution::count());
        $this->assertSame('Nội dung đã chỉnh', $solution->fresh()->title);
        $this->assertNotEmpty($solution->fresh()->slug);
    }

    public function test_homepage_uses_each_solutions_image_and_detail_link(): void
    {
        $this->seed(SolutionSeeder::class);
        $response = $this->get('/')->assertOk()->assertSee('Giải pháp cho từng loại công trình');
        foreach (Solution::published()->where('is_home', true)->get() as $solution) {
            $response->assertSee(route('solutions.show', ['solution' => $solution->slug]), false)
                ->assertSee($solution->image_url, false);
        }
        $response->assertViewHas('solutions', fn ($rows) => $rows->count() === 5);
        $hidden = Solution::create(['title' => 'Giải pháp ẩn QA', 'is_active' => false, 'is_home' => true]);
        $this->get('/')->assertDontSee($hidden->title);
        Solution::query()->update(['is_home' => false]);
        $this->get('/')->assertDontSee('id="giai-phap"', false);
    }

    public function test_detail_and_sitemap_only_publish_active_solutions(): void
    {
        $this->seed(SolutionSeeder::class);
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
}
