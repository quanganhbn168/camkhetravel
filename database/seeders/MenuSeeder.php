<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header'],
            ['name' => 'Menu chính', 'is_active' => true],
        );

        $menu->items()->delete();

        foreach ([
            ['Trang chủ', 'home'], ['Giới thiệu', 'about'], ['Dịch vụ', 'services.index'],
            ['Dự án', 'projects.index'], ['Sản phẩm', 'products.index'], ['Kiến thức', 'posts.index'], ['Liên hệ', 'contact'],
        ] as $index => [$label, $url]) {
            MenuItem::query()->create([
                'menu_id' => $menu->getKey(), 'label' => $label, 'url' => $url,
                'linked_source_type' => 'native_route', 'position' => ($index + 1) * 10, 'target' => '_self',
            ]);
        }
    }
}
