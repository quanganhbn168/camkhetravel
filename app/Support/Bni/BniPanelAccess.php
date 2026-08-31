<?php

namespace App\Support\Bni;

use App\Models\BniActivity;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class BniPanelAccess
{
    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function canManageEverything(): bool
    {
        return self::user()?->hasAnyRole(['super_admin', 'bni_admin']) ?? false;
    }

    public static function isChapterManager(): bool
    {
        return self::user()?->hasRole('bni_chapter_manager') ?? false;
    }

    public static function canManageChapterContent(): bool
    {
        return self::canManageEverything() || (self::isChapterManager() && self::chapterId() !== null);
    }

    public static function chapterId(): ?int
    {
        return self::user()?->bni_chapter_id;
    }

    /** @param Builder<Model> $query */
    public static function scopeChapter(Builder $query, string $column = 'bni_chapter_id'): Builder
    {
        if (self::isChapterManager() && ! self::canManageEverything()) {
            $query->where($column, self::chapterId() ?? 0);
        }

        return $query;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function forceChapter(array $data): array
    {
        if (self::isChapterManager() && ! self::canManageEverything()) {
            $data['bni_chapter_id'] = self::chapterId();
        }

        return $data;
    }

    /** @param Builder<Model> $query */
    public static function scopePublishedEvents(Builder $query): Builder
    {
        if (self::isChapterManager() && ! self::canManageEverything()) {
            $query->where('status', 'published');
        }

        return $query;
    }

    /** @param Builder<Model> $query */
    public static function scopeInvitationEvents(Builder $query): Builder
    {
        if (self::isChapterManager() && ! self::canManageEverything()) {
            $query->whereKey(self::chapterEventId() ?? 0);
        }

        return $query;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function prepareArticleData(array $data): array
    {
        $data = self::forceChapter($data);
        self::ensurePublishedEvent($data['bni_event_id'] ?? null);

        return $data;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function prepareInvitationData(array $data): array
    {
        $data = self::forceChapter($data);

        if (self::isChapterManager() && ! self::canManageEverything()) {
            $eventId = self::chapterEventId();

            if ($eventId === null) {
                throw ValidationException::withMessages([
                    'bni_event_id' => 'Chapter chưa được gắn với sự kiện để tạo thư mời.',
                ]);
            }

            $data['bni_event_id'] = $eventId;
        }

        return $data;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function prepareRegistrationData(array $data): array
    {
        $data = self::forceChapter($data);

        if (! self::isChapterManager() || self::canManageEverything()) {
            return $data;
        }

        $invitationId = $data['bni_invitation_id'] ?? null;

        if (filled($invitationId)) {
            $invitation = BniInvitation::query()
                ->where('bni_chapter_id', self::chapterId() ?? 0)
                ->find($invitationId);

            if (! $invitation) {
                throw ValidationException::withMessages([
                    'bni_invitation_id' => 'Thư mời không thuộc chapter đang quản lý.',
                ]);
            }

            $data['bni_event_id'] = $invitation->bni_event_id;
        }

        self::ensurePublishedEvent($data['bni_event_id'] ?? null);

        return $data;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function prepareGalleryData(array $data): array
    {
        $data = self::forceChapter($data);
        $activity = BniActivity::query()->find($data['bni_activity_id'] ?? null);

        if (! $activity) {
            throw ValidationException::withMessages([
                'bni_activity_id' => 'Vui lòng chọn hoạt động / album ảnh.',
            ]);
        }

        if (filled($data['bni_event_id'] ?? null) && (int) $data['bni_event_id'] !== (int) $activity->bni_event_id) {
            throw ValidationException::withMessages([
                'bni_activity_id' => 'Hoạt động đã chọn không thuộc sự kiện này.',
            ]);
        }

        $data['bni_event_id'] = $activity->bni_event_id;
        $data['group'] = $activity->type;
        self::ensurePublishedEvent($data['bni_event_id'] ?? null);

        return $data;
    }

    private static function chapterEventId(): ?int
    {
        $eventId = BniChapter::query()->whereKey(self::chapterId() ?? 0)->value('bni_event_id');

        return $eventId === null ? null : (int) $eventId;
    }

    private static function ensurePublishedEvent(mixed $eventId): void
    {
        if (! self::isChapterManager() || self::canManageEverything() || blank($eventId)) {
            return;
        }

        if (! BniEvent::query()->published()->whereKey($eventId)->exists()) {
            throw ValidationException::withMessages([
                'bni_event_id' => 'Chapter chỉ được chọn sự kiện đã xuất bản.',
            ]);
        }
    }
}
