<?php

namespace Tests\Feature;

use App\Filament\Bni\Resources\BniGalleryComments\BniGalleryCommentResource;
use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use App\Models\BniActivity;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Models\Comment;
use App\Models\User;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BniCompletionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pickleball_landing_renders_managed_prizes_rules_countdown_and_rsvp(): void
    {
        $event = BniEvent::query()->published()->where('type', 'pickleball')->firstOrFail();
        $event->update([
            'settings' => [
                'countdown_label' => 'Đếm ngược kiểm thử',
                'prizes_title' => 'Giải thưởng kiểm thử',
                'prizes' => [[
                    'title' => 'Hạng mục vô địch kiểm thử',
                    'value' => 'Phần thưởng do Ban tổ chức xác nhận',
                    'description' => 'Nội dung quản trị được hiển thị.',
                    'highlight' => true,
                ]],
                'rules_title' => 'Thể lệ kiểm thử',
                'rules' => '<p>Nội dung thể lệ lấy từ quản trị BNI.</p>',
                'registration_title' => 'RSVP Pickleball kiểm thử',
                'registration_description' => 'Mô tả đăng ký do Ban tổ chức quản lý.',
            ],
        ]);

        $this->get(route('bni.pickleball'))
            ->assertOk()
            ->assertSee('data-bni-countdown=', false)
            ->assertSee('Đếm ngược kiểm thử')
            ->assertSee('Giải thưởng kiểm thử')
            ->assertSee('Hạng mục vô địch kiểm thử')
            ->assertSee('Thể lệ kiểm thử')
            ->assertSee('Nội dung thể lệ lấy từ quản trị BNI.')
            ->assertSee('RSVP Pickleball kiểm thử')
            ->assertSee('name="bni_chapter_id"', false)
            ->assertSee('name="skill_level"', false);
    }

    public function test_a_visitor_can_upload_multiple_gallery_images_for_moderation(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $activity = $event->activities()->where('is_active', true)->firstOrFail();
        $galleryCount = BniGalleryItem::query()->count();
        $mediaCount = Media::query()->count();

        $this->from(route('bni.gallery.index'))
            ->post(route('bni.gallery.store'), [
                'bni_activity_id' => $activity->id,
                'uploader_name' => 'Khách gửi ảnh kiểm thử',
                'uploader_phone' => '0900000000',
                'uploader_email' => 'gallery@example.test',
                'title' => 'Bộ ảnh kiểm thử',
                'caption' => 'Khoảnh khắc được gửi từ biểu mẫu cá nhân.',
                'images' => [
                    UploadedFile::fake()->image('moment-one.jpg', 1200, 800),
                    UploadedFile::fake()->image('moment-two.png', 900, 900),
                ],
            ])
            ->assertRedirect(route('bni.gallery.index'))
            ->assertSessionHas('success');

        $this->assertSame($galleryCount + 2, BniGalleryItem::query()->count());
        $this->assertDatabaseHas('bni_gallery_items', [
            'bni_event_id' => $event->id,
            'bni_activity_id' => $activity->id,
            'bni_chapter_id' => null,
            'group' => $activity->type,
            'uploader_name' => 'Khách gửi ảnh kiểm thử',
            'source' => BniGalleryItem::SOURCE_GUEST,
            'status' => BniGalleryItem::STATUS_PENDING,
        ]);
        $this->assertSame($mediaCount + 2, Media::query()->count());
        $storedMedia = Media::query()->latest('id')->firstOrFail();
        $this->assertContains($storedMedia->ext, ['webp', 'png']);
        $this->assertLessThanOrEqual(2400, max($storedMedia->width, $storedMedia->height));
        Storage::disk('public')->assertExists($storedMedia->path);
    }

    public function test_gallery_uses_database_managed_activities_without_public_chapter_grouping(): void
    {
        Storage::fake('public');
        $handoverEvent = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $pickleballEvent = BniEvent::query()->published()->where('type', 'pickleball')->firstOrFail();
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $beforeHandover = BniActivity::query()->create([
            'bni_event_id' => $handoverEvent->id,
            'type' => 'handover',
            'title' => 'Trước lễ chuyển giao',
            'sort_order' => 91,
            'is_active' => true,
        ]);
        $insideGala = BniActivity::query()->create([
            'bni_event_id' => $handoverEvent->id,
            'type' => 'gala',
            'title' => 'Trong Gala Dinner',
            'sort_order' => 92,
            'is_active' => true,
        ]);
        $pickleballActivity = BniActivity::query()->create([
            'bni_event_id' => $pickleballEvent->id,
            'type' => 'pickleball',
            'title' => 'Giao lưu sau trận đấu',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $handover = $this->makeGalleryItem($handoverEvent, 'Ảnh trước chuyển giao kiểm thử', BniGalleryItem::STATUS_APPROVED, $beforeHandover);
        $gala = $this->makeGalleryItem($handoverEvent, 'Ảnh trong Gala kiểm thử', BniGalleryItem::STATUS_APPROVED, $insideGala);
        $pickleball = $this->makeGalleryItem($pickleballEvent, 'Ảnh giao lưu Pickleball kiểm thử', BniGalleryItem::STATUS_APPROVED, $pickleballActivity);
        $legacy = $this->makeGalleryItem($handoverEvent, 'Ảnh chapter cũ được giữ lại', BniGalleryItem::STATUS_APPROVED);
        $legacy->update(['bni_activity_id' => null, 'bni_chapter_id' => $chapter->id, 'group' => 'chapter']);

        $this->get(route('bni.gallery.index'))
            ->assertOk()
            ->assertSee('name="activity"', false)
            ->assertSee('name="bni_activity_id"', false)
            ->assertSee('Trước lễ chuyển giao')
            ->assertSee('Trong Gala Dinner')
            ->assertSee('Giao lưu sau trận đấu')
            ->assertSee('Xóa tất cả')
            ->assertDontSee('name="chapter"', false)
            ->assertDontSee('name="bni_chapter_id"', false)
            ->assertDontSee('Theo chapter');

        $this->get(route('bni.gallery.index', ['activity' => $insideGala->id]))
            ->assertOk()
            ->assertSee($gala->title)
            ->assertDontSee($handover->title)
            ->assertDontSee($pickleball->title)
            ->assertDontSee($legacy->title);

        $this->get(route('bni.gallery.index', ['activity' => $beforeHandover->id]))
            ->assertOk()
            ->assertSee($handover->title)
            ->assertDontSee($legacy->title)
            ->assertDontSee($gala->title)
            ->assertDontSee($pickleball->title);

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('Trước lễ chuyển giao')
            ->assertSee('Trong Gala Dinner')
            ->assertSee('Giao lưu sau trận đấu')
            ->assertDontSee('Theo sự kiện')
            ->assertDontSee('Theo chapter');

        $this->get(route('bni.gallery.show', ['galleryItem' => $legacy]))
            ->assertOk()
            ->assertSee($handoverEvent->title)
            ->assertDontSee('<p>Chapter</p>', false);
    }

    public function test_gallery_upload_honeypot_is_rejected_before_files_are_stored(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);
        Storage::fake('public');
        $activity = BniEvent::query()->published()->where('type', 'handover')->firstOrFail()->activities()->where('is_active', true)->firstOrFail();
        $galleryCount = BniGalleryItem::query()->count();

        $this->from(route('bni.gallery.index'))
            ->post(route('bni.gallery.store'), [
                'bni_activity_id' => $activity->id,
                'uploader_name' => 'Bot kiểm thử',
                'uploader_phone' => '0900000000',
                'website' => 'https://spam.example.test',
                'images' => [UploadedFile::fake()->image('spam.jpg', 800, 800)],
            ])
            ->assertRedirect(route('bni.gallery.index'))
            ->assertSessionHasErrors('website');

        $this->assertSame($galleryCount, BniGalleryItem::query()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_only_approved_photos_and_comments_are_public(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $approved = $this->makeGalleryItem($event, 'Ảnh đã duyệt', BniGalleryItem::STATUS_APPROVED);
        $pending = $this->makeGalleryItem($event, 'Ảnh chờ duyệt', BniGalleryItem::STATUS_PENDING);
        $approved->comments()->create([
            'author_name' => 'Người bình luận đã duyệt',
            'body' => 'Bình luận công khai kiểm thử.',
            'status' => Comment::STATUS_APPROVED,
        ]);
        $approved->comments()->create([
            'author_name' => 'Người bình luận chờ duyệt',
            'body' => 'Bình luận chưa công khai kiểm thử.',
            'status' => Comment::STATUS_PENDING,
        ]);

        $this->get(route('bni.gallery.index'))
            ->assertOk()
            ->assertSee('Ảnh đã duyệt')
            ->assertDontSee('Ảnh chờ duyệt')
            ->assertSee('Gửi ảnh cá nhân');

        $this->get(route('bni.gallery.show', ['galleryItem' => $approved]))
            ->assertOk()
            ->assertSee('Bình luận công khai kiểm thử.')
            ->assertDontSee('Bình luận chưa công khai kiểm thử.')
            ->assertSee('Gửi bình luận để duyệt');

        $this->get(route('bni.gallery.show', ['galleryItem' => $pending]))->assertNotFound();
    }

    public function test_a_visitor_can_submit_a_pending_gallery_comment(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $galleryItem = $this->makeGalleryItem($event, 'Ảnh nhận bình luận', BniGalleryItem::STATUS_APPROVED);

        $this->from(route('bni.gallery.show', ['galleryItem' => $galleryItem]))
            ->post(route('bni.gallery.comments.store', ['galleryItem' => $galleryItem]), [
                'author_name' => 'Người xem ảnh',
                'author_email' => 'viewer@example.test',
                'body' => 'Một bình luận hợp lệ cho ảnh sự kiện.',
            ])
            ->assertRedirect(route('bni.gallery.show', ['galleryItem' => $galleryItem]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'commentable_type' => $galleryItem->getMorphClass(),
            'commentable_id' => $galleryItem->id,
            'author_name' => 'Người xem ảnh',
            'status' => Comment::STATUS_PENDING,
        ]);
    }

    public function test_chapter_manager_can_access_scoped_gallery_and_comment_workflows(): void
    {
        Storage::fake('public');
        Role::findOrCreate('bni_chapter_manager', 'web');
        $chapter = BniChapter::query()->where('is_active', true)->firstOrFail();
        $otherChapter = BniChapter::query()->where('is_active', true)->whereKeyNot($chapter->getKey())->firstOrFail();
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');
        $ownGalleryItem = $this->makeGalleryItem($event, 'Ảnh thuộc Chapter quản lý', BniGalleryItem::STATUS_PENDING);
        $ownGalleryItem->update(['bni_chapter_id' => $chapter->id]);
        $otherGalleryItem = $this->makeGalleryItem($event, 'Ảnh thuộc Chapter khác', BniGalleryItem::STATUS_PENDING);
        $otherGalleryItem->update(['bni_chapter_id' => $otherChapter->id]);
        $ownComment = $ownGalleryItem->comments()->create([
            'author_name' => 'Khách Chapter quản lý',
            'body' => 'Bình luận thuộc đúng Chapter.',
            'status' => Comment::STATUS_PENDING,
        ]);
        $otherComment = $otherGalleryItem->comments()->create([
            'author_name' => 'Khách Chapter khác',
            'body' => 'Bình luận không thuộc Chapter quản lý.',
            'status' => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($user)->get('/bni-admin/bni-gallery-items')
            ->assertOk()
            ->assertSee('Thư viện ảnh')
            ->assertSee('Tải nhiều ảnh')
            ->assertSee('Hoạt động / album')
            ->assertDontSee('Theo chapter');
        $this->actingAs($user)->get('/bni-admin/bni-gallery-comments')->assertOk()->assertSee('Bình luận ảnh');

        $galleryIds = BniGalleryItemResource::getEloquentQuery()->pluck('id');
        $commentIds = BniGalleryCommentResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($galleryIds->contains($ownGalleryItem->id));
        $this->assertFalse($galleryIds->contains($otherGalleryItem->id));
        $this->assertTrue($commentIds->contains($ownComment->id));
        $this->assertFalse($commentIds->contains($otherComment->id));
    }

    public function test_deleting_a_guest_gallery_item_removes_its_private_upload_and_comments(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $activity = $event->activities()->where('is_active', true)->firstOrFail();
        $path = 'media/bni/community/tests/guest-upload.webp';
        Storage::disk('public')->put($path, 'guest-image');
        $media = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/bni/community/tests',
            'visibility' => 'public',
            'name' => 'guest-upload',
            'path' => $path,
            'size' => 11,
            'type' => 'image/webp',
            'ext' => 'webp',
            'title' => 'Ảnh khách cần xóa',
        ]);
        $galleryItem = BniGalleryItem::query()->create([
            'bni_event_id' => $event->id,
            'bni_activity_id' => $activity->id,
            'group' => $activity->type,
            'title' => 'Ảnh khách cần xóa',
            'source' => BniGalleryItem::SOURCE_GUEST,
            'status' => BniGalleryItem::STATUS_PENDING,
            'media_id' => $media->id,
            'is_active' => true,
        ]);
        $comment = $galleryItem->comments()->create([
            'author_name' => 'Người bình luận',
            'body' => 'Bình luận cần xóa cùng ảnh.',
            'status' => Comment::STATUS_PENDING,
        ]);

        $galleryItem->delete();

        $this->assertDatabaseMissing('bni_gallery_items', ['id' => $galleryItem->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('curator', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_bni_manifest_includes_gallery_and_pickleball_shortcuts(): void
    {
        $this->get(route('bni.manifest', ['locale' => 'vi']))
            ->assertOk()
            ->assertJsonFragment(['url' => '/le-chuyen-giao/thu-vien-anh'])
            ->assertJsonFragment(['url' => '/le-chuyen-giao/pickleball']);
    }

    private function makeGalleryItem(BniEvent $event, string $title, string $status, ?BniActivity $activity = null): BniGalleryItem
    {
        $activity ??= $event->activities()->where('is_active', true)->firstOrFail();
        $path = 'media/bni/tests/'.str()->uuid().'.jpg';
        Storage::disk('public')->put($path, 'fake-image-content');
        $media = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/bni/tests',
            'visibility' => 'public',
            'name' => pathinfo($path, PATHINFO_FILENAME),
            'path' => $path,
            'size' => Storage::disk('public')->size($path),
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'title' => $title,
        ]);

        return BniGalleryItem::query()->create([
            'bni_event_id' => $event->id,
            'bni_activity_id' => $activity->id,
            'group' => $activity->type,
            'title' => $title,
            'source' => BniGalleryItem::SOURCE_ADMIN,
            'status' => $status,
            'media_id' => $media->id,
            'is_active' => true,
        ]);
    }
}
