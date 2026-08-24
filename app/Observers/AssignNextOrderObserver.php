<?php

namespace App\Observers;

use App\Models\ContentItem;
use Illuminate\Database\Eloquent\Model;

class AssignNextOrderObserver
{
    public function creating(Model $model): void
    {
        $column = $model instanceof ContentItem ? 'menu_order' : 'sort_order';

        if ($model->getAttribute($column) !== null) {
            return;
        }

        $query = $model->newQuery();

        if ($model instanceof ContentItem) {
            $query->where('parent_id', $model->getAttribute('parent_id'));
        }

        $model->setAttribute($column, ((int) $query->max($column)) + 1);
    }
}
