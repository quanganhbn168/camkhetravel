<?php

namespace App\Filament\Resources\Page\Pages;

use App\Filament\Resources\Page\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->hidden(fn (): bool => $this->getRecord()->isSystem())];
    }
}
