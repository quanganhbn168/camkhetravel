<?php

namespace App\Support\Pages;

use App\Settings\SystemPageSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use LogicException;

final class SystemPageProfileResolver
{
    private const KEYS = ['home', 'about', 'services', 'solutions', 'contact', 'projects'];

    private const REQUIRED_FIELDS = ['title', 'seo_title', 'seo_description', 'og_image_media_id'];

    public function __construct(private readonly SystemPageSettings $settings) {}

    /** @return array<string, mixed> */
    public function require(string $key): array
    {
        if (! in_array($key, self::KEYS, true)) {
            throw new LogicException("Hồ sơ trang hệ thống [{$key}] không tồn tại.");
        }

        $profile = $this->settings->{$key};

        foreach (self::REQUIRED_FIELDS as $field) {
            if (blank($profile[$field] ?? null)) {
                throw new LogicException("Thiếu cấu hình bắt buộc system_pages.{$key}.{$field}.");
            }
        }

        $ogImage = Media::query()->find((int) $profile['og_image_media_id']);

        if (! $ogImage || ! str_starts_with((string) $ogImage->type, 'image/')) {
            throw new LogicException("Ảnh system_pages.{$key}.og_image_media_id không hợp lệ.");
        }

        $bannerMediaId = filled($profile['banner_media_id'] ?? null)
            ? (int) $profile['banner_media_id']
            : null;
        $banner = $bannerMediaId !== null
            ? Media::query()->find($bannerMediaId)
            : null;

        if ($bannerMediaId !== null && (! $banner || ! str_starts_with((string) $banner->type, 'image/'))) {
            throw new LogicException("Ảnh system_pages.{$key}.banner_media_id không hợp lệ.");
        }

        return [
            ...$profile,
            'key' => $key,
            'og_image_url' => MediaUrl::versioned($ogImage),
            'banner_url' => MediaUrl::versioned($banner),
        ];
    }
}
