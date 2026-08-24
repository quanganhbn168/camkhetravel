<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Support\Seo\SeoMaterializer;
use Filament\Resources\Pages\CreateRecord;

class CreateContentItem extends CreateRecord
{
    protected static string $resource = ContentItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seo_source'] = 'manual';

        return $data;
    }

    protected function afterCreate(): void
    {
        app(SeoMaterializer::class)->materializeItem($this->record->refresh());
    }
}
