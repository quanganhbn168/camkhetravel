<?php

namespace App\Filament\Bni\Resources\BniChapters\Pages;

use App\Filament\Bni\Resources\BniChapters\BniChapterResource;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniChapters extends ManageRecords
{
    protected static string $resource = BniChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm chapter')->visible(fn (): bool => BniPanelAccess::canManageEverything())];
    }
}
