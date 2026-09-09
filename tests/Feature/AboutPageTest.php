<?php

namespace Tests\Feature;

use App\Filament\Forms\Components\GalleryPicker;
use App\Filament\Resources\AboutDepartments\Pages\CreateAboutDepartment;
use App\Models\AboutDepartment;
use App\Models\User;
use App\Settings\AboutSettings;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_office_gallery_clear_all_action_requires_confirmation(): void
    {
        $action = GalleryPicker::make('office_gallery')->getRemoveAllAction();

        $this->assertTrue($action->isConfirmationRequired());
        $this->assertSame('Xóa toàn bộ ảnh trong gallery?', $action->getModalHeading());
    }

    public function test_quick_contact_actions_use_brand_roles_instead_of_neutral_ink(): void
    {
        $website = app(WebsiteSettings::class);
        $website->hotline = '0375 433 678';
        $website->zalo_url = 'https://zalo.me/0375433678';
        $website->save();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('floating-action floating-action--phone', false)
            ->assertSee('floating-action floating-action--zalo', false)
            ->assertDontSee('floating-action bg-ink', false);
    }

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

    public function test_about_page_uses_section_images_before_its_own_fallback(): void
    {
        Storage::fake('public');
        $fallback = $this->createImage('media/tests/about-fallback.jpg');
        $story = $this->createImage('media/tests/about-story.jpg');
        $coreValues = $this->createImage('media/tests/about-core-values.jpg');
        $team = $this->createImage('media/tests/about-team.jpg');
        $office = $this->createImage('media/tests/about-office.jpg');
        $officeSecond = $this->createImage('media/tests/about-office-second.jpg');

        $settings = app(AboutSettings::class);
        $settings->default_image_media_id = $fallback->id;
        $settings->video_source = '';
        $settings->story_image_media_id = $story->id;
        $settings->core_values_image_media_id = $coreValues->id;
        $settings->team_title = ['vi' => 'Đội ngũ nhân sự'];
        $settings->team_image_media_id = $team->id;
        $settings->office_title = ['vi' => 'Văn phòng THT Media'];
        $settings->office_image_media_id = $office->id;
        $settings->office_gallery = [$office->id, $officeSecond->id];
        $settings->save();

        $html = $this->get(route('about'))->assertOk()->getContent();

        foreach ([$story->path, $coreValues->path, $team->path, $office->path, $officeSecond->path] as $path) {
            $this->assertStringContainsString($path, $html);
        }
        $this->assertStringContainsString('about-office-gallery', $html);
        $this->assertTrue(strpos($html, $office->path) < strpos($html, $officeSecond->path));
        $this->assertSame(1, substr_count($html, $fallback->path));

        $settings->story_image_media_id = null;
        $settings->core_values_image_media_id = null;
        $settings->team_image_media_id = null;
        $settings->office_image_media_id = null;
        $settings->office_gallery = [];
        $settings->save();

        $fallbackHtml = $this->get(route('about'))->assertOk()->getContent();

        $this->assertSame(5, preg_match_all('/<img[^>]+src="[^"]*'.preg_quote($fallback->path, '/').'[^"]*"/i', $fallbackHtml));
    }

    public function test_about_page_renders_active_departments_and_members_below_the_large_team_image(): void
    {
        Storage::fake('public');
        $team = $this->createImage('media/tests/team-large.jpg');
        $director = $this->createImage('media/tests/director.jpg');

        $settings = app(AboutSettings::class);
        $settings->team_title = ['vi' => 'Đội ngũ nhân sự'];
        $settings->team_image_media_id = $team->id;
        $settings->save();

        $department = AboutDepartment::query()->create([
            'name' => 'Phòng điều hành kiểm thử',
            'description' => 'Đội ngũ phụ trách định hướng và vận hành.',
            'sort_order' => 999,
            'is_active' => true,
        ]);
        $department->members()->createMany([
            [
                'media_id' => $director->id,
                'name' => 'Nguyễn Điều Hành',
                'position' => 'Giám đốc điều hành',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Trần Quản Lý',
                'position' => 'Quản lý dự án',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Nhân sự đang ẩn',
                'position' => 'Không hiển thị',
                'sort_order' => 3,
                'is_active' => false,
            ],
        ]);

        $html = $this->get(route('about'))
            ->assertOk()
            ->assertSee('Phòng điều hành kiểm thử')
            ->assertSee('Đội ngũ phụ trách định hướng và vận hành.')
            ->assertSee('Nguyễn Điều Hành')
            ->assertSee('Giám đốc điều hành')
            ->assertSee('Trần Quản Lý')
            ->assertDontSee('Nhân sự đang ẩn')
            ->getContent();

        $teamImagePosition = strpos($html, $team->path);
        $departmentPosition = strpos($html, 'Phòng điều hành kiểm thử');

        $this->assertNotFalse($teamImagePosition);
        $this->assertNotFalse($departmentPosition);
        $this->assertTrue($teamImagePosition < $departmentPosition);
        $this->assertStringContainsString($director->path, $html);
    }

    public function test_a_super_admin_can_manage_about_departments_and_team_members(): void
    {
        Storage::fake('public');
        $portrait = $this->createImage('media/tests/admin-team-member.jpg');
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));

        $this->actingAs($user)
            ->get('/admin/about-departments')
            ->assertOk()
            ->assertSee('Phòng ban &amp; nhân sự', false);

        $this->actingAs($user)
            ->get('/admin/about-departments/create')
            ->assertOk()
            ->assertSee('Tên phòng ban')
            ->assertSee('Danh sách nhân sự')
            ->assertSee('Mỗi người có ảnh riêng, họ tên và chức vụ')
            ->assertSee('Thêm nhân sự');

        $component = Livewire::test(CreateAboutDepartment::class)
            ->fillForm([
                'name' => 'Phòng sáng tạo kiểm thử',
                'description' => 'Phòng phục vụ kiểm thử quản trị.',
                'sort_order' => 1000,
                'is_active' => true,
                'members' => [[
                    'name' => 'Nhân sự được tạo từ quản trị',
                    'position' => 'Giám đốc sáng tạo',
                    'is_active' => true,
                ]],
            ])
            ->set('data.members.0.media_id', [$portrait->toArray()]);
        $component
            ->call('create')
            ->assertHasNoFormErrors();

        $department = AboutDepartment::query()
            ->where('name', 'Phòng sáng tạo kiểm thử')
            ->with('members')
            ->firstOrFail();

        $this->assertCount(1, $department->members);
        $this->assertSame('Giám đốc sáng tạo', $department->members->first()->position);
        $this->assertSame($portrait->id, $department->members->first()->media_id);
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
