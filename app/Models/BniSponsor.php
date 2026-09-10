<?php

namespace App\Models;

use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class BniSponsor extends Model implements HasMedia
{
    use HasBniMedia;

    public const TIER_DIAMOND = 'diamond';

    public const TIER_GOLD = 'gold';

    public const TIER_SILVER = 'silver';

    public const TIER_CO_SPONSOR = 'co_sponsor';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return array<string, string> */
    public static function tierOptions(): array
    {
        return [
            self::TIER_DIAMOND => 'Nhà tài trợ Kim cương',
            self::TIER_GOLD => 'Nhà tài trợ Vàng',
            self::TIER_SILVER => 'Nhà tài trợ Bạc',
            self::TIER_CO_SPONSOR => 'Đồng tài trợ',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function publicUrl(): ?string
    {
        $url = trim((string) $this->url);

        if ($url === '') {
            return null;
        }

        if (Str::startsWith($url, '/') && ! Str::startsWith($url, '//')) {
            return $url;
        }

        $scheme = Str::lower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL)
            ? $url
            : null;
    }

    protected function bniImageCollections(): array
    {
        return ['logo'];
    }
}
