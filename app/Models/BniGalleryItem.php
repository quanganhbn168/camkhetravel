<?php

namespace App\Models;

use App\Traits\ConvertsBniMediaToWebp;
use App\Traits\HasComments;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BniGalleryItem extends Model
{
    use ConvertsBniMediaToWebp;
    use HasComments;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_CHAPTER = 'chapter';

    public const SOURCE_GUEST = 'guest';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if (! $item->isDirty('status')) {
                return;
            }

            $item->approved_at = $item->status === self::STATUS_APPROVED
                ? ($item->approved_at ?: now())
                : null;
        });

        static::deleting(function (self $item): void {
            $item->comments()->delete();
        });

        static::deleted(function (self $item): void {
            if ($item->source !== self::SOURCE_GUEST) {
                return;
            }

            $media = $item->media;

            if (! $media || ! str_starts_with((string) $media->path, 'media/bni/community/')) {
                return;
            }

            if (! self::query()->where('media_id', $media->id)->exists()) {
                $media->delete();
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(BniActivity::class, 'bni_activity_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BniChapter::class, 'bni_chapter_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_APPROVED)
            ->where('is_active', true);
    }

    public function scopeForActivity(Builder $query, ?int $activityId): Builder
    {
        return $activityId ? $query->where('bni_activity_id', $activityId) : $query;
    }

    public function galleryGroupKey(): string
    {
        return $this->activity
            ? 'activity-'.$this->activity->getKey()
            : 'event-'.($this->event?->getKey() ?: 'bni');
    }

    public function galleryGroupLabel(): string
    {
        return $this->activity?->title ?: $this->event?->title ?: 'Sự kiện BNI';
    }

    public function publicSourceLabel(): string
    {
        return $this->source === self::SOURCE_GUEST
            ? 'Khách tham dự'
            : 'Ban tổ chức BNI';
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt',
            self::STATUS_REJECTED => 'Từ chối',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_ADMIN => 'Quản trị hệ thống',
            self::SOURCE_CHAPTER => 'Quản trị Chapter',
            self::SOURCE_GUEST => 'Khách tham dự',
        ];
    }

    protected function bniWebpMediaAttributes(): array
    {
        return ['media_id'];
    }
}
