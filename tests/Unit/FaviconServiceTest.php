<?php

namespace Tests\Unit;

use App\Support\Branding\FaviconService;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class FaviconServiceTest extends TestCase
{
    private string $originalPublicPath;

    private string $testPublicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPublicPath = public_path();
        $this->testPublicPath = storage_path('framework/testing/favicon-'.Str::uuid());

        File::ensureDirectoryExists($this->testPublicPath);
        app()->usePublicPath($this->testPublicPath);
    }

    protected function tearDown(): void
    {
        app()->usePublicPath($this->originalPublicPath);
        File::deleteDirectory($this->testPublicPath);

        parent::tearDown();
    }

    public function test_it_overwrites_the_fixed_public_favicon_pack_from_the_uploaded_source(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('favicon-source.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        ));

        File::put(public_path('favicon.ico'), 'old-fixed-favicon');

        $media = new Media;
        $media->forceFill([
            'id' => 123,
            'disk' => 'public',
            'path' => 'favicon-source.png',
            'ext' => 'png',
        ]);

        app(FaviconService::class)->sync($media);

        foreach ([
            'favicon.ico',
            'favicon.svg',
            'favicon-16x16.png',
            'favicon-32x32.png',
            'favicon-48x48.png',
            'favicon-96x96.png',
            'apple-touch-icon.png',
            'android-chrome-192x192.png',
            'android-chrome-512x512.png',
            'site.webmanifest',
        ] as $filename) {
            $this->assertFileExists(public_path($filename));
        }

        $this->assertNotSame('old-fixed-favicon', File::get(public_path('favicon.ico')));
        $this->assertDirectoryDoesNotExist(public_path('favicon-assets'));
    }

    public function test_it_only_emits_versioned_urls_for_the_fixed_public_files(): void
    {
        File::put(public_path('favicon.ico'), 'fixed');

        $links = app(FaviconService::class)->links();

        $this->assertNotEmpty($links);
        $this->assertStringContainsString('/favicon.ico?v=', app(FaviconService::class)->primaryUrl());
        $this->assertSame(public_path('favicon.ico'), app(FaviconService::class)->primaryPath());

        foreach ($links as $link) {
            $this->assertStringNotContainsString('favicon-assets', $link['href']);
            $this->assertStringNotContainsString('/storage/', $link['href']);
        }
    }

    public function test_it_checks_all_destinations_before_overwriting_any_favicon(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('favicon-source.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        ));
        File::put(public_path('favicon-16x16.png'), 'original favicon');
        // A directory at a later destination is unwritable as a file on every OS.
        File::makeDirectory(public_path('site.webmanifest'));
        $media = new Media;
        $media->forceFill(['disk' => 'public', 'path' => 'favicon-source.png', 'ext' => 'png']);

        try {
            app(FaviconService::class)->sync($media);
            $this->fail('Expected the invalid destination to stop synchronization.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('site.webmanifest', $exception->getMessage());
            $this->assertSame('original favicon', File::get(public_path('favicon-16x16.png')));
        }
    }
}
