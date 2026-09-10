<?php

namespace App\Filament\Widgets;

use App\Services\LandingTracking\LandingTrackingComparisonService;
use Filament\Widgets\Widget;

final class LandingTrackingComparison extends Widget
{
    protected static ?int $sort = -1;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.landing-tracking-comparison';

    protected int|string|array $columnSpan = 'full';

    /** @return array{summary: array<string, mixed>} */
    protected function getViewData(): array
    {
        return [
            'summary' => app(LandingTrackingComparisonService::class)->summary(),
        ];
    }
}
