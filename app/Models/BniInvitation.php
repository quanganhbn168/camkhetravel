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

    protected $hidden = [
        'access_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invitation): void {
            if (blank($invitation->access_token)) {
                $invitation->access_token = self::generateAccessToken();
            }
        });
    }

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

    public function displayGuestName(string $fallback = 'Anh/Chị chủ doanh nghiệp'): string
    {
        return filled($this->guest_name) ? $this->guest_name : $fallback;
    }

    public function hasValidAccessToken(?string $token): bool
    {
        return filled($this->access_token)
            && filled($token)
            && hash_equals((string) $this->access_token, (string) $token);
    }

    public function regenerateAccessToken(): void
    {
        $this->forceFill(['access_token' => self::generateAccessToken()])->save();
    }

    public function publicUrl(): string
    {
        return route('bni.invitations.show', [
            'invitation' => $this,
            'accessToken' => $this->access_token,
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

    private static function generateAccessToken(): string
    {
        do {
            $token = Str::random(48);
        } while (self::query()->where('access_token', $token)->exists());

        return $token;
    }
}
