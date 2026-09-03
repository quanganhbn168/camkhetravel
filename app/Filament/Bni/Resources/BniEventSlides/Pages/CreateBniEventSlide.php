<?php

namespace App\Filament\Bni\Resources\BniEventSlides\Pages;

use App\Filament\Bni\Resources\BniEventSlides\BniEventSlideResource;
use App\Filament\Bni\Resources\Pages\CreateBniRecord;

class CreateBniEventSlide extends CreateBniRecord
{
    protected static string $resource = BniEventSlideResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return BniEventSlideResource::prepareCreateData($data);
    }
}
