<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_a_visitor_can_submit_a_pending_comment_for_a_published_post(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $post = Post::query()->published()->firstOrFail();
        $body = 'Bình luận kiểm thử '.Str::uuid();
        $origin = LocalizedUrl::post($post);

        $this->from($origin)
            ->post(route('comments.store', ['post' => $post]), [
                'author_name' => 'Độc giả kiểm thử',
                'author_email' => 'reader@example.test',
                'body' => $body,
            ])
            ->assertRedirect($origin)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'commentable_type' => $post->getMorphClass(),
            'commentable_id' => $post->id,
            'author_name' => 'Độc giả kiểm thử',
            'body' => $body,
            'status' => Comment::STATUS_PENDING,
        ]);
    }

    public function test_only_approved_comments_are_shown_on_the_public_post(): void
    {
        $post = Post::query()->published()->firstOrFail();
        $approvedBody = 'Bình luận được duyệt '.Str::uuid();
        $pendingBody = 'Bình luận đang chờ '.Str::uuid();

        $post->comments()->create([
            'author_name' => 'Độc giả đã duyệt',
            'body' => $approvedBody,
            'status' => Comment::STATUS_APPROVED,
        ]);
        $post->comments()->create([
            'author_name' => 'Độc giả chờ duyệt',
            'body' => $pendingBody,
            'status' => Comment::STATUS_PENDING,
        ]);

        $this->get(LocalizedUrl::post($post))
            ->assertOk()
            ->assertSee('Bình luận (')
            ->assertSee($approvedBody)
            ->assertDontSee($pendingBody);
    }

    public function test_a_project_category_has_a_public_slug_url(): void
    {
        $category = ProjectCategory::query()
            ->where('is_active', true)
            ->whereHas('projects', fn ($query) => $query->published())
            ->firstOrFail();

        $this->get(LocalizedUrl::projectCategory($category))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_a_visitor_can_submit_a_pending_rated_comment_for_a_published_project(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $project = Project::query()->published()->firstOrFail();
        $body = 'Đánh giá dự án kiểm thử '.Str::uuid();
        $origin = LocalizedUrl::project($project);

        $this->from($origin)
            ->post(route('projects.comments.store', ['project' => $project]), [
                'author_name' => 'Khách hàng kiểm thử',
                'author_email' => 'client@example.test',
                'rating' => 5,
                'body' => $body,
            ])
            ->assertRedirect($origin)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'commentable_type' => $project->getMorphClass(),
            'commentable_id' => $project->id,
            'author_name' => 'Khách hàng kiểm thử',
            'rating' => 5,
            'body' => $body,
            'status' => Comment::STATUS_PENDING,
        ]);
    }

    public function test_only_approved_project_ratings_are_shown_publicly(): void
    {
        $project = Project::query()->published()->firstOrFail();
        $approvedBody = 'Đánh giá dự án đã duyệt '.Str::uuid();
        $pendingBody = 'Đánh giá dự án chờ duyệt '.Str::uuid();

        $project->comments()->create([
            'author_name' => 'Khách hàng đã duyệt',
            'rating' => 4,
            'body' => $approvedBody,
            'status' => Comment::STATUS_APPROVED,
        ]);
        $project->comments()->create([
            'author_name' => 'Khách hàng chờ duyệt',
            'rating' => 5,
            'body' => $pendingBody,
            'status' => Comment::STATUS_PENDING,
        ]);

        $this->get(LocalizedUrl::project($project))
            ->assertOk()
            ->assertSee('Đánh giá từ khách hàng')
            ->assertSee($approvedBody)
            ->assertDontSee($pendingBody);
    }

    public function test_post_detail_builds_a_table_of_contents_from_article_headings(): void
    {
        $post = Post::query()->published()->firstOrFail();
        $post->update([
            'body' => '<p>Mở đầu bài viết.</p><h2>Định hướng sản xuất</h2><p>Nội dung minh họa.</p><h3>Tiêu chí triển khai</h3><table><thead><tr><th>Hạng mục</th><th>Nội dung</th></tr></thead><tbody><tr><td>Ví dụ</td><td>Minh họa</td></tr></tbody></table><p>Nội dung chi tiết.</p>',
        ]);

        $this->get(LocalizedUrl::post($post))
            ->assertOk()
            ->assertSee('Mục lục bài viết')
            ->assertSee('href="#dinh-huong-san-xuat"', false)
            ->assertSee('href="#tieu-chi-trien-khai"', false)
            ->assertSee('id="dinh-huong-san-xuat"', false)
            ->assertSee('id="tieu-chi-trien-khai"', false)
            ->assertSee('class="article-table-wrap"', false)
            ->assertSee('x-ref="inlineToc"', false)
            ->assertSee('x-show="tocPassed"', false)
            ->assertSee('facebook.com/sharer/sharer.php', false)
            ->assertSee('linkedin.com/sharing/share-offsite', false)
            ->assertSee('twitter.com/intent/tweet', false)
            ->assertSee('Sao chép link');
    }

    public function test_a_super_admin_can_open_the_comment_moderation_screen(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));

        $this->actingAs($user)
            ->get('/admin/comments')
            ->assertOk()
            ->assertSee('Bình luận');
    }
}
