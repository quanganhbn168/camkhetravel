<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemovedSubsystemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_pricing_and_tracking_subsystems_are_not_registered(): void
    {
        $this->assertFalse(Route::has('pricing.index'));
        $this->assertFalse(Route::has('landing-pages.comments.store'));

        foreach ([
            'Http/Controllers/Frontend/LandingController.php',
            'Http/Controllers/Frontend/PricingController.php',
            'Models/LandingPage.php',
            'Models/PricingPlan.php',
            'Models/ServicePricing.php',
            'Models/PricingPackage.php',
            'Models/PricingPackageItem.php',
            'Providers/TrackingServiceProvider.php',
            'Settings/TrackingSettings.php',
            'Support/Tracking/TrackingScripts.php',
        ] as $removedFile) {
            $this->assertFileDoesNotExist(app_path($removedFile), $removedFile.' must not remain in the application.');
        }

        $this->assertNotContains('App\\Settings\\TrackingSettings', config('settings.settings'));

        foreach ([
            'landing_pages',
            'landing_page_post',
            'landing_page_project',
            'landing_page_service',
            'landing_page_service_category',
            'pricing_plans',
            'service_pricings',
            'pricing_packages',
            'pricing_package_items',
            'languages',
            'about_departments',
            'about_team_members',
            'redirects',
            'post_post_category',
            'project_service',
            'post_project',
            'hero_slide_translations',
        ] as $removedTable) {
            $this->assertFalse(Schema::hasTable($removedTable), $removedTable.' must not remain in the database.');
        }
    }

    public function test_schema_baseline_does_not_create_then_remove_deleted_subsystems(): void
    {
        $migrationNames = collect(glob(database_path('migrations/*.php')))
            ->map(fn (string $path): string => basename($path));

        $this->assertFalse($migrationNames->contains(fn (string $name): bool => str_contains($name, 'remove_')));
        $this->assertFalse($migrationNames->contains(fn (string $name): bool => str_contains($name, 'landing_page')));
        $this->assertFalse($migrationNames->contains(fn (string $name): bool => str_contains($name, 'pricing_')));
    }
}
