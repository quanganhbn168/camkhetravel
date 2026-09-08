<?php

namespace App\Support\Frontend;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class VideoMediaLibrary
{
    public const EXTENSIONS = ['mp4', 'webm', 'mov', 'm4v', 'avi', 'mkv', 'mpeg', 'mpg', 'ogv'];

    public static function query(): Builder
    {
        return Media::query()->where('type', 'like', 'video/%')->whereIn('ext', self::EXTENSIONS);
    }

    public static function options(?string $search = null): array
    {
        return self::query()
            ->when(filled($search), fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('title', 'like', '%'.$search.'%')->orWhere('name', 'like', '%'.$search.'%')))
            ->latest('id')->limit(50)->get()
            ->mapWithKeys(fn (Media $media) => [$media->id => self::label($media)])->all();
    }

    public static function label(Media $media): string
    {
        return ($media->title ?: $media->name).' · '.Str::upper($media->ext);
    }
}
