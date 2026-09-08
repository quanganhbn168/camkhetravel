<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Settings\WebsiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsNavigationSeeder extends Seeder
{
    public function run(): void
    {
        $website = app(WebsiteSettings::class);
        $menu = Menu::query()->where('is_active', true)->find($website->header_menu_id)
            ?? Menu::query()->where('is_active', true)->where('location', 'header')->first();
        if (! $menu) {
            return;
        }

        DB::transaction(function () use ($menu): void {
            $legacy = $menu->items()->where('linked_source_type', 'native_route')->where('url', 'events.index')->first();
            $parent = MenuItem::query()->firstOrCreate([
                'menu_id' => $menu->id,
                'linked_source_type' => 'native_route',
                'url' => 'bni.handover',
            ], [
                'parent_id' => null,
                'label' => 'Lễ chuyển giao BNI',
                'target' => '_self',
                'position' => (int) $menu->topLevelItems()->max('position') + 1,
            ]);

            if ($legacy) {
                $parent->update(['parent_id' => null, 'position' => $legacy->position]);
                $legacy->update(['parent_id' => $parent->id, 'url' => 'bni.events.index', 'position' => 1]);
                $menu->items()->where('parent_id', $legacy->id)->update(['parent_id' => $parent->id]);
            }

            $parent->children()->where('linked_source_type', 'native_route')
                ->whereIn('url', ['bni.events.index', 'bni.pickleball'])->delete();
        });
    }
}
