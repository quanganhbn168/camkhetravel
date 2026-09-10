<?php

namespace Tests\Feature;

use App\Filament\Resources\HeroSlides\Pages\EditHeroSlide;
use App\Models\HeroSlide;
use App\Models\User;
use App\Support\Media\VideoMediaLibrary;
use Awcodes\Curator\Models\Media;
use Database\Seeders\WebsiteSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HeroVideoLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_library_search_filters_out_images_even_when_their_names_match(): void
    {
        $video = $this->media('qa-library-video', 'video/mp4', 'mp4');
        $image = $this->media('qa-library-image', 'image/jpeg', 'jpg');
        $misnamed = $this->media('qa-library-misnamed', 'image/jpeg', 'mp4');
        $options = VideoMediaLibrary::options('qa-library');
        $this->assertArrayHasKey($video->id, $options);
        $this->assertArrayNotHasKey($image->id, $options);
        $this->assertArrayNotHasKey($misnamed->id, $options);
        $this->assertStringContainsString('MP4', $options[$video->id]);
    }

    public function test_selected_library_video_is_saved_and_used_by_the_homepage_hero(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $video = $this->media('qa-hero-library', 'video/mp4', 'mp4');
        $slide = HeroSlide::create(['title' => 'Hero video QA', 'is_active' => true]);
        $component = Livewire::test(EditHeroSlide::class, ['record' => $slide->id])
            ->set('data.video_source', 'upload')
            ->callAction(TestAction::make('chooseLibraryVideo')->schemaComponent('video_media_id', 'form'), data: ['media_id' => $video->id])
            ->assertHasNoActionErrors();
        $this->assertSame($video->id, collect($component->get('data.video_media_id'))->first()['id']);
        $component->call('save');
        $this->assertSame($video->id, $slide->fresh()->video_media_id);
        $this->get('/')->assertOk()->assertViewHas('heroSlides', fn ($slides) => $slides->contains(fn ($item) => $item['title'] === 'Hero video QA' && $item['video_url'] === $video->url));
    }

    private function media(string $name, string $type, string $ext): Media
    {
        return Media::create(['disk' => 'public', 'directory' => 'qa', 'visibility' => 'public', 'name' => $name, 'title' => $name, 'path' => 'qa/'.$name.'.'.$ext, 'type' => $type, 'ext' => $ext, 'size' => 10]);
    }
}
