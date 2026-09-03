<?php

namespace App\Filament\Bni\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

abstract class ListBniRecords extends ListRecords
{
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm mới'),
        ];
    }
}
