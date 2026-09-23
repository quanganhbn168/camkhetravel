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
            ['Trang chủ', '/', 'native_page'],
            ['Dịch vụ', '/dich-vu', 'native_page'],
            ['Đội xe', '/#doi-xe', 'native_page'],
            ['Đối tác', '/#doi-tac', 'native_page'],
            ['Xe cưới', '/#xe-cuoi', 'native_page'],
            ['Tin tức', '/blog', 'native_page'],
            ['Liên hệ', '/lien-he', 'native_page'],
        ] as $index => [$label, $url, $linkType]) {
            MenuItem::query()->create([
                'menu_id' => $menu->getKey(), 'label' => $label, 'url' => $url,
                'linked_source_type' => $linkType, 'position' => ($index + 1) * 10, 'target' => '_self',
            ]);
        }

        $footerMenu = Menu::query()->updateOrCreate(
            ['location' => 'footer'],
            ['name' => 'Liên kết chân trang', 'is_active' => true],
        );

        $footerMenu->items()->delete();

        foreach ([
            ['Trang chủ', '/'],
            ['Giới thiệu', '/gioi-thieu'],
            ['Dịch vụ', '/dich-vu'],
            ['Tin tức', '/blog'],
            ['Liên hệ', '/lien-he'],
        ] as $index => [$label, $url]) {
            MenuItem::query()->create([
                'menu_id' => $footerMenu->getKey(), 'label' => $label, 'url' => $url,
                'linked_source_type' => 'native_page', 'position' => ($index + 1) * 10, 'target' => '_self',
            ]);
        }
    }
}
