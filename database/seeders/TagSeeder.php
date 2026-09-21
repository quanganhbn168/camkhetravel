<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

final class TagSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Giải pháp', 'Công trình', 'Thiết bị'] as $name) {
            Tag::query()->updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
