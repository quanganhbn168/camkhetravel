<?php

namespace Tests\Feature;

use App\Filament\Bni\Resources\BniArticleComments\BniArticleCommentResource;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniInvitation;
use App\Models\Comment;
use App\Models\User;
use App\Support\Bni\BniPanelAccess;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BniSecurityHardeningTest extends TestCase
{
    use DatabaseTransactions;

    public function test_personal_invitation_uses_one_generated_code_for_view_and_rsvp(): void
    {
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $invitation = BniInvitation::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'guest_name' => 'Khách bảo mật',
            'slug' => 'khach-bao-mat-'.str()->random(8),
        ]);

        $this->assertMatchesRegularExpression('/^tm-[a-z0-9]{10}$/', $invitation->invitation_code);

        $validUrl = route('bni.invitations.show', ['invitation' => $invitation]);

        $this->get($validUrl)->assertOk()->assertSee('Khách bảo mật');
        $this->get('/le-chuyen-giao/thu-moi/'.$invitation->slug)->assertNotFound();
        $this->get('/le-chuyen-giao/thu-moi/tm-xxxxxxxxxx')->assertNotFound();

        $this->post('/le-chuyen-giao/thu-moi/tm-xxxxxxxxxx/rsvp', [
            'rsvp_status' => BniInvitation::RSVP_ATTENDING,
            'guest_count' => 2,
        ])->assertNotFound();
        $this->assertSame(BniInvitation::RSVP_PENDING, $invitation->fresh()->rsvp_status);

        $this->post(route('bni.invitations.rsvp', ['invitation' => $invitation]), [
            'rsvp_status' => BniInvitation::RSVP_ATTENDING,
            'guest_count' => 2,
            'rsvp_note' => 'Đã xác nhận bằng mã thư mời.',
        ])->assertRedirect();

        $this->assertDatabaseHas('bni_invitations', [
            'id' => $invitation->id,
            'rsvp_status' => BniInvitation::RSVP_ATTENDING,
            'guest_count' => 2,
        ]);
    }

    public function test_chapter_manager_sees_only_article_comments_from_their_chapter(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $otherChapter = BniChapter::query()->where('is_active', true)->whereKeyNot($chapter->id)->firstOrFail();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');
        $ownArticle = $this->article($event, $chapter, 'Tin đúng chapter');
        $otherArticle = $this->article($event, $otherChapter, 'Tin chapter khác');
        $ownComment = $ownArticle->comments()->create($this->commentData('Bình luận đúng chapter'));
        $otherComment = $otherArticle->comments()->create($this->commentData('Bình luận chapter khác'));

        $this->actingAs($user)
            ->get('/bni-admin/bni-article-comments')
            ->assertOk()
            ->assertSee('Bình luận tin BNI');

        $commentIds = BniArticleCommentResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($commentIds->contains($ownComment->id));
        $this->assertFalse($commentIds->contains($otherComment->id));
    }

    public function test_chapter_owned_mutations_force_the_assigned_chapter_and_secure_relationships(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');
        $this->actingAs($user);
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $activity = $event->activities()->where('is_active', true)->firstOrFail();
        $invitation = BniInvitation::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'guest_name' => 'Khách đúng chapter',
            'slug' => 'khach-dung-chapter-'.str()->random(8),
        ]);

        $galleryData = BniPanelAccess::prepareGalleryData([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => null,
            'bni_activity_id' => $activity->id,
        ]);
        $registrationData = BniPanelAccess::prepareRegistrationData([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => null,
            'bni_invitation_id' => $invitation->id,
        ]);

        $this->assertSame($chapter->id, $galleryData['bni_chapter_id']);
        $this->assertSame($activity->id, $galleryData['bni_activity_id']);
        $this->assertSame($activity->type, $galleryData['group']);
        $this->assertSame($chapter->id, $registrationData['bni_chapter_id']);
        $this->assertSame($invitation->bni_event_id, $registrationData['bni_event_id']);
    }

    public function test_chapter_manager_cannot_attach_another_chapters_invitation(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $otherChapter = BniChapter::query()->where('is_active', true)->whereKeyNot($chapter->id)->firstOrFail();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');
        $this->actingAs($user);
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $otherInvitation = BniInvitation::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $otherChapter->id,
            'guest_name' => 'Khách chapter khác',
            'slug' => 'khach-chapter-khac-'.str()->random(8),
        ]);

        $this->expectException(ValidationException::class);

        BniPanelAccess::prepareRegistrationData([
            'bni_event_id' => $event->id,
            'bni_invitation_id' => $otherInvitation->id,
        ]);
    }

    public function test_bni_member_can_interact_with_news_but_cannot_open_the_admin_panel(): void
    {
        Role::findOrCreate('bni_member', 'web');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $article = $this->article($event, $chapter, 'Tin dành cho tương tác hội viên');
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_member');

        $this->post(route('bni.articles.comments.store', ['article' => $article]), [
            'body' => 'Khách chưa đăng nhập không được gửi bình luận tin.',
        ])->assertRedirect(route('bni.member.login'));
        $this->assertDatabaseMissing('comments', [
            'commentable_type' => $article->getMorphClass(),
            'commentable_id' => $article->id,
        ]);

        $this->actingAs($user)->post(route('bni.articles.comments.store', ['article' => $article]), [
            'body' => 'Bình luận hội viên đang chờ duyệt.',
        ])->assertRedirect();
        $this->actingAs($user)->post(route('bni.articles.reactions.store', ['article' => $article]), [
            'reaction' => 'celebrate',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'commentable_type' => $article->getMorphClass(),
            'commentable_id' => $article->id,
            'user_id' => $user->id,
            'status' => Comment::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('bni_reactions', [
            'reactable_type' => $article->getMorphClass(),
            'reactable_id' => $article->id,
            'user_id' => $user->id,
            'reaction' => 'celebrate',
        ]);
        $this->actingAs($user)->get('/bni-admin')->assertForbidden();
    }

    private function article(BniEvent $event, BniChapter $chapter, string $title): BniArticle
    {
        return BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'type' => 'chapter',
            'title' => $title,
            'slug' => str()->slug($title).'-'.str()->random(8),
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /** @return array<string, mixed> */
    private function commentData(string $body): array
    {
        return [
            'author_name' => 'Hội viên kiểm thử',
            'author_email' => 'member@example.test',
            'body' => $body,
            'status' => Comment::STATUS_PENDING,
        ];
    }
}
