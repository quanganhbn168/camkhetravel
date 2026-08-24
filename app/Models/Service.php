<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @deprecated All public service pages are now managed as Landing records.
 * Kept so historical migrations can still resolve their original model class.
 */
class Service extends Landing
{
}
