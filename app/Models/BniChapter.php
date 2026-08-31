<?php

namespace App\Models;

use App\Traits\ConvertsBniMediaToWebp;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BniChapter extends Model
{
    use ConvertsBniMediaToWebp;

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

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function videoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'video_media_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(BniArticle::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(BniInvitation::class);
    }

    protected function bniWebpMediaAttributes(): array
    {
        return ['logo_media_id', 'cover_media_id'];
    }
}
