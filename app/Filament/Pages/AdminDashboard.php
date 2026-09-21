<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\WebsiteStatsOverview;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AdminDashboard extends Page
{
    protected static ?string $slug = 'dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Tổng quan';

    protected static ?string $title = 'Tổng quan website';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -2;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.admin-dashboard';

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    /** @return array<int, class-string> */
    protected function getHeaderWidgets(): array
    {
        return [WebsiteStatsOverview::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

}
