<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BniContact extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (BniContact $contact): void {
            if (! $contact->is_primary) {
                return;
            }

            static::query()
                ->whereKeyNot($contact->getKey())
                ->when(
                    $contact->bni_chapter_id,
                    fn (Builder $query, int $chapterId): Builder => $query->where('bni_chapter_id', $chapterId),
                    fn (Builder $query): Builder => $query
                        ->whereNull('bni_chapter_id')
                        ->when(
                            $contact->bni_event_id,
                            fn (Builder $query, int $eventId): Builder => $query->where('bni_event_id', $eventId),
                            fn (Builder $query): Builder => $query->whereNull('bni_event_id'),
                        ),
                )
                ->update(['is_primary' => false]);
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BniChapter::class, 'bni_chapter_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->whereNull('bni_chapter_id');
    }

    public function scopeForChapters(Builder $query): Builder
    {
        return $query->whereNotNull('bni_chapter_id');
    }
}
