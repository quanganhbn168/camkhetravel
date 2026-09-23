<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (app(SettingsCacheFactory::class)->all() as $settingsCache) {
            $settingsCache->clear();
        }

        $this->call([
            ShieldSeeder::class,
            AdminUserSeeder::class,
            MediaSeeder::class,
            WebsiteSettingsSeeder::class,
            HomepageSettingsSeeder::class,
            CompanySettingsSeeder::class,
            AboutSettingsSeeder::class,
            SystemPageSettingsSeeder::class,
            MenuSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            TestimonialSeeder::class,
            HeroSlideSeeder::class,
        ]);
    }
}
