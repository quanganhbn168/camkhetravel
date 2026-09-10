<?php

namespace Tests\Feature;

use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Models\LandingPage;
use App\Models\LandingEvent;
use App\Models\Post;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Support\Landing\LandingRegistry;
use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingBuilderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_builder_landing_renders_managed_blocks_and_linked_content(): void
    {
        $media = Media::query()->firstOrFail();
        $project = Project::query()->published()->firstOrFail();
        $category = ServiceCategory::query()->where('is_active', true)->firstOrFail();
        $service = Service::query()->published()->firstOrFail();
        $post = Post::query()->published()->firstOrFail();
        $landing = $this->createBuilderLanding($media);
        $landing->projects()->sync([$project->id]);
        $landing->serviceCategories()->sync([$category->id]);
        $landing->services()->sync([$service->id]);
        $landing->posts()->sync([$post->id]);
        $landing->update([
            'sections' => [
                ...$landing->sections,
                ['type' => 'service_categories', 'data' => ['block_id' => 'danh-muc-dich-vu', 'title' => 'Danh mục dịch vụ']],
                ['type' => 'services', 'data' => ['block_id' => 'dich-vu-lien-quan', 'title' => 'Dịch vụ liên quan']],
                ['type' => 'posts', 'data' => ['block_id' => 'bai-viet-lien-quan', 'title' => 'Bài viết liên quan']],
            ],
        ]);
        PricingPlan::query()->create([
            'landing_page_id' => $landing->id,
            'name' => 'Gói landing kiểm thử',
            'price_label' => 'Miễn phí',
            'features' => ['Quyền lợi kiểm thử'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get('/'.$landing->slug.'?utm_source=phpunit')
            ->assertOk()
            ->assertSee('data-landing-page', false)
            ->assertSee('data-landing-countdown', false)
            ->assertSee('landing-page--anniversary', false)
            ->assertSee('Landing builder kiểm thử')
            ->assertSee('Gói landing kiểm thử')
            ->assertSee($project->title)
            ->assertSee('data-landing-block="service_categories"', false)
            ->assertSee('data-landing-block="services"', false)
            ->assertSee('data-landing-block="posts"', false)
            ->assertSee($category->name)
            ->assertSee($service->title)
            ->assertSee($post->title)
            ->assertDontSee('javascript:alert', false)
            ->assertSee($media->url, false);
    }

    public function test_tracking_endpoint_records_first_party_attribution_without_raw_ip(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
            ->post(route('landing-pages.track', ['landingPage' => $landing->id]), [
                'event_name' => 'cta_click',
                'block_id' => 'hero-test',
                'visitor_id' => 'visitor-test',
                'session_id' => 'session-test',
                'utm_source' => 'facebook',
                'utm_campaign' => 'anniversary',
                'page_url' => 'https://example.test/landing',
                'payload' => ['label' => 'Đăng ký'],
            ])
            ->assertNoContent();

        $event = LandingEvent::query()->where('landing_page_id', $landing->id)->firstOrFail();

        $this->assertSame('cta_click', $event->event_name);
        $this->assertSame('facebook', $event->utm_source);
        $this->assertSame('anniversary', $event->utm_campaign);
        $this->assertSame(64, strlen((string) $event->ip_hash));
        $this->assertStringNotContainsString('203.0.113.50', (string) $event->ip_hash);
    }

    public function test_landing_form_stores_attribution_and_records_a_lead_conversion(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $this->post('/lien-he', [
            'from_landing_page' => 1,
            'landing_page_id' => $landing->id,
            'landing_block_id' => 'lead-test',
            'name' => 'Khách hàng kiểm thử',
            'phone' => '0900000000',
            'message' => 'Đăng ký landing builder',
            'visitor_id' => 'visitor-conversion',
            'session_id' => 'session-conversion',
            'utm_source' => 'google',
            'utm_campaign' => 'landing-test',
            'first_url' => 'https://example.test/?utm_source=google',
            'return_to' => '/'.$landing->slug.'#tu-van',
        ])->assertRedirect('/'.$landing->slug.'#tu-van');

        $this->assertDatabaseHas('contact_requests', [
            'landing_page_id' => $landing->id,
            'landing_block_id' => 'lead-test',
            'utm_source' => 'google',
            'utm_campaign' => 'landing-test',
        ]);
        $this->assertDatabaseHas('landing_events', [
            'landing_page_id' => $landing->id,
            'event_name' => 'lead_submit',
            'block_id' => 'lead-test',
        ]);
    }

    public function test_service_native_landing_modal_uses_the_shared_lead_contract(): void
    {
        $service = Service::query()->where('title', 'Tổ chức sự kiện trọn gói')->firstOrFail();

        $this->get('/to-chuc-su-kien-tron-goi-chuyen-nghiep')
            ->assertOk()
            ->assertSee('action="'.\App\Support\Localization\LocalizedUrl::route('contact.store').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="from_landing_page"', false)
            ->assertSee('name="service_id" value="'.$service->id.'"', false)
            ->assertSee('name="landing_block_id"', false)
            ->assertSee('name="name"', false)
            ->assertDontSee('name="fullname"', false)
            ->assertDontSee('script.google.com', false);
    }

    public function test_super_admin_can_open_builder_and_tracking_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $response = $this->actingAs($user)
            ->get('/admin/landing-pages/'.$landing->id.'/edit')
            ->assertOk();

        foreach ([
            'Bố cục landing page',
            'Các khối nội dung',
            'Danh mục dịch vụ',
            'Bài viết / blog liên quan',
            'Tri ân / Sự kiện',
            'Thiết lập template Tri ân / Sự kiện',
        ] as $expectedAdminCopy) {
            $this->assertTrue(
                str_contains($response->getContent(), $expectedAdminCopy),
                'Admin landing schema thiếu nội dung: '.$expectedAdminCopy,
            );
        }

        $options = LandingTemplateRegistry::options();
        $this->assertSame('Tri ân / Sự kiện', $options[LandingTemplateRegistry::ANNIVERSARY]);
        $this->assertSame('Landing / Sản xuất phim doanh nghiệp', $options[LandingRegistry::CORPORATE_FILM]);
        $this->assertSame('Landing / Quay phim, chụp ảnh sự kiện', $options[LandingRegistry::EVENT_MEDIA]);
        $this->assertCount(15, $options);
        foreach (array_keys(LandingRegistry::pages()) as $key) {
            $this->assertArrayHasKey($key, $options);
        }

        Livewire::test(EditLandingPage::class, ['record' => $landing->id])
            ->set('data.template_key', LandingTemplateRegistry::CONVERSION)
            ->assertSee('Thiết lập template Chuyển đổi / Báo giá')
            ->assertDontSee('Thiết lập template Tri ân / Sự kiện')
            ->set('data.template_key', LandingTemplateRegistry::PORTFOLIO)
            ->assertSee('Thiết lập template Dự án / Hồ sơ năng lực')
            ->assertDontSee('Thiết lập template Chuyển đổi / Báo giá')
            ->set('data.template_key', LandingRegistry::CORPORATE_FILM)
            ->assertSee('Thiết lập Sản xuất phim doanh nghiệp')
            ->assertDontSee('Thiết lập template Dự án / Hồ sơ năng lực')
            ->set('data.template_key', LandingRegistry::EVENT_MEDIA)
            ->assertSee('Thiết lập Quay phim, chụp ảnh sự kiện')
            ->assertDontSee('Thiết lập Sản xuất phim doanh nghiệp');

        $this->actingAs($user)
            ->get('/admin/landing-events')
            ->assertOk()
            ->assertSee('Tracking landing');

        $this->actingAs($user)
            ->get('/admin/landing-tracking-overview')
            ->assertOk()
            ->assertSee('Tổng quan tracking nội bộ')
            ->assertSee('Bộ lọc báo cáo')
            ->assertSee('Lượt xem ghi nhận')
            ->assertSee('Nguồn traffic');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Tổng quan quản trị')
            ->assertSee('Lượt xem ghi nhận')
            ->assertSee('Tổng quan', false)
            ->assertSee('Khách hàng & tracking')
            ->assertSee('Trang chủ', false)
            ->assertDontSee('Nội dung trang chủ')
            ->assertDontSee('Khách hàng & liên hệ');
    }

    public function test_builder_templates_and_native_landings_resolve_distinct_contracts(): void
    {
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());
        $cases = [
            LandingTemplateRegistry::CONVERSION => [
                'class' => 'landing-page--conversion',
                'copy' => 'THT MEDIA / GROWTH',
            ],
            LandingTemplateRegistry::PORTFOLIO => [
                'class' => 'landing-page--portfolio',
                'copy' => 'THT MEDIA / SELECTED WORKS',
            ],
        ];

        foreach ($cases as $templateKey => $expected) {
            $landing->update([
                'template_key' => $templateKey,
                'template_settings' => LandingTemplateRegistry::defaultSettings($templateKey),
                'theme_settings' => LandingTemplateRegistry::palette($templateKey),
            ]);

            $this->get('/'.$landing->slug)
                ->assertOk()
                ->assertSee($expected['class'], false)
                ->assertSee($expected['copy']);

            $definition = LandingTemplateRegistry::find($templateKey);

            $this->assertNotNull($definition);
            $this->assertFileExists(base_path($definition['css_source']));
        }

        foreach (LandingRegistry::pages() as $key => $page) {
            $definition = LandingTemplateRegistry::find($key);

            $this->assertSame('frontend.landing.shell', $definition['view']);
            $this->assertSame($page['css'], $definition['css_source']);
        }
    }

    public function test_native_landing_templates_use_database_content_not_builder_blueprints(): void
    {
        $this->assertSame([], LandingTemplateRegistry::defaultSections(LandingRegistry::CORPORATE_FILM));
        $this->assertSame([], LandingTemplateRegistry::defaultSections(LandingRegistry::EVENT_MEDIA));
        $this->assertNotEmpty($this->service('san-xuat-phim-doanh-nghiep')->landing_content);
        $this->assertNotEmpty($this->service('quay-chup-live-su-kien-chuong-trinh')->landing_content);
    }

    public function test_selecting_a_landing_template_seeds_only_an_empty_builder(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());
        $landing->update(['sections' => []]);

        $this->actingAs($user);

        Livewire::test(EditLandingPage::class, ['record' => $landing->id])
            ->set('data.template_key', LandingRegistry::CORPORATE_FILM)
            ->assertSet('data.sections', []);

        $landing->update([
            'sections' => [[
                'type' => 'hero',
                'data' => ['block_id' => 'custom-hero', 'title' => 'Nội dung đang quản lý'],
            ]],
        ]);

        Livewire::test(EditLandingPage::class, ['record' => $landing->id])
            ->set('data.template_key', LandingRegistry::EVENT_MEDIA)
            ->assertSet('data.sections', fn (mixed $sections): bool => str_contains(
                json_encode($sections, JSON_UNESCAPED_UNICODE),
                'Nội dung đang quản lý',
            ));
    }

    private function createBuilderLanding(Media $media): LandingPage
    {
        return LandingPage::query()->create([
            'title' => 'Landing builder kiểm thử '.uniqid(),
            'slug' => 'landing-builder-kiem-thu-'.uniqid(),
            'excerpt' => 'Nội dung landing builder kiểm thử.',
            'curator_media_id' => $media->id,
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'layout_mode' => 'custom_template',
            'template_key' => LandingTemplateRegistry::ANNIVERSARY,
            'template_settings' => LandingTemplateRegistry::defaultSettings(LandingTemplateRegistry::ANNIVERSARY),
            'theme_settings' => LandingTemplateRegistry::palette(LandingTemplateRegistry::ANNIVERSARY),
            'campaign_starts_at' => now()->subDay(),
            'campaign_ends_at' => now()->addDay(),
            'show_header' => false,
            'show_footer' => false,
            'tracking_enabled' => true,
            'sections' => [
                [
                    'type' => 'hero',
                    'data' => [
                        'block_id' => 'hero-test',
                        'title' => 'Landing builder kiểm thử',
                        'media_id' => $media->id,
                        'cta_label' => 'Đăng ký',
                        'cta_url' => '#tu-van',
                        'secondary_label' => 'Liên kết không an toàn',
                        'secondary_url' => 'javascript:alert(1)',
                    ],
                ],
                [
                    'type' => 'countdown',
                    'data' => [
                        'block_id' => 'countdown-test',
                        'title' => 'Thời gian còn lại',
                        'ends_at' => now()->addDay()->format('Y-m-d H:i:s'),
                    ],
                ],
                ['type' => 'pricing', 'data' => ['block_id' => 'pricing-test', 'title' => 'Gói giá']],
                ['type' => 'projects', 'data' => ['block_id' => 'projects-test', 'title' => 'Dự án']],
                ['type' => 'lead_form', 'data' => ['block_id' => 'tu-van', 'title' => 'Đăng ký']],
            ],
        ]);
    }

    private function service(string $slug): Service
    {
        return Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }
}
