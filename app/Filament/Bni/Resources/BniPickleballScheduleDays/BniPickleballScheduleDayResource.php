<?php

namespace App\Filament\Bni\Resources\BniPickleballScheduleDays;

use App\Filament\Bni\Resources\BniPickleballScheduleDays\Pages\CreateBniPickleballScheduleDay;
use App\Filament\Bni\Resources\BniPickleballScheduleDays\Pages\EditBniPickleballScheduleDay;
use App\Filament\Bni\Resources\BniPickleballScheduleDays\Pages\ListBniPickleballScheduleDays;
use App\Filament\Bni\Resources\BniScheduleDays\BniScheduleDayResource;

class BniPickleballScheduleDayResource extends BniScheduleDayResource
{
    protected static ?string $navigationLabel = 'Lịch thi đấu';

    protected static ?string $modelLabel = 'ngày thi đấu';

    protected static ?string $pluralModelLabel = 'Lịch thi đấu';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Pickleball';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBniPickleballScheduleDays::route('/'),
            'create' => CreateBniPickleballScheduleDay::route('/create'),
            'edit' => EditBniPickleballScheduleDay::route('/{record}/edit'),
        ];
    }

    protected static function eventType(): string
    {
        return 'pickleball';
    }
}
