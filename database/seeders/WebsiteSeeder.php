<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** @deprecated Use the focused seeders from DatabaseSeeder. */
final class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([MenuSeeder::class]);
    }
}
