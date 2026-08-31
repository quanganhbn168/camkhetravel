<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BniInvitation extends Model
{
    public const RSVP_PENDING = 'pending';

    public const RSVP_ATTENDING = 'attending';

    public const RSVP_DECLINED = 'declined';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $invitation): void {
            if (blank($invitation->invitation_code)) {
                $invitation->invitation_code = self::newInvitationCode();
            }

            if (blank($invitation->slug)) {
                $invitation->slug = $invitation->invitation_code;
            }
        });
    }

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'invitation_code';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BniChapter::class, 'bni_chapter_id');
    }

    public function displayGuestName(string $fallback = 'Anh/Chị chủ doanh nghiệp'): string
    {
        return filled($this->guest_name) ? $this->guest_name : $fallback;
    }

    public function publicUrl(): string
    {
        return route('bni.invitations.show', [
            'invitation' => $this,
        ]);
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

    public static function newInvitationCode(): string
    {
        do {
            $code = 'tm-'.Str::lower(Str::random(10));
        } while (self::query()->where('invitation_code', $code)->exists());

        return $code;
    }
}
