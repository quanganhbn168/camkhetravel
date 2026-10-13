<?php

namespace App\Models;

use App\Support\Media\MediaUrl;
use App\Traits\HasCuratorImage;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * Mọi trang của website. `system`: trang cố định có sẵn route (trang chủ, dịch vụ, ...);
 * `custom`: trang tự tạo (Chính sách bảo mật, ...) hiển thị tại /{slug}.
 */
class Page extends Model
{
    use HasCuratorImage;
    use HasSeoImage;
    use HasSlug;

    public const TYPE_SYSTEM = 'system';

    public const TYPE_CUSTOM = 'custom';

    /** @var array<string, array{label: string, route: string}> */
    public const SYSTEM_PAGES = [
        'home' => ['label' => 'Trang chủ', 'route' => 'home'],
        'about' => ['label' => 'Giới thiệu', 'route' => 'about'],
        'services' => ['label' => 'Dịch vụ', 'route' => 'services.index'],
        'solutions' => ['label' => 'Giải pháp', 'route' => 'solutions.index'],
        'products' => ['label' => 'Sản phẩm', 'route' => 'products.index'],
        'posts' => ['label' => 'Tin tức', 'route' => 'posts.index'],
        'contact' => ['label' => 'Liên hệ', 'route' => 'contact'],
    ];

    protected $attributes = ['type' => self::TYPE_CUSTOM];

    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (Page $page): void {
            if ($page->isSystem()) {
                throw ValidationException::withMessages(['page' => 'Không thể xóa trang hệ thống.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /** Trang được định danh bằng id (trang hệ thống không có slug). */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->newQuery()->where($field ?: 'id', $value)->first();
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CUSTOM);
    }

    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SYSTEM);
    }

    /** Trang tự tạo đang xuất bản và đã tới giờ đăng. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->custom()
            ->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function isSystem(): bool
    {
        return $this->type === self::TYPE_SYSTEM;
    }

    /** Trang hệ thống có route cố định nên không dùng bảng slug. */
    public function shouldSyncSlug(): bool
    {
        return ! $this->isSystem();
    }

    public function getUrlAttribute(): string
    {
        return $this->isSystem()
            ? route(self::SYSTEM_PAGES[$this->system_key]['route'])
            : route('slug.show', ['slug' => $this->slug]);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return MediaUrl::versioned($this->bannerMedia);
    }
}
