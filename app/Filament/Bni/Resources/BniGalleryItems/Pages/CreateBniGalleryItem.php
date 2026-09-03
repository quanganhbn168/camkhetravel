<?php

namespace App\Filament\Bni\Resources\BniGalleryItems\Pages;

use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use App\Filament\Bni\Resources\Pages\CreateBniRecord;

class CreateBniGalleryItem extends CreateBniRecord
{
    protected static string $resource = BniGalleryItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BniGalleryItemResource::prepareCreateData($data);
    }
}
