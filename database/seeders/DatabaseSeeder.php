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
            ShieldSeeder::class,
            AdminUserSeeder::class,
            WebsiteSettingsSeeder::class,
            MenuSeeder::class,
            MediaSeeder::class,
            ServiceCategorySeeder::class,
            ProjectCategorySeeder::class,
            ProductCategorySeeder::class,
            PostCategorySeeder::class,
            TagSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            ProductSeeder::class,
            PostSeeder::class,
            HeroSlideSeeder::class,
        ]);
    }
}
