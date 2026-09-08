<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\LandingTemplate;
use App\Models\PricingPackage;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\User;
use App\Support\Landing\LandingRegistry;
use App\Support\Landing\LandingTemplateRegistry;
use Database\Seeders\LandingContentSeeder;
use Database\Seeders\LandingTemplateSeeder;
use Database\Seeders\SourceServiceSeeder;
use Database\Seeders\Support\LandingSeedCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingTemplateDatabaseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_template_catalog_is_database_backed_with_source_and_schema(): void
    {
        $this->assertSame(14, LandingTemplate::query()->count());

        $film = LandingTemplate::query()
            ->where('key', LandingRegistry::CORPORATE_FILM)
            ->firstOrFail();

        $this->assertSame('Landing / Sản xuất phim doanh nghiệp', $film->name);
        $this->assertSame(
            'database/seeders/data/landing/landing_corporate_film.json',
            $film->source_path,
        );
        $this->assertSame('frontend.landing.shell', $film->view_name);
        $this->assertCount(4, $film->settings_schema['fields']);
        $this->assertSame($film->id, LandingTemplateRegistry::id($film->key));

        $this->assertSame(11, LandingTemplate::query()->where('source_name', 'like', 'Landing%')->count());
        foreach (LandingRegistry::templateDefinitions() as $key => $definition) {
            $template = LandingTemplate::query()->where('key', $key)->firstOrFail();

            $this->assertSame($definition['view'], $template->view_name);
            $this->assertStringStartsWith('database/seeders/data/landing/', (string) $template->source_path);
        }
    }

    public function test_landing_content_seeder_is_idempotent_and_links_native_content(): void
    {
        $this->seed(SourceServiceSeeder::class);
        $this->seed(LandingContentSeeder::class);

        $counts = [
            'templates' => LandingTemplate::query()->count(),
            'film_plans' => $this->service('san-xuat-phim-doanh-nghiep')->pricingCatalog?->packages()->count() ?? 0,
            'event_plans' => $this->service('quay-chup-live-su-kien-chuong-trinh')->pricingCatalog?->packages()->count() ?? 0,
        ];
        $filmBeforeReseed = $this->service('san-xuat-phim-doanh-nghiep');
        $filmBeforeReseed->pricingCatalog->packages()->where('name', 'Chụp ảnh doanh nghiệp , TVC cơ bản')->update([
            'description' => 'Mô tả gói do quản trị sửa',
        ]);

        $this->seed(SourceServiceSeeder::class);
        $this->seed(LandingContentSeeder::class);

        $film = $this->service('san-xuat-phim-doanh-nghiep');
        $event = $this->service('quay-chup-live-su-kien-chuong-trinh');

        $this->assertSame($counts['templates'], LandingTemplate::query()->count());
        $this->assertSame($counts['film_plans'], $film->pricingCatalog?->packages()->count() ?? 0);
        $this->assertSame($counts['event_plans'], $event->pricingCatalog?->packages()->count() ?? 0);
        $this->assertSame(3, $film->pricingCatalog->packages()->count());
        $this->assertSame(0, $event->pricingCatalog?->packages()->count() ?? 0);
        $this->assertGreaterThanOrEqual(0, $film->backstageProjects()->count());
        $this->assertGreaterThanOrEqual(0, $event->backstageProjects()->count());
        $this->assertNotEmpty($film->landing_content);
        $this->assertNotEmpty($event->landing_content);
        $this->assertSame(
            '13,000,000',
            $film->pricingCatalog->packages()->where('name', 'Chụp ảnh doanh nghiệp , TVC cơ bản')->value('price_label'),
        );

        foreach (LandingSeedCatalog::pages() as $slug => $page) {
            if (in_array($slug, ['phong-su-cuoi', 'to-chuc-su-kien-tron-goi'], true)) {
                continue;
            }

            $landing = $this->landing($slug);

            $this->assertSame($page['template_key'], $landing->template_key);
            $this->assertSame(7, count($landing->sections));
            $this->assertNotEmpty($landing->pricingPlans);
            $this->assertNotEmpty($landing->projects);
            $this->assertNotEmpty($landing->landing_content);
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

    public function test_seeded_landing_pages_render_their_template_data(): void
    {
        $this->get('/san-xuat-phim-doanh-nghiep')
            ->assertOk()
            ->assertSee('Sản xuất phim doanh nghiệp')
            ->assertSee('Thu thập dữ liệu khách hàng')
            ->assertSee('Chụp ảnh doanh nghiệp , TVC cơ bản')
            ->assertSee('13.000.000đ');

        $this->get('/quay-chup-live-su-kien-chuong-trinh')
            ->assertOk()
            ->assertSee('Quay, chụp, live sự kiện, chương trình')
            ->assertSee('HÌNH ẢNH HẬU TRƯỜNG');

        foreach (LandingSeedCatalog::pages() as $slug => $page) {
            if (in_array($slug, ['phong-su-cuoi', 'to-chuc-su-kien-tron-goi'], true)) {
                continue;
            }

            $this->get('/'.$slug)
                ->assertOk()
                ->assertSee('tht-landing', false)
                ->assertDontSee('Landing page chưa có template')
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('thtmedia.com.vn');
        }
    }

    public function test_published_source_catalog_uses_native_service_pages(): void
    {
        $this->seed(SourceServiceSeeder::class);

        $sourceSlugs = [
            'to-chuc-su-kien-tron-goi',
            'cham-soc-fanpage-chuyen-nghiep',
            'san-xuat-phim-doanh-nghiep',
            'dang-bai-quang-cao-tren-fanpage-quevo-media',
            'quay-chup-le-mung-tho',
            'quay-chup-hop-lop',
            'phong-su-cuoi',
            'chay-quang-cao-facebook',
            'thiet-ke-website',
            'khoa-dao-tao-nhiep-anh',
            'khoa-dao-tao-chay-quang-cao',
            'quay-chup-live-su-kien-chuong-trinh',
            'quay-chup-anh-du-lich',
            'bang-gia-in-anh',
        ];

        foreach ($sourceSlugs as $slug) {
            $this->assertNotNull(Service::query()->whereHas('slugs', fn ($query) => $query->where('slug', $slug))->first(), $slug);
            $this->assertFalse(LandingPage::query()->whereHas('slugs', fn ($query) => $query->where('slug', $slug))->exists(), $slug);
        }

        $film = $this->service('san-xuat-phim-doanh-nghiep');
        $this->assertSame(7, count($film->process_items));
        $this->assertSame(5, count($film->benefit_items));
        $this->assertSame(3, $film->pricingCatalog->packages()->count());
        $this->assertSame(7, count($film->faq_items));
        $this->assertSame(19, count($film->backstage_gallery));
        $this->assertSame(5, count($film->reference_videos));
        $this->assertSame('service', DB::table('slugs')->where('slug', 'san-xuat-phim-doanh-nghiep')->value('sluggable_type'));
        $this->assertStringNotContainsString('thtmedia.com.vn', (string) $film->body);
    }

    public function test_super_admin_can_manage_the_database_template_catalog(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $template = LandingTemplate::query()->where('key', LandingRegistry::EVENT_MEDIA)->firstOrFail();

        $this->actingAs($user)
            ->get('/admin/landing-templates')
            ->assertOk()
            ->assertSee('Kho template landing')
            ->assertSee('Landing / Quay phim, chụp ảnh sự kiện');

        $this->actingAs($user)
            ->get('/admin/landing-templates/'.$template->id.'/edit')
            ->assertOk()
            ->assertSee('Schema quản trị của template')
            ->assertSee('Bảng màu mặc định')
            ->assertSee('CSS riêng');
    }

    private function landing(string $slug): LandingPage
    {
        return LandingPage::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }

    private function service(string $slug): Service
    {
        return Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }
}
