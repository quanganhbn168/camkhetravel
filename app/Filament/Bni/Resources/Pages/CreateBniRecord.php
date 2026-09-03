<?php

namespace App\Filament\Bni\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

abstract class CreateBniRecord extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
