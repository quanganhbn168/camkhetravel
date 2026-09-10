<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LandingEvents\LandingEventResource;
use App\Services\LandingTracking\LandingTrackingComparisonService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class LandingTrackingComparison extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Tracking landing mới';

    protected ?string $description = 'Chỉ hiển thị khi hôm nay có dữ liệu tracking mới.';

    protected function getStats(): array
    {
        $summary = app(LandingTrackingComparisonService::class)->summary();

        return collect(['today', 'week', 'month'])
            ->map(function (string $period) use ($summary): Stat {
                $data = $summary[$period];

                return Stat::make($data['label'], $data['current'])
                    ->description($this->comparisonDescription($data))
                    ->descriptionIcon($this->comparisonIcon($data['delta']))
                    ->color($this->comparisonColor($data['delta']))
                    ->url(LandingEventResource::getUrl('index'));
            })
            ->all();
    }

    /**
     * @param  array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float}  $data
     */
    private function comparisonDescription(array $data): string
    {
        if ($data['previous'] === 0) {
            return $data['current'] > 0
                ? 'Mới phát sinh · so với '.$data['comparison_label']
                : 'Chưa phát sinh · so với '.$data['comparison_label'];
        }

        $delta = ($data['delta'] > 0 ? '+' : '').number_format($data['delta']);
        $percent = $data['percent'] ?? 0;
        $percentText = ($percent > 0 ? '+' : '').number_format($percent, 1, ',', '.').'%';

        return $delta.' ('.$percentText.') so với '.$data['comparison_label'];
    }

    private function comparisonIcon(int $delta): Heroicon
    {
        return match (true) {
            $delta > 0 => Heroicon::OutlinedArrowTrendingUp,
            $delta < 0 => Heroicon::OutlinedArrowTrendingDown,
            default => Heroicon::OutlinedMinus,
        };
    }

    private function comparisonColor(int $delta): string
    {
        return match (true) {
            $delta > 0 => 'success',
            $delta < 0 => 'danger',
            default => 'gray',
        };
    }
}
