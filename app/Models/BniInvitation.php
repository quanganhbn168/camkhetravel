<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BniInvitation extends Model
{
    public const RSVP_PENDING = 'pending';

    public const RSVP_ATTENDING = 'attending';

    public const RSVP_DECLINED = 'declined';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BniChapter::class, 'bni_chapter_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(BniRegistration::class);
    }

    public static function rsvpOptions(): array
    {
        return [
            self::RSVP_PENDING => 'Chờ phản hồi',
            self::RSVP_ATTENDING => 'Tham dự',
            self::RSVP_DECLINED => 'Không tham dự',
        ];
    }
}
