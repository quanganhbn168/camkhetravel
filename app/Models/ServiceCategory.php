<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }

    public function landingPages(): BelongsToMany
    {
        return $this->belongsToMany(LandingPage::class, 'landing_page_service_category')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
