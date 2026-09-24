<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MediaSeeder::class,
            WebsiteSettingsSeeder::class,
            HomepageSettingsSeeder::class,
            CompanySettingsSeeder::class,
            AboutSettingsSeeder::class,
            SystemPageSettingsSeeder::class,
            MenuSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            PostSeeder::class,
            TestimonialSeeder::class,
            HeroSlideSeeder::class,
        ]);
    }
}
