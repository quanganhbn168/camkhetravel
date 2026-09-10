<?php

namespace Tests\Feature;

use App\Filament\Bni\Resources\BniEventSlides\BniEventSlideResource;
use App\Models\BniArticle;
use App\Models\BniArticleCategory;
use App\Models\BniChapter;
use App\Models\BniContact;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniEventVideo;
use App\Models\BniGalleryItem;
use App\Models\BniInvitation;
use App\Models\BniScheduleDay;
use App\Models\BniScheduleItem;
use App\Models\BniSponsor;
use App\Models\User;
use App\Settings\BniInvitationSettings;
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
        $response = $this->get(route('bni.handover'))
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
            ->assertSee('Thư viện ảnh')
            ->assertSee('href="'.route('bni.articles.index').'"', false);

        $body = $response->getContent();
        $navStart = strpos($body, '<nav class="bni-handover-page-nav"');
        $navEnd = strpos($body, '</nav>', $navStart);
        $navigation = substr($body, $navStart, $navEnd - $navStart);

        $this->assertStringNotContainsString('Video giới thiệu', $navigation);
        $this->assertStringNotContainsString('activeChapter:', $body);
        $this->assertStringNotContainsString('@click="activeChapter =', $body);
        $this->assertStringContainsString(route('bni.events.index'), $navigation);
        $this->assertStringContainsString(route('bni.articles.index'), $navigation);
        $this->assertStringNotContainsString('KINHBAC', $navigation);
        $this->assertStringNotContainsString('KBG', $navigation);
        $this->assertStringNotContainsString('IMPACT', $navigation);
        $this->assertStringNotContainsString('FAMOUS', $navigation);
    }

    public function test_special_activity_grid_uses_featured_events_and_square_activity_media(): void
    {
        Storage::fake('public');

        $featured = BniEvent::query()->create([
            'type' => 'community',
            'title' => 'Sự kiện vuông lấy từ CMS',
            'summary' => 'Mô tả sự kiện được quản trị trong phần Sự kiện.',
            'status' => 'published',
            'is_featured' => true,
            'starts_at' => now()->addMonths(3),
            'venue' => 'Địa điểm sự kiện kiểm thử',
            'landing_url' => 'https://example.test/su-kien-vuong',
        ]);
        $featured->addMedia(UploadedFile::fake()->image('activity-square.png', 1200, 1200))
            ->toMediaCollection('activity_image', 'public');

        $draft = BniEvent::query()->create([
            'type' => 'community',
            'title' => 'Sự kiện nháp không được hiển thị',
            'status' => 'draft',
            'is_featured' => true,
        ]);

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee($featured->title)
            ->assertSee($featured->summary)
            ->assertSee('bni-activity-card--featured')
            ->assertSee('bni-activity-card--compact')
            ->assertSee('bni-activities-grid__side')
            ->assertSee('href="https://example.test/su-kien-vuong"', false)
            ->assertDontSee($draft->title);

        $body = $response->getContent();
        $this->assertSame(1, substr_count($body, 'bni-activity-card--featured'));
        $this->assertLessThanOrEqual(3, substr_count($body, 'bni-activity-card--compact'));
        $this->assertStringContainsString($featured->bniMediaUrl('activity_image'), $body);
    }

    public function test_special_activity_grid_keeps_a_single_event_to_one_card(): void
    {
        BniEvent::query()->where('is_featured', true)->update(['is_featured' => false]);

        $single = BniEvent::query()->create([
            'type' => 'community',
            'title' => 'Chỉ một sự kiện đặc biệt',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee($single->title)
            ->assertSee('bni-activities-grid--single')
            ->assertDontSee('bni-activities-grid__side')
            ->assertDontSee('bni-activity-card--compact');

        $this->assertSame(1, substr_count($response->getContent(), 'bni-activity-card--featured'));
    }

    public function test_special_activity_grid_keeps_one_secondary_event_compact(): void
    {
        BniEvent::query()->where('is_featured', true)->update(['is_featured' => false]);

        BniEvent::query()->create([
            'type' => 'community',
            'title' => 'Sự kiện lớn kiểm thử',
            'status' => 'published',
            'is_featured' => true,
        ]);
        BniEvent::query()->create([
            'type' => 'community',
            'title' => 'Sự kiện phụ kiểm thử',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('bni-activities-grid__side--single')
            ->assertSee('bni-activity-card--compact');
    }

    public function test_handover_renders_active_sponsors_grouped_by_tier(): void
    {
        Storage::fake('public');

        $event = BniEvent::query()->where('type', 'handover')->published()->firstOrFail();
        $sponsors = collect(BniSponsor::tierOptions())->map(function (string $label, string $tier) use ($event): BniSponsor {
            $sponsor = BniSponsor::query()->create([
                'bni_event_id' => $event->getKey(),
                'tier' => $tier,
                'name' => 'Logo '.$tier,
                'url' => 'https://'.$tier.'.example.test',
                'sort_order' => 1,
                'is_active' => true,
            ]);
            $sponsor->addMedia(UploadedFile::fake()->image($tier.'.png', 800, 400))
                ->toMediaCollection('logo', 'public');

            return $sponsor;
        });
        $hidden = BniSponsor::query()->create([
            'bni_event_id' => $event->getKey(),
            'tier' => BniSponsor::TIER_GOLD,
            'name' => 'Logo ẩn không hiển thị',
            'is_active' => false,
        ]);

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('Nhà tài trợ')
            ->assertSee('Nhà tài trợ Kim cương')
            ->assertSee('Nhà tài trợ Vàng')
            ->assertSee('Nhà tài trợ Bạc')
            ->assertSee('Đồng tài trợ')
            ->assertSee('Logo diamond')
            ->assertSee('Logo gold')
            ->assertSee('Logo silver')
            ->assertSee('Logo co_sponsor')
            ->assertSee('href="https://diamond.example.test"', false)
            ->assertDontSee('class="bni-sponsor-card__name"', false)
            ->assertDontSee($hidden->name);

        foreach ($sponsors as $sponsor) {
            $this->assertStringContainsString($sponsor->bniMediaUrl('logo'), $response->getContent());
        }
    }

    public function test_bni_news_registration_and_gallery_pages_share_the_handover_navigation(): void
    {
        $article = BniArticle::query()->published()->firstOrFail();

        foreach ([
            route('bni.articles.index'),
            route('bni.articles.show', ['article' => $article]),
            route('bni.registrations.create'),
            route('bni.gallery.index'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('class="bni-handover-page-nav"', false)
                ->assertSee('href="'.route('bni.articles.index').'"', false)
                ->assertSee('href="'.route('bni.gallery.index').'"', false)
                ->assertSee('href="'.route('bni.registrations.create').'"', false);
        }
    }

    public function test_bni_article_detail_exposes_reading_and_member_comment_ui(): void
    {
        Role::findOrCreate('bni_member', 'web');
        $article = BniArticle::query()->published()->firstOrFail();
        $relatedArticle = BniArticle::query()
            ->published()
            ->whereKeyNot($article->getKey())
            ->firstOrFail();
        $user = User::factory()->create();
        $user->assignRole('bni_member');

        $this->actingAs($user)
            ->get(route('bni.articles.show', ['article' => $article]))
            ->assertOk()
            ->assertSee('class="bni-article__content"', false)
            ->assertSee('id="bni-reactions-title"', false)
            ->assertSee('id="bni-comments-title"', false)
            ->assertSee('bni-comment-form', false)
            ->assertSee('bni-related-articles', false)
            ->assertSee('Bài viết khác')
            ->assertSee($relatedArticle->title)
            ->assertDontSee('Thông tin bài viết')
            ->assertDontSee('ĐÃ ĐĂNG')
            ->assertSee('maxlength="3000"', false)
            ->assertSee('Bình luận sẽ hiển thị sau khi được Ban quản trị duyệt.')
            ->assertSee('Đang đăng nhập với', false);
    }

    public function test_bni_news_index_only_lists_published_handover_news_and_filters_by_category(): void
    {
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $category = BniArticleCategory::query()->create([
            'name' => 'Danh mục kiểm thử',
            'slug' => 'danh-muc-kiem-thu-'.str()->random(8),
            'is_active' => true,
            'sort_order' => 99,
        ]);
        $publishedArticle = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'type' => 'event',
            'title' => 'Tin Lễ chuyển giao hiển thị',
            'slug' => 'tin-le-chuyen-giao-hien-thi-'.str()->random(8),
            'body' => '<p>Nội dung kiểm thử.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $draftArticle = BniArticle::query()->create([
            'bni_event_id' => $event->id,
            'type' => 'event',
            'title' => 'Tin nháp không được hiển thị',
            'slug' => 'tin-nhap-khong-hien-thi-'.str()->random(8),
            'body' => '<p>Nội dung nháp.</p>',
            'status' => 'draft',
        ]);
        $publishedArticle->categories()->attach($category);
        $draftArticle->categories()->attach($category);

        $this->get(route('bni.articles.index', ['danh-muc' => $category->slug]))
            ->assertOk()
            ->assertSee('id="bni-articles-main"', false)
            ->assertSee('Tin tức Lễ chuyển giao BNI')
            ->assertSee($publishedArticle->title)
            ->assertDontSee($draftArticle->title);
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
            'is_active' => true,
            'sort_order' => 99,
        ]);
        BniContact::query()->create([
            'bni_chapter_id' => $chapter->id,
            'name' => 'Đầu mối chapter kiểm thử',
            'position' => 'Giám đốc phát triển',
            'phone' => '0900 111 222',
            'email' => 'chapter-detail@example.test',
            'is_primary' => true,
            'is_active' => true,
        ]);
        BniContact::query()->create([
            'bni_chapter_id' => $chapter->id,
            'name' => 'Đầu mối chapter thứ hai',
            'position' => 'Điều phối viên',
            'phone' => '0900 333 444',
            'is_active' => true,
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
            ->assertSee('Giám đốc phát triển')
            ->assertSee('0900 111 222')
            ->assertSee('chapter-detail@example.test')
            ->assertSee('Đầu mối chapter thứ hai')
            ->assertSee('0900 333 444')
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
        $textSlide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'title' => 'Nội dung slide lấy từ database',
            'description' => 'Phần chữ nằm riêng, không phủ lên ảnh.',
            'button_label' => 'Xem lịch trình',
            'button_url' => '#lich-trinh',
            'alt_text' => 'Ảnh Lễ chuyển giao kiểm thử',
            'is_active' => true,
            'sort_order' => 1,
        ])->refresh();
        $optimizedMedia = $textSlide->addMedia(UploadedFile::fake()->image('handover-slide.jpg', 2600, 1300))
            ->usingName('Ảnh slide kiểm thử')
            ->toMediaCollection('image', 'public');

        $imageOnlySlide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $imageOnlySlide->addMedia(UploadedFile::fake()->image('handover-slide-only.jpg', 1600, 900))
            ->toMediaCollection('image', 'public');

        $this->assertSame('jpg', $optimizedMedia->extension);
        $this->assertSame('image/jpeg', $optimizedMedia->mime_type);
        $this->assertTrue($optimizedMedia->hasGeneratedConversion('webp'));
        $this->assertSame([2600, 1300], array_slice(getimagesize($optimizedMedia->getPath()), 0, 2));
        Storage::disk('public')->assertExists($optimizedMedia->getPathRelativeToRoot('webp'));

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-hero-swiper', false)
            ->assertSee('data-bni-hero-swiper-prev', false)
            ->assertSee('data-bni-hero-swiper-next', false)
            ->assertDontSee('Nội dung slide lấy từ database')
            ->assertDontSee('Phần chữ nằm riêng, không phủ lên ảnh.')
            ->assertSee($optimizedMedia->getUrl('webp'), false);

        $body = $response->getContent();
        $sliderStart = strpos($body, '<section class="bni-event-slider"');
        $sliderEnd = strpos($body, '</section>', $sliderStart);
        $slider = substr($body, $sliderStart, $sliderEnd - $sliderStart);

        $this->assertStringContainsString('class="swiper-slide bni-event-slide"', $slider);
        $this->assertStringNotContainsString('bni-event-slide__content', $slider);
        $this->assertStringNotContainsString('Xem lịch trình', $slider);
        $this->assertStringNotContainsString('<h2', $slider);
        $this->assertStringNotContainsString('<h1', $slider);
        $this->assertStringNotContainsString('bni-experience-kicker', $slider);
        $this->assertStringNotContainsString('overlay', strtolower($slider));
    }

    public function test_a_handover_slide_can_render_an_external_video_with_its_image_as_poster(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $event->slides()->update(['is_active' => false]);
        $slide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'alt_text' => 'Video đầu trang Lễ chuyển giao',
            'video_url' => 'https://www.youtube.com/watch?v=video-dau-trang',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $image = $slide->addMedia(UploadedFile::fake()->image('video-cover.jpg', 1600, 900))
            ->toMediaCollection('image', 'public');

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('class="bni-event-slide__video-link glightbox"', false)
            ->assertSee('href="https://www.youtube.com/watch?v=video-dau-trang"', false)
            ->assertSee('src="'.$image->getUrl('webp'), false)
            ->assertSee('Phát video: Video đầu trang Lễ chuyển giao');
    }

    public function test_a_handover_slide_upload_autoplays_muted_with_native_audio_and_pause_controls(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $event->slides()->update(['is_active' => false]);
        $slide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'alt_text' => 'Video tải lên cho slide đầu trang',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $image = $slide->addMedia(UploadedFile::fake()->image('video-slide-cover.jpg', 1600, 900))
            ->toMediaCollection('image', 'public');
        $videoPath = tempnam(sys_get_temp_dir(), 'bni-slide-video-');
        file_put_contents($videoPath, "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00mp42isom");

        try {
            $video = $slide->addMedia($videoPath)
                ->usingFileName('video-slide.mp4')
                ->toMediaCollection('video', 'public');
        } finally {
            @unlink($videoPath);
        }

        $secondarySlide = BniEventSlide::query()->create([
            'bni_event_id' => $event->id,
            'alt_text' => 'Ảnh phụ của slide đầu trang',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $secondarySlide->addMedia(UploadedFile::fake()->image('video-slide-secondary.jpg', 1600, 900))
            ->toMediaCollection('image', 'public');

        $body = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-hero-video', false)
            ->assertSee('data-bni-hero-swiper-toggle', false)
            ->getContent();
        $sliderStart = strpos($body, '<section class="bni-event-slider"');
        $sliderEnd = strpos($body, '</section>', $sliderStart);
        $slider = substr($body, $sliderStart, $sliderEnd - $sliderStart);

        $this->assertStringContainsString(
            'class="bni-event-slide__video" controls autoplay muted playsinline preload="metadata" poster="'.$image->getUrl('webp'),
            $slider,
        );
        $this->assertStringContainsString($video->getUrl(), $slider);
        $this->assertStringNotContainsString(' loop', $slider);

        $javascript = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($javascript);
        $this->assertStringContainsString('const syncHeroVideos = () => {', $javascript);
        $this->assertStringContainsString('video.play().catch(() => {});', $javascript);

        $heroJavascriptStart = strpos($javascript, 'const initialiseBniHeroSwipers = () => {');
        $heroJavascriptEnd = strpos($javascript, 'const initialisePostSwipers = () => {', $heroJavascriptStart);
        $heroJavascript = substr($javascript, $heroJavascriptStart, $heroJavascriptEnd - $heroJavascriptStart);

        $this->assertStringNotContainsString('pauseOnMouseEnter', $heroJavascript);
    }

    public function test_overview_uses_the_separate_event_video_and_keeps_chapter_media_in_the_four_small_cards(): void
    {
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = $event->chapters()->where('is_active', true)->orderBy('sort_order')->firstOrFail();
        $chapter->update(['video_url' => 'https://www.youtube.com/watch?v=chapter-video']);
        BniEventVideo::query()->updateOrCreate(
            ['bni_event_id' => $event->id],
            ['external_url' => 'https://www.youtube.com/watch?v=intro-video'],
        );

        $body = $this->get(route('bni.handover'))->assertOk()->getContent();
        $overviewStart = strpos($body, '<aside class="bni-overview__video"');
        $overviewEnd = strpos($body, '</aside>', $overviewStart);
        $overview = substr($body, $overviewStart, $overviewEnd - $overviewStart);
        $chapterListStart = strpos($overview, '<div class="bni-chapter-video-list"');
        $featured = substr($overview, 0, $chapterListStart);
        $chapterList = substr($overview, $chapterListStart);

        $this->assertStringContainsString('intro-video', $featured);
        $this->assertStringNotContainsString('chapter-video', $featured);
        $this->assertStringContainsString('chapter-video', $chapterList);
        $this->assertStringNotContainsString('id="video-gioi-thieu"', $body);
        $this->assertDatabaseHas('bni_event_videos', ['bni_event_id' => $event->id, 'external_url' => 'https://www.youtube.com/watch?v=intro-video']);
    }

    public function test_separate_event_video_poster_drives_the_featured_media_without_falling_back_to_chapter_media(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $chapter = $event->chapters()->where('is_active', true)->orderBy('sort_order')->firstOrFail();
        $chapter->update(['video_url' => null]);
        $chapter->clearMediaCollection('cover');
        $chapter->clearMediaCollection('video');

        $eventVideo = BniEventVideo::query()->updateOrCreate(
            ['bni_event_id' => $event->id],
            ['external_url' => 'https://www.youtube.com/watch?v=separate-intro-video'],
        );
        $eventPoster = $eventVideo->addMedia(UploadedFile::fake()->image('separate-event-poster.jpg', 1600, 900))
            ->toMediaCollection('poster', 'public');

        $body = $this->get(route('bni.handover'))->assertOk()->getContent();
        $overviewStart = strpos($body, '<aside class="bni-overview__video"');
        $overviewEnd = strpos($body, '</aside>', $overviewStart);
        $overview = substr($body, $overviewStart, $overviewEnd - $overviewStart);

        $this->assertStringContainsString($eventPoster->getUrl('webp'), $overview);
        $this->assertStringContainsString('separate-intro-video', $overview);
        $this->assertStringNotContainsString('Chưa gắn ảnh cover hoặc video trong quản trị Video sự kiện', $overview);
    }

    public function test_a_new_handover_slide_is_bound_without_an_event_field_in_the_admin_form(): void
    {
        $event = BniEvent::query()
            ->published()
            ->where('type', 'handover')
            ->orderByDesc('is_featured')
            ->orderByDesc('starts_at')
            ->firstOrFail();

        $data = BniEventSlideResource::prepareCreateData([
            'title' => 'Slide tự động thuộc trang Lễ chuyển giao',
        ]);

        $this->assertSame($event->getKey(), $data['bni_event_id']);
    }

    public function test_handover_slider_uses_the_bni_key_visual_when_no_slide_image_exists(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->firstOrFail();
        $event->clearMediaCollection('hero');
        $event->slides->each(function (BniEventSlide $slide): void {
            $slide->clearMediaCollection('image');
            $slide->update([
                'title' => null,
                'description' => null,
                'button_label' => null,
                'button_url' => null,
                'is_active' => true,
            ]);
        });

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-hero-swiper', false)
            ->assertSee('class="swiper-slide bni-event-slide"', false)
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
            ->assertSee('qr_dang_ky.jpg')
            ->assertSee('Quét mã QR để đăng ký')
            ->assertSee('name="full_name"', false)
            ->assertSee('action="'.route('bni.invitations.template.rsvp').'"', false)
            ->assertSee('data-bni-ajax-form', false)
            ->assertSee('data-bni-form-status', false)
            ->assertSee('Ngày 1')
            ->assertSee('Ngày 2')
            ->assertSee('Chỉ đường')
            ->assertSee('https://maps.google.com/?q=BNI+Handover', false)
            ->assertSee('width: fit-content;', false)
            ->assertSee('margin-inline: 0;', false)
            ->assertSee('text-align: left;', false)
            ->assertDontSee('Vì sao nên tham dự?')
            ->assertDontSee('<details', false)
            ->assertDontSee('Mã thư mời')
            ->assertDontSee('bni-invite-rsvp-card', false)
            ->assertDontSee('.bni-invite-heading h2::before', false)
            ->assertDontSee('xem-thu');
    }

    public function test_the_shared_invitation_rsvp_creates_an_event_registration(): void
    {
        $event = BniEvent::query()->where('slug', 'le-chuyen-giao-bni')->firstOrFail();

        $this->postJson(route('bni.invitations.template.rsvp'), [
            'full_name' => 'Khách RSVP kiểm thử',
            'phone' => '0900000000',
            'email' => 'rsvp@example.test',
            'note' => 'Xác nhận tham dự chương trình.',
        ])->assertCreated()
            ->assertJsonPath('message', 'Thông tin RSVP đã được ghi nhận. Ban tổ chức sẽ liên hệ xác nhận.');

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
        $event->update([
            'starts_at' => '2026-10-01 08:00:00',
            'ends_at' => '2026-10-01 11:30:00',
            'venue' => 'Trung tâm Hội nghị BNI',
            'address' => '01 Đường Kết Nối, Bắc Ninh',
            'directions_url' => 'https://maps.app.goo.gl/bni-handover-test',
        ]);

        $this->get(route('bni.registrations.create'))
            ->assertOk()
            ->assertSee('id="bni-registration-main"', false)
            ->assertSee('Đăng ký Lễ chuyển giao')
            ->assertSee('01/10/2026')
            ->assertSee('08:00 – 11:30')
            ->assertSee('Trung tâm Hội nghị BNI, 01 Đường Kết Nối, Bắc Ninh')
            ->assertSee('https://maps.app.goo.gl/bni-handover-test', false)
            ->assertSee('Xem đường đi')
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
            'address' => 'Số 01, đường Kiểm Thử, Hà Nội',
        ]);
        $settings = app(BniInvitationSettings::class);
        $settings->content_title = 'Tiêu đề nội dung lấy từ database';
        $settings->content = '<p>Nội dung chung toàn hệ thống.</p>';
        $settings->note_content = '<p>Dress code chung toàn hệ thống.</p>';
        $settings->rsvp_description = 'Mô tả RSVP lấy từ database.';
        $settings->save();

        $chapter = BniChapter::query()->create([
            'bni_event_id' => $event->id,
            'name' => 'Chapter Kiểm Thử',
            'slug' => 'chapter-kiem-thu',
        ]);
        BniContact::query()->create([
            'bni_chapter_id' => $chapter->id,
            'name' => 'Người phụ trách chapter',
            'phone' => '0900000000',
            'is_primary' => true,
            'is_active' => true,
        ]);
        $scheduleDay = BniScheduleDay::query()->create([
            'bni_event_id' => $event->id,
            'event_date' => '2026-10-01',
            'title' => 'Ngày đón khách',
            'is_active' => true,
        ]);
        BniScheduleItem::query()->create([
            'bni_schedule_day_id' => $scheduleDay->id,
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
            ->assertSee('Thư mời')
            ->assertSee('background-thumoi.jpg')
            ->assertSee('BNI Accelerator')
            ->assertSee('Tới tham dự chương trình chào mừng')
            ->assertSee('class="bni-invite-brand__logo"', false)
            ->assertSee('bni-logo-red.svg')
            ->assertDontSee('bni-invite-brand__mark')
            ->assertSee('Lễ Chuyển Giao Kiểm Thử')
            ->assertSee('class="bni-invite-hero__event-image"', false)
            ->assertSee('src="'.asset('images/bni/le-chuyen-giao.png').'"', false)
            ->assertDontSee('class="bni-invite-hero__event-type">Lễ chuyển giao</p>', false)
            ->assertSee('Kiểm Thử')
            ->assertSee('Địa chỉ')
            ->assertSee('Trung tâm hội nghị, Số 01, đường Kiểm Thử, Hà Nội')
            ->assertDontSee('>Hình thức<', false)
            ->assertSee('class="bni-invite-hero__guest"', false)
            ->assertDontSee('bni-invite-hero__guest--default')
            ->assertSee('Anh/Chị chủ doanh nghiệp')
            ->assertSee('Tiêu đề nội dung lấy từ database')
            ->assertSee('Nội dung chung toàn hệ thống.')
            ->assertSee('Lịch trình sự kiện')
            ->assertSee('Đón tiếp khách mời')
            ->assertSee('Dress code chung toàn hệ thống.')
            ->assertSee('Chapter Kiểm Thử')
            ->assertDontSee('BNI Famous')
            ->assertDontSee('Sự kiện nổi bật')
            ->assertDontSee('bni-invite-featured-events')
            ->assertSee('class="bni-invite-rsvp-layout"', false)
            ->assertSee('qr_dang_ky.jpg')
            ->assertSee('Quét mã QR để đăng ký')
            ->assertSee('Xác nhận tham dự')
            ->assertSee('Mô tả RSVP lấy từ database.')
            ->assertSee('name="rsvp_status"', false)
            ->assertSee('Người phụ trách chapter')
            ->assertSee('property="og:type"', false)
            ->assertSee('"@type":"Event"', false);
    }

    public function test_invitation_does_not_render_featured_events(): void
    {
        $this->get(route('bni.invitations.template'))
            ->assertOk()
            ->assertDontSee('Sự kiện nổi bật')
            ->assertDontSee('bni-invite-featured-events')
            ->assertDontSee('BNI Pickleball Championship');
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
            'title' => 'Bài viết lấy từ danh mục tin BNI',
            'slug' => 'bai-viet-danh-muc-bni-'.str()->random(8),
            'excerpt' => 'Nội dung kiểm thử nguồn BniArticle và BniArticleCategory.',
            'body' => '<p>Nội dung bài viết BNI.</p>',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
        ]);
        $article->addMedia(UploadedFile::fake()->image('article-cover.jpg', 1200, 800))
            ->usingName('Ảnh bài viết BNI từ database')
            ->toMediaCollection('cover', 'public');
        $article->categories()->attach($category);
        $gallery = BniGalleryItem::query()->create([
            'bni_event_id' => $event->id,
            'bni_activity_id' => $activity->id,
            'group' => $activity->type,
            'title' => 'Ảnh BNI lấy trực tiếp từ database',
            'source' => BniGalleryItem::SOURCE_ADMIN,
            'status' => BniGalleryItem::STATUS_APPROVED,
            'is_active' => true,
            'approved_at' => now(),
        ]);
        $galleryMedia = $gallery->addMedia(UploadedFile::fake()->image('database-gallery.jpg', 1200, 800))
            ->usingName('Ảnh BNI từ database')
            ->toMediaCollection('image', 'public');
        $path = $galleryMedia->getUrl('webp');

        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee("@click=\"newsTab = 'bni-article-category-{$category->id}'\"", false)
            ->assertSee('bni-news__heading--articles', false)
            ->assertSee('bni-news-card__media', false)
            ->assertSee('bni-news-card__body', false)
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
            ->assertSeeText('Lịch trình sự kiện')
            ->assertDontSeeText('Lịch thi đấu')
            ->assertDontSee('pickleball-timeline__dot')
            ->assertDontSeeText('Kết quả trực tiếp')
            ->assertSee('Đăng ký tham gia')
            ->assertSee('id="pickleball-news"', false)
            ->assertSeeText('Tin Pickleball')
            ->assertSee('data-bni-ajax-form', false);

        $this->get('/bni-admin/login')->assertOk();
    }

    public function test_pickleball_news_comes_from_the_tin_pickleball_category(): void
    {
        $category = BniArticleCategory::query()->firstOrCreate(
            ['slug' => 'tin-pickleball'],
            ['name' => 'Tin Pickleball', 'is_active' => true, 'sort_order' => 3],
        );
        $category->update(['is_active' => true]);
        $article = BniArticle::query()->create([
            'bni_event_id' => BniEvent::query()->where('type', 'handover')->value('id'),
            'type' => 'event',
            'title' => 'Tin Pickleball lấy theo danh mục kiểm thử',
            'slug' => 'tin-pickleball-theo-danh-muc-'.str()->random(8),
            'excerpt' => 'Bài viết được chọn bằng danh mục Tin Pickleball.',
            'body' => '<p>Nội dung kiểm thử.</p>',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now(),
        ]);
        $article->categories()->attach($category);
        $uncategorized = BniArticle::query()->create([
            'bni_event_id' => BniEvent::query()->where('type', 'pickleball')->value('id'),
            'type' => 'pickleball',
            'title' => 'Tin Pickleball chưa gắn đúng danh mục',
            'slug' => 'tin-pickleball-khong-danh-muc-'.str()->random(8),
            'body' => '<p>Không được hiển thị.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('bni.pickleball'))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee(route('bni.articles.show', ['article' => $article]), false)
            ->assertDontSee($uncategorized->title);
    }

    public function test_pickleball_registration_supports_ajax_without_a_page_redirect(): void
    {
        $event = BniEvent::query()->published()->where('type', 'pickleball')->firstOrFail();

        $this->postJson(route('bni.pickleball.register'), [
            'full_name' => 'Vận động viên AJAX',
            'phone' => '0911222333',
            'email' => 'pickleball-ajax@example.test',
            'team_name' => 'Đội Kết Nối',
            'skill_level' => 'intermediate',
            'note' => 'Đăng ký không tải lại trang.',
        ])->assertCreated()
            ->assertJsonPath('message', 'Đăng ký đã được ghi nhận. Ban tổ chức sẽ liên hệ xác nhận.');

        $this->assertDatabaseHas('bni_registrations', [
            'bni_event_id' => $event->id,
            'full_name' => 'Vận động viên AJAX',
            'phone' => '0911222333',
            'status' => 'pending',
        ]);
    }

    public function test_the_pickleball_hero_uses_the_image_managed_on_the_event(): void
    {
        Storage::fake('public');

        $event = BniEvent::query()->published()->where('type', 'pickleball')->firstOrFail();
        $event->clearMediaCollection('hero');
        $hero = $event->addMedia(UploadedFile::fake()->image('pickleball-cms-hero.jpg', 1600, 900))
            ->toMediaCollection('hero', 'public');

        $this->get(route('bni.pickleball'))
            ->assertOk()
            ->assertSee($hero->getUrl('webp'), false)
            ->assertDontSee('images/pickleball/hero-pickleball.jpg');

        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('.pickleball-hero__image, .pickleball-hero__inner { grid-area: 1 / 1; }', $css);
        $this->assertStringNotContainsString('.pickleball-hero::before', $css);
        $this->assertStringNotContainsString('.pickleball-hero__image { position: absolute', $css);
        $this->assertStringNotContainsString('pickleball-hero__veil', $css);
        $this->assertStringNotContainsString("background-image: url('/images/pickleball/backgroud-pickleball.png');", $css);
        $this->assertStringContainsString('.pickleball-intro { background: transparent; }', $css);
        $this->assertStringContainsString('.pickleball-schedule { background: #fff; }', $css);
        $this->assertStringContainsString(".pickleball-rules { overflow: hidden; background: #780009 url('/images/pickleball/sections/rules-background-bottom-left.webp') left bottom", $css);
        $this->assertStringContainsString('.pickleball-prizes { background: var(--pickleball-cream); }', $css);
        $this->assertStringContainsString('.pickleball-news { background: #fff; }', $css);
        $this->assertStringNotContainsString('pickleball-timeline__dot', $css);
    }

    public function test_a_bni_administrator_can_open_the_dedicated_event_cms(): void
    {
        Role::findOrCreate('bni_admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('bni_admin');

        $this->actingAs($user)
            ->get('/bni-admin/bni-events')
            ->assertOk()
            ->assertSee('Thông tin sự kiện');

        $this->actingAs($user)
            ->get('/bni-admin/bni-members')
            ->assertOk()
            ->assertSee('Hội viên');

        $this->actingAs($user)
            ->get('/bni-admin/manage-bni-invitation-settings')
            ->assertOk()
            ->assertSee('Mẫu thư mời BNI')
            ->assertSee('Lịch trình trên thư mời được lấy tự động')
            ->assertDontSee('Tiêu đề lịch trình');
    }

    public function test_bni_crud_uses_dedicated_create_and_edit_pages(): void
    {
        Role::findOrCreate('bni_admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('bni_admin');
        $event = BniEvent::query()->firstOrFail();
        $chapter = BniChapter::query()->firstOrFail();
        $invitation = BniInvitation::query()->firstOrCreate(
            ['slug' => 'bni-admin-full-page-test'],
            [
                'bni_event_id' => $event->id,
                'bni_chapter_id' => $chapter->id,
                'guest_name' => 'Khách kiểm thử trang quản trị',
            ],
        );

        foreach ([
            '/bni-admin/bni-events/create',
            "/bni-admin/bni-events/{$event->id}/edit",
            '/bni-admin/bni-event-slides/create',
            '/bni-admin/bni-event-videos/create',
            '/bni-admin/bni-chapters/create',
            '/bni-admin/bni-chapter-contacts/create',
            '/bni-admin/bni-general-contacts/create',
            "/bni-admin/bni-chapters/{$chapter->slug}/edit",
            '/bni-admin/bni-purposes/create',
            '/bni-admin/bni-schedule-days/create',
            '/bni-admin/bni-event-landings/create',
            '/bni-admin/bni-event-prizes/create',
            '/bni-admin/bni-pickleball-schedule-days/create',
            '/bni-admin/bni-activities/create',
            '/bni-admin/bni-articles/create',
            '/bni-admin/bni-gallery-items/create',
            '/bni-admin/bni-invitations/create',
            "/bni-admin/bni-invitations/{$invitation->invitation_code}/edit",
            '/bni-admin/bni-registrations/create',
            '/bni-admin/bni-members/create',
        ] as $url) {
            $response = $this->actingAs($user)->get($url);

            $this->assertSame(200, $response->status(), $url);
        }
    }

    public function test_the_invitation_table_prioritizes_a_single_copy_link_action(): void
    {
        Role::findOrCreate('bni_admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('bni_admin');
        $event = BniEvent::query()->firstOrCreate(
            ['slug' => 'copy-link-test-event'],
            ['title' => 'Sự kiện kiểm thử sao chép link'],
        );
        BniInvitation::query()->create([
            'bni_event_id' => $event->id,
            'guest_name' => 'Khách mời kiểm thử thao tác nhanh',
        ]);

        $this->actingAs($user)
            ->get('/bni-admin/bni-invitations')
            ->assertOk()
            ->assertSee('Sao chép link')
            ->assertSee('Thao tác khác')
            ->assertSee('window.navigator.clipboard.writeText', false);
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
        $this->actingAs($user)->get('/bni-admin/bni-articles/create')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-invitations/create')->assertOk();
        $this->actingAs($user)->get('/bni-admin/bni-chapters/create')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-events')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-event-slides')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-event-videos')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-purposes')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-schedule-days')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-event-landings')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-event-prizes')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-pickleball-schedule-days')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-chapter-contacts')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-general-contacts')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-activities')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/bni-members')->assertForbidden();
        $this->actingAs($user)->get('/bni-admin/manage-bni-invitation-settings')->assertForbidden();
    }
}
