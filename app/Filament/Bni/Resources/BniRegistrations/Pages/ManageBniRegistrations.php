<?php

namespace App\Filament\Bni\Resources\BniRegistrations\Pages;

use App\Filament\Bni\Resources\BniRegistrations\BniRegistrationResource;
use App\Support\Bni\BniPanelAccess;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBniRegistrations extends ManageRecords
{
    protected static string $resource = BniRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm đăng ký')
                ->mutateDataUsing(fn (array $data): array => BniPanelAccess::forceChapter($data)),
        ];
    }
}
