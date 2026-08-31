<?php

namespace Tests\Feature;

use Tests\TestCase;

class BniPwaTest extends TestCase
{
    public function test_bni_pages_emit_the_bni_manifest_and_mobile_shell(): void
    {
        $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('data-bni-page="true"', false)
            ->assertSee('bni-manifest')
            ->assertSee('bni-mobile-bar')
            ->assertSee('bni-pwa-install');
    }

    public function test_non_bni_pages_do_not_emit_the_bni_pwa_shell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-bni-page="false"', false)
            ->assertDontSee('bni.webmanifest')
            ->assertDontSee('bni-mobile-bar');
    }

    public function test_bni_manifest_and_install_assets_are_valid(): void
    {
        $manifest = $this->get(route('bni.manifest', ['locale' => 'vi']))
            ->assertOk()
            ->json();

        $this->assertSame('/le-chuyen-giao', $manifest['id']);
        $this->assertSame('/le-chuyen-giao', $manifest['scope']);
        $this->assertSame('BNI', $manifest['short_name']);
        $this->assertFileExists(public_path('bni-icon-192x192.png'));
        $this->assertFileExists(public_path('bni-icon-512x512.png'));
        $this->assertStringContainsString("const CACHE_NAME = 'tht-bni-v2';", file_get_contents(public_path('bni-sw.js')));
    }

    public function test_the_bni_manifest_always_uses_the_single_vietnamese_scope(): void
    {
        $this->get(route('bni.manifest', ['locale' => 'en']))
            ->assertOk()
            ->assertJsonPath('id', '/le-chuyen-giao')
            ->assertJsonPath('start_url', '/le-chuyen-giao')
            ->assertJsonPath('scope', '/le-chuyen-giao');
    }
}
