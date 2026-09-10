<?php

namespace App\Filament\Resources\LandingEvents\Pages;

use App\Filament\Resources\LandingEvents\LandingEventResource;
use App\Filament\Widgets\LandingTrackingComparison;
use App\Services\LandingTracking\LandingTrackingComparisonService;
use Filament\Resources\Pages\ListRecords;

class ListLandingEvents extends ListRecords
{
    protected static string $resource = LandingEventResource::class;

    /** @return array<int, class-string> */
    protected function getHeaderWidgets(): array
    {
        return app(LandingTrackingComparisonService::class)->hasNewEvents()
            ? [LandingTrackingComparison::class]
            : [];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
