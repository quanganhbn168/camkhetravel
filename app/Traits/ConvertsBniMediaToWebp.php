<?php

namespace App\Traits;

use App\Support\Bni\BniMediaOptimizer;

trait ConvertsBniMediaToWebp
{
    protected static function bootConvertsBniMediaToWebp(): void
    {
        static::saving(function (self $model): void {
            $optimizer = app(BniMediaOptimizer::class);

            foreach ($model->bniWebpMediaAttributes() as $attribute) {
                if (! $model->isDirty($attribute) || blank($model->getAttribute($attribute))) {
                    continue;
                }

                $model->setAttribute(
                    $attribute,
                    $optimizer->optimize((int) $model->getAttribute($attribute)),
                );
            }
        });
    }

    /** @return array<int, string> */
    abstract protected function bniWebpMediaAttributes(): array;
}
