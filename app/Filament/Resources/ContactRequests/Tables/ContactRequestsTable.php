<?php

namespace App\Filament\Resources\ContactRequests\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ContactRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->copyable()->copyMessage('Đã sao chép')->label('Khách hàng')->searchable()->sortable(),
                TextColumn::make('phone')->label('Điện thoại')->copyable(),
                TextColumn::make('request_type')->label('Loại yêu cầu')->badge()->formatStateUsing(fn (?string $state): string => match ($state) {
                    'trip' => 'Báo giá chuyến đi',
                    'partner' => 'Hợp tác đối tác',
                    'wedding' => 'Xe cưới',
                    'shared' => 'Xe ghép',
                    default => 'Liên hệ chung',
                })->sortable(),
                TextColumn::make('service.title')->copyable()->copyMessage('Đã sao chép')->label('Dịch vụ')->toggleable(),
                TextColumn::make('status')->label('Trạng thái')->badge(),
                TextColumn::make('created_at')->label('Gửi lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'qualified' => 'Tiềm năng', 'closed' => 'Đã xử lý']),
                SelectFilter::make('request_type')->label('Loại yêu cầu')->options([
                    'contact' => 'Liên hệ chung', 'trip' => 'Báo giá chuyến đi', 'partner' => 'Hợp tác đối tác',
                    'wedding' => 'Xe cưới', 'shared' => 'Xe ghép',
                ]),
                SelectFilter::make('service_id')->label('Dịch vụ')->relationship('service', 'title')->searchable()->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
