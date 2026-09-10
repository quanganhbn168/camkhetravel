<?php

namespace App\Services\LandingTracking;

use App\Models\LandingEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class LandingTrackingComparisonService
{
    public function newEventCount(?CarbonImmutable $reference = null): int
    {
        $periods = $this->periods($this->reference($reference));

        return $this->count($periods['today']['current_from'], $periods['today']['current_to']);
    }

    public function hasNewEvents(?CarbonImmutable $reference = null): bool
    {
        return $this->newEventCount($reference) > 0;
    }

    /**
     * @return array{
     *     has_new_events: bool,
     *     today: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     *     week: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     *     month: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     * }
     */
    public function summary(?CarbonImmutable $reference = null): array
    {
        $periods = $this->periods($this->reference($reference));
        $today = $this->compare($periods['today']);
        $week = $this->compare($periods['week']);
        $month = $this->compare($periods['month']);

        return [
            'has_new_events' => $today['current'] > 0,
            'today' => $today,
            'week' => $week,
            'month' => $month,
        ];
    }

    /**
     * Current periods are measured through today and compared with the same
     * number of calendar days in the previous period.
     *
     * @return array<string, array{label: string, comparison_label: string, current_from: CarbonImmutable, current_to: CarbonImmutable, previous_from: CarbonImmutable, previous_to: CarbonImmutable}>
     */
    private function periods(CarbonImmutable $now): array
    {
        $today = $now->startOfDay();
        $weekStart = $today->startOfWeek(CarbonInterface::MONDAY);
        $previousWeekStart = $weekStart->subWeek();
        $previousWeekEnd = $previousWeekStart
            ->addDays($now->dayOfWeekIso - 1)
            ->endOfDay();

        $monthStart = $today->startOfMonth();
        $previousMonthStart = $monthStart->subMonthNoOverflow()->startOfMonth();
        $previousMonthDay = min($now->day, $previousMonthStart->daysInMonth);
        $previousMonthEnd = $previousMonthStart
            ->addDays($previousMonthDay - 1)
            ->endOfDay();

        return [
            'today' => [
                'label' => 'Lượt tracking hôm nay',
                'comparison_label' => 'hôm qua',
                'current_from' => $today,
                'current_to' => $today->endOfDay(),
                'previous_from' => $today->subDay(),
                'previous_to' => $today->subDay()->endOfDay(),
            ],
            'week' => [
                'label' => 'Lượt tracking tuần này',
                'comparison_label' => 'tuần trước',
                'current_from' => $weekStart,
                'current_to' => $today->endOfDay(),
                'previous_from' => $previousWeekStart,
                'previous_to' => $previousWeekEnd,
            ],
            'month' => [
                'label' => 'Lượt tracking tháng này',
                'comparison_label' => 'tháng trước',
                'current_from' => $monthStart,
                'current_to' => $today->endOfDay(),
                'previous_from' => $previousMonthStart,
                'previous_to' => $previousMonthEnd,
            ],
        ];
    }

    /**
     * @param  array{label: string, comparison_label: string, current_from: CarbonImmutable, current_to: CarbonImmutable, previous_from: CarbonImmutable, previous_to: CarbonImmutable}  $period
     * @return array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float}
     */
    private function compare(array $period): array
    {
        $current = $this->count($period['current_from'], $period['current_to']);
        $previous = $this->count($period['previous_from'], $period['previous_to']);
        $delta = $current - $previous;

        return [
            'label' => $period['label'],
            'comparison_label' => $period['comparison_label'],
            'current' => $current,
            'previous' => $previous,
            'delta' => $delta,
            'percent' => $previous > 0 ? round(($delta / $previous) * 100, 1) : null,
        ];
    }

    private function count(CarbonImmutable $from, CarbonImmutable $to): int
    {
        return LandingEvent::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->count();
    }

    private function reference(?CarbonImmutable $reference): CarbonImmutable
    {
        return ($reference ?? CarbonImmutable::now(config('app.timezone')))
            ->setTimezone(config('app.timezone'));
    }
}
