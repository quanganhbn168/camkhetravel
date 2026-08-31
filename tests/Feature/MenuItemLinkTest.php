<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\MenuItem;
use App\Support\Localization\LocalizedUrl;
use Tests\TestCase;

class MenuItemLinkTest extends TestCase
{
    public function test_a_native_route_menu_item_uses_its_saved_route_name(): void
    {
        $item = new MenuItem([
            'linked_source_type' => 'native_route',
            'url' => 'contact',
        ]);

        $this->assertSame(LocalizedUrl::route('contact'), $item->link);
    }

    public function test_an_empty_native_route_does_not_fall_back_to_the_homepage(): void
    {
        $item = new MenuItem([
            'linked_source_type' => 'native_route',
            'url' => null,
        ]);

        $this->assertSame('#', $item->link);
    }

    public function test_a_native_landing_page_menu_item_uses_its_landing_page_slug(): void
    {
        $landingPage = LandingPage::query()->published()->firstOrFail();
        $item = new MenuItem([
            'linked_source_type' => 'native_landing_page',
            'linked_source_id' => $landingPage->id,
        ]);

        $this->assertSame(LocalizedUrl::landingPage($landingPage), $item->link);
    }

}
