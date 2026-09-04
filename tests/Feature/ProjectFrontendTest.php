<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectFrontendTest extends TestCase
{
    use DatabaseTransactions;

    public function test_project_detail_renders_only_its_configured_faqs(): void
    {
        $project = Project::query()->published()->firstOrFail();
        $question = 'Dự án này triển khai trong bao lâu?';
        $answer = 'Thời gian được thống nhất theo phạm vi và mốc bàn giao của dự án.';

        $project->update([
            'faq_title' => 'Câu hỏi về dự án',
            'faq_description' => 'Thông tin để khách hàng tham khảo trước khi trao đổi.',
            'faq_items' => [
                ['question' => $question, 'answer' => $answer],
            ],
        ]);

        $this->get(LocalizedUrl::project($project))
            ->assertOk()
            ->assertSee('Câu hỏi về dự án')
            ->assertSee($question)
            ->assertSee($answer);
    }

    public function test_homepage_no_longer_renders_the_faq_section(): void
    {
        $this->get(LocalizedUrl::route('home'))
            ->assertOk()
            ->assertDontSee('id="cau-hoi-thuong-gap"', false)
            ->assertDontSee('id="nang-luc"', false)
            ->assertDontSee('Cam kết đồng hành')
            ->assertDontSee('Năng lực triển khai');
    }

    public function test_project_detail_renders_managed_related_services_posts_and_sidebar_links(): void
    {
        $project = Project::query()->published()->firstOrFail();
        $service = Service::query()->published()->firstOrFail();
        $post = Post::query()->published()->firstOrFail();

        $project->backstageServices()->syncWithoutDetaching([$service->id]);
        $project->relatedPosts()->syncWithoutDetaching([$post->id]);

        $this->get(LocalizedUrl::project($project))
            ->assertOk()
            ->assertSee('Khám phá thêm')
            ->assertSee('Dịch vụ đồng hành cùng dự án')
            ->assertSee($service->title)
            ->assertSee('Bài viết liên quan')
            ->assertSee($post->title)
            ->assertSee('href="#dich-vu-lien-quan"', false)
            ->assertSee('href="#bai-viet-lien-quan"', false);
    }

    public function test_project_editor_offers_related_services_and_posts_selection(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $project = Project::query()->firstOrFail();

        $this->actingAs($user)
            ->get('/admin/projects/'.$project->id.'/edit')
            ->assertOk()
            ->assertSee('Mô tả dự án')
            ->assertSee('Nội dung liên quan')
            ->assertSee('Dịch vụ liên quan')
            ->assertSee('Bài viết liên quan');
    }
}
