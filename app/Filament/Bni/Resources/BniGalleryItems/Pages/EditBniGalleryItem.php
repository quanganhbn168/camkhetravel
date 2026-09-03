<?php

namespace App\Filament\Bni\Resources\BniGalleryItems\Pages;

use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use App\Filament\Bni\Resources\Pages\EditBniRecord;

class EditBniGalleryItem extends EditBniRecord
{
    protected static string $resource = BniGalleryItemResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return BniGalleryItemResource::prepareUpdateData($data);
    }
}
