<?php

namespace Tests\Feature;

use App\Settings\AboutSettings;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_about_page_uses_the_requested_story_video_services_and_principles_order(): void
    {
        $settings = app(AboutSettings::class);
        $settings->video_source = 'youtube';
        $settings->video_youtube_url = 'https://youtu.be/dQw4w9WgXcQ';
        $settings->video_media_id = null;
        $settings->services_title = ['vi' => 'Hệ sinh thái dịch vụ của chúng tôi'];
        $settings->team_title = ['vi' => 'Đội ngũ nhân sự'];
        $settings->office_title = ['vi' => 'Văn phòng THT Media'];
        $settings->cta_title = ['vi' => 'Cùng THT Media kể câu chuyện thương hiệu của bạn'];
        $settings->save();

        $response = $this->get(route('about'))
            ->assertOk()
            ->assertSee('about-page-story__media', false)
            ->assertSee('about-page-story__content', false)
            ->assertSee('Video giới thiệu')
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', false)
            ->assertSee('Hệ sinh thái dịch vụ của chúng tôi')
            ->assertSee('Tầm nhìn')
            ->assertSee('Sứ mệnh')
            ->assertSee('Đội ngũ nhân sự')
            ->assertSee('Văn phòng THT Media')
            ->assertSee('Cùng THT Media kể câu chuyện thương hiệu của bạn');

        $html = $response->getContent();
        $story = strpos($html, 'about-page-story section-space');
        $storyMedia = strpos($html, 'about-page-story__media', $story);
        $storyContent = strpos($html, 'about-page-story__content', $story);
        $video = strpos($html, 'about-page-video section-space');
        $services = strpos($html, 'about-page-services section-space');
        $principles = strpos($html, 'about-page-principles section-space');
        $history = strpos($html, 'about-page-history section-space');
        $stats = strpos($html, 'about-page-stats section-space');
        $team = strpos($html, 'about-page-team section-space');
        $office = strpos($html, 'about-page-office section-space');
        $cta = strpos($html, 'about-page-cta');
        $vision = strpos($html, '<h3>Tầm nhìn</h3>', $principles);
        $mission = strpos($html, '<h3>Sứ mệnh</h3>', $principles);

        foreach ([$story, $storyMedia, $storyContent, $video, $services, $principles, $history, $stats, $team, $office, $cta, $vision, $mission] as $position) {
            $this->assertNotFalse($position);
        }
        $this->assertTrue($story < $storyMedia && $storyMedia < $storyContent);
        $this->assertTrue($story < $video && $video < $services && $services < $principles && $principles < $history);
        $this->assertTrue($history < $stats && $stats < $team && $team < $office && $office < $cta);
        $this->assertTrue($vision < $mission);
    }

    public function test_about_page_can_render_an_uploaded_intro_video(): void
    {
        Storage::fake('public');
        $path = 'media/tests/about-intro.mp4';
        Storage::disk('public')->put($path, 'video-content');
        $media = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/tests',
            'visibility' => 'public',
            'name' => 'about-intro',
            'path' => $path,
            'size' => Storage::disk('public')->size($path),
            'type' => 'video/mp4',
            'ext' => 'mp4',
            'title' => 'Video giới thiệu kiểm thử',
        ]);

        $settings = app(AboutSettings::class);
        $settings->video_source = 'upload';
        $settings->video_youtube_url = '';
        $settings->video_media_id = $media->id;
        $settings->save();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<video controls playsinline preload="metadata"', false)
            ->assertSee($path, false)
            ->assertDontSee('youtube-nocookie.com/embed', false);
    }

    public function test_about_page_uses_section_images_before_the_shared_fallback(): void
    {
        Storage::fake('public');
        $fallback = $this->createImage('media/tests/about-fallback.jpg');
        $story = $this->createImage('media/tests/about-story.jpg');
        $coreValues = $this->createImage('media/tests/about-core-values.jpg');
        $team = $this->createImage('media/tests/about-team.jpg');
        $office = $this->createImage('media/tests/about-office.jpg');

        $website = app(WebsiteSettings::class);
        $website->about_image_media_id = $fallback->id;
        $website->save();

        $settings = app(AboutSettings::class);
        $settings->video_source = '';
        $settings->story_image_media_id = $story->id;
        $settings->core_values_image_media_id = $coreValues->id;
        $settings->team_title = ['vi' => 'Đội ngũ nhân sự'];
        $settings->team_image_media_id = $team->id;
        $settings->office_title = ['vi' => 'Văn phòng THT Media'];
        $settings->office_image_media_id = $office->id;
        $settings->save();

        $html = $this->get(route('about'))->assertOk()->getContent();

        foreach ([$story->path, $coreValues->path, $team->path, $office->path] as $path) {
            $this->assertStringContainsString($path, $html);
        }
        $this->assertSame(1, substr_count($html, $fallback->path));

        $settings->story_image_media_id = null;
        $settings->core_values_image_media_id = null;
        $settings->team_image_media_id = null;
        $settings->office_image_media_id = null;
        $settings->save();

        $fallbackHtml = $this->get(route('about'))->assertOk()->getContent();

        $this->assertSame(5, substr_count($fallbackHtml, $fallback->path));
    }

    private function createImage(string $path): Media
    {
        Storage::disk('public')->put($path, 'image-content');

        return Media::query()->create([
            'disk' => 'public',
            'directory' => dirname($path),
            'visibility' => 'public',
            'name' => pathinfo($path, PATHINFO_FILENAME),
            'path' => $path,
            'width' => 1600,
            'height' => 900,
            'size' => Storage::disk('public')->size($path),
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'title' => pathinfo($path, PATHINFO_FILENAME),
        ]);
    }
}
