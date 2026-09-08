<?php

namespace Tests\Feature;

use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Models\BniArticle;
use App\Models\BniEvent;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Settings\WebsiteSettings;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShareImageTest extends TestCase
{
    use DatabaseTransactions;

    private function shareImage(): Media
    {
        return Media::query()->create([
            'disk' => 'public', 'directory' => 'media/seo-test', 'name' => 'share',
            'path' => 'media/seo-test/share.webp', 'type' => 'image/webp', 'ext' => 'webp',
            'width' => 1200, 'height' => 630, 'size' => 100,
        ]);
    }

    public function test_content_and_categories_render_their_own_og_and_twitter_image(): void
    {
        $image = $this->shareImage();
        $cases = [
            [LandingPage::class, 'landingPage'], [Service::class, 'service'],
            [Project::class, 'project'], [Post::class, 'post'],
            [ServiceCategory::class, 'serviceCategory'], [ProjectCategory::class, 'projectCategory'],
            [PostCategory::class, 'postCategory'],
        ];
        foreach ($cases as [$model, $urlMethod]) {
            $isCategory = in_array($model, [ServiceCategory::class, ProjectCategory::class, PostCategory::class]);
            $query = $isCategory ? $model::query()->where('is_active', true) : $model::query()->published();
            $records = $model === LandingPage::class ? $query->get() : $query->limit(1)->get();
            $this->assertNotEmpty($records);
            foreach ($records as $record) {
                $originalImageId = $record->getAttribute('curator_media_id');
                $record->update(['seo_image_media_id' => $image->id]);
                $url = MediaUrl::versioned($image);
                $this->get(LocalizedUrl::$urlMethod($record))->assertOk()
                    ->assertSee('<meta property="og:image" content="'.e($url).'">', false)
                    ->assertSee('<meta name="twitter:image" content="'.e($url).'">', false);
                $this->assertSame($originalImageId, $record->fresh()->getAttribute('curator_media_id'));
            }
        }
    }

    public function test_share_image_can_be_saved_reopened_and_cleared_in_landing_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $page = LandingPage::where('template_key', 'landing_tiktok')->firstOrFail();
        $image = $this->shareImage();
        $before = $page->landing_content;
        Livewire::test(EditLandingPage::class, ['record' => $page->id])
            ->set('data.seo_image_media_id', ['share' => $image->toArray()])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame($image->id, $page->fresh()->seo_image_media_id);
        $after = $page->fresh()->landing_content;
        $assertExistingValues = function (array $expected, array $actual) use (&$assertExistingValues): void {
            foreach ($expected as $key => $value) {
                $this->assertArrayHasKey($key, $actual);
                if (is_array($value)) {
                    $assertExistingValues($value, $actual[$key]);
                } else {
                    $this->assertSame($value, $actual[$key]);
                }
            }
        };
        $assertExistingValues($before, $after);
        $editor = Livewire::test(EditLandingPage::class, ['record' => $page->id]);
        $selected = collect($editor->get('data.seo_image_media_id'))->first();
        $this->assertSame($image->id, $selected['id']);
        $editor->set('data.seo_image_media_id', [])->call('save')->assertHasNoFormErrors();
        $this->assertNull($page->fresh()->seo_image_media_id);
    }

    public function test_cleared_or_deleted_share_image_falls_back_without_changing_the_cover(): void
    {
        $page = LandingPage::where('template_key', 'landing_tiktok')->firstOrFail();
        $image = $this->shareImage();
        $page->update(['seo_image_media_id' => $image->id]);
        $page->setAttribute('image_url', 'https://example.test/cover.webp');
        $seo = app(FrontendSeoBuilder::class);
        $this->assertSame(MediaUrl::versioned($image), $seo->landingPage($page)['image']);

        // Test the FK without deleting or modifying a physical upload.
        DB::table('curator')->where('id', $image->id)->delete();
        $page = $page->fresh();
        $this->assertNull($page->seo_image_media_id);
        $page->setAttribute('image_url', 'https://example.test/cover.webp');
        $this->assertSame('https://example.test/cover.webp', $seo->landingPage($page)['image']);
        $page->setAttribute('image_url', null);
        $settings = app(WebsiteSettings::class);
        $defaultImage = Media::find($settings->seo_image_media_id ?: $settings->logo_media_id)?->url;
        $this->assertSame($defaultImage, $seo->landingPage($page)['image']);
    }

    public function test_bni_event_and_article_use_separate_share_media(): void
    {
        Storage::fake('public');
        $event = BniEvent::query()->published()->where('type', 'handover')->orderByDesc('is_featured')->orderByDesc('starts_at')->firstOrFail();
        $event->addMedia(UploadedFile::fake()->image('event-share.png', 1200, 630))->toMediaCollection('seo_image', 'public');
        $event->unsetRelation('media');
        $this->get('/le-chuyen-giao')->assertOk()
            ->assertSee('<meta property="og:image" content="'.e($event->bniMediaUrl('seo_image')).'">', false);

        $article = BniArticle::query()->published()->firstOrFail();
        $article->addMedia(UploadedFile::fake()->image('article-share.png', 1200, 630))->toMediaCollection('seo_image', 'public');
        $article->unsetRelation('media');
        $this->get(LocalizedUrl::route('bni.articles.show', ['article' => $article]))->assertOk()
            ->assertSee('<meta property="og:image" content="'.e($article->bniMediaUrl('seo_image')).'">', false)
            ->assertSee('<meta name="twitter:image" content="'.e($article->bniMediaUrl('seo_image')).'">', false);
    }
}
