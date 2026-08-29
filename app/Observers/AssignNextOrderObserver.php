<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class AssignNextOrderObserver
{
    public function creating(Model $model): void
    {
        $column = 'sort_order';

        if ($model->getAttribute($column) !== null) {
            return;
        }

        $query = $model->newQuery();

        $model->setAttribute($column, ((int) $query->max($column)) + 1);
    }
}
