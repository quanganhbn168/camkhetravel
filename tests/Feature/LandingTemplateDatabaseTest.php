<?php

namespace Tests\Feature;

use App\Models\Landing;
use App\Models\LandingTemplate;
use App\Models\PricingPlan;
use App\Models\User;
use App\Support\Landing\Landing07Catalog;
use App\Support\Landing\LandingTemplateRegistry;
use Database\Seeders\Landing07ContentSeeder;
use Database\Seeders\LandingTemplateSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingTemplateDatabaseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_template_catalog_is_database_backed_with_source_and_schema(): void
    {
        $this->assertSame(13, LandingTemplate::query()->count());

        $film = LandingTemplate::query()
            ->where('key', LandingTemplateRegistry::CORPORATE_FILM)
            ->firstOrFail();

        $this->assertSame('Landing07 / Phim doanh nghiệp', $film->name);
        $this->assertSame(
            'landing-07/dichvulamphimdoanhnghiep/data.php',
            $film->source_path,
        );
        $this->assertSame('frontend.services.templates.landing07-corporate-film', $film->view_name);
        $this->assertCount(8, $film->settings_schema['fields']);
        $this->assertSame('hero', $film->default_sections[0]['type']);
        $this->assertSame($film->id, LandingTemplateRegistry::id($film->key));

        $this->assertSame(10, LandingTemplate::query()->where('source_name', 'like', 'Landing07%')->count());
        foreach (Landing07Catalog::templates() as $key => $definition) {
            $template = LandingTemplate::query()->where('key', $key)->firstOrFail();

            $this->assertSame($definition['view'], $template->view_name);
            $this->assertNotEmpty($template->default_sections);
            $this->assertStringNotContainsString('wp-content', (string) $template->source_path);
        }
    }

    public function test_landing07_content_seeder_is_idempotent_and_links_native_content(): void
    {
        $this->seed(Landing07ContentSeeder::class);

        $counts = [
            'templates' => LandingTemplate::query()->count(),
            'film_plans' => PricingPlan::query()->where('landing_id', $this->landing('san-xuat-phim-doanh-nghiep')->id)->count(),
            'event_plans' => PricingPlan::query()->where('landing_id', $this->landing('quay-chup-live-su-kien-chuong-trinh')->id)->count(),
        ];
        $filmBeforeReseed = $this->landing('san-xuat-phim-doanh-nghiep');
        $filmBeforeReseed->update([
            'template_settings' => array_replace($filmBeforeReseed->template_settings, [
                'film_brand_label' => 'Nhãn do quản trị sửa',
            ]),
        ]);
        $filmBeforeReseed->pricingPlans()->where('name', 'Cơ bản')->update([
            'description' => 'Mô tả gói do quản trị sửa',
        ]);

        $this->seed(Landing07ContentSeeder::class);

        $film = $this->landing('san-xuat-phim-doanh-nghiep');
        $event = $this->landing('quay-chup-live-su-kien-chuong-trinh');

        $this->assertSame($counts['templates'], LandingTemplate::query()->count());
        $this->assertSame($counts['film_plans'], $film->pricingPlans()->count());
        $this->assertSame($counts['event_plans'], $event->pricingPlans()->count());
        $this->assertSame(4, $film->pricingPlans()->count());
        $this->assertSame(3, $event->pricingPlans()->count());
        $this->assertGreaterThanOrEqual(4, $film->backstageProjects()->count());
        $this->assertGreaterThanOrEqual(4, $event->backstageProjects()->count());
        $this->assertSame(LandingTemplateRegistry::CORPORATE_FILM, $film->template_key);
        $this->assertSame(LandingTemplateRegistry::EVENT_MEDIA, $event->template_key);
        $this->assertNotNull($film->landing_template_id);
        $this->assertNotNull($event->landing_template_id);
        $this->assertSame('Nhãn do quản trị sửa', $film->template_settings['film_brand_label']);
        $this->assertSame(
            'Mô tả gói do quản trị sửa',
            $film->pricingPlans()->where('name', 'Cơ bản')->value('description'),
        );

        foreach (Landing07Catalog::pages() as $slug => $page) {
            $landing = $this->landing($slug);

            $this->assertSame($page['template_key'], $landing->template_key);
            $this->assertSame(7, count($landing->sections));
            $this->assertNotEmpty($landing->pricingPlans);
            $this->assertNotEmpty($landing->backstageProjects);
            $this->assertNull($landing->legacy_content_item_id);
            $this->assertNull($landing->legacy_media_asset_id);
        }
    }

    public function test_template_seeder_preserves_admin_managed_values(): void
    {
        $template = LandingTemplate::query()->where('key', LandingTemplateRegistry::CONVERSION)->firstOrFail();
        $template->update([
            'name' => 'Tên template do quản trị đặt',
            'is_active' => false,
            'palette' => array_replace($template->palette, ['accent' => '#123456']),
        ]);

        $this->seed(LandingTemplateSeeder::class);
        $template->refresh();

        $this->assertSame('Tên template do quản trị đặt', $template->name);
        $this->assertFalse($template->is_active);
        $this->assertSame('#123456', $template->palette['accent']);
    }

    public function test_seeded_landing07_pages_render_their_template_data(): void
    {
        $this->get('/san-xuat-phim-doanh-nghiep')
            ->assertOk()
            ->assertSee('landing-page--landing07-film', false)
            ->assertSee('Khi khách hàng chưa đến doanh nghiệp')
            ->assertSee('Cơ bản')
            ->assertSee('Chất lượng cao');

        $this->get('/quay-chup-live-su-kien-chuong-trinh')
            ->assertOk()
            ->assertSee('landing-page--landing07-event', false)
            ->assertSee('Không chỉ giao file')
            ->assertSee('Chụp ảnh sự kiện')
            ->assertSee('Media toàn diện');

        foreach (Landing07Catalog::pages() as $slug => $page) {
            $this->get('/'.$slug)
                ->assertOk()
                ->assertSee('data-landing-block="hero"', false)
                ->assertSee('data-landing-block="pricing"', false)
                ->assertDontSee('thtmedia.com.vn')
                ->assertDontSee('wp-content');
        }
    }

    public function test_super_admin_can_manage_the_database_template_catalog(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $template = LandingTemplate::query()->where('key', LandingTemplateRegistry::EVENT_MEDIA)->firstOrFail();

        $this->actingAs($user)
            ->get('/admin/landing-templates')
            ->assertOk()
            ->assertSee('Kho template landing')
            ->assertSee('Landing07 / Quay chụp sự kiện');

        $this->actingAs($user)
            ->get('/admin/landing-templates/'.$template->id.'/edit')
            ->assertOk()
            ->assertSee('Schema quản trị của template')
            ->assertSee('Bảng màu mặc định')
            ->assertSee('CSS riêng');
    }

    private function landing(string $slug): Landing
    {
        return Landing::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }
}
