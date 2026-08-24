<?php

namespace Tests\Unit;

use App\Services\WordPress\WordPressMediaUrlMapper;
use Illuminate\Support\Str;
use Tests\TestCase;

class WordPressMediaUrlMapperTest extends TestCase
{
    private string $sourceRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sourceRoot = sys_get_temp_dir().DIRECTORY_SEPARATOR.'thtmedia-mapper-'.Str::uuid();

        mkdir(
            $this->sourceRoot.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'
            .DIRECTORY_SEPARATOR.'2026'.DIRECTORY_SEPARATOR.'08',
            0777,
            true,
        );

        config()->set([
            'app.url' => 'https://thtmedia-laravel.test',
            'wordpress.source_url' => 'https://thtmedia.com.vn',
            'wordpress.local_path' => $this->sourceRoot,
            'wordpress.media_directory' => 'media/wordpress',
            'wordpress.fallback_media_path' => 'fallback/default.webp',
        ]);

        mkdir(
            $this->sourceRoot.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'
            .DIRECTORY_SEPARATOR.'fallback',
            0777,
            true,
        );
        file_put_contents(
            $this->sourceRoot.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'
            .DIRECTORY_SEPARATOR.'fallback'.DIRECTORY_SEPARATOR.'default.webp',
            'fallback image',
        );
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->sourceRoot);

        parent::tearDown();
    }

    public function test_it_maps_a_wordpress_upload_to_local_storage_and_keeps_its_query_string(): void
    {
        file_put_contents(
            $this->sourceRoot.DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'
            .DIRECTORY_SEPARATOR.'2026'.DIRECTORY_SEPARATOR.'08'.DIRECTORY_SEPARATOR.'anh-bai-viet.jpg',
            'test image',
        );

        $stats = [];
        $localized = app(WordPressMediaUrlMapper::class)->localizeUrl(
            'https://thtmedia.com.vn/wp-content/uploads/2026/08/anh-bai-viet.jpg?resize=1200%2C630&quality=90',
            $stats,
        );

        $this->assertSame(
            '/storage/media/wordpress/2026/08/anh-bai-viet.jpg?resize=1200%2C630&quality=90',
            $localized,
        );
        $this->assertSame(1, $stats['urls_rewritten']);
        $this->assertArrayNotHasKey('missing_source_references', $stats);
    }

    public function test_it_uses_the_configured_local_fallback_when_the_source_file_is_missing(): void
    {
        $stats = [];
        $localized = app(WordPressMediaUrlMapper::class)->localizeUrl(
            'https://thtmedia.com.vn/wp-content/uploads/2026/08/khong-ton-tai.jpg?fit=cover',
            $stats,
        );

        $this->assertSame(
            '/storage/media/wordpress/fallback/default.webp?fit=cover',
            $localized,
        );
        $this->assertSame(1, $stats['urls_rewritten']);
        $this->assertSame(1, $stats['missing_source_references']);
    }

    public function test_the_public_absolute_url_follows_the_current_app_url(): void
    {
        $mapper = app(WordPressMediaUrlMapper::class);
        $path = '/storage/media/wordpress/2026/08/anh-bai-viet.jpg';

        $this->assertSame(
            'https://thtmedia-laravel.test'.$path,
            $mapper->absoluteUrl($path),
        );

        config()->set('app.url', 'https://media.example.vn/subdir');

        $this->assertSame(
            'https://media.example.vn/subdir'.$path,
            $mapper->absoluteUrl($path),
        );
    }

    public function test_it_rejects_traversal_and_absolute_source_paths(): void
    {
        $mapper = app(WordPressMediaUrlMapper::class);

        $this->assertNull($mapper->sanitizeRelativePath('../secret.env'));
        $this->assertNull($mapper->sanitizeRelativePath('2026/08/../../secret.env'));
        $this->assertNull($mapper->sanitizeRelativePath('C:/Windows/system.ini'));
        $this->assertNull($mapper->relativePathFromSourcePath('wp-content/uploads/../wp-config.php'));
        $this->assertSame(
            '2026/08/anh-hop-le.jpg',
            $mapper->sanitizeRelativePath('2026/08/anh-hop-le.jpg'),
        );
        $this->assertFalse($mapper->sourceFileExists('../secret.env'));
    }

    public function test_it_keeps_the_source_url_when_no_safe_fallback_file_exists(): void
    {
        config()->set('wordpress.fallback_media_path', 'fallback/missing.webp');
        $stats = [];
        $source = 'https://thtmedia.com.vn/wp-content/uploads/2026/08/khong-ton-tai.jpg';

        $this->assertSame(
            $source,
            app(WordPressMediaUrlMapper::class)->localizeUrl($source, $stats),
        );
        $this->assertSame(1, $stats['unresolved_source_references']);
    }

    public function test_it_makes_local_media_in_content_follow_the_current_app_url(): void
    {
        $mapper = app(WordPressMediaUrlMapper::class);
        $html = '<img src="/storage/media/wordpress/test.jpg">'
            .'<img src="https://external.test/image.jpg">';

        $localized = $mapper->absoluteLocalMediaUrls($html);

        $this->assertStringContainsString(
            'src="https://thtmedia-laravel.test/storage/media/wordpress/test.jpg"',
            $localized,
        );
        $this->assertStringContainsString('src="https://external.test/image.jpg"', $localized);
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory.DIRECTORY_SEPARATOR.$entry;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
