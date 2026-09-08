<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\LandingTemplate;
use App\Models\Slug;
use App\Support\Landing\LandingRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BrandingLandingSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $key = LandingRegistry::BRANDING;
            $definition = LandingRegistry::templateDefinitions()[$key];
            $template = LandingTemplate::query()->firstOrCreate(['key' => $key], [
                'name' => $definition['label'],
                'description' => $definition['description'],
                'view_name' => $definition['view'],
                'css_class' => $definition['css_class'],
                'css_source' => $definition['css_source'],
                'source_name' => $definition['source_name'],
                'source_path' => $definition['source_path'],
                'icon' => $definition['icon'],
                'use_case' => $definition['use_case'],
                'palette' => $definition['palette'],
                'settings_schema' => $definition['settings_schema'],
                'default_settings' => $definition['settings'],
                'default_sections' => [],
                'is_active' => true,
                'sort_order' => $definition['sort_order'],
            ]);

            // Initial content only: later CMS edits are never overwritten by seeding.
            if (LandingPage::query()->where('template_key', $key)->exists()) {
                return;
            }

            $slug = LandingRegistry::find($key)['slug'];
            if (Slug::query()->where('slug', $slug)->exists()) {
                throw new \RuntimeException('The branding landing URL is already owned by another resource.');
            }

            $content = File::json(database_path('seeders/data/landing/'.$key.'.json'));
            LandingPage::query()->create([
                'title' => 'Bộ nhận diện thương hiệu doanh nghiệp',
                'slug' => $slug,
                'excerpt' => $content['seo']['description'],
                'status' => 'published',
                'published_at' => now(),
                'template_key' => $key,
                'landing_template_id' => $template->id,
                'landing_content' => $content,
                'theme_settings' => $definition['palette'],
                'sections' => [],
                'show_header' => true,
                'show_footer' => true,
                'tracking_enabled' => true,
            ]);
        });
    }
}
