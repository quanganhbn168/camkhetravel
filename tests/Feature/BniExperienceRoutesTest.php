<?php

namespace Tests\Feature;

use App\Models\BniArticle;
use App\Models\BniArticleCategory;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniGalleryItem;
use App\Models\BniInvitation;
use App\Models\BniScheduleItem;
use App\Models\User;
use App\Settings\BniInvitationSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
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
            ->assertSee('id="bni-handover-overview-title"', false)
            ->assertSee('bni-overview__featured-media')
            ->assertSee('bni-chapter-video-list')
            ->assertSee('id="chapter-kinhbac"', false)
            ->assertSee('id="chapter-kbg"', false)
            ->assertSee('id="chapter-impact"', false)
            ->assertSee('id="chapter-famous"', false)
            ->assertSee('Lịch trình sự kiện')
            ->assertSee('Đăng ký ngay')
            ->assertSee(route('bni.registrations.create'), false)
            ->assertSee('Những hoạt động đặc biệt')
            ->assertSee('Thư viện ảnh');
    }

    public function test_each_active_chapter_has_a_database_backed_public_detail_page(): void
    {
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = BniChapter::query()->create([
            'bni_event_id' => $event->id,
            'name' => 'Chapter Chi Tiết Kiểm Thử',
            'short_name' => 'CHI TIẾT',
            'slug' => 'chapter-chi-tiet-'.str()->random(8),
            'description' => 'Giới thiệu chapter lấy trực tiếp từ cơ sở dữ liệu.',
            'contact_name' => 'Đầu mối chapter kiểm thử',
            'contact_phone' => '0900 111 222',
            'contact_email' => 'chapter-detail@example.test',
            'is_active' => true,
            'sort_order' => 99,
        ]);
        $article = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'type' => 'chapter',
            'title' => 'Tin riêng của chapter kiểm thử',
            'slug' => 'tin-chapter-chi-tiet-'.str()->random(8),
            'excerpt' => 'Bài viết được lọc đúng theo chapter.',
            'body' => '<p>Nội dung bài viết chapter.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $url = route('bni.chapters.show', ['chapter' => $chapter]);

        $response = $this->get($url)
            ->assertOk()
            ->assertSee('id="bni-chapter-main"', false)
            ->assertSee('CHI TIẾT')
            ->assertSee($chapter->description)
            ->assertSee('Đầu mối chapter kiểm thử')
            ->assertSee('0900 111 222')
            ->assertSee('chapter-detail@example.test')
            ->assertSee($article->title)
            ->assertSee('<meta name="robots" content="index, follow', false)
            ->assertSee('<link rel="canonical" href="'.$url.'">', false)
            ->assertDontSee('bni-gallery-grid')
            ->assertDontSee('Theo chapter');

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('href="'.$url.'"', false);

        $this->get(route('seo.sitemap'))
            ->assertOk()
            ->assertSee($url, false);

        $chapter->update(['is_active' => false]);
        $this->get($url)->assertNotFound();
    }

    public function test_handover_slides_are_database_backed_webp_and_never_use_overlay_eyebrow_or_h1(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $upload = UploadedFile::fake()->image('handover-slide.jpg', 2600, 1300);
        $path = $upload->storeAs('media/bni/tests', 'handover-slide.jpg', 'public');
        $sourceMedia = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/bni/tests',
            'visibility' => 'public',
            'name' => 'handover-slide',
            'path' => $path,
            'width' => 2600,
            'height' => 1300,
            'size' => Storage::disk('public')->size($path),
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'title' => 'Ảnh slide kiểm thử',
        ]);

        $textSlide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'media_id' => $sourceMedia->id,
            'title' => 'Nội dung slide lấy từ database',
            'description' => 'Phần chữ nằm riêng, không phủ lên ảnh.',
            'button_label' => 'Xem lịch trình',
            'button_url' => '#lich-trinh',
            'alt_text' => 'Ảnh Lễ chuyển giao kiểm thử',
            'is_active' => true,
            'sort_order' => 1,
        ])->refresh();
        $optimizedMedia = $textSlide->media()->firstOrFail();

        BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'media_id' => $optimizedMedia->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->assertSame('webp', $optimizedMedia->ext);
        $this->assertSame('image/webp', $optimizedMedia->type);
        $this->assertLessThanOrEqual(2400, max($optimizedMedia->width, $optimizedMedia->height));
        Storage::disk('public')->assertExists($optimizedMedia->path);

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-hero-swiper', false)
            ->assertSee('Nội dung slide lấy từ database')
            ->assertSee('bni-event-slide--image-only', false)
            ->assertSee($optimizedMedia->path, false);

        $body = $response->getContent();
        $sliderStart = strpos($body, '<section class="bni-event-slider"');
        $sliderEnd = strpos($body, '</section>', $sliderStart);
        $slider = substr($body, $sliderStart, $sliderEnd - $sliderStart);

        $this->assertStringContainsString('<h2>Nội dung slide lấy từ database</h2>', $slider);
        $this->assertStringNotContainsString('<h1', $slider);
        $this->assertStringNotContainsString('bni-experience-kicker', $slider);
        $this->assertStringNotContainsString('overlay', strtolower($slider));
    }

    public function test_handover_slider_uses_the_bni_key_visual_when_no_slide_image_exists(): void
    {
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $event->update(['hero_media_id' => null]);
        $event->slides()->update([
            'media_id' => null,
            'title' => null,
            'description' => null,
            'button_label' => null,
            'button_url' => null,
            'is_active' => true,
        ]);

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-hero-swiper', false)
            ->assertSee('bni-event-slide--image-only', false)
            ->assertSee('bni-kv-milk-red', false);
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

    public function test_the_handover_has_a_dedicated_registration_page_and_stores_bni_data(): void
    {
        $event = BniEvent::query()->where('slug', 'le-chuyen-giao-bni')->firstOrFail();
        $chapter = $event->chapters()->where('is_active', true)->firstOrFail();

        $this->get(route('bni.registrations.create'))
            ->assertOk()
            ->assertSee('id="bni-registration-main"', false)
            ->assertSee('Đăng ký Lễ chuyển giao')
            ->assertSee('name="bni_chapter_id"', false)
            ->assertSee('action="'.route('bni.registrations.store').'"', false);

        $this->post(route('bni.registrations.store'), [
            'full_name' => 'Khách đăng ký BNI',
            'phone' => '0912345678',
            'email' => 'dangky-bni@example.test',
            'bni_chapter_id' => $chapter->id,
            'note' => 'Đăng ký trực tiếp từ trang BNI.',
        ])->assertRedirect(route('bni.registrations.create'));

        $this->assertDatabaseHas('bni_registrations', [
            'bni_event_id' => $event->id,
            'bni_chapter_id' => $chapter->id,
            'full_name' => 'Khách đăng ký BNI',
            'phone' => '0912345678',
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

        $this->get(route('bni.invitations.show', ['invitation' => $invitation]))
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

    public function test_handover_news_tabs_use_bni_article_categories_and_gallery_images_are_rendered_from_database_records(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $activity = $event->activities()->where('is_active', true)->firstOrFail();
        $uncategorizedArticle = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'type' => 'event',
            'title' => 'Tin BNI chưa được xếp danh mục',
            'slug' => 'tin-bni-chua-xep-danh-muc-'.str()->random(8),
            'excerpt' => 'Bài BNI chưa có danh mục không được đưa lên tab.',
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
        BniArticleCategory::query()->update(['is_active' => false]);
        $category = BniArticleCategory::query()->create([
            'name' => 'Danh mục BNI kiểm thử',
            'slug' => 'danh-muc-bni-'.str()->random(8),
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $emptyCategory = BniArticleCategory::query()->create([
            'name' => 'Danh mục BNI chưa có bài xuất bản',
            'slug' => 'danh-muc-bni-trong-'.str()->random(8),
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $article = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'type' => 'event',
            'cover_media_id' => $media->id,
            'title' => 'Bài viết lấy từ danh mục tin BNI',
            'slug' => 'bai-viet-danh-muc-bni-'.str()->random(8),
            'excerpt' => 'Nội dung kiểm thử nguồn BniArticle và BniArticleCategory.',
            'body' => '<p>Nội dung bài viết BNI.</p>',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
        ]);
        $article->categories()->attach($category);
        $gallery = BniGalleryItem::query()->create([
            'bni_event_id' => $event->id,
            'bni_activity_id' => $activity->id,
            'group' => $activity->type,
            'title' => 'Ảnh BNI lấy trực tiếp từ database',
            'source' => BniGalleryItem::SOURCE_ADMIN,
            'status' => BniGalleryItem::STATUS_APPROVED,
            'media_id' => $media->id,
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee("@click=\"newsTab = 'bni-article-category-{$category->id}'\"", false)
            ->assertSee($article->title)
            ->assertSee(route('bni.articles.show', ['article' => $article]), false)
            ->assertDontSee($emptyCategory->name)
            ->assertDontSee($uncategorizedArticle->title)
            ->assertDontSee('>Tin sự kiện</button>', false)
            ->assertDontSee('>Tin các chapter</button>', false)
            ->assertSee($activity->title)
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
