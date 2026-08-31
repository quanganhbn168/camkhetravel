<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\LandingTemplate;
use App\Support\Landing\LandingTemplateRegistry;
use Illuminate\Database\Seeder;

class LandingTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LandingTemplateRegistry::seedDefinitions() as $key => $definition) {
            $template = LandingTemplate::query()->firstOrNew(['key' => $key]);

            if (! $template->exists) {
                $template->fill([
                    'name' => $definition['label'],
                    'description' => $definition['description'],
                    'use_case' => $definition['use_case'],
                    'palette' => $definition['palette'],
                    'settings_schema' => $definition['settings_schema'],
                    'default_settings' => $definition['settings'],
                    'default_sections' => $definition['default_sections'],
                    'is_active' => $definition['is_active'],
                    'sort_order' => $definition['sort_order'],
                ]);
            }

            // Keep source-backed Landing07 contracts current without
            // overwriting admin-owned labels, activation, or palette values.
            if (str_starts_with((string) ($definition['source_name'] ?? ''), 'Landing07')) {
                $template->fill([
                    'settings_schema' => $definition['settings_schema'],
                    'default_settings' => $definition['settings'],
                    'default_sections' => $definition['default_sections'],
                ]);
            }

            $template->fill([
                'view_name' => $definition['view'],
                'css_class' => $definition['css_class'],
                'css_source' => $definition['css_source'],
                'source_name' => $definition['source_name'],
                'source_path' => $definition['source_path'],
                'icon' => $definition['icon'],
            ])->save();

            LandingPage::query()
                ->where('template_key', $key)
                ->whereNull('landing_template_id')
                ->update(['landing_template_id' => $template->id]);
        }
    }
}
