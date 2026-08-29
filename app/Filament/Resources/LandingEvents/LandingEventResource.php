<?php

namespace App\Filament\Resources\LandingEvents;

use App\Filament\Resources\LandingEvents\Pages\ListLandingEvents;
use App\Filament\Resources\LandingEvents\Tables\LandingEventsTable;
use App\Models\LandingEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LandingEventResource extends Resource
{
    protected static ?string $model = LandingEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Tracking landing';

    protected static ?string $modelLabel = 'sự kiện landing';

    protected static ?string $pluralModelLabel = 'Tracking landing';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Khách hàng & liên hệ';
    }

    public static function table(Table $table): Table
    {
        return LandingEventsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) LandingEvent::query()->whereDate('occurred_at', today())->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingEvents::route('/'),
        ];
    }
}
