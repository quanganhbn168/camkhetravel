<?php

namespace Tests\Feature;

use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniGalleryItem;
use App\Models\BniInvitation;
use App\Models\BniScheduleItem;
use App\Models\User;
use App\Settings\BniInvitationSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BniExperienceRoutesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_bni_handover_experience_uses_the_shared_layout_main_and_public_sections(): void
    {
        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('id="bni-handover-main"', false)
            ->assertSee('Sự kiện chuyển giao')
            ->assertSee('bni-overview__featured-media')
            ->assertSee('bni-chapter-video-list')
            ->assertSee('id="chapter-kinhbac"', false)
            ->assertSee('id="chapter-kbg"', false)
            ->assertSee('id="chapter-impact"', false)
            ->assertSee('id="chapter-famous"', false)
            ->assertSee('Lịch trình sự kiện')
            ->assertSee('Đăng ký ngay')
            ->assertSee('Những hoạt động đặc biệt')
            ->assertSee('Thư viện ảnh');
    }

    public function test_the_handover_countdown_uses_the_configured_first_of_october_start(): void
    {
        $event = BniEvent::query()->where('slug', 'le-chuyen-giao-bni')->firstOrFail();

        $this->assertSame('2026-10-01 08:00:00', $event->starts_at?->format('Y-m-d H:i:s'));
        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-countdown="2026-10-01T08:00:00+07:00"', false);
    }

    public function test_the_invitation_template_uses_the_shared_handover_url_without_a_preview_slug(): void
    {
        $event = BniEvent::query()->where('slug', 'le-chuyen-giao-bni')->firstOrFail();
        $event->update(['directions_url' => 'https://maps.google.com/?q=BNI+Handover']);

        $this->get(route('bni.invitations.template'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow, noarchive">', false)
            ->assertSee('Xác nhận tham dự')
            ->assertSee('name="full_name"', false)
            ->assertSee('Ngày 1')
            ->assertSee('Ngày 2')
            ->assertSee('BNI Pickleball')
            ->assertSee('Sự kiện nổi bật')
            ->assertSee('Chỉ đường')
            ->assertSee('https://maps.google.com/?q=BNI+Handover', false)
            ->assertDontSee('Mã thư mời')
            ->assertDontSee('xem-thu');
    }

    public function test_the_shared_invitation_rsvp_creates_an_event_registration(): void
    {
        $event = BniEvent::query()->where('slug', 'le-chuyen-giao-bni')->firstOrFail();

        $this->post(route('bni.invitations.template.rsvp'), [
            'full_name' => 'Khách RSVP kiểm thử',
            'phone' => '0900000000',
            'email' => 'rsvp@example.test',
            'note' => 'Xác nhận tham dự chương trình.',
        ])->assertRedirect();

        $this->assertDatabaseHas('bni_registrations', [
            'bni_event_id' => $event->id,
            'full_name' => 'Khách RSVP kiểm thử',
            'phone' => '0900000000',
            'status' => 'pending',
        ]);
    }

    public function test_an_invitation_uses_global_copy_and_chapter_contact_data(): void
    {
        $event = BniEvent::query()->create([
            'type' => 'handover',
            'title' => 'Lễ Chuyển Giao Kiểm Thử',
            'slug' => 'le-chuyen-giao-kem-thu',
            'starts_at' => '2026-10-01 08:00:00',
            'venue' => 'Trung tâm hội nghị',
            'settings' => ['invitation' => ['content' => '<p>Không được lấy từ sự kiện.</p>']],
        ]);
        $settings = app(BniInvitationSettings::class);
        $settings->content = '<p>Nội dung chung toàn hệ thống.</p>';
        $settings->note_content = '<p>Dress code chung toàn hệ thống.</p>';
        $settings->save();

        $chapter = BniChapter::query()->create([
            'bni_event_id' => $event->id,
            'name' => 'Chapter Kiểm Thử',
            'slug' => 'chapter-kiem-thu',
            'contact_name' => 'Người phụ trách chapter',
            'contact_phone' => '0900000000',
        ]);
        BniScheduleItem::query()->create([
            'bni_event_id' => $event->id,
            'title' => 'Đón tiếp khách mời',
            'starts_at' => '08:00',
            'ends_at' => '09:00',
        ]);
        $invitation = BniInvitation::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'guest_name' => null,
            'slug' => 'thu-moi-kiem-thu',
        ]);

        $this->get(route('bni.invitations.show', [
            'invitation' => $invitation,
            'accessToken' => $invitation->access_token,
        ]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow, noarchive">', false)
            ->assertSee('id="bni-invitation-main"', false)
            ->assertSee('THƯ MỜI')
            ->assertSee('LỄ CHUYỂN GIAO')
            ->assertSee('Anh/Chị chủ doanh nghiệp')
            ->assertSee('Nội dung chung toàn hệ thống.')
            ->assertDontSee('Không được lấy từ sự kiện.')
            ->assertSee('Lịch trình sự kiện')
            ->assertSee('Đón tiếp khách mời')
            ->assertSee('Dress code chung toàn hệ thống.')
            ->assertSee('Xác nhận tham dự')
            ->assertSee('Người phụ trách chapter')
            ->assertSee('property="og:type"', false)
            ->assertSee('"@type":"Event"', false);
    }

    public function test_handover_news_and_gallery_images_are_rendered_from_database_records(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        BniArticle::query()->update(['is_featured' => false]);
        $article = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'type' => 'event',
            'title' => 'Tin BNI lấy trực tiếp từ database',
            'slug' => 'tin-bni-database-'.str()->random(8),
            'excerpt' => 'Nội dung kiểm thử nguồn dữ liệu BNI.',
            'body' => '<p>Nội dung kiểm thử.</p>',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
        ]);

        $path = 'media/bni/tests/database-gallery.jpg';
        Storage::disk('public')->put($path, 'fake-image-content');
        $media = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/bni/tests',
            'visibility' => 'public',
            'name' => 'database-gallery',
            'path' => $path,
            'size' => Storage::disk('public')->size($path),
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'title' => 'Ảnh BNI từ database',
        ]);
        $gallery = BniGalleryItem::query()->create([
            'bni_event_id' => $event->id,
            'group' => 'event',
            'title' => 'Ảnh BNI lấy trực tiếp từ database',
            'source' => BniGalleryItem::SOURCE_ADMIN,
            'status' => BniGalleryItem::STATUS_APPROVED,
            'media_id' => $media->id,
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee($gallery->title)
            ->assertSee($path, false);
    }

    public function test_the_pickleball_landing_and_bni_panel_login_routes_are_available(): void
    {
        $this->get(route('bni.pickleball'))
            ->assertOk()
            ->assertSee('id="bni-pickleball-main"', false)
            ->assertSeeText('Lịch thi đấu & kết quả')
            ->assertSee('Đăng ký tham gia');

        $this->get('/bni-admin/login')->assertOk();
    }

    public function test_a_bni_administrator_can_open_the_dedicated_event_cms(): void
    {
        Role::findOrCreate('bni_admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('bni_admin');

        $this->actingAs($user)
            ->get('/bni-admin/bni-events')
            ->assertOk()
            ->assertSee('Sự kiện');

        $this->actingAs($user)
            ->get('/bni-admin/bni-members')
            ->assertOk()
            ->assertSee('Hội viên');

        $this->actingAs($user)
            ->get('/bni-admin/manage-bni-invitation-settings')
            ->assertOk()
            ->assertSee('Mẫu thư mời BNI');
    }

    public function test_a_chapter_manager_is_limited_to_chapter_workflows(): void
    {
        Role::findOrCreate('bni_chapter_manager', 'web');
        $event = BniEvent::query()->firstOrCreate(['slug' => 'test-bni-event'], ['title' => 'Sự kiện kiểm thử']);
        $chapter = BniChapter::query()->firstOrCreate(['slug' => 'test-bni-chapter'], [
            'bni_event_id' => $event->id,
            'name' => 'Test BNI Chapter',
        ]);
        $user = User::factory()->create(['bni_chapter_id' => $chapter->id]);
        $user->assignRole('bni_chapter_manager');

        $this->actingAs($user)->get('/bni-admin/bni-articles')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-chapters')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-invitations')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-registrations')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-article-comments')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-events')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-members')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/manage-bni-invitation-settings')->assertForbidden();
    }
}
