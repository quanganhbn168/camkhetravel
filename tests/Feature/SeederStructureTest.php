<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Tests\TestCase;

class SeederStructureTest extends TestCase
{
    public function test_database_seeder_orchestrates_named_domain_seeders(): void
    {
        $source = file_get_contents(database_path('seeders/DatabaseSeeder.php'));

        $this->assertStringNotContainsString('FoundationContentSeeder', $source);

        foreach ([
            'AdminUserSeeder',
            'LanguageSeeder',
            'WebsiteSettingsSeeder',
            'MenuSeeder',
            'MediaSeeder',
            'ServiceSeeder',
            'ProjectSeeder',
            'ProductSeeder',
            'PostSeeder',
        ] as $seeder) {
            $this->assertFileExists(database_path('seeders/'.$seeder.'.php'));
            $this->assertStringContainsString($seeder.'::class', $source);
        }
    }

    public function test_core_content_models_have_factories_for_isolated_tests(): void
    {
        foreach ([Service::class, Project::class, Product::class, Post::class] as $model) {
            $record = $model::factory()->make();

            $this->assertNotEmpty($record->title);
        }
    }

    public function test_seed_baseline_is_not_branded_to_a_specific_project(): void
    {
        $seedSource = collect([
            ...glob(database_path('seeders/*.php')),
            ...glob(database_path('settings/*.php')),
        ])->map(fn (string $path): string => file_get_contents($path))->implode("\n");

        $this->assertStringNotContainsStringIgnoringCase('dvtec', $seedSource);
    }
}
