<?php

namespace App\Models;

use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class BniChapter extends Model implements HasMedia
{
    use HasBniMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(BniArticle::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(BniInvitation::class);
    }

    protected function bniImageCollections(): array
    {
        return ['logo', 'cover'];
    }

    protected function bniVideoCollections(): array
    {
        return ['video'];
    }
}
