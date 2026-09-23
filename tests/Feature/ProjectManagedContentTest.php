<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\Project;
use App\Models\User;
use Awcodes\Curator\Models\Media;
use Database\Seeders\MediaSeeder;
use Database\Seeders\ProjectDetailContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectManagedContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_removed_messages_are_absent_from_frontend_admin_and_fresh_seed(): void
    {
        $project = Project::create(['title' => 'Dự án công trình mẫu', 'status' => 'published']);
        $this->seed(ProjectDetailContentSeeder::class);
        $this->assertArrayNotHasKey('note', $project->fresh()->details['hero']);
        $this->assertArrayNotHasKey('signature', $project->fresh()->details['testimonial']);
        $details = $project->fresh()->details;
        $details['hero']['note'] = 'Thông điệp banner cũ QA';
        $details['testimonial']['signature'] = 'Thông điệp bên phải cũ QA';
        $project->update(['details' => $details]);
        $this->get(route('projects.show', ['slug' => $project->slug]))->assertOk()
            ->assertDontSee('Thông điệp banner cũ QA')->assertDontSee('Thông điệp bên phải cũ QA');
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        Livewire::test(EditProject::class, ['record' => $project->id])
            ->assertFormFieldDoesNotExist('details.hero.note')
            ->assertFormFieldDoesNotExist('details.testimonial.signature');
        Livewire::test(\App\Filament\Resources\Projects\Pages\CreateProject::class)
            ->assertFormFieldDoesNotExist('details.hero.note')
            ->assertFormFieldDoesNotExist('details.testimonial.signature');
    }

    public function test_admin_content_is_saved_and_rendered_without_preview_fallbacks(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $project = Project::create(['title' => 'Dự án CMS QA', 'status' => 'published']);
        $form = Livewire::test(EditProject::class, ['record' => $project->id])->fillForm([
            'excerpt' => 'Mô tả quản trị QA', 'body' => '<p>Tổng quan quản trị QA</p>',
            'gallery' => [...$this->mediaState('equipment'), ...$this->mediaState('facility')],
            'details' => [
                'hero' => ['location' => 'Địa điểm QA', 'area' => '1200 m²', 'banner_media_id' => $this->mediaState('warehouse')],
                'overview' => ['title' => 'Hồ sơ QA'],
                'challenges' => ['title' => 'Thách thức quản trị QA', 'items' => [['title' => 'Khó khăn QA', 'icon' => 'fa-industry']]],
                'solution' => ['title' => 'Giải pháp quản trị QA', 'items' => [['text' => 'Hạng mục giải pháp QA']], 'media_id' => $this->mediaState('equipment')],
                'scope' => ['title' => 'Phạm vi QA', 'items' => [['title' => 'Công việc QA', 'media_id' => $this->mediaState('facility')]]],
                'construction' => ['title' => 'Thi công QA', 'images' => $this->mediaState('facility')],
                'results' => ['title' => 'Kết quả quản trị QA', 'items' => [['title' => 'Chỉ số QA', 'description' => 'Chi tiết QA', 'icon' => 'fa-gear']]],
                'testimonial' => ['quote' => 'Nhận xét quản trị QA', 'name' => 'Khách hàng QA', 'role' => 'Đại diện QA'],
            ],
        ]);
        // Curator updates the whole selection, not individual metadata properties.
        $form->set('data.gallery', [...$this->mediaState('equipment'), ...$this->mediaState('facility')])
            ->set('data.details.hero.banner_media_id', $this->mediaState('warehouse'))
            ->set('data.details.solution.media_id', $this->mediaState('equipment'))
            ->set('data.details.construction.images', $this->mediaState('facility'));
        $scopeKey = array_key_first($form->get('data.details.scope.items'));
        $form->set("data.details.scope.items.{$scopeKey}.media_id", $this->mediaState('facility'))
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Địa điểm QA', data_get($project->fresh()->details, 'hero.location'));
        $this->assertSame(MediaSeeder::id('warehouse'), data_get($project->fresh()->details, 'hero.banner_media_id'));
        $this->assertSame([MediaSeeder::id('equipment'), MediaSeeder::id('facility')], $project->fresh()->gallery);
        // Reloading and saving the real form must keep nested Curator media and repeater data.
        Livewire::test(EditProject::class, ['record' => $project->id])->call('save')->assertHasNoFormErrors();
        $this->assertSame(MediaSeeder::id('warehouse'), data_get($project->fresh()->details, 'hero.banner_media_id'));
        $response = $this->get(route('projects.show', ['slug' => $project->slug]))->assertOk();
        foreach (['Hồ sơ QA', 'Tổng quan quản trị QA', 'Thách thức quản trị QA', 'Hạng mục giải pháp QA', 'Công việc QA', 'Chi tiết QA', 'Nhận xét quản trị QA', 'Địa điểm QA'] as $text) {
            $response->assertSee($text);
        }
        $response->assertDontSee('Công ty TNHH ABC Việt Nam')->assertDontSee('Nội dung minh họa');
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertStringEndsWith('/warehouse.png', $xpath->query('//img[contains(@class,"project-case__hero-image")]')->item(0)->getAttribute('src'));
        $this->assertStringEndsWith('/facility.png', $xpath->query('//*[@id="hang-muc"]//img')->item(0)->getAttribute('src'));
        $gallery = $xpath->query('//*[@data-project-gallery]//img');
        $this->assertStringEndsWith('/equipment.png', $gallery->item(0)->getAttribute('src'));
        $this->assertStringEndsWith('/facility.png', $gallery->item(1)->getAttribute('src'));
        $project->update(['details' => [], 'gallery' => []]);
        $response = $this->get(route('projects.show', ['slug' => $project->slug]))->assertOk();
        $response->assertDontSee('id="hang-muc"', false)->assertDontSee('id="ket-qua"', false)
            ->assertDontSee('data-project-gallery', false)->assertDontSee('Nhận xét quản trị QA');
    }

    public function test_only_selected_published_related_projects_are_shown_and_rich_text_is_safe(): void
    {
        $related = Project::create(['title' => 'Dự án liên quan QA', 'status' => 'published']);
        $draft = Project::create(['title' => 'Dự án bí mật QA', 'status' => 'draft']);
        $project = Project::create(['title' => 'Dự án an toàn QA', 'status' => 'published',
            'body' => '<p>Nội dung hợp lệ</p><script>alert("unsafe")</script>',
            'details' => ['related' => ['title' => 'Dự án tương tự', 'ids' => [$draft->id, $related->id]]],
        ]);
        $this->get(route('projects.show', ['slug' => $project->slug]))->assertOk()
            ->assertSee('Dự án liên quan QA')->assertDontSee('Dự án bí mật QA')
            ->assertSee('Nội dung hợp lệ')->assertDontSee('<script>alert("unsafe")</script>', false);
    }

    private function mediaState(string $name): array
    {
        return [Media::findOrFail(MediaSeeder::id($name))->toArray()];
    }

    public function test_detail_seeder_keeps_admin_changes_and_does_not_duplicate_related_projects(): void
    {
        $project = Project::create(['title' => 'Dự án công trình mẫu', 'status' => 'published']);
        $this->seed(ProjectDetailContentSeeder::class);
        $this->assertCount(5, $project->fresh()->gallery);
        $this->assertNotEmpty(data_get($project->fresh()->details, 'solution.media_id'));
        $project->update(['details' => ['hero' => ['location' => 'Giữ nội dung đã sửa']], 'body' => '<p>Nội dung đã sửa</p>']);
        $count = Project::count();
        $this->seed(ProjectDetailContentSeeder::class);
        $this->assertSame('Giữ nội dung đã sửa', data_get($project->fresh()->details, 'hero.location'));
        $this->assertSame('<p>Nội dung đã sửa</p>', $project->fresh()->body);
        $this->assertSame($count, Project::count());
    }
}
