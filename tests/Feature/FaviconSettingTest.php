<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Models\User;
use App\Support\Branding\FaviconService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FaviconSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_settings_expose_the_favicon_upload_field(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);

        Livewire::test(ManageSettings::class)->assertFormFieldExists('favicon_media_id');
    }

    public function test_frontend_head_links_only_existing_favicon_files(): void
    {
        $links = app(FaviconService::class)->links();

        $this->assertNotEmpty($links);

        foreach ($links as $link) {
            $this->assertFileExists(public_path(basename((string) parse_url($link['href'], PHP_URL_PATH))));
        }

        $this->get('/')->assertOk()->assertSee('favicon.ico', false);
    }
}
