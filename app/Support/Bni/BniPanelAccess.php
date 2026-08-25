<?php

namespace App\Support\Bni;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
}
