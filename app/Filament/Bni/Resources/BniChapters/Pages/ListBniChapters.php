<?php

namespace App\Filament\Bni\Resources\BniChapters\Pages;

use App\Filament\Bni\Resources\BniChapters\BniChapterResource;
use App\Filament\Bni\Resources\Pages\ListBniRecords;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;

class ListBniChapters extends ListBniRecords
{
    protected static string $resource = BniChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm chapter')->visible(fn (): bool => BniPanelAccess::canManageEverything())];
    }
}
