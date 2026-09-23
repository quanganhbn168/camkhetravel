<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class SolutionSeeder extends Seeder
{
    public function run(): void
    {
        // Retain compatibility with existing seed commands, without republishing old demos.
        $this->call(SolutionCategorySeeder::class);
    }
}
