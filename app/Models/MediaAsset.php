<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'source_snapshot' => 'array',
            'import_locked_fields' => 'array',
            'published_at' => 'datetime',
            'localized_at' => 'datetime',
        ];
    }

    public function getPublicUrlAttribute(): ?string
    {
        if ($this->disk && $this->file_path) {
            if ($this->disk === 'public') {
                return rtrim((string) config('app.url'), '/')
                    .'/storage/'.ltrim((string) $this->file_path, '/');
            }

            return Storage::disk($this->disk)->url($this->file_path);
        }

        return $this->source_url;
    }
}
