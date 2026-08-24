<?php

namespace App\Observers;

use App\Support\Seo\ContentSeoFallbacks;
use Illuminate\Database\Eloquent\Model;

class ContentSeoFallbackObserver
{
    public function saving(Model $model): void
    {
        if (blank($model->getAttribute('seo_title'))) {
            $title = ContentSeoFallbacks::title(
                $model->getAttribute('title'),
                $model->getAttribute('name'),
            );

            if ($title !== null) {
                $model->setAttribute('seo_title', $title);
            }
        }

        if (blank($model->getAttribute('seo_description'))) {
            $description = ContentSeoFallbacks::description(
                $model->getAttribute('excerpt'),
                $model->getAttribute('description'),
            );

            if ($description !== null) {
                $model->setAttribute('seo_description', $description);
            }
        }
    }
}
