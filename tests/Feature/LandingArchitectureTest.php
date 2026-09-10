<?php

namespace Tests\Feature;

use App\Support\Landing\LandingRegistry;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LandingArchitectureTest extends TestCase
{
    public function test_all_landing_pages_use_native_blade_and_page_specific_assets(): void
    {
        $vite = file_get_contents(base_path('vite.config.js'));
        $shell = file_get_contents(resource_path('views/frontend/landing/shell.blade.php'));

        $this->assertStringContainsString('<x-landing.header', $shell);
        $this->assertStringContainsString('<x-landing.footer', $shell);
        $this->assertStringNotContainsString('source-runtime', $vite);
        $this->assertStringNotContainsString('bootstrap.min.css', $vite);
        $this->assertStringNotContainsString('bootstrap.bundle.min.js', $vite);

        foreach (LandingRegistry::pages() as $templateKey => $page) {
            $assets = LandingRegistry::viteAssets($templateKey);
            $view = resource_path('views/'.str_replace('.', '/', $page['view']).'.blade.php');
            $seed = database_path('seeders/data/landing/'.$templateKey.'.json');

            $this->assertFileExists($seed, $templateKey);
            $this->assertSame($templateKey, File::json($seed)['template_key'] ?? null);
            $this->assertSame($templateKey, LandingRegistry::templateForSlug($page['slug']));
            foreach ($page['aliases'] ?? [] as $alias) {
                $this->assertSame($templateKey, LandingRegistry::templateForSlug($alias));
            }
            $this->assertFileExists($view, $templateKey);
            $this->assertFileExists(base_path($page['css']), $templateKey);
            if (isset($page['js'])) {
                $this->assertFileExists(base_path($page['js']), $templateKey);
            }
            $this->assertContains('resources/css/app.css', $assets, $templateKey);
            $this->assertContains($page['css'], $assets, $templateKey);
            $this->assertContains('resources/js/app.js', $assets, $templateKey);
        }
    }

    public function test_landing_runtime_has_no_wordpress_import_or_php_include_layer(): void
    {
        $this->assertFileDoesNotExist(app_path('Console/Commands/ImportLandingWordPressSourceCommand.php'));
        $this->assertFileDoesNotExist(app_path('Support/Landing/LandingSourceCatalog.php'));
        $this->assertFileDoesNotExist(app_path('Support/Landing/LandingSourcePresenter.php'));
        $this->assertDirectoryDoesNotExist(resource_path('views/frontend/landing/source'));
        $this->assertDirectoryDoesNotExist(resource_path('css/landing-pages'));
        $this->assertDirectoryDoesNotExist(resource_path('data/landing-pages'));
        $needles = [
            'LandingSource',
            'source-faithful',
            'require_once',
            'get_template_part',
            "defined('ABSPATH')",
            'esc_html(',
            'esc_attr(',
            'esc_url(',
            'wp_kses',
            'wp_json_encode',
            'data-bs-',
            'bootstrap.min.css',
            'bootstrap.bundle.min.js',
        ];

        foreach (File::allFiles(resource_path('views/frontend/landing')) as $file) {
            $contents = $file->getContents();
            foreach ($needles as $needle) {
                $this->assertStringNotContainsString($needle, $contents, $file->getRelativePathname());
            }
            $this->assertDoesNotMatchRegularExpression('/<\?php\s+(?:include|require)/', $contents, $file->getRelativePathname());
        }
    }

    public function test_production_communications_slug_renders_the_shared_landing_shell(): void
    {
        $this->get('/giai-phap-truyen-thong-doanh-nghiep')
            ->assertOk()
            ->assertSee('GIẢI PHÁP TRUYỀN THÔNG DOANH NGHIỆP')
            ->assertSee('tht-landing-header', false)
            ->assertSee('tht-landing-footer', false)
            ->assertSee('tht-landing-hero', false)
            ->assertDontSee('source-faithful', false)
            ->assertDontSee('Landing page chưa có template')
            ->assertDontSee('data-bs-', false);
    }

    public function test_film_and_media_landings_expose_stable_success_classes(): void
    {
        $this->get('/dich-vu-san-xuat-phim-doanh-nghiep')
            ->assertOk()
            ->assertSee('film-form-success', false);

        $this->get('/quay-phim-chup-anh-su-kien-tai-bac-ninh')
            ->assertOk()
            ->assertSee('media-form-success', false);
    }

    public function test_production_profile_slug_renders_its_own_design_and_remains_scrollable(): void
    {
        $this->get('/thiet-ke-profile-doanh-nghiep-ho-so-nang-luc')
            ->assertOk()
            ->assertSee('THIẾT KẾ PROFILE – HỒ SƠ NĂNG LỰC DOANH NGHIỆP')
            ->assertSee('ƯU ĐÃI THÁNG')
            ->assertSee('hero-thiet-ke-profile.jpg', false)
            ->assertSee('tht-landing-profile-page', false)
            ->assertSee('data-landing-modal-open', false)
            ->assertSee('overflow-x-clip', false)
            ->assertDontSee('source-faithful', false)
            ->assertDontSee('data-bs-', false);
    }

    public function test_academy_variants_still_use_the_shared_header_and_footer_components(): void
    {
        $this->get('/hoc-vien-nhiep-anh-va-sang-tao-noi-dung')
            ->assertOk()
            ->assertSee('tht-landing-header', false)
            ->assertSee('tht-landing-footer', false)
            ->assertDontSee('academy-header', false)
            ->assertDontSee('academy-footer', false);

        $this->get('/khoa-hoc-nhiep-anh')
            ->assertOk()
            ->assertSee('tht-landing-header', false)
            ->assertSee('tht-landing-footer', false)
            ->assertDontSee('academy-v2-header', false)
            ->assertDontSee('academy-v2-footer', false);
    }
}
