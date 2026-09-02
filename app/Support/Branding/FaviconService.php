<?php

namespace App\Support\Branding;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickPixel;
use RuntimeException;

final class FaviconService
{
    /** @var array<string, int> */
    private const PNG_FILES = [
        'favicon-16x16.png' => 16,
        'favicon-32x32.png' => 32,
        'favicon-48x48.png' => 48,
        'favicon-96x96.png' => 96,
        'apple-touch-icon.png' => 180,
        'android-chrome-192x192.png' => 192,
        'android-chrome-512x512.png' => 512,
    ];

    /** @return array<int, array{rel: string, type: string, href: string, sizes?: string, color?: string}> */
    public function links(): array
    {
        $asset = fn (string $filename): string => $this->versionedAsset($filename);

        return [
            [
                'rel' => 'icon',
                'type' => 'image/svg+xml',
                'href' => $asset('favicon.svg'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '16x16',
                'href' => $asset('favicon-16x16.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '32x32',
                'href' => $asset('favicon-32x32.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '48x48',
                'href' => $asset('favicon-48x48.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '96x96',
                'href' => $asset('favicon-96x96.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/x-icon',
                'href' => $asset('favicon.ico'),
            ],
            [
                'rel' => 'shortcut icon',
                'type' => 'image/x-icon',
                'href' => $asset('favicon.ico'),
            ],
            [
                'rel' => 'apple-touch-icon',
                'type' => 'image/png',
                'sizes' => '180x180',
                'href' => $asset('apple-touch-icon.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '192x192',
                'href' => $asset('android-chrome-192x192.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '512x512',
                'href' => $asset('android-chrome-512x512.png'),
            ],
            [
                'rel' => 'mask-icon',
                'type' => 'image/svg+xml',
                'color' => '#ee6b2d',
                'href' => $asset('favicon.svg'),
            ],
            [
                'rel' => 'manifest',
                'type' => 'application/manifest+json',
                'href' => $asset('site.webmanifest'),
            ],
        ];
    }

    public function primaryUrl(): string
    {
        return $this->versionedAsset('favicon.ico');
    }

    public function primaryPath(): string
    {
        return public_path('favicon.ico');
    }

    public function primaryMimeType(): string
    {
        return 'image/x-icon';
    }

    /**
     * Turn the selected Curator upload into the fixed favicon files in public.
     * Curator remains the source file; every existing static favicon is overwritten.
     */
    public function sync(?Media $customMedia): void
    {
        if (! $customMedia) {
            return;
        }

        $storage = Storage::disk($customMedia->disk);

        if (! $storage->exists($customMedia->path)) {
            throw new RuntimeException('Không tìm thấy file favicon đã chọn trong kho media.');
        }

        $source = (string) $storage->get($customMedia->path);
        $directory = public_path();

        $this->ensureDirectory($directory);

        $pngs = [];
        $files = [];

        foreach (self::PNG_FILES as $filename => $size) {
            $pngs[$size] = $this->renderPng($source, strtolower($customMedia->ext), $size);
            $files[$filename] = $pngs[$size];
        }

        $files['favicon.svg'] = $this->svgFromPng($pngs[512]);
        $files['favicon.ico'] = $this->icoFromPngs([
            16 => $pngs[16],
            32 => $pngs[32],
            48 => $pngs[48],
        ]);
        $files['site.webmanifest'] = $this->manifest();

        foreach ($files as $filename => $contents) {
            $this->writeFile($directory.DIRECTORY_SEPARATOR.$filename, $contents);
        }
    }

    private function renderPng(string $source, string $extension, int $size): string
    {
        if ($extension === 'svg') {
            return $this->renderSvgPng($source, $size);
        }

        $image = Glide::getServer()->getApi()->getImageManager()->read($source);

        return $image
            ->orient()
            ->contain($size, $size, 'transparent', 'center')
            ->toPng()
            ->toString();
    }

    private function renderSvgPng(string $source, int $size): string
    {
        if (! class_exists(Imagick::class)) {
            throw new RuntimeException('Để xử lý favicon SVG cần bật PHP Imagick, hoặc anh upload bản PNG vuông.');
        }

        $image = new Imagick;
        $canvas = new Imagick;

        try {
            $image->setBackgroundColor(new ImagickPixel('transparent'));
            $image->readImageBlob(Curator::sanitizeSvg($source));
            $image->setIteratorIndex(0);
            $image->setImageFormat('png');
            $image->thumbnailImage($size, $size, true);
            $image->setImagePage(0, 0, 0, 0);

            $canvas->newImage($size, $size, new ImagickPixel('transparent'), 'png');
            $x = (int) floor(($size - $image->getImageWidth()) / 2);
            $y = (int) floor(($size - $image->getImageHeight()) / 2);
            $canvas->compositeImage($image, Imagick::COMPOSITE_OVER, $x, $y);

            return $canvas->getImageBlob();
        } finally {
            $image->clear();
            $image->destroy();
            $canvas->clear();
            $canvas->destroy();
        }
    }

    private function svgFromPng(string $png): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">'
            .'<image href="data:image/png;base64,'.base64_encode($png).'" width="512" height="512" preserveAspectRatio="none"/>'
            .'</svg>';
    }

    /** @param array<int, string> $pngs */
    private function icoFromPngs(array $pngs): string
    {
        $header = pack('vvv', 0, 1, count($pngs));
        $entries = '';
        $images = '';
        $offset = 6 + (16 * count($pngs));

        foreach ($pngs as $size => $png) {
            $entries .= pack(
                'CCCCvvVV',
                $size >= 256 ? 0 : $size,
                $size >= 256 ? 0 : $size,
                0,
                0,
                1,
                32,
                strlen($png),
                $offset,
            );
            $images .= $png;
            $offset += strlen($png);
        }

        return $header.$entries.$images;
    }

    private function manifest(): string
    {
        return (string) json_encode([
            'name' => 'THT Media',
            'short_name' => 'THT Media',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#10233e',
            'theme_color' => '#ee6b2d',
            'icons' => [
                [
                    'src' => 'android-chrome-192x192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => 'android-chrome-512x512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
    }

    private function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục favicon tĩnh trong public.');
        }
    }

    private function writeFile(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents, LOCK_EX) === false) {
            throw new RuntimeException("Không thể ghi file favicon tĩnh: {$path}");
        }

        clearstatcache(true, $path);
    }

    private function versionedAsset(string $filename): string
    {
        $path = public_path($filename);
        $version = is_file($path) ? hash_file('sha256', $path) : false;

        return asset($filename).($version === false ? '' : '?v='.substr($version, 0, 12));
    }
}
