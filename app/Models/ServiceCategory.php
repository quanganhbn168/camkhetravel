<?php

namespace App\Models;

use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasSeoImage;
    use HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_featured' => 'boolean', 'is_home' => 'boolean'];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }

}
