<?php

namespace Tests\Feature;

use App\Filament\Resources\Services\Pages\EditService;
use App\Models\Landing;
use App\Models\LandingEvent;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\User;
use App\Support\Landing\Landing07Catalog;
use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $landing = $this->createBuilderLanding($media);
        $landing->backstageProjects()->sync([$project->id]);
        PricingPlan::query()->create([
            'landing_id' => $landing->id,
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
            ->assertDontSee('javascript:alert', false)
            ->assertSee($media->url, false);
    }

    public function test_tracking_endpoint_records_first_party_attribution_without_raw_ip(): void
    {
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
            ->post(route('landings.track', ['landing' => $landing->id]), [
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

        $event = LandingEvent::query()->where('landing_id', $landing->id)->firstOrFail();

        $this->assertSame('cta_click', $event->event_name);
        $this->assertSame('facebook', $event->utm_source);
        $this->assertSame('anniversary', $event->utm_campaign);
        $this->assertSame(64, strlen((string) $event->ip_hash));
        $this->assertStringNotContainsString('203.0.113.50', (string) $event->ip_hash);
    }

    public function test_landing_form_stores_attribution_and_records_a_lead_conversion(): void
    {
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $this->post('/lien-he', [
            'from_landing' => 1,
            'landing_id' => $landing->id,
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
            'landing_id' => $landing->id,
            'landing_block_id' => 'lead-test',
            'utm_source' => 'google',
            'utm_campaign' => 'landing-test',
        ]);
        $this->assertDatabaseHas('landing_events', [
            'landing_id' => $landing->id,
            'event_name' => 'lead_submit',
            'block_id' => 'lead-test',
        ]);
    }

    public function test_super_admin_can_open_builder_and_tracking_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());

        $response = $this->actingAs($user)
            ->get('/admin/services/'.$landing->id.'/edit')
            ->assertOk();

        foreach ([
            'Bố cục landing page',
            'Các khối nội dung',
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
        $this->assertSame('Landing07 / Phim doanh nghiệp', $options[LandingTemplateRegistry::CORPORATE_FILM]);
        $this->assertSame('Landing07 / Quay chụp sự kiện', $options[LandingTemplateRegistry::EVENT_MEDIA]);
        $this->assertCount(13, $options);
        foreach (array_keys(Landing07Catalog::templates()) as $key) {
            $this->assertArrayHasKey($key, $options);
        }

        Livewire::test(EditService::class, ['record' => $landing->id])
            ->set('data.template_key', LandingTemplateRegistry::CONVERSION)
            ->assertSee('Thiết lập template Chuyển đổi / Báo giá')
            ->assertDontSee('Thiết lập template Tri ân / Sự kiện')
            ->set('data.template_key', LandingTemplateRegistry::PORTFOLIO)
            ->assertSee('Thiết lập template Dự án / Hồ sơ năng lực')
            ->assertDontSee('Thiết lập template Chuyển đổi / Báo giá')
            ->set('data.template_key', LandingTemplateRegistry::CORPORATE_FILM)
            ->assertSee('Thiết lập Landing07 / Phim doanh nghiệp')
            ->assertDontSee('Thiết lập template Dự án / Hồ sơ năng lực')
            ->set('data.template_key', LandingTemplateRegistry::EVENT_MEDIA)
            ->assertSee('Thiết lập Landing07 / Quay chụp sự kiện')
            ->assertDontSee('Thiết lập Landing07 / Phim doanh nghiệp');

        $this->actingAs($user)
            ->get('/admin/landing-events')
            ->assertOk()
            ->assertSee('Tracking landing');
    }

    public function test_each_template_resolves_its_own_schema_view_and_css_source(): void
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
            LandingTemplateRegistry::CORPORATE_FILM => [
                'class' => 'landing-page--landing07-film',
                'copy' => 'THT FILMS',
            ],
            LandingTemplateRegistry::EVENT_MEDIA => [
                'class' => 'landing-page--landing07-event',
                'copy' => 'THT EVENT MEDIA',
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

        $landing->update([
            'template_key' => LandingTemplateRegistry::CORPORATE_FILM,
            'template_settings' => array_replace(
                LandingTemplateRegistry::defaultSettings(LandingTemplateRegistry::CORPORATE_FILM),
                ['film_showreel_url' => 'javascript:alert(1)'],
            ),
        ]);

        $this->get('/'.$landing->slug)
            ->assertOk()
            ->assertDontSee('javascript:alert', false);
    }

    public function test_landing07_templates_provide_source_backed_builder_blueprints(): void
    {
        $filmSections = LandingTemplateRegistry::defaultSections(LandingTemplateRegistry::CORPORATE_FILM);
        $eventSections = LandingTemplateRegistry::defaultSections(LandingTemplateRegistry::EVENT_MEDIA);

        $this->assertSame(
            ['hero', 'benefits', 'projects', 'pricing', 'gallery', 'faqs', 'lead_form'],
            array_column($filmSections, 'type'),
        );
        $this->assertSame(
            ['hero', 'benefits', 'gallery', 'pricing', 'projects', 'faqs', 'lead_form'],
            array_column($eventSections, 'type'),
        );
        $this->assertSame('Các sản phẩm tiêu biểu', $filmSections[2]['data']['title']);
        $this->assertSame('Không chỉ giao file – giao bộ tài nguyên có thể sử dụng ngay', $eventSections[1]['data']['title']);
    }

    public function test_selecting_a_landing07_template_seeds_only_an_empty_builder(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $landing = $this->createBuilderLanding(Media::query()->firstOrFail());
        $landing->update(['sections' => []]);

        $this->actingAs($user);

        Livewire::test(EditService::class, ['record' => $landing->id])
            ->set('data.template_key', LandingTemplateRegistry::CORPORATE_FILM)
            ->assertSet('data.sections.0.type', 'hero')
            ->assertSet('data.sections.2.data.title', 'Các sản phẩm tiêu biểu');

        $landing->update([
            'sections' => [[
                'type' => 'hero',
                'data' => ['block_id' => 'custom-hero', 'title' => 'Nội dung đang quản lý'],
            ]],
        ]);

        Livewire::test(EditService::class, ['record' => $landing->id])
            ->set('data.template_key', LandingTemplateRegistry::EVENT_MEDIA)
            ->assertSet('data.sections', fn (mixed $sections): bool => str_contains(
                json_encode($sections, JSON_UNESCAPED_UNICODE),
                'Nội dung đang quản lý',
            ));
    }

    private function createBuilderLanding(Media $media): Landing
    {
        return Landing::query()->create([
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
}
