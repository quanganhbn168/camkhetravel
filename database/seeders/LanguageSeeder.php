<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

final class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->updateOrCreate(['code' => 'vi'], [
            'name' => 'Vietnamese',
            'native_name' => 'Tiếng Việt',
            'og_locale' => 'vi_VN',
            'is_active' => true,
            'is_default' => true,
            'is_indexable' => true,
            'sort_order' => 1,
        ]);
    }
}
