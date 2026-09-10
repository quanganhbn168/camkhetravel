<?php

namespace Tests\Unit;

use App\Filament\Resources\LandingEvents\LandingEventResource;
use App\Filament\Resources\LandingEvents\Pages\ListLandingEvents;
use App\Filament\Widgets\LandingTrackingComparison;
use App\Models\LandingEvent;
use App\Models\LandingPage;
use App\Services\LandingTracking\LandingTrackingComparisonService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ReflectionMethod;
use Tests\TestCase;

class LandingTrackingComparisonServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_compares_today_week_and_month_with_previous_periods(): void
    {
        $landing = LandingPage::query()->firstOrFail();
        $reference = CarbonImmutable::create(2030, 9, 10, 14, 30, 0, config('app.timezone'));

        $this->record($landing, '2030-09-10 09:00:00');
        $this->record($landing, '2030-09-10 10:00:00');
        $this->record($landing, '2030-09-09 09:00:00');
        $this->record($landing, '2030-09-08 09:00:00');
        $this->record($landing, '2030-09-08 10:00:00');
        $this->record($landing, '2030-09-01 09:00:00');
        $this->record($landing, '2030-09-02 09:00:00');
        $this->record($landing, '2030-09-02 10:00:00');
        $this->record($landing, '2030-09-02 11:00:00');
        $this->record($landing, '2030-08-04 09:00:00');
        $this->record($landing, '2030-08-04 10:00:00');
        $this->record($landing, '2030-08-04 11:00:00');

        $summary = app(LandingTrackingComparisonService::class)->summary($reference);

        $this->assertTrue($summary['has_new_events']);
        $this->assertSame(['current' => 2, 'previous' => 1, 'delta' => 1, 'percent' => 100.0], [
            'current' => $summary['today']['current'],
            'previous' => $summary['today']['previous'],
            'delta' => $summary['today']['delta'],
            'percent' => $summary['today']['percent'],
        ]);
        $this->assertSame(3, $summary['week']['current']);
        $this->assertSame(3, $summary['week']['previous']);
        $this->assertSame(9, $summary['month']['current']);
        $this->assertSame(3, $summary['month']['previous']);
    }

    public function test_it_reports_no_new_events_for_an_empty_day_and_hides_navigation_badge(): void
    {
        $reference = CarbonImmutable::create(2031, 9, 10, 14, 30, 0, config('app.timezone'));
        CarbonImmutable::setTestNow($reference);

        try {
            $service = app(LandingTrackingComparisonService::class);

            $this->assertFalse($service->hasNewEvents());
            $this->assertNull(LandingEventResource::getNavigationBadge());
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_it_exposes_the_widget_only_while_the_day_has_new_events(): void
    {
        $landing = LandingPage::query()->firstOrFail();
        $reference = CarbonImmutable::create(2032, 9, 10, 14, 30, 0, config('app.timezone'));
        CarbonImmutable::setTestNow($reference);

        try {
            $this->record($landing, '2032-09-10 09:00:00');

            $pageMethod = new ReflectionMethod(ListLandingEvents::class, 'getHeaderWidgets');
            $pageMethod->setAccessible(true);
            $widgetMethod = new ReflectionMethod(LandingTrackingComparison::class, 'getStats');
            $widgetMethod->setAccessible(true);

            $this->assertSame(
                [LandingTrackingComparison::class],
                $pageMethod->invoke(new ListLandingEvents),
            );

            $stats = $widgetMethod->invoke(new LandingTrackingComparison);
            $this->assertCount(3, $stats);
            $this->assertSame('Lượt tracking hôm nay', $stats[0]->getLabel());
            $this->assertStringContainsString('so với hôm qua', (string) $stats[0]->getDescription());

            CarbonImmutable::setTestNow($reference->addDay());

            $this->assertSame([], $pageMethod->invoke(new ListLandingEvents));
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    private function record(LandingPage $landing, string $occurredAt): void
    {
        LandingEvent::query()->create([
            'landing_page_id' => $landing->id,
            'event_name' => 'page_view',
            'occurred_at' => $occurredAt,
        ]);
    }
}
