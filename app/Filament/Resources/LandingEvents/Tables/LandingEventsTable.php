<?php

namespace App\Filament\Resources\LandingEvents\Tables;

use App\Models\LandingEvent;
use App\Services\LandingTracking\LandingTrackingComparisonService;
use App\Support\Landing\LandingEventRecorder;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LandingEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')->label('Thời gian')->dateTime('d/m/Y H:i:s')->sortable(),
                TextColumn::make('landingPage.title')->label('Landing page')->searchable()->wrap(),
                TextColumn::make('service.title')->label('Dịch vụ')->searchable()->wrap()->toggleable(),
                TextColumn::make('event_name')
                    ->label('Sự kiện')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => app(LandingTrackingComparisonService::class)->eventDefinition($state)['label'])
                    ->color(fn (LandingEvent $record): string => app(LandingTrackingComparisonService::class)->eventDefinition((string) $record->event_name)['filament_color']),
                TextColumn::make('event_name_code')
                    ->label('Mã event')
                    ->getStateUsing(fn (LandingEvent $record): string => (string) $record->event_name)
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event_detail')
                    ->label('Chi tiết')
                    ->getStateUsing(fn (LandingEvent $record): string => app(LandingTrackingComparisonService::class)->eventDetail($record))
                    ->limit(48)
                    ->tooltip(fn (LandingEvent $record): string => app(LandingTrackingComparisonService::class)->eventDetail($record))
                    ->toggleable(),
                TextColumn::make('block_id')->label('Khối')->toggleable(),
                TextColumn::make('utm_source')->label('Nguồn')->badge()->toggleable(),
                TextColumn::make('utm_campaign')->label('Chiến dịch')->toggleable(),
                TextColumn::make('session_id')->label('Phiên')->copyable()->limit(16)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('page_url')->label('URL')->limit(42)->tooltip(fn ($record): ?string => $record->page_url)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('referrer')->label('Referrer')->limit(42)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('landing_page_id')->label('Landing page')->relationship('landingPage', 'title')->searchable()->preload(),
                SelectFilter::make('service_id')->label('Dịch vụ')->relationship('service', 'title')->searchable()->preload(),
                SelectFilter::make('event_name')
                    ->label('Sự kiện')
                    ->options(collect(LandingEventRecorder::EVENT_NAMES)
                        ->mapWithKeys(fn (string $event): array => [
                            $event => app(LandingTrackingComparisonService::class)->eventDefinition($event)['label'],
                        ])
                        ->all()),
                Filter::make('occurred_at')
                    ->label('Khoảng thời gian')
                    ->schema([
                        DatePicker::make('from')->label('Từ ngày'),
                        DatePicker::make('until')->label('Đến ngày'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('occurred_at', '<=', $date))),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->recordActions([])
            ->bulkActions([]);
    }
}
