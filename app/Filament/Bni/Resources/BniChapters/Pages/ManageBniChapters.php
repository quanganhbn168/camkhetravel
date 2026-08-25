<?php

namespace App\Filament\Bni\Resources\BniChapters\Pages;

use App\Filament\Bni\Resources\BniChapters\BniChapterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniChapters extends ManageRecords
{
    protected static string $resource = BniChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm chapter')];
    }
}
