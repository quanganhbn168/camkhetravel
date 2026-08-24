<?php

namespace App\Models;

use App\Support\Localization\LanguageCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

class Language extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_indexable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Language $language): void {
            if ($language->is_default) {
                $defaults = static::query()->where('is_default', true);

                if ($language->exists) {
                    $defaults->whereKeyNot($language->getKey());
                }

                $defaults->update(['is_default' => false]);
                $language->is_active = true;
                $language->is_indexable = true;
            }

            if (! $language->is_active) {
                $language->is_indexable = false;
            }

            if ($language->is_indexable) {
                $language->is_active = true;
            }
        });

        static::saved(function (): void {
            app(LanguageCatalog::class)->forget();
            static::clearRouteCacheIfNeeded();
        });
        static::deleted(function (): void {
            app(LanguageCatalog::class)->forget();
            static::clearRouteCacheIfNeeded();
        });

        static::deleting(function (Language $language): void {
            if ($language->is_default) {
                throw ValidationException::withMessages([
                    'language' => 'Không thể xóa ngôn ngữ mặc định.',
                ]);
            }
        });
    }

    private static function clearRouteCacheIfNeeded(): void
    {
        if (app()->routesAreCached()) {
            Artisan::call('route:clear');
        }
    }
}
