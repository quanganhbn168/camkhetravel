<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class ThtMediaHeaderMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::query()->firstOrNew(
            ['location' => 'header'],
            [
                'name' => 'THT Media Header',
                'location' => 'header',
                'is_active' => true,
            ],
        );
        $menu->fill([
            'name' => 'THT Media Header',
            'location' => 'header',
            'is_active' => true,
        ])->save();

        $items = [
            ['label' => 'Trang chủ', 'type' => 'route', 'route' => 'home', 'position' => 10, 'css_classes' => 'header-home'],
            ['label' => 'Giới thiệu', 'type' => 'route', 'route' => 'about', 'position' => 20],
            ['label' => 'TVC - Sản xuất phim doanh nghiệp', 'type' => 'service', 'slug' => 'san-xuat-phim-doanh-nghiep', 'position' => 30],
            ['label' => 'Quay chụp sự kiện', 'type' => 'service', 'slug' => 'quay-chup-live-su-kien-chuong-trinh', 'position' => 40],
            ['label' => 'Thiết kế Website', 'type' => 'service', 'slug' => 'thiet-ke-website', 'position' => 60],
            ['label' => 'Dịch vụ', 'type' => 'route', 'route' => 'services.index', 'position' => 70, 'css_classes' => 'header-services'],
            ['label' => 'Bảng giá', 'type' => 'route', 'route' => 'pricing.index', 'position' => 80],
            ['label' => 'Dự án', 'type' => 'route', 'route' => 'projects.index', 'position' => 90],
            ['label' => 'Tin tức', 'type' => 'route', 'route' => 'posts.index', 'position' => 100],
        ];

        $labels = collect($items)->pluck('label');
        $menu->items()->whereNotIn('label', $labels)->delete();

        foreach ($items as $item) {
            $linkedSourceId = match ($item['type']) {
                'service' => Service::query()
                    ->whereHas('slugs', fn ($query) => $query->where('slug', $item['slug']))
                    ->value('id'),
                default => null,
            };

            MenuItem::query()->updateOrCreate(
                [
                    'menu_id' => $menu->getKey(),
                    'label' => $item['label'],
                ],
                [
                    'parent_id' => null,
                    'linked_source_id' => $linkedSourceId,
                    'linked_source_type' => 'native_'.$item['type'],
                    'label' => $item['label'],
                    'url' => $item['route'] ?? null,
                    'target' => null,
                    'css_classes' => $item['css_classes'] ?? null,
                    'position' => $item['position'],
                ],
            );
        }
    }
}
