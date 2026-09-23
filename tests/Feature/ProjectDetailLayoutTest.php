<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDetailLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_project_has_reference_layout_and_gallery_controls(): void
    {
        $project = Project::create(['title' => 'Dự án công trình mẫu', 'status' => 'published']);
        $this->seed(\Database\Seeders\ProjectDetailContentSeeder::class);
        $response = $this->get(route('projects.show', ['slug' => $project->slug]))->assertOk();
        $response->assertSee('Dự án công trình mẫu')->assertSee('Thông tin chung')
            ->assertSee('Thách thức của dự án')->assertSee('Hiệu quả sau bàn giao')
            ->assertSee('data-project-gallery', false)->assertSee('data-project-thumbs', false)
            ->assertSee('data-project-site-swiper', false)->assertSee('Nội dung minh họa', false);
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        foreach (['tong-quan', 'giai-phap-du-an', 'hang-muc', 'hinh-anh-du-an', 'ket-qua', 'du-an-lien-quan'] as $id) {
            $this->assertSame(1, $xpath->query('//*[@id="'.$id.'"]')->length);
            $this->assertGreaterThan(0, $xpath->query('//a[@href="#'.$id.'"]')->length);
        }
        $this->assertSame(1, $xpath->query('//h1')->length);
    }

    public function test_draft_project_remains_private(): void
    {
        $project = Project::create(['title' => 'Dự án nháp', 'status' => 'draft']);
        $this->get(route('projects.show', ['slug' => $project->slug]))->assertNotFound();
    }
}
