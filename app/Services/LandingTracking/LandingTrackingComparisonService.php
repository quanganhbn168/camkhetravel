<?php

namespace App\Services\LandingTracking;

use App\Models\LandingEvent;
use App\Support\Landing\LandingEventRecorder;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class LandingTrackingComparisonService
{
    /** @var array<string, array{label: string, description: string, color: string, filament_color: string}> */
    private const EVENT_DEFINITIONS = [
        'page_view' => [
            'label' => 'Lượt xem trang',
            'description' => 'Khách mở landing page.',
            'color' => '#3b82f6',
            'filament_color' => 'info',
        ],
        'lead_submit' => [
            'label' => 'Gửi form thành công',
            'description' => 'Form đã lưu thành công thành yêu cầu tư vấn.',
            'color' => '#16a34a',
            'filament_color' => 'success',
        ],
        'cta_click' => [
            'label' => 'Bấm CTA',
            'description' => 'Khách bấm nút kêu gọi hành động.',
            'color' => '#f59e0b',
            'filament_color' => 'warning',
        ],
        'pricing_view' => [
            'label' => 'Bấm CTA gói giá',
            'description' => 'Khách chọn gói hoặc mở tư vấn từ bảng giá.',
            'color' => '#a855f7',
            'filament_color' => 'primary',
        ],
        'project_click' => [
            'label' => 'Bấm dự án',
            'description' => 'Khách mở một dự án liên quan.',
            'color' => '#0ea5e9',
            'filament_color' => 'info',
        ],
        'phone_click' => [
            'label' => 'Bấm gọi điện',
            'description' => 'Khách bấm số hotline.',
            'color' => '#f97316',
            'filament_color' => 'warning',
        ],
        'zalo_click' => [
            'label' => 'Bấm Zalo',
            'description' => 'Khách bấm nút liên hệ Zalo.',
            'color' => '#14b8a6',
            'filament_color' => 'success',
        ],
        'nav_click' => [
            'label' => 'Bấm điều hướng',
            'description' => 'Khách bấm một liên kết điều hướng trên landing.',
            'color' => '#64748b',
            'filament_color' => 'gray',
        ],
        'countdown_view' => [
            'label' => 'Xem countdown',
            'description' => 'Countdown được khởi tạo trên màn hình.',
            'color' => '#64748b',
            'filament_color' => 'gray',
        ],
        'countdown_expired' => [
            'label' => 'Countdown hết hạn',
            'description' => 'Countdown đã chạm mốc hết hạn.',
            'color' => '#64748b',
            'filament_color' => 'gray',
        ],
    ];

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
     * @return array<string, array{label: string, description: string, color: string, filament_color: string}>
     */
    public function eventDefinitions(): array
    {
        return collect(LandingEventRecorder::EVENT_NAMES)
            ->mapWithKeys(fn (string $eventName): array => [$eventName => $this->eventDefinition($eventName)])
            ->all();
    }

    /**
     * @return array{label: string, description: string, color: string, filament_color: string}
     */
    public function eventDefinition(string $eventName): array
    {
        return self::EVENT_DEFINITIONS[$eventName] ?? [
            'label' => $eventName,
            'description' => 'Sự kiện do hệ thống ghi nhận.',
            'color' => '#64748b',
            'filament_color' => 'gray',
        ];
    }

    public function eventDetail(LandingEvent $event): string
    {
        $payload = is_array($event->payload) ? $event->payload : [];
        $details = collect();

        if (is_scalar($payload['label'] ?? null) && filled((string) $payload['label'])) {
            $details->push(trim((string) $payload['label']));
        }

        foreach ([
            'contact_request_id' => 'Yêu cầu tư vấn #',
            'pricing_plan_id' => 'Gói giá #',
            'project_id' => 'Dự án #',
        ] as $key => $prefix) {
            if (is_scalar($payload[$key] ?? null) && filled((string) $payload[$key])) {
                $details->push($prefix.(int) $payload[$key]);
            }
        }

        return $details->isNotEmpty()
            ? $details->unique()->join(' · ')
            : $this->eventDefinition((string) $event->event_name)['description'];
    }

    /**
     * @return array{
     *     has_new_events: bool,
     *     today: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     *     week: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     *     month: array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float},
     *     event_breakdown: list<array{
     *         event_name: string,
     *         label: string,
     *         description: string,
     *         color: string,
     *         today: array{current: int, previous: int, delta: int, percent: ?float},
     *         week: array{current: int, previous: int, delta: int, percent: ?float},
     *         month: array{current: int, previous: int, delta: int, percent: ?float},
     *     }>,
     * }
     */
    public function summary(?CarbonImmutable $reference = null): array
    {
        $periods = $this->periods($this->reference($reference));
        $periodCounts = [];
        $periodSummaries = [];

        foreach ($periods as $periodName => $period) {
            $currentCounts = $this->countByEvent($period['current_from'], $period['current_to']);
            $previousCounts = $this->countByEvent($period['previous_from'], $period['previous_to']);
            $periodCounts[$periodName] = [
                'current' => $currentCounts,
                'previous' => $previousCounts,
            ];
            $periodSummaries[$periodName] = $this->comparePeriod(
                $period['label'],
                $period['comparison_label'],
                array_sum($currentCounts),
                array_sum($previousCounts),
            );
        }

        $eventNames = collect(array_keys($this->eventDefinitions()));

        foreach ($periodCounts as $counts) {
            $eventNames = $eventNames
                ->merge(array_keys($counts['current']))
                ->merge(array_keys($counts['previous']));
        }

        $eventBreakdown = $eventNames
            ->unique()
            ->map(function (string $eventName) use ($periodCounts): array {
                $definition = $this->eventDefinition($eventName);

                return [
                    'event_name' => $eventName,
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'color' => $definition['color'],
                    'today' => $this->compareCounts(
                        $periodCounts['today']['current'][$eventName] ?? 0,
                        $periodCounts['today']['previous'][$eventName] ?? 0,
                    ),
                    'week' => $this->compareCounts(
                        $periodCounts['week']['current'][$eventName] ?? 0,
                        $periodCounts['week']['previous'][$eventName] ?? 0,
                    ),
                    'month' => $this->compareCounts(
                        $periodCounts['month']['current'][$eventName] ?? 0,
                        $periodCounts['month']['previous'][$eventName] ?? 0,
                    ),
                ];
            })
            ->filter(fn (array $event): bool => collect(['today', 'week', 'month'])
                ->contains(fn (string $period): bool => $event[$period]['current'] > 0 || $event[$period]['previous'] > 0))
            ->values()
            ->all();

        return [
            'has_new_events' => $periodSummaries['today']['current'] > 0,
            'today' => $periodSummaries['today'],
            'week' => $periodSummaries['week'],
            'month' => $periodSummaries['month'],
            'event_breakdown' => $eventBreakdown,
        ];
    }

    /**
     * @return list<array{event_name: string, label: string, description: string, total: int, share: float, color: string}>
     */
    public function eventBreakdown(
        CarbonInterface $from,
        CarbonInterface $to,
        ?int $landingPageId = null,
        ?string $utmSource = null,
    ): array {
        $rows = LandingEvent::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->when($landingPageId, fn ($query) => $query->where('landing_page_id', $landingPageId))
            ->when(filled($utmSource), fn ($query) => $query->where('utm_source', $utmSource))
            ->select('event_name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->get()
            ->map(function (LandingEvent $event): array {
                $definition = $this->eventDefinition((string) $event->event_name);

                return [
                    'event_name' => (string) $event->event_name,
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'total' => (int) $event->total,
                    'share' => 0.0,
                    'color' => $definition['color'],
                ];
            });
        $total = (int) $rows->sum('total');

        return $rows
            ->map(fn (array $row): array => [
                ...$row,
                'share' => $total > 0 ? round(($row['total'] / $total) * 100, 1) : 0.0,
            ])
            ->values()
            ->all();
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
     * @return array{label: string, comparison_label: string, current: int, previous: int, delta: int, percent: ?float}
     */
    private function comparePeriod(string $label, string $comparisonLabel, int $current, int $previous): array
    {
        return [
            'label' => $label,
            'comparison_label' => $comparisonLabel,
            ...$this->compareCounts($current, $previous),
        ];
    }

    /**
     * @return array{current: int, previous: int, delta: int, percent: ?float}
     */
    private function compareCounts(int $current, int $previous): array
    {
        $delta = $current - $previous;

        return [
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

    /** @return array<string, int> */
    private function countByEvent(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return LandingEvent::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->select('event_name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('event_name')
            ->pluck('total', 'event_name')
            ->map(fn (mixed $total): int => (int) $total)
            ->all();
    }

    private function reference(?CarbonImmutable $reference): CarbonImmutable
    {
        return ($reference ?? CarbonImmutable::now(config('app.timezone')))
            ->setTimezone(config('app.timezone'));
    }
}
