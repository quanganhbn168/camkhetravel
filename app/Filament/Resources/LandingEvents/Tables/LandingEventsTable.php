<?php

namespace App\Filament\Resources\LandingEvents\Tables;

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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'page_view' => 'Xem trang',
                        'cta_click' => 'Bấm CTA',
                        'pricing_view' => 'Xem gói giá',
                        'project_click' => 'Xem dự án',
                        'phone_click' => 'Bấm gọi',
                        'zalo_click' => 'Bấm Zalo',
                        'countdown_view' => 'Xem countdown',
                        'countdown_expired' => 'Countdown hết hạn',
                        'lead_submit' => 'Gửi lead',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'lead_submit' => 'success',
                        'cta_click', 'phone_click', 'zalo_click' => 'warning',
                        'page_view' => 'info',
                        default => 'gray',
                    }),
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
                SelectFilter::make('event_name')->label('Sự kiện')->options(array_combine(
                    LandingEventRecorder::EVENT_NAMES,
                    array_map(
                        fn (string $event): string => match ($event) {
                            'page_view' => 'Xem trang',
                            'cta_click' => 'Bấm CTA',
                            'pricing_view' => 'Xem gói giá',
                            'project_click' => 'Xem dự án',
                            'phone_click' => 'Bấm gọi',
                            'zalo_click' => 'Bấm Zalo',
                            'countdown_view' => 'Xem countdown',
                            'countdown_expired' => 'Countdown hết hạn',
                            'lead_submit' => 'Gửi lead',
                            default => $event,
                        },
                        LandingEventRecorder::EVENT_NAMES,
                    ),
                )),
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
