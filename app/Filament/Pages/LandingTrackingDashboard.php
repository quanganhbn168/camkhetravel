<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\LandingEvents\LandingEventResource;
use App\Filament\Widgets\LandingTrackingComparison;
use App\Models\ContactRequest;
use App\Models\LandingEvent;
use App\Models\LandingPage;
use App\Services\LandingTracking\LandingTrackingComparisonService;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class LandingTrackingDashboard extends Page
{
    protected static ?string $slug = 'landing-tracking-overview';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Tổng quan tracking';

    protected static ?string $title = 'Tổng quan tracking nội bộ';

    protected static string|UnitEnum|null $navigationGroup = 'Khách hàng & tracking';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.landing-tracking-dashboard';

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?string $landingPageId = null;

    public ?string $utmSource = null;

    public static function canAccess(): bool
    {
        return ContactRequestResource::canViewAny();
    }

    public function mount(): void
    {
        $this->resetFilters();
    }

    /** @return array<int, Action> */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('events')
                ->label('Danh sách sự kiện')
                ->icon(Heroicon::OutlinedListBullet)
                ->url(LandingEventResource::getUrl('index')),
            Action::make('leads')
                ->label('Danh sách lead')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->url(ContactRequestResource::getUrl('index')),
        ];
    }

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

    public function getLeadsUrl(): string
    {
        return ContactRequestResource::getUrl('index');
    }

    public function resetFilters(): void
    {
        $today = CarbonImmutable::today();

        $this->dateFrom = $today->subDays(29)->toDateString();
        $this->dateTo = $today->toDateString();
        $this->landingPageId = null;
        $this->utmSource = null;
    }

    /** @return array<int|string, string> */
    public function getLandingPageOptions(): array
    {
        return LandingPage::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    /** @return array<int|string, string> */
    public function getUtmSourceOptions(): array
    {
        return LandingEvent::query()
            ->whereNotNull('utm_source')
            ->where('utm_source', '<>', '')
            ->distinct()
            ->orderBy('utm_source')
            ->pluck('utm_source', 'utm_source')
            ->all();
    }

    /**
     * @return array{
     *     date_label: string,
     *     total_events: int,
     *     views: int,
     *     sessions: int,
     *     leads: int,
     *     intentional_interactions: int,
     *     conversion_rate: float,
     *     event_breakdown: list<array{event_name: string, label: string, description: string, total: int, share: float, color: string}>,
     *     source_breakdown: list<array{label: string, total: int, width: float}>,
     *     landing_breakdown: list<array{title: string, views: int, leads: int, conversion_rate: float}>,
     *     recent_leads: Collection<int, ContactRequest>,
     * }
     */
    public function getDashboardData(): array
    {
        [$from, $to] = $this->period();
        $events = $this->eventQuery($from, $to);
        $views = (clone $events)->where('event_name', 'page_view')->count();
        $sessions = (clone $events)
            ->whereNotNull('session_id')
            ->where('session_id', '<>', '')
            ->distinct()
            ->count('session_id');
        $leads = (clone $events)->where('event_name', 'lead_submit')->count();
        $intentionalInteractions = (clone $events)
            ->whereIn('event_name', ['cta_click', 'pricing_view', 'project_click', 'phone_click', 'zalo_click'])
            ->count();
        $eventBreakdown = app(LandingTrackingComparisonService::class)->eventBreakdown(
            $from,
            $to,
            filled($this->landingPageId) ? (int) $this->landingPageId : null,
            $this->utmSource,
        );

        $sourceBreakdown = (clone $events)
            ->where('event_name', 'page_view')
            ->select('utm_source', DB::raw('COUNT(*) as total'))
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->get()
            ->map(fn (LandingEvent $event): array => [
                'label' => filled($event->utm_source) ? (string) $event->utm_source : 'Trực tiếp / chưa gắn UTM',
                'total' => (int) $event->total,
                'width' => 0,
            ])
            ->groupBy('label')
            ->map(fn ($rows, string $label): array => [
                'label' => $label,
                'total' => (int) $rows->sum('total'),
                'width' => 0,
            ])
            ->sortByDesc('total')
            ->take(8)
            ->values();
        $maxSourceTotal = max(1, (int) $sourceBreakdown->max('total'));
        $sourceBreakdown = $sourceBreakdown
            ->map(fn (array $row): array => [
                ...$row,
                'width' => round(((int) $row['total'] / $maxSourceTotal) * 100, 1),
            ])
            ->all();

        $landingBreakdown = (clone $events)
            ->whereNotNull('landing_page_id')
            ->select('landing_page_id')
            ->selectRaw("SUM(CASE WHEN event_name = 'page_view' THEN 1 ELSE 0 END) as views")
            ->selectRaw("SUM(CASE WHEN event_name = 'lead_submit' THEN 1 ELSE 0 END) as leads")
            ->groupBy('landing_page_id')
            ->orderByDesc('views')
            ->limit(8)
            ->with('landingPage:id,title')
            ->get()
            ->map(function (LandingEvent $event): array {
                $views = (int) $event->views;
                $leads = (int) $event->leads;

                return [
                    'title' => $event->landingPage?->title ?? 'Landing page không còn tồn tại',
                    'views' => $views,
                    'leads' => $leads,
                    'conversion_rate' => $views > 0 ? round(($leads / $views) * 100, 1) : 0.0,
                ];
            })
            ->all();

        $recentLeads = ContactRequest::query()
            ->with('landingPage:id,title')
            ->whereNotNull('landing_page_id')
            ->whereBetween('created_at', [$from, $to])
            ->when(
                filled($this->landingPageId),
                fn (Builder $query): Builder => $query->where('landing_page_id', (int) $this->landingPageId),
            )
            ->when(
                filled($this->utmSource),
                fn (Builder $query): Builder => $query->where('utm_source', $this->utmSource),
            )
            ->latest()
            ->limit(8)
            ->get([
                'id',
                'name',
                'phone',
                'landing_page_id',
                'utm_source',
                'status',
                'created_at',
            ]);

        return [
            'date_label' => $from->format('d/m/Y').' - '.$to->format('d/m/Y'),
            'total_events' => (clone $events)->count(),
            'views' => $views,
            'sessions' => $sessions,
            'leads' => $leads,
            'intentional_interactions' => $intentionalInteractions,
            'conversion_rate' => $views > 0 ? round(($leads / $views) * 100, 1) : 0.0,
            'event_breakdown' => $eventBreakdown,
            'source_breakdown' => $sourceBreakdown,
            'landing_breakdown' => $landingBreakdown,
            'recent_leads' => $recentLeads,
        ];
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    private function period(): array
    {
        $today = CarbonImmutable::today();
        $from = $this->parseDate($this->dateFrom, $today->subDays(29))->startOfDay();
        $to = $this->parseDate($this->dateTo, $today)->endOfDay();

        if ($from->gt($to)) {
            return [$to->startOfDay(), $from->endOfDay()];
        }

        return [$from, $to];
    }

    private function parseDate(?string $value, CarbonImmutable $fallback): CarbonImmutable
    {
        if (blank($value)) {
            return $fallback;
        }

        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);

            return $date instanceof CarbonImmutable ? $date : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function eventQuery(CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return LandingEvent::query()
            ->whereBetween('occurred_at', [$from, $to])
            ->when(
                filled($this->landingPageId),
                fn (Builder $query): Builder => $query->where('landing_page_id', (int) $this->landingPageId),
            )
            ->when(
                filled($this->utmSource),
                fn (Builder $query): Builder => $query->where('utm_source', $this->utmSource),
            );
    }
}
