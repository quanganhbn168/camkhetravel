<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class SolutionModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([SolutionCategorySeeder::class, SolutionPermissionsSeeder::class]);
    }
}
