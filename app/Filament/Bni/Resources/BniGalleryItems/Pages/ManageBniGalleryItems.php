<?php

namespace App\Filament\Bni\Resources\BniGalleryItems\Pages;

use App\Filament\Bni\Resources\BniGalleryItems\BniGalleryItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniGalleryItems extends ManageRecords
{
    protected static string $resource = BniGalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm hình ảnh')];
    }
}
