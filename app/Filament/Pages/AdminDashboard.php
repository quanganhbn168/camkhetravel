<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AdminDashboard extends LandingTrackingDashboard
{
    protected static ?string $slug = 'dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Tổng quan';

    protected static ?string $title = 'Tổng quan quản trị';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -2;

    protected static bool $shouldRegisterNavigation = true;

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }
}
