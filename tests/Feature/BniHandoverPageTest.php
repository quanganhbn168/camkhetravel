<?php

namespace Tests\Feature;

use Tests\TestCase;

class BniHandoverPageTest extends TestCase
{
    public function test_the_bni_handover_page_uses_the_fixed_bni_identity(): void
    {
        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('bni-handover-menu-link')
            ->assertSee('bni-logo-red.svg')
            ->assertSee('LỄ CHUYỂN GIAO')
            ->assertSee('bni-handover-header-brand')
            ->assertSee('bni-handover-header-chapters')
            ->assertSee('border-t border-white/15 bg-midnight')
            ->assertDontSee('bni-handover-chapter-nav')
            ->assertSee('KINHBAC')
            ->assertSee('KBG')
            ->assertSee('IMPACT')
            ->assertSee('FAMOUS');
    }
}
