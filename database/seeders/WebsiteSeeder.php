<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/** Seeds only the safe baseline required to open a fresh DVTEC website. */
final class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->updateOrCreate(
            ['code' => 'vi'],
            [
                'name' => 'Vietnamese',
                'native_name' => 'Tiếng Việt',
                'og_locale' => 'vi_VN',
                'is_active' => true,
                'is_default' => true,
                'is_indexable' => true,
                'sort_order' => 1,
            ],
        );

        if (app()->environment(['local', 'testing'])) {
            $user = User::query()->firstOrCreate(
                ['email' => 'admin@dvtec.test'],
                ['name' => 'DVTEC Admin', 'password' => Hash::make('password')],
            );

            if (method_exists($user, 'assignRole')) {
                $user->assignRole(Role::findOrCreate('super_admin', 'web'));
            }
        }

        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header'],
            ['name' => 'Menu chính', 'is_active' => true],
        );

        $items = [
            ['label' => 'Trang chủ', 'url' => 'home', 'position' => 10],
            ['label' => 'Giới thiệu', 'url' => 'about', 'position' => 20],
            ['label' => 'Dịch vụ', 'url' => 'services.index', 'position' => 30],
            ['label' => 'Dự án', 'url' => 'projects.index', 'position' => 40],
            ['label' => 'Bài viết', 'url' => 'posts.index', 'position' => 50],
            ['label' => 'Liên hệ', 'url' => 'contact', 'position' => 60],
        ];

        $menu->items()->delete();

        foreach ($items as $item) {
            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'label' => $item['label'],
                'url' => $item['url'],
                'linked_source_type' => 'native_route',
                'position' => $item['position'],
                'target' => '_self',
            ]);
        }
    }
}
