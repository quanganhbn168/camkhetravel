<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use App\Models\HeroSlide;
use App\Models\Menu;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use Awcodes\Curator\Models\Media;
use Database\Seeders\CurrentSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurrentSiteSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot_remaps_ids_restores_content_and_keeps_accounts_and_enquiries(): void
    {
        Storage::fake('public');
        Media::forceCreate(['id' => 100, 'disk' => 'public', 'directory' => 'uploads', 'path' => 'uploads/keep.webp', 'name' => 'keep', 'type' => 'image/webp', 'ext' => 'webp']);
        $category = ServiceCategory::create(['id' => 50, 'name' => 'Danh mục cũ', 'is_active' => true]);
        $service = Service::create(['id' => 200, 'title' => 'Dịch vụ đã sửa tên', 'slug' => 'bao-xe-di-tinh', 'service_category_id' => $category->id, 'status' => 'published']);
        $extra = Service::create(['title' => 'Dịch vụ ngoài bản chốt', 'service_category_id' => $category->id, 'status' => 'published']);
        $request = ContactRequest::create(['name' => 'Khách đang xử lý', 'phone' => '0912345678', 'message' => 'Giữ yêu cầu này', 'service_id' => $service->id]);
        $user = User::factory()->create();

        $this->seed(CurrentSiteSeeder::class);

        $this->assertSame('Bao xe đi tỉnh', $service->fresh()->title);
        $this->assertSame($service->id, $request->fresh()->service_id);
        $this->assertTrue(User::whereKey($user->id)->exists());
        $this->assertSame('draft', $extra->fresh()->status);
        $this->assertSame(5, Service::published()->count());
        $this->assertSame(3, Post::published()->count());
        $this->assertSame('2026-09-23', Post::where('title', 'Trao đổi lịch đón cho chuyến xe cưới')->first()->published_at->toDateString());
        $home = app(HomepageSettings::class)->refresh();
        $this->assertCount(3, $home->audience_groups);
        $this->assertContains($service->id, $home->audience_groups[0]['service_ids']);
        $this->assertSame('media/homepage/v1/service-private.webp', Media::findOrFail($home->audience_groups[0]['media_id'])->path);
        $this->assertGreaterThan(100, $home->audience_groups[0]['media_id']);
        $this->assertSame(7, Menu::where('location', 'header')->first()->items()->count());
        $this->assertSame('0354865688', app(WebsiteSettings::class)->refresh()->hotline);
        $this->get('/')->assertOk()->assertSee('tel:0354865688', false)->assertSee('https://zalo.me/0354865688', false);
        $this->assertCount(17, Storage::disk('public')->allFiles('media'));

        $counts = [Media::count(), Service::count(), Post::count(), HeroSlide::count()];
        $home->audience_groups[0]['title'] = 'Nội dung khác bản chốt';
        $home->save();
        HeroSlide::first()->update(['title' => 'Hero đã chỉnh']);
        Storage::disk('public')->delete('media/homepage/v1/hero-vinfast.webp');
        $this->seed(CurrentSiteSeeder::class);
        $this->assertSame($counts, [Media::count(), Service::count(), Post::count(), HeroSlide::count()]);
        $this->assertSame('Chuyến đi riêng', $home->refresh()->audience_groups[0]['title']);
        $this->assertSame('Mỗi hành trình, một sự đồng hành.', HeroSlide::active()->first()->title);
        Storage::disk('public')->assertExists('media/homepage/v1/hero-vinfast.webp');
        $this->assertSame(1, ContactRequest::count());
    }

    public function test_snapshot_runs_on_an_empty_content_database_without_other_seeders(): void
    {
        DB::table('settings')->delete();
        DB::table('curator')->delete();
        Storage::fake('public');
        $this->seed(CurrentSiteSeeder::class);
        $this->assertSame(9, Media::count());
        $this->get('/')->assertOk()->assertSee('Thêm cảm hứng, thêm kinh nghiệm')->assertSee('0354865688');
        $this->get('/bao-xe-di-tinh')->assertOk();
        $this->get('/blog')->assertOk()->assertSee('Kinh nghiệm hành trình');
        $this->get('/gioi-thieu')->assertOk();
    }

    public function test_a_conflicting_content_url_rolls_back_database_changes(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/homepage/v1/hero-vinfast.webp', 'Existing image');
        Post::create(['title' => 'Bài đang sở hữu URL', 'slug' => 'bao-xe-di-tinh', 'status' => 'published']);
        $beforeMedia = Media::count();
        try {
            $this->seed(CurrentSiteSeeder::class);
            $this->fail('Expected a conflicting URL to stop the snapshot.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('bao-xe-di-tinh', $exception->getMessage());
        }
        $this->assertSame(0, Service::count());
        $this->assertSame($beforeMedia, Media::count());
        $this->assertSame('Bài đang sở hữu URL', Post::first()->title);
        $this->assertSame(hash('sha256', 'Existing image'), hash('sha256', Storage::disk('public')->get('media/homepage/v1/hero-vinfast.webp')));
        $this->assertCount(1, Storage::disk('public')->allFiles('media'));
    }

    public function test_a_media_write_failure_restores_files_and_database(): void
    {
        $disk = Storage::fake('public');
        $disk->put('media/homepage/v1/hero-vinfast.webp', 'Existing hero');
        $settings = app(WebsiteSettings::class);
        $settings->hotline = '0912345678';
        $settings->save();
        $adapter = \Mockery::mock($disk)->makePartial();
        $failed = false;
        $adapter->shouldReceive('put')->andReturnUsing(function ($path, $contents) use ($disk, &$failed) {
            if (! $failed && $path === 'media/homepage/v1/service-private.webp') {
                $failed = true;

                return false;
            }

            return $disk->put($path, $contents);
        });
        Storage::set('public', $adapter);
        try {
            $this->seed(CurrentSiteSeeder::class);
            $this->fail('Expected the failed file write to stop the snapshot.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Không thể ghi ảnh', $exception->getMessage());
        }
        $this->assertSame('Existing hero', $disk->get('media/homepage/v1/hero-vinfast.webp'));
        $this->assertCount(1, $disk->allFiles('media'));
        $this->assertSame(0, Service::count());
        $this->assertSame(1, Media::count());
        $this->assertSame('0912345678', $settings->refresh()->hotline);
    }
}
