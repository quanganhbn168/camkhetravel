<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_a_native_route_menu_item_uses_its_saved_route_name(): void
    {
        $item = new MenuItem([
            'linked_source_type' => 'native_route',
            'url' => 'contact',
        ]);

        $this->assertSame(route('contact'), $item->link);
    }

    public function test_an_empty_native_route_does_not_fall_back_to_the_homepage(): void
    {
        $item = new MenuItem([
            'linked_source_type' => 'native_route',
            'url' => null,
        ]);

        $this->assertSame('#', $item->link);
    }

}
