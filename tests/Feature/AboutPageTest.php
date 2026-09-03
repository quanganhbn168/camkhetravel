<?php

namespace Tests\Feature;

use App\Settings\AboutSettings;
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
        $settings->save();

        $response = $this->get(route('about'))
            ->assertOk()
            ->assertSee('about-page-story__media', false)
            ->assertSee('about-page-story__content', false)
            ->assertSee('Video giới thiệu')
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', false)
            ->assertSee('Hệ sinh thái dịch vụ của chúng tôi')
            ->assertSee('Tầm nhìn')
            ->assertSee('Sứ mệnh');

        $html = $response->getContent();
        $story = strpos($html, 'about-page-story section-space');
        $storyMedia = strpos($html, 'about-page-story__media', $story);
        $storyContent = strpos($html, 'about-page-story__content', $story);
        $video = strpos($html, 'about-page-video section-space');
        $services = strpos($html, 'about-page-services section-space');
        $principles = strpos($html, 'about-page-principles section-space');
        $history = strpos($html, 'about-page-history section-space');
        $vision = strpos($html, '<h3>Tầm nhìn</h3>', $principles);
        $mission = strpos($html, '<h3>Sứ mệnh</h3>', $principles);

        $this->assertNotFalse($history);
        $this->assertTrue($story < $storyMedia && $storyMedia < $storyContent);
        $this->assertTrue($story < $video && $video < $services && $services < $principles && $principles < $history);
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
}
